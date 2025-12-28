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

// Check if the order can be cancelled (only Pending or Processing orders can be cancelled)
if ($order['status'] != 'Pending' && $order['status'] != 'Processing') {
    redirect('order.php?id=' . $order_id . '&error=1');
}

// Cancel the order
$query = "UPDATE orders SET status = 'Cancelled', updated_at = NOW() WHERE id = ? AND user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("ii", $order_id, $user_id);

if ($stmt->execute()) {
    redirect('order.php?id=' . $order_id . '&cancelled=1');
} else {
    redirect('order.php?id=' . $order_id . '&error=2');
}
?>
