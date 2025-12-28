<?php
require_once 'includes/db_connection.php';
require_once 'includes/functions.php';

// Check if form was submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get form data
    $name = isset($_POST['name']) ? sanitize_input($_POST['name']) : '';
    $email = isset($_POST['email']) ? sanitize_input($_POST['email']) : '';
    $subject = isset($_POST['subject']) ? sanitize_input($_POST['subject']) : '';
    $message = isset($_POST['message']) ? sanitize_input($_POST['message']) : '';
    
    // Validate inputs
    if (empty($name) || empty($email) || empty($subject) || empty($message)) {
        redirect('contact.php?error=1');
    }
    
    // Insert message into database
    $query = "INSERT INTO contact_messages (name, email, subject, message, created_at)
              VALUES (?, ?, ?, ?, NOW())";
    
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ssss", $name, $email, $subject, $message);
    
    if ($stmt->execute()) {
        // Send email notification to admin (in a production environment)
        // mail('admin@thedesires.com', 'New Contact Form Submission', $message, "From: $email");
        
        // Redirect to contact page with success message
        redirect('contact.php?success=1');
    } else {
        // Redirect back to contact page with error
        redirect('contact.php?error=2');
    }
} else {
    // If not submitted via POST, redirect to contact page
    redirect('contact.php');
}
?>
