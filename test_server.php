<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Set proper headers
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Accept');

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Log request details
error_log("Request Method: " . $_SERVER['REQUEST_METHOD']);
error_log("Request URI: " . $_SERVER['REQUEST_URI']);
error_log("Content Type: " . $_SERVER['CONTENT_TYPE']);

// Check if this is a POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Log POST data
    error_log("POST data: " . print_r($_POST, true));
    error_log("FILES data: " . print_r($_FILES, true));
    
    // Return success response
    echo json_encode([
        'success' => true,
        'message' => 'POST request received successfully',
        'data' => [
            'post' => $_POST,
            'files' => $_FILES
        ]
    ]);
} else {
    // Return error for non-POST requests
    http_response_code(405);
    echo json_encode([
        'success' => false,
        'message' => 'Method not allowed. Only POST requests are accepted.',
        'received_method' => $_SERVER['REQUEST_METHOD']
    ]);
}
?> 