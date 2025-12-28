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
    $current_password = isset($_POST['current_password']) ? $_POST['current_password'] : '';
    $new_password = isset($_POST['new_password']) ? $_POST['new_password'] : '';
    $confirm_password = isset($_POST['confirm_password']) ? $_POST['confirm_password'] : '';
    
    // Validate inputs
    if (empty($current_password) || empty($new_password) || empty($confirm_password) || $user_id <= 0 || $user_id != $_SESSION['user_id']) {
        redirect('profile.php?tab=password&error=2');
    }
    
    // Check if passwords match
    if ($new_password !== $confirm_password) {
        redirect('profile.php?tab=password&error=2');
    }
    
    // Change password
    $result = change_user_password($user_id, $current_password, $new_password);
    
    if ($result['success']) {
        redirect('profile.php?tab=password&password_changed=1');
    } else {
        if (strpos($result['message'], 'incorrect') !== false) {
            redirect('profile.php?tab=password&error=1');
        } else {
            redirect('profile.php?tab=password&error=2');
        }
    }
} else {
    // If not submitted via POST, redirect to profile page
    redirect('profile.php');
}
?>
