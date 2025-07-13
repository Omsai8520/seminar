
<?php
header('Content-Type: application/json'); // Set header to indicate JSON response

$response = ['success' => false, 'message' => 'An unknown error occurred.'];

// Check if $_SERVER is set and if 'REQUEST_METHOD' key exists within it
if (isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize and validate inputs
    $name = isset($_POST['name']) ? htmlspecialchars(trim($_POST['name'])) : '';
    $email = isset($_POST['email']) ? htmlspecialchars(trim($_POST['email'])) : '';
    $message = isset($_POST['message']) ? htmlspecialchars(trim($_POST['message'])) : '';

    if (empty($message)) {
        $response['message'] = 'Feedback message cannot be empty.';
        echo json_encode($response);
        exit;
    }

    // Optional: Basic email validation if provided
    if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $response['message'] = 'Invalid email format.';
        echo json_encode($response);
        exit;
    }

    // Define the file to store feedback
    $feedbackFile = 'feedback.txt';

    // Prepare data to save
    $timestamp = date('Y-m-d H:i:s');
    $feedbackEntry = "Timestamp: {$timestamp}\n";
    $feedbackEntry .= "Name: " . ($name ?: 'N/A') . "\n";
    $feedbackEntry .= "Email: " . ($email ?: 'N/A') . "\n";
    $feedbackEntry .= "Message:\n{$message}\n";
    $feedbackEntry .= "----------------------------------------\n\n";

    // Append feedback to the file
    // FILE_APPEND flag ensures data is added to the end of the file
    // LOCK_EX flag prevents anyone else from writing to the file at the same time
    if (file_put_contents($feedbackFile, $feedbackEntry, FILE_APPEND | LOCK_EX) !== false) {
        $response['success'] = true;
        $response['message'] = 'Feedback submitted successfully!';
    } else {
        $response['message'] = 'Failed to save feedback. Please check server permissions.';
    }
} else {
    // This block is executed if the request is not a POST request
    $response['message'] = 'Invalid request method.';
}

echo json_encode($response);
?>