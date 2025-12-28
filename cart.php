<?php
$page_title = "Shopping Cart";
require_once 'includes/db_connection.php';
require_once 'includes/functions.php';

// Get cart items
$cart_items = get_cart_items();
$cart_total = get_cart_total();

// Success/error messages
$message = '';
$message_type = '';

if (isset($_GET['added']) && $_GET['added'] == 1) {
    $message = 'Item added to cart successfully!';
    $message_type = 'success';
} elseif (isset($_GET['removed']) && $_GET['removed'] == 1) {
    $message = 'Item removed from cart.';
    $message_type = 'success';
} elseif (isset($_GET['updated']) && $_GET['updated'] == 1) {
    $message = 'Cart updated successfully!';
    $message_type = 'success';
}

// Include header
include 'includes/header.php';
?>

<!-- Cart Banner -->
<section class="page-banner" style="background-image: linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.6)), url('https://source.unsplash.com/1600x400/?restaurant,food');">
    <div class="container">
        <h1>Your Cart</h1>
        <p>Review your items and proceed to checkout</p>
    </div>
</section>

<!-- Cart Section -->
<section class="cart-section">
    <div class="container">
        <?php if (!empty($message)): ?>
            <div class="alert alert-<?php echo $message_type; ?>">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>
        
        <?php if (empty($cart_items)): ?>
            <div class="empty-cart">
                <i class="fas fa-shopping-cart"></i>
                <p>Your cart is empty.</p>
                <a href="menu.php" class="btn">Browse Our Menu</a>
            </div>
        <?php else: ?>
            <div class="cart-container">
                <h2>Shopping Cart</h2>
                
                <div class="cart-items">
                    <?php foreach ($cart_items as $item): ?>
                        <div class="cart-item">
                            <div class="cart-item-image" style="background-image: url('<?php echo htmlspecialchars($item['image_url']); ?>')"></div>
                            <div class="cart-item-details">
                                <h3 class="cart-item-name"><?php echo htmlspecialchars($item['name']); ?></h3>
                                <p class="cart-item-price"><?php echo format_currency($item['price']); ?></p>
                            </div>
                            <form action="update_cart.php" method="post" class="cart-item-quantity">
                                <input type="hidden" name="item_id" value="<?php echo $item['id']; ?>">
                                <button type="button" class="quantity-btn decrement">-</button>
                                <input type="number" name="quantity" value="<?php echo $item['quantity']; ?>" min="1" class="quantity-input" readonly>
                                <button type="button" class="quantity-btn increment">+</button>
                            </form>
                            <div class="cart-item-subtotal"><?php echo format_currency($item['subtotal']); ?></div>
                            <form action="remove_from_cart.php" method="post">
                                <input type="hidden" name="item_id" value="<?php echo $item['id']; ?>">
                                <button type="submit" class="cart-item-remove"><i class="fas fa-trash-alt"></i></button>
                            </form>
                        </div>
                    <?php endforeach; ?>
                </div>
                
                <div class="cart-summary">
                    <h3 class="cart-summary-title">Order Summary</h3>
                    <div class="cart-summary-item">
                        <span>Subtotal</span>
                        <span><?php echo format_currency($cart_total); ?></span>
                    </div>
                    <div class="cart-summary-item">
                        <span>Delivery Fee</span>
                        <span><?php echo format_currency(80.00); ?></span>
                    </div>
                    <div class="cart-summary-item">
                        <span>Tax (8%)</span>
                        <span><?php echo format_currency($cart_total * 0.08); ?></span>
                    </div>
                    <div class="cart-summary-total">
                        <span>Total</span>
                        <span><?php echo format_currency($cart_total + 80.00 + ($cart_total * 0.08)); ?></span>
                    </div>
                </div>
                
                <div class="cart-actions">
                    <a href="menu.php" class="btn btn-secondary">Continue Shopping</a>
                    <a href="checkout.php" class="btn">Proceed to Checkout</a>
                </div>
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- You Might Also Like Section -->
<?php 
// Get popular dishes for recommendations
$popular_dishes = get_popular_menu_items(4);
if (!empty($popular_dishes)):
?>
<section class="popular-dishes">
    <div class="container">
        <h2 class="section-title">You Might Also Like</h2>
        <div class="dishes-container">
            <?php foreach ($popular_dishes as $dish): ?>
                <div class="dish-card">
                    <div class="dish-image" style="background-image: url('<?php echo htmlspecialchars($dish['image_url']); ?>')"></div>
                    <div class="dish-info">
                        <h3><?php echo htmlspecialchars($dish['name']); ?></h3>
                        <div class="rating">
                            <?php 
                            $rating = round($dish['avg_rating'] ?? 0);
                            for ($i = 1; $i <= 5; $i++) {
                                if ($i <= $rating) {
                                    echo '<i class="fas fa-star"></i>';
                                } else {
                                    echo '<i class="far fa-star"></i>';
                                }
                            }
                            ?>
                        </div>
                        <p class="price"><?php echo format_currency($dish['price']); ?></p>
                        <div class="dish-actions">
                            <a href="menu.php#dish-<?php echo $dish['id']; ?>" class="btn">View Details</a>
                            <form action="add_to_cart.php" method="post">
                                <input type="hidden" name="item_id" value="<?php echo $dish['id']; ?>">
                                <input type="hidden" name="quantity" value="1">
                                <button type="submit" class="btn btn-secondary add-to-cart-btn">
                                    <i class="fas fa-shopping-cart"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<?php include 'includes/footer.php'; ?>
