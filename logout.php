<?php
require_once 'includes/db_connection.php';
require_once 'includes/functions.php';
require_once 'includes/auth.php';

// Log out the user
$result = logout_user();

// Redirect to home page
redirect('index.php');


// function logout_user() {
//     ensure_session_started();
    
//     // Unset session variables
//     $_SESSION = [];
    
//     // Destroy the session
//     session_destroy();
    
//     return ['success' => true, 'message' => 'Logout successful!'];
// }

// ?>
