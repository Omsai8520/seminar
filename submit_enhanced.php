<?php
require 'db.php';

// Set content type to JSON for AJAX requests
header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get POST data and sanitize
    $name = trim($conn->real_escape_string($_POST['name']));
    $email = trim($conn->real_escape_string($_POST['email']));
    $message = trim($conn->real_escape_string($_POST['message']));
    
    // Validation
    $errors = [];
    
    if (empty($name)) {
        $errors[] = "Name is required";
    }
    
    if (empty($email)) {
        $errors[] = "Email is required";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Please enter a valid email address";
    }
    
    if (empty($message)) {
        $errors[] = "Message is required";
    }
    
    if (empty($errors)) {
        // Insert into database
        $sql = "INSERT INTO feedback (name, email, message) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sss", $name, $email, $message);
        
        if ($stmt->execute()) {
            $response = [
                'success' => true,
                'message' => 'Thank you for your feedback! We appreciate your input.'
            ];
        } else {
            $response = [
                'success' => false,
                'message' => 'Database error: ' . $stmt->error
            ];
        }
        
        $stmt->close();
    } else {
        $response = [
            'success' => false,
            'message' => implode(', ', $errors)
        ];
    }
} else {
    $response = [
        'success' => false,
        'message' => 'Invalid request method'
    ];
}

$conn->close();

// Return JSON response
echo json_encode($response);
?> 