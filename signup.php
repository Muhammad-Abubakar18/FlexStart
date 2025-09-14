<?php
require_once 'db_connect.php';

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Content-Type: application/json');

try {
    // Check if it's a POST request
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Invalid request method');
    }

    // Get and sanitize input
    $fullname = filter_input(INPUT_POST, 'fullname', FILTER_SANITIZE_STRING);
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Validate input
    if (!$fullname || !$email || !$password || !$confirm_password) {
        throw new Exception('All fields are required');
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        throw new Exception('Invalid email format');
    }

    if ($password !== $confirm_password) {
        throw new Exception('Passwords do not match');
    }

    if (strlen($password) < 6) {
        throw new Exception('Password must be at least 6 characters long');
    }

    // Start transaction
    $conn->begin_transaction();

    try {
        // Check if email already exists
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            throw new Exception('Email already registered');
        }

        // Hash password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Insert new user
        $stmt = $conn->prepare("INSERT INTO users (fullname, email, password) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $fullname, $email, $hashed_password);
        
        if (!$stmt->execute()) {
            throw new Exception('Failed to create account');
        }

        $user_id = $stmt->insert_id;

        // Create initial personal_info entry
        $stmt = $conn->prepare("INSERT INTO personal_info (user_id, full_name, email) VALUES (?, ?, ?)");
        $stmt->bind_param("iss", $user_id, $fullname, $email);
        
        if (!$stmt->execute()) {
            throw new Exception('Failed to create personal information');
        }

        // Commit transaction
        $conn->commit();

        // Start session and store user info
        session_start();
        $_SESSION['user_id'] = $user_id;
        $_SESSION['fullname'] = $fullname;
        $_SESSION['email'] = $email;

        echo json_encode([
            'success' => true,
            'message' => 'Account created successfully',
            'redirect' => 'step1-personal.html'
        ]);

    } catch (Exception $e) {
        // Rollback transaction on error
        $conn->rollback();
        throw $e;
    }

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?> 