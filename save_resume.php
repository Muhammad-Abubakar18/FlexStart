<?php
header('Content-Type: application/json');
require_once 'db_connect.php';

// Function to sanitize input
function sanitize_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

try {
    // Start transaction
    $conn->begin_transaction();

    // Personal Information
    $fullName = sanitize_input($_POST['fullName']);
    $email = sanitize_input($_POST['email']);
    $phone = sanitize_input($_POST['phone']);
    $address = sanitize_input($_POST['address']);

    // Insert personal information
    $sql = "INSERT INTO personal_info (full_name, email, phone, address) 
            VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssss", $fullName, $email, $phone, $address);
    $stmt->execute();
    $resume_id = $conn->insert_id;

    // Education
    if (isset($_POST['degree']) && is_array($_POST['degree'])) {
        $sql = "INSERT INTO education (resume_id, degree, university, location, grad_year) 
                VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        
        for ($i = 0; $i < count($_POST['degree']); $i++) {
            $degree = sanitize_input($_POST['degree'][$i]);
            $university = sanitize_input($_POST['university'][$i]);
            $location = sanitize_input($_POST['location'][$i]);
            $gradYear = sanitize_input($_POST['grad-year'][$i]);
            
            $stmt->bind_param("issss", $resume_id, $degree, $university, $location, $gradYear);
            $stmt->execute();
        }
    }

    // Experience
    if (isset($_POST['jobTitle']) && is_array($_POST['jobTitle'])) {
        $sql = "INSERT INTO experience (resume_id, job_title, company, duration, description) 
                VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        
        for ($i = 0; $i < count($_POST['jobTitle']); $i++) {
            $jobTitle = sanitize_input($_POST['jobTitle'][$i]);
            $company = sanitize_input($_POST['company'][$i]);
            $duration = sanitize_input($_POST['duration'][$i]);
            $description = sanitize_input($_POST['description'][$i]);
            
            $stmt->bind_param("issss", $resume_id, $jobTitle, $company, $duration, $description);
            $stmt->execute();
        }
    }

    // Skills
    if (isset($_POST['skills']) && is_array($_POST['skills'])) {
        $sql = "INSERT INTO skills (resume_id, skill_name) VALUES (?, ?)";
        $stmt = $conn->prepare($sql);
        
        foreach ($_POST['skills'] as $skill) {
            $skill = sanitize_input($skill);
            $stmt->bind_param("is", $resume_id, $skill);
            $stmt->execute();
        }
    }

    // Languages
    if (isset($_POST['languages']) && is_array($_POST['languages'])) {
        $sql = "INSERT INTO languages (resume_id, language, proficiency) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        
        for ($i = 0; $i < count($_POST['languages']); $i++) {
            $language = sanitize_input($_POST['languages'][$i]);
            $proficiency = sanitize_input($_POST['language-level'][$i]);
            
            $stmt->bind_param("iss", $resume_id, $language, $proficiency);
            $stmt->execute();
        }
    }

    // Activities
    if (isset($_POST['activities']) && is_array($_POST['activities'])) {
        $sql = "INSERT INTO activities (resume_id, activity) VALUES (?, ?)";
        $stmt = $conn->prepare($sql);
        
        foreach ($_POST['activities'] as $activity) {
            $activity = sanitize_input($activity);
            $stmt->bind_param("is", $resume_id, $activity);
            $stmt->execute();
        }
    }

    // Professional Summary
    if (isset($_POST['summary'])) {
        $summary = sanitize_input($_POST['summary']);
        $sql = "INSERT INTO summary (resume_id, content) VALUES (?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("is", $resume_id, $summary);
        $stmt->execute();
    }

    // Handle profile picture if uploaded
    if (isset($_FILES['profile_pic']) && $_FILES['profile_pic']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = 'uploads/';
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        
        $fileName = $resume_id . '_' . basename($_FILES['profile_pic']['name']);
        $uploadFile = $uploadDir . $fileName;
        
        if (move_uploaded_file($_FILES['profile_pic']['tmp_name'], $uploadFile)) {
            $sql = "UPDATE personal_info SET profile_pic = ? WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("si", $fileName, $resume_id);
            $stmt->execute();
        }
    }

    // Commit transaction
    $conn->commit();
    
    echo json_encode([
        'success' => true,
        'message' => 'Resume saved successfully',
        'resume_id' => $resume_id
    ]);

} catch (Exception $e) {
    // Rollback transaction on error
    $conn->rollback();
    
    echo json_encode([
        'success' => false,
        'message' => 'Error saving resume: ' . $e->getMessage()
    ]);
}

$conn->close();

// Assume you have the resume_id or user_id
$resume_id = $_POST['resume_id'] ?? null;
$profile_pic_filename = 'profilepic1.jpeg'; // default

if ($resume_id) {
    require_once 'db_connect.php';
    $sql = "SELECT profile_pic FROM personal_info WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $resume_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        if (!empty($row['profile_pic'])) {
            $profile_pic_filename = 'uploads/' . $row['profile_pic'];
        }
    }
}
?> 