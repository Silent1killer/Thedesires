<?php
$page_title = "Checkout";
require_once 'includes/db_connection.php';
require_once 'includes/functions.php';
require_once 'includes/auth.php';

// Get user data if logged in
$user = null;
if (is_logged_in()) {
    $user = get_user_by_id($_SESSION['user_id']);
} else {
    redirect('login.php');
}


// Check if cart is empty
$cart_items = get_cart_items();
if (empty($cart_items)) {
    redirect('cart.php');
}

// Calculate totals
$subtotal = get_cart_total();
$delivery_fee = 80.00;
$tax = $subtotal * 0.08;
$total = $subtotal + $delivery_fee + $tax;

// Include header
include 'includes/header.php';

// Additional scripts for validation
$additional_scripts = '<script src="js/validation.js"></script>';
?>

<!-- Checkout Banner -->
<section class="page-banner"
    style="background-image: linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.6)), url('https://source.unsplash.com/1600x400/?restaurant,food');">
    <div class="container">
        <h1>Checkout</h1>
        <p>Complete your order</p>
    </div>
</section>

<!-- Checkout Section -->
<section class="checkout-section">
    <div class="container">
        <div class="checkout-container">
            <div class="checkout-form">
                <h2 class="checkout-section-title">Billing & Delivery Details</h2>

                <form action="process_order.php" method="post" id="checkout-form">
                    <!-- Personal Information -->
                    <div class="form-section">
                        <h3 class="form-section-title">Personal Information</h3>
                        <div class="form-row">
                            <div class="form-col">
                                <div class="form-group">
                                    <label for="name">Full Name</label>
                                    <input type="text" id="name" name="name" class="form-control"
                                        value="<?php echo $user ? htmlspecialchars($user['name']) : ''; ?>" required>
                                </div>
                            </div>
                            <div class="form-col">
                                <div class="form-group">
                                    <label for="email">Email Address</label>
                                    <input type="email" id="email" name="email" class="form-control"
                                        value="<?php echo $user ? htmlspecialchars($user['email']) : ''; ?>" required>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="phone">Phone Number</label>
                            <input type="tel" id="phone" name="phone" class="form-control"
                                value="<?php echo $user && isset($user['phone']) ? htmlspecialchars($user['phone']) : ''; ?>"
                                required>
                        </div>
                    </div>

                    <!-- Delivery Address -->
                    <div class="form-section">
                        <h3 class="form-section-title">Delivery Address</h3>
                        <div class="form-group">
                            <label for="address">Street Address</label>
                            <input type="text" id="address" name="address" class="form-control"
                                value="<?php echo $user && isset($user['address']) ? htmlspecialchars($user['address']) : ''; ?>"
                                required>
                        </div>
                        <div class="form-row">
                            <div class="form-col">
                                <div class="form-group">
                                    <label for="city">City</label>
                                    <input type="text" id="city" name="city" class="form-control" required>
                                </div>
                            </div>
                            <div class="form-col">
                                <div class="form-group">
                                    <label for="zip">ZIP Code</label>
                                    <input type="text" id="zip" name="zip" class="form-control" required>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="special_instructions">Special Instructions (Optional)</label>
                            <textarea id="special_instructions" name="special_instructions" class="form-control"
                                rows="3"></textarea>
                        </div>
                    </div>

                    <!-- Payment Method -->
                    <div class="form-section">
                        <h3 class="form-section-title">Payment Method</h3>
                        <div class="payment-methods">
                            <label class="payment-method">
                                <input type="radio" name="payment_method" value="cash" checked> Cash on Delivery
                            </label>
                            <label class="payment-method">
                                <input type="radio" name="payment_method" value="credit_card"> Credit Card
                            </label>
                        </div>

                        <!-- Credit Card Details (initially hidden, will be shown with JavaScript) -->
                        <div id="credit-card-details" style="display: none;">
                            <div class="form-group">
                                <label for="card-number">Card Number</label>
                                <input type="text" id="card-number" name="card_number" class="form-control"
                                    placeholder="XXXX XXXX XXXX XXXX">
                            </div>
                            <div class="form-row">
                                <div class="form-col">
                                    <div class="form-group">
                                        <label for="card-name">Name on Card</label>
                                        <input type="text" id="card-name" name="card_name" class="form-control">
                                    </div>
                                </div>
                                <div class="form-col">
                                    <div class="form-group">
                                        <label for="expiry-date">Expiry Date (MM/YY)</label>
                                        <input type="text" id="expiry-date" name="expiry_date" class="form-control"
                                            placeholder="MM/YY">
                                    </div>
                                </div>
                                <div class="form-col">
                                    <div class="form-group">
                                        <label for="cvv">CVV</label>
                                        <input type="text" id="cvv" name="cvv" class="form-control" placeholder="XXX">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-actions">
                        <a href="cart.php" class="btn btn-secondary">Back to Cart</a>
                        <button type="submit" class="btn">Place Order</button>
                    </div>
                </form>
            </div>

            <div class="order-summary">
                <h2 class="checkout-section-title">Order Summary</h2>

                <div class="order-items">
                    <?php foreach ($cart_items as $item): ?>
                        <div class="order-item">
                            <div class="order-item-name">
                                <?php echo htmlspecialchars($item['name']); ?> × <?php echo $item['quantity']; ?>
                            </div>
                            <div class="order-item-price">
                                <?php echo format_currency($item['subtotal']); ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div class="order-summary-items">
                    <div class="order-summary-item">
                        <span>Subtotal</span>
                        <span><?php echo format_currency($subtotal); ?></span>
                    </div>
                    <div class="order-summary-item">
                        <span>Delivery Fee</span>
                        <span><?php echo format_currency($delivery_fee); ?></span>
                    </div>
                    <div class="order-summary-item">
                        <span>Tax (8%)</span>
                        <span><?php echo format_currency($tax); ?></span>
                    </div>
                    <!-- <div class="order-summary-item" id="discount-row" style="display: none;">
                        <span>Discount</span>
                        <span id="discount-amount"></span> -->
                    </div>
                    <div class="order-summary-total">
                        <span>Total</span>
                        <span><?php echo format_currency($total); ?></span>
                    </div>
                </div>
                <!-- coupen -->
                <!-- <div class="coupon-section">
                    <h3>Apply Coupon</h3>
                    <form id="coupon-form" method="post">
                        <input type="text" id="coupon-code" name="coupon_code" class="form-control"
                            placeholder="Enter coupon code">
                        <button type="button" id="apply-coupon" class="btn btn-secondary">Apply</button>
                    </form>
                    <div id="coupon-message" style="margin-top: 10px; color: red;"></div>
                </div> -->
            </div>
        </div>
    </div>
</section>


<script>
//coupon
    // document.addEventListener('DOMContentLoaded', function () {
    //     const applyCouponButton = document.getElementById('apply-coupon');
    //     const couponCodeInput = document.getElementById('coupon-code');
    //     const discountRow = document.getElementById('discount-row');
    //     const discountAmount = document.getElementById('discount-amount');
    //     const totalElement = document.querySelector('.order-summary-total span:last-child');

        // let total = <?php echo json_encode($total); ?>;

    //     applyCouponButton.addEventListener('click', function () {
    //         const couponCode = couponCodeInput.value.trim();

    //         if (couponCode === '') {
    //             document.getElementById('coupon-message').textContent = 'Please enter a coupon code.';
    //             return;
    //         }

    //         // Simulate an AJAX request to validate the coupon
    //         fetch('validate_coupon.php', {
    //             method: 'POST',
    //             headers: { 'Content-Type': 'application/json' },
    //             body: JSON.stringify({ coupon_code: couponCode })
    //         })
    //             .then(response => response.json())
    //             .then(data => {
    //                 if (data.success) {
    //                     const discount = data.discount_amount;
    //                     discountRow.style.display = 'flex';
    //                     discountAmount.textContent = `- ${discount.toFixed(2)}`;
    //                     total -= discount;
    //                     totalElement.textContent = total.toFixed(2);
    //                     document.getElementById('coupon-message').textContent = 'Coupon applied successfully!';
    //                     document.getElementById('coupon-message').style.color = 'green';
    //                 } else {
    //                     document.getElementById('coupon-message').textContent = data.message;
    //                     document.getElementById('coupon-message').style.color = 'red';
    //                 }
    //             })
    //             .catch(error => {
    //                 console.error('Error:', error);
    //                 document.getElementById('coupon-message').textContent = 'An error occurred. Please try again.';
    //             });
    //     });
    // });

    // Script to toggle credit card details
    document.addEventListener('DOMContentLoaded', function () {
        const paymentMethods = document.querySelectorAll('input[name="payment_method"]');
        const creditCardDetails = document.getElementById('credit-card-details');

        paymentMethods.forEach(method => {
            method.addEventListener('change', function () {
                if (this.value === 'credit_card') {
                    creditCardDetails.style.display = 'block';
                } else {
                    creditCardDetails.style.display = 'none';
                }
            });
        });
    });
</script>


<?php include 'includes/footer.php'; ?>