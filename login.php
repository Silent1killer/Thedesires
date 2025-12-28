<?php
$page_title = "Login";
require_once 'includes/db_connection.php';
require_once 'includes/functions.php';
require_once 'includes/auth.php';

// Check if user is already logged in
if (is_logged_in()) {
    redirect('index.php');
}

// Error message if login failed
$error_message = '';
if (isset($_GET['error']) && $_GET['error'] == 1) {
    $error_message = 'Invalid email or password. Please try again.';
}

// Success message if registration was successful
$success_message = '';
if (isset($_GET['registered']) && $_GET['registered'] == 1) {
    $success_message = 'Registration successful! You can now log in with your credentials.';
}

// Include header
include 'includes/header.php';

// Additional scripts for validation
$additional_scripts = '<script src="js/validation.js"></script>';
?>

<!-- Login Section -->
<section class="page-banner" style="background-image: linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.6)), url('assets/other/login.jpg');">
    <div class="container">
        <h1>Login to Your Account</h1>
        <p>Access your profile, order food, and manage your reservations.</p>
    </div>
</section>

<section class="auth-section">
    <div class="container">
        <div class="form-container">
            <h2 class="form-title">Login</h2>
            
            <?php if (!empty($error_message)): ?>
                <div class="error-message">
                    <?php echo $error_message; ?>
                </div>
            <?php endif; ?>
            
            <?php if (!empty($success_message)): ?>
                <div class="success-message">
                    <?php echo $success_message; ?>
                </div>
            <?php endif; ?>
            
            <form action="process_login.php" method="post" id="login-form">
                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" id="email" name="email" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" class="form-control" required>
                </div>
                
                <div class="form-actions">
                    <button type="submit" class="btn">Login</button>
                </div>
                
                <div class="form-text">
                    Don't have an account? <a href="signup.php">Sign up here</a>
                </div>
            </form>
        </div>
    </div>
</section>

<!-- Features Section -->
<section class="features">
    <div class="container">
        <h2 class="section-title">Benefits of Creating an Account</h2>
        
        <div class="features-container">
            <div class="feature-box">
                <i class="fas fa-history"></i>
                <h3>Order History</h3>
                <p>Easily access your order history and reorder your favorite meals with just a few clicks.</p>
            </div>
            <div class="feature-box">
                <i class="fas fa-calendar-check"></i>
                <h3>Manage Reservations</h3>
                <p>View and manage your table reservations directly from your account.</p>
            </div>
            <div class="feature-box">
                <i class="fas fa-star"></i>
                <h3>Reviews & Ratings</h3>
                <p>Share your dining experiences and help others discover our most popular dishes.</p>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
