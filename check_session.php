<?php
// Start the session if it hasn't been started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Set header to return JSON
header('Content-Type: application/json');

// Simple response based on session status
if (isset($_SESSION['user_id']) && isset($_SESSION['fullname'])) {
    echo json_encode([
        'logged_in' => true,
        'fullname' => $_SESSION['fullname']
    ]);
} else {
    echo json_encode([
        'logged_in' => false
    ]);
}
?> 