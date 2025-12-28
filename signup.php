<?php
$page_title = "Sign Up";
require_once 'includes/db_connection.php';
require_once 'includes/functions.php';
require_once 'includes/auth.php';

// Check if user is already logged in
if (is_logged_in()) {
    redirect('index.php');
}

// Error message if signup failed
$error_message = '';
if (isset($_GET['error'])) {
    if ($_GET['error'] == 1) {
        $error_message = 'Email already registered. Please login or use a different email.';
    } else {
        $error_message = 'An error occurred during registration. Please try again.';
    }
}

// Include header
include 'includes/header.php';

// Additional scripts for validation
$additional_scripts = '<script src="js/validation.js"></script>';
?>

<!-- Signup Section -->
<section class="page-banner" style="background-image: linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.6)), url('assets/other/signup.jpg');">
    <div class="container">
        <h1>Create an Account</h1>
        <p>Join the desires family to enjoy exclusive benefits and a personalized experience.</p>
    </div>
</section>

<section class="auth-section">
    <div class="container">
        <div class="form-container">
            <h2 class="form-title">Sign Up</h2>
            
            <?php if (!empty($error_message)): ?>
                <div class="error-message">
                    <?php echo $error_message; ?>
                </div>
            <?php endif; ?>
            
            <form action="process_signup.php" method="post" id="signup-form">
                <div class="form-group">
                    <label for="name">Full Name</label>
                    <input type="text" id="name" name="name" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" id="email" name="email" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" class="form-control" required>
                    <small class="form-text text-muted">Password must be at least 6 characters long.</small>
                </div>
                
                <div class="form-group">
                    <label for="confirm-password">Confirm Password</label>
                    <input type="password" id="confirm-password" name="confirm_password" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label for="phone">Phone Number (Optional)</label>
                    <input type="tel" id="phone" name="phone" class="form-control">
                </div>
                
                <div class="form-group">
                    <label for="address">Address (Optional)</label>
                    <textarea id="address" name="address" class="form-control" rows="3"></textarea>
                </div>
                
                <div class="form-actions">
                    <button type="submit" class="btn">Create Account</button>
                </div>
                
                <div class="form-text">
                    Already have an account? <a href="login.php">Login here</a>
                </div>
            </form>
        </div>
    </div>
</section>

<!-- Features Section -->
<section class="features">
    <div class="container">
        <h2 class="section-title">Why Join the desires?</h2>
        
        <div class="features-container">
            <div class="feature-box">
                <i class="fas fa-gift"></i>
                <h3>Exclusive Offers</h3>
                <p>Be the first to know about special promotions, events, and seasonal menu updates.</p>
            </div>
            <div class="feature-box">
                <i class="fas fa-stopwatch"></i>
                <h3>Quick Ordering</h3>
                <p>Save your favorite dishes and delivery information for faster checkout.</p>
            </div>
            <div class="feature-box">
                <i class="fas fa-user-check"></i>
                <h3>Personalized Experience</h3>
                <p>Receive recommendations based on your preferences and dining history.</p>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
