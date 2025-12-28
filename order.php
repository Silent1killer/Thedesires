<?php
$page_title = "Order History";
require_once 'includes/db_connection.php';
require_once 'includes/functions.php';
require_once 'includes/auth.php';

// Check if user is logged in
if (!is_logged_in()) {
    redirect('login.php');
}

// Get user data
$user_id = $_SESSION['user_id'];
$user = get_user_by_id($user_id);

// Get user orders
$orders = get_user_orders($user_id);

// Order details if specific order is requested
$order_details = null;
$order_items = [];
if (isset($_GET['id']) && !empty($_GET['id'])) {
    $order_id = (int)$_GET['id'];
    $order_details = get_order_by_id($order_id);
    
    // Make sure the order belongs to the current user
    if ($order_details && $order_details['user_id'] == $user_id) {
        $order_items = get_order_items($order_id);
    } else {
        $order_details = null;
    }
}

// Include header
include 'includes/header.php';
?>

<!-- Order History Banner -->
<section class="page-banner" style="background-image: linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.6)), url('https://source.unsplash.com/1600x400/?restaurant,food');">
    <div class="container">
        <h1>Order History</h1>
        <p>View your previous orders and track current ones</p>
    </div>
</section>

<!-- Order History Section -->
<section class="profile-section">
    <div class="container">
        <?php if ($order_details): ?>
            <!-- Single Order Details -->
            <div class="order-details-container">
                <div class="section-header">
                    <h2>Order #<?php echo htmlspecialchars($order_details['order_number']); ?></h2>
                    <a href="order.php?history=1" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back to Orders
                    </a>
                </div>
                
                <div class="order-status-container">
                    <div class="order-info-item">
                        <span class="label">Status:</span>
                        <span class="value status-<?php echo strtolower($order_details['status']); ?>">
                            <?php echo htmlspecialchars($order_details['status']); ?>
                        </span>
                    </div>
                    <div class="order-info-item">
                        <span class="label">Order Date:</span>
                        <span class="value"><?php echo date('F d, Y H:i', strtotime($order_details['order_date'])); ?></span>
                    </div>
                    <div class="order-info-item">
                        <span class="label">Payment Method:</span>
                        <span class="value"><?php echo htmlspecialchars($order_details['payment_method']); ?></span>
                    </div>
                </div>
                
                <div class="order-details-content">
                    <div class="cart-container">
                        <h3>Order Items</h3>
                        <div class="cart-items">
                            <?php foreach ($order_items as $item): ?>
                                <div class="cart-item">
                                    <div class="cart-item-image" style="background-image: url('<?php echo htmlspecialchars($item['image_url']); ?>')"></div>
                                    <div class="cart-item-details">
                                        <h4 class="cart-item-name"><?php echo htmlspecialchars($item['name']); ?></h4>
                                        <div class="cart-item-price"><?php echo format_currency($item['price']); ?> × <?php echo $item['quantity']; ?></div>
                                    </div>
                                    <div class="cart-item-subtotal"><?php echo format_currency($item['price'] * $item['quantity']); ?></div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        
                        <div class="cart-summary">
                            <div class="cart-summary-item">
                                <span>Subtotal</span>
                                <span><?php echo format_currency($order_details['subtotal']); ?></span>
                            </div>
                            <div class="cart-summary-item">
                                <span>Delivery Fee</span>
                                <span><?php echo format_currency($order_details['delivery_fee']); ?></span>
                            </div>
                            <div class="cart-summary-item">
                                <span>Tax</span>
                                <span><?php echo format_currency($order_details['tax']); ?></span>
                            </div>
                            <div class="cart-summary-total">
                                <span>Total</span>
                                <span><?php echo format_currency($order_details['total_amount']); ?></span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="order-address-container">
                        <h3>Delivery Address</h3>
                        <p><?php echo htmlspecialchars($order_details['delivery_address']); ?></p>
                        
                        <h3>Contact Information</h3>
                        <p>Name: <?php echo htmlspecialchars($user['name']); ?></p>
                        <p>Phone: <?php echo htmlspecialchars($user['phone'] ?? 'Not provided'); ?></p>
                        <p>Email: <?php echo htmlspecialchars($user['email']); ?></p>
                        
                        <?php if (!empty($order_details['special_instructions'])): ?>
                            <h3>Special Instructions</h3>
                            <p><?php echo htmlspecialchars($order_details['special_instructions']); ?></p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <!-- Order History List -->
            <div class="order-history-container">
                <h2>Your Orders</h2>
                
                <?php if (empty($orders)): ?>
                    <div class="empty-cart">
                        <i class="fas fa-shopping-bag"></i>
                        <p>You haven't placed any orders yet.</p>
                        <a href="menu.php" class="btn">Browse Our Menu</a>
                    </div>
                <?php else: ?>
                    <div class="order-history">
                        <?php foreach ($orders as $order): ?>
                            <div class="order-card">
                                <div class="order-header">
                                    <span class="order-id"><?php echo htmlspecialchars($order['order_number']); ?></span>
                                    <span class="order-date"><?php echo date('M d, Y', strtotime($order['order_date'])); ?></span>
                                    <span class="order-status status-<?php echo strtolower($order['status']); ?>">
                                        <?php echo htmlspecialchars($order['status']); ?>
                                    </span>
                                </div>
                                <div class="order-details">
                                    <?php 
                                    $order_items = get_order_items($order['id']);
                                    ?>
                                    <div class="order-items-list">
                                        <?php foreach ($order_items as $item): ?>
                                            <div class="order-items-list-item">
                                                <div class="order-item-details">
                                                    <span><?php echo htmlspecialchars($item['name']); ?></span>
                                                    <span class="order-item-quantity">x<?php echo $item['quantity']; ?></span>
                                                </div>
                                                <div class="order-item-price">
                                                    <?php echo format_currency($item['price'] * $item['quantity']); ?>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                    <div class="order-total">
                                        Total: <?php echo format_currency($order['total_amount']); ?>
                                    </div>
                                    <div class="order-actions">
                                        <a href="order.php?id=<?php echo $order['id']; ?>" class="btn">View Details</a>
                                        <?php if ($order['status'] == 'Pending' || $order['status'] == 'Processing'): ?>
                                            <a href="cancel_order.php?id=<?php echo $order['id']; ?>" class="btn btn-danger" onclick="return confirm('Are you sure you want to cancel this order?');">Cancel Order</a>
                                        <?php endif; ?>
                                        <?php if ($order['status'] == 'Completed'): ?>
                                            <a href="reorder.php?id=<?php echo $order['id']; ?>" class="btn btn-secondary">Reorder</a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
