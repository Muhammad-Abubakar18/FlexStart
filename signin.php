<?php
// Prevent any output before headers
ob_start();

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 0); // Don't display errors to client
ini_set('log_errors', 1);     // Log errors instead

require_once 'db_connect.php';

// Function to send JSON response
function sendJsonResponse($success, $message, $data = []) {
    // Ensure data is an array
    if (!is_array($data)) {
        $data = [];
    }
    
    // Ensure redirect URL is set if success is true
    if ($success && !isset($data['redirect'])) {
        $data['redirect'] = 'template.html';
    }
    
    header('Content-Type: application/json');
    echo json_encode([
        'success' => $success,
        'message' => $message,
        'data' => $data
    ]);
    exit;
}

try {
    // Check if it's a POST request
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Invalid request method');
    }

    // Log received data for debugging
    error_log('Received POST data: ' . print_r($_POST, true));

    // Get and sanitize input
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'] ?? '';

    // Validate input
    if (empty($email) || empty($password)) {
        throw new Exception('Email and password are required');
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        throw new Exception('Invalid email format');
    }

    // Get user from database
    $stmt = $conn->prepare("SELECT id, fullname, email, password FROM users WHERE email = ?");
    if (!$stmt) {
        throw new Exception('Database error: ' . $conn->error);
    }

    $stmt->bind_param("s", $email);
    if (!$stmt->execute()) {
        throw new Exception('Database error: ' . $stmt->error);
    }

    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        throw new Exception('Invalid email or password');
    }

    $user = $result->fetch_assoc();

    // Verify password using password_verify()
    // This function automatically:
    // 1. Extracts the salt from the stored hash
    // 2. Hashes the provided password with the same salt
    // 3. Compares the hashes
    if (!password_verify($password, $user['password'])) {
        throw new Exception('Invalid email or password');
    }

    // Start session and store user info
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['fullname'] = $user['fullname'];
    $_SESSION['email'] = $user['email'];

    // Send success response with redirect URL
    sendJsonResponse(true, 'Login successful', [
        'redirect' => 'C:\xampp\htdocs\FlexStart\template.html'
    ]);

} catch (Exception $e) {
    error_log('Sign in error: ' . $e->getMessage());
    http_response_code(400);
    sendJsonResponse(false, $e->getMessage());
} catch (Error $e) {
    error_log('PHP Error: ' . $e->getMessage());
    http_response_code(500);
    sendJsonResponse(false, 'An unexpected error occurred');
}
?> 