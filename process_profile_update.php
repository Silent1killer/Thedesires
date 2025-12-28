<?php
require_once 'includes/db_connection.php';
require_once 'includes/functions.php';
require_once 'includes/auth.php';

// Check if user is logged in
if (!is_logged_in()) {
    redirect('login.php');
}

// Check if form was submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get form data
    $user_id = isset($_POST['user_id']) ? (int)$_POST['user_id'] : 0;
    $name = isset($_POST['name']) ? sanitize_input($_POST['name']) : '';
    $email = isset($_POST['email']) ? sanitize_input($_POST['email']) : '';
    $phone = isset($_POST['phone']) ? sanitize_input($_POST['phone']) : '';
    $address = isset($_POST['address']) ? sanitize_input($_POST['address']) : '';
    
    // Validate inputs
    if (empty($name) || empty($email) || $user_id <= 0 || $user_id != $_SESSION['user_id']) {
        redirect('profile.php?error=2');
    }
    
    // Update user profile
    $result = update_user_profile($user_id, $name, $email, $phone, $address);
    
    if ($result['success']) {
        redirect('profile.php?profile_updated=1');
    } else {
        if (strpos($result['message'], 'already registered') !== false) {
            redirect('profile.php?error=3');
        } else {
            redirect('profile.php?error=2');
        }
    }
} else {
    // If not submitted via POST, redirect to profile page
    redirect('profile.php');
}
?>
