<?php
require_once 'includes/db_connection.php';
require_once 'includes/functions.php';
require_once 'includes/auth.php';

// Check if form was submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get form data
    $name = isset($_POST['name']) ? $_POST['name'] : '';
    $email = isset($_POST['email']) ? $_POST['email'] : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';
    $confirm_password = isset($_POST['confirm_password']) ? $_POST['confirm_password'] : '';
    $phone = isset($_POST['phone']) ? $_POST['phone'] : '';
    $address = isset($_POST['address']) ? $_POST['address'] : '';
    
    // Validate inputs
    if (empty($name) || empty($email) || empty($password) || empty($confirm_password)) {
        redirect('signup.php?error=2');
    }
    
    // Check if passwords match
    if ($password !== $confirm_password) {
        redirect('signup.php?error=2');
    }
    
    // Register the user
    $result = register_user($name, $email, $password, $phone, $address);
    
    if ($result['success']) {
        // Redirect to login page with success message
        redirect('login.php?registered=1');
    } else {
        // Redirect back to signup page with error
        redirect('signup.php?error=1');
    }
} else {
    // If not submitted via POST, redirect to signup page
    redirect('signup.php');
}
?>
