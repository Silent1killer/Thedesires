<?php
require_once 'includes/db_connection.php';
require_once 'includes/functions.php';

// Check if form was submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get form data
    $item_id = isset($_POST['item_id']) ? (int)$_POST['item_id'] : 0;
    $quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;
    
    // Validate inputs
    if ($item_id <= 0 || $quantity <= 0) {
        redirect('menu.php');
    }
    
    // Add item to cart
    add_to_cart($item_id, $quantity);
    
    // Get the referring page
    $referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'menu.php';
    
    // Redirect back to the referring page with success message
    // redirect('cart.php?added=1');
    
// } else {
    // If not submitted via POST, redirect to menu page
    redirect('menu.php');
}
?>
