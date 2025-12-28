<?php
require_once 'includes/db_connection.php';
require_once 'includes/functions.php';
require_once 'includes/auth.php';

// Check if form was submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get form data
    $email = isset($_POST['email']) ? $_POST['email'] : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';
    
    // Validate inputs
    if (empty($email) || empty($password)) {
        redirect('login.php?error=1');
    }
    
    // Attempt to log in
    $result = login_user($email, $password);
    
    if ($result['success']) {
        // Check if there's a redirect URL
        if (isset($_SESSION['redirect_url'])) {
            $redirect_url = $_SESSION['redirect_url'];
            unset($_SESSION['redirect_url']);
            redirect($redirect_url);
        } else {
            // Redirect to home page
            redirect('index.php');
        }
    } else {
        // Redirect back to login page with error
        redirect('login.php?error=1');
    }
} else {
    // If not submitted via POST, redirect to login page
    redirect('login.php');
}
?>
