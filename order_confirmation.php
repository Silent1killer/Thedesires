<?php
$page_title = "Order Confirmation";
require_once 'includes/db_connection.php';
require_once 'includes/functions.php';

// Check if order ID is provided
if (!isset($_GET['order_id']) || !is_numeric($_GET['order_id'])) {
    redirect('menu.php');
}

$order_id = (int)$_GET['order_id'];
$order = get_order_by_id($order_id);

// If order doesn't exist, redirect to menu page
if (!$order) {
    redirect('menu.php');
}

// Get order items
$order_items = get_order_items($order_id);

// Include header
include 'includes/header.php';
?>

<style>
.confirmation-section, .confirmation-details, .confirmation-totals{
    padding: 10px;
    margin: 10px;
    align-items: center;
    justify-content: center;

}

</style>

<!-- Confirmation Banner -->
<section class="page-banner" style="background-image: linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.6)), url('https://source.unsplash.com/1600x400/?restaurant,food');">
    <div class="container">
        <h1>Order Confirmation</h1>
        <p>Thank you for your order!</p>
    </div>
</section>

<!-- Confirmation Section -->
<section class="confirmation-section">
    <div class="container">
        <div class="confirmation-message">
            <div class="icon">
                <i class="fas fa-check-circle"></i>
            </div>
            <h2>Your Order Has Been Placed</h2>
            <p>We've received your order and will begin processing it right away.</p>
            <div class="order-number">
                <strong>Order Number:</strong> <?php echo htmlspecialchars($order['order_number']); ?>
            </div>
        </div>
        
        <div class="confirmation-details">
            <div class="confirmation-summary">
                <h3>Order Summary</h3>
                <div class="confirmation-items">
                    <?php foreach ($order_items as $item): ?>
                        <div class="confirmation-item">
                            <div class="confirmation-item-name">
                                <?php echo htmlspecialchars($item['name']); ?> × <?php echo $item['quantity']; ?>
                            </div>
                            <div class="confirmation-item-price">
                                <?php echo format_currency($item['price'] * $item['quantity']); ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                <div class="confirmation-totals">
                    <div class="confirmation-total-item">
                        <span>Subtotal</span>
                        <span><?php echo format_currency($order['subtotal']); ?></span>
                    </div>
                    <div class="confirmation-total-item">
                        <span>Delivery Fee</span>
                        <span><?php echo format_currency($order['delivery_fee']); ?></span>
                    </div>
                    <div class="confirmation-total-item">
                        <span>Tax</span>
                        <span><?php echo format_currency($order['tax']); ?></span>
                    </div>
                    <div class="confirmation-total-item total">
                        <span>Total</span>
                        <span><?php echo format_currency($order['total_amount']); ?></span>
                    </div>
                </div>
            </div>
            
            <div class="confirmation-info">
                <div class="confirmation-info-section">
                    <h3>Delivery Information</h3>
                    <p><strong>Name:</strong> <?php echo htmlspecialchars($order['name']); ?></p>
                    <p><strong>Email:</strong> <?php echo htmlspecialchars($order['email']); ?></p>
                    <p><strong>Phone:</strong> <?php echo htmlspecialchars($order['phone']); ?></p>
                    <p><strong>Address:</strong> <?php echo htmlspecialchars($order['delivery_address']); ?></p>
                    <?php if (!empty($order['special_instructions'])): ?>
                        <p><strong>Special Instructions:</strong> <?php echo htmlspecialchars($order['special_instructions']); ?></p>
                    <?php endif; ?>
                </div>
                
                <div class="confirmation-info-section">
                    <h3>Payment Information</h3>
                    <p><strong>Payment Method:</strong> <?php echo htmlspecialchars($order['payment_method']); ?></p>
                    <p><strong>Order Date:</strong> <?php echo date('F d, Y H:i', strtotime($order['order_date'])); ?></p>
                    <p><strong>Order Status:</strong> <?php echo htmlspecialchars($order['status']); ?></p>
                </div>
            </div>
        </div>
        
        <div class="confirmation-actions">
            <a href="index.php" class="btn">Return to Home</a>
            <a href="menu.php" class="btn btn-secondary">Continue Shopping</a>
            <?php if (is_logged_in()): ?>
                <a href="order.php?id=<?php echo $order_id; ?>" class="btn">View Order Details</a>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- What's Next Section -->
<section class="features">
    <div class="container">
        <h2 class="section-title">What's Next?</h2>
        
        <div class="features-container">
            <div class="feature-box">
                <i class="fas fa-clock"></i>
                <h3>Order Processing</h3>
                <p>We'll begin preparing your order. Delivery usually takes 30-45 minutes depending on your location.</p>
            </div>
            <div class="feature-box">
                <i class="fas fa-envelope"></i>
                <h3>Email Confirmation</h3>
                <p>You'll receive an email confirmation with your order details and tracking information.</p>
            </div>
            <div class="feature-box">
                <i class="fas fa-truck"></i>
                <h3>Delivery</h3>
                <p>Our delivery partner will bring your food directly to your door. Enjoy your meal!</p>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
