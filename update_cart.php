<?php
require_once 'includes/db_connection.php';
require_once 'includes/functions.php';

// Check if form was submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get form data
    $item_id = isset($_POST['item_id']) ? (int)$_POST['item_id'] : 0;
    $quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 0;
    
    // Validate inputs
    if ($item_id <= 0 || $quantity <= 0) {
        redirect('cart.php');
    }
    
    // Ensure session is started
    ensure_session_started();
    
    // Update cart quantity
    if (isset($_SESSION['cart']) && isset($_SESSION['cart'][$item_id])) {
        $_SESSION['cart'][$item_id] = $quantity;
    }
    
    // Redirect back to cart
    redirect('cart.php?updated=1');
} else {
    // If not submitted via POST, redirect to cart
    redirect('cart.php');
}
?>
