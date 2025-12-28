<?php
require_once 'includes/db_connection.php';
require_once 'includes/functions.php';

// Check if form was submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get form data
    $item_id = isset($_POST['item_id']) ? (int)$_POST['item_id'] : 0;
    
    // Validate inputs
    if ($item_id <= 0) {
        redirect('cart.php');
    }
    
    // Remove item from cart
    remove_from_cart($item_id);
    
    // Redirect back to cart
    redirect('cart.php?removed=1');
} else {
    // If not submitted via POST, redirect to cart
    redirect('cart.php');
}
?>
