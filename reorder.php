<?php
require_once 'includes/db_connection.php';
require_once 'includes/functions.php';
require_once 'includes/auth.php';

// Check if user is logged in
if (!is_logged_in()) {
    redirect('login.php');
}

// Check if order ID is provided
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    redirect('order.php?history=1');
}

$order_id = (int)$_GET['id'];
$user_id = $_SESSION['user_id'];

// Get the order
$order = get_order_by_id($order_id);

// Verify that the order exists and belongs to the current user
if (!$order || $order['user_id'] != $user_id) {
    redirect('order.php?history=1');
}

// Get order items
$order_items = get_order_items($order_id);

// Clear current cart
$_SESSION['cart'] = [];

// Add order items to cart
foreach ($order_items as $item) {
    add_to_cart($item['menu_item_id'], $item['quantity']);
}

// Redirect to cart
redirect('cart.php?reordered=1');
?>
