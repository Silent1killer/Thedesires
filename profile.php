<?php
$page_title = "My Profile";
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

// Get user reservations
$reservations = get_user_reservations($user_id);

// Success messages
$success_message = '';
if (isset($_GET['profile_updated']) && $_GET['profile_updated'] == 1) {
    $success_message = 'Your profile has been successfully updated.';
} elseif (isset($_GET['password_changed']) && $_GET['password_changed'] == 1) {
    $success_message = 'Your password has been successfully changed.';
}

// Error messages
$error_message = '';
if (isset($_GET['error'])) {
    if ($_GET['error'] == 1) {
        $error_message = 'Current password is incorrect.';
    } elseif ($_GET['error'] == 2) {
        $error_message = 'An error occurred. Please try again.';
    } elseif ($_GET['error'] == 3) {
        $error_message = 'Email is already registered to another user.';
    }
}

// Current active tab
$active_tab = isset($_GET['tab']) ? $_GET['tab'] : 'profile';

// Include header
include 'includes/header.php';

// Additional scripts for validation
$additional_scripts = '<script src="js/validation.js"></script>';
?>

<!-- Profile Banner -->
<section class="page-banner" style="background-image: linear-gradient(rgba(93, 88, 88, 0.6), rgba(120, 105, 105, 0.6)), url('assets/other/profile.jpg');">
    <div class="container">
        <h1>My Profile</h1>
        <p>Manage your account, orders, and reservations</p>
    </div>
</section>

<!-- Profile Section -->
<section class="profile-section">
    <div class="container">
        <?php if (!empty($success_message)): ?>
            <div class="alert alert-success">
                <?php echo $success_message; ?>
            </div>
        <?php endif; ?>
        
        <?php if (!empty($error_message)): ?>
            <div class="alert alert-error">
                <?php echo $error_message; ?>
            </div>
        <?php endif; ?>
        
        <div class="profile-container">
            <div class="profile-sidebar">
                <div class="profile-image">
                    <i class="fas fa-user"></i>
                </div>
                <div class="profile-details">
                    <h3><?php echo htmlspecialchars($user['name']); ?></h3>
                    <p><?php echo htmlspecialchars($user['email']); ?></p>
                </div>
                <div class="profile-menu">
                    <a href="?tab=profile" class="profile-menu-item <?php echo $active_tab == 'profile' ? 'active' : ''; ?>" data-tab="profile-tab">
                        <i class="fas fa-user-circle"></i> Profile Information
                    </a>
                    <a href="?tab=orders" class="profile-menu-item <?php echo $active_tab == 'orders' ? 'active' : ''; ?>" data-tab="orders-tab">
                        <i class="fas fa-shopping-bag"></i> Order History
                    </a>
                    <a href="?tab=reservations" class="profile-menu-item <?php echo $active_tab == 'reservations' ? 'active' : ''; ?>" data-tab="reservations-tab">
                        <i class="fas fa-calendar-alt"></i> My Reservations
                    </a>
                    <a href="?tab=password" class="profile-menu-item <?php echo $active_tab == 'password' ? 'active' : ''; ?>" data-tab="password-tab">
                        <i class="fas fa-lock"></i> Change Password
                    </a>
                    <!-- <a href="logout.php" class="profile-menu-item">
                        <i class="fas fa-sign-out-alt"></i> Logout
                    </a> -->
                </div>
            </div>
            
            <div class="profile-content">
                <!-- Profile Information Tab -->
                <div id="profile-tab" class="profile-tab" style="display: <?php echo $active_tab == 'profile' ? 'block' : 'none'; ?>">
                    <h2 class="profile-section-title">Profile Information</h2>
                    <form action="process_profile_update.php" method="post" id="profile-form">
                        <input type="hidden" name="user_id" value="<?php echo $user_id; ?>">
                        
                        <div class="profile-form-group">
                            <label for="name">Full Name</label>
                            <input type="text" id="name" name="name" class="form-control" value="<?php echo htmlspecialchars($user['name']); ?>" required>
                        </div>
                        
                        <div class="profile-form-group">
                            <label for="email">Email Address</label>
                            <input type="email" id="email" name="email" class="form-control" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                        </div>
                        
                        <div class="profile-form-group">
                            <label for="phone">Phone Number</label>
                            <input type="tel" id="phone" name="phone" class="form-control" value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>">
                        </div>
                        
                        <div class="profile-form-group">
                            <label for="address">Address</label>
                            <textarea id="address" name="address" class="form-control" rows="3"><?php echo htmlspecialchars($user['address'] ?? ''); ?></textarea>
                        </div>
                        
                        <div class="profile-actions">
                            <button type="submit" class="btn">Update Profile</button>
                        </div>
                    </form>
                </div>
                
                <!-- Order History Tab -->
                <div id="orders-tab" class="profile-tab" style="display: <?php echo $active_tab == 'orders' ? 'block' : 'none'; ?>">
                    <h2 class="profile-section-title">Order History</h2>
                    
                    <?php if (empty($orders)): ?>
                        <div class="empty-state">
                            <p>You haven't placed any orders yet.</p>
                            <a href="menu.php" class="btn">Explore Our Menu</a>
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
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
                
                <!-- Reservations Tab -->
                <div id="reservations-tab" class="profile-tab" style="display: <?php echo $active_tab == 'reservations' ? 'block' : 'none'; ?>">
                    <h2 class="profile-section-title">My Reservations</h2>
                    
                    <?php if (empty($reservations)): ?>
                        <div class="empty-state">
                            <p>You haven't made any reservations yet.</p>
                            <a href="reservation.php" class="btn">Make a Reservation</a>
                        </div>
                    <?php else: ?>
                        <div class="order-history">
                            <?php foreach ($reservations as $reservation): ?>
                                <div class="order-card">
                                    <div class="order-header">
                                        <span class="order-id">Reservation #<?php echo $reservation['id']; ?></span>
                                        <span class="order-date"><?php echo date('M d, Y', strtotime($reservation['reservation_date'])); ?></span>
                                        <span class="order-status status-<?php echo strtolower($reservation['status']); ?>">
                                            <?php echo htmlspecialchars($reservation['status']); ?>
                                        </span>
                                    </div>
                                    <div class="order-details">
                                        <div class="order-items-list">
                                            <div class="order-items-list-item">
                                                <div class="order-item-details">
                                                    <span>Date:</span>
                                                </div>
                                                <div class="order-item-price">
                                                    <?php echo date('F d, Y', strtotime($reservation['reservation_date'])); ?>
                                                </div>
                                            </div>
                                            <div class="order-items-list-item">
                                                <div class="order-item-details">
                                                    <span>Time:</span>
                                                </div>
                                                <div class="order-item-price">
                                                    <?php echo $reservation['reservation_time']; ?>
                                                </div>
                                            </div>
                                            <div class="order-items-list-item">
                                                <div class="order-item-details">
                                                    <span>Number of Guests:</span>
                                                </div>
                                                <div class="order-item-price">
                                                    <?php echo $reservation['guests']; ?>
                                                </div>
                                            </div>
                                            <?php if (!empty($reservation['special_requests'])): ?>
                                                <div class="order-items-list-item">
                                                    <div class="order-item-details">
                                                        <span>Special Requests:</span>
                                                    </div>
                                                    <div class="order-item-price">
                                                        <?php echo htmlspecialchars($reservation['special_requests']); ?>
                                                    </div>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
                
                <!-- Change Password Tab -->
                <div id="password-tab" class="profile-tab" style="display: <?php echo $active_tab == 'password' ? 'block' : 'none'; ?>">
                    <h2 class="profile-section-title">Change Password</h2>
                    <form action="process_password_change.php" method="post" id="password-form">
                        <input type="hidden" name="user_id" value="<?php echo $user_id; ?>">
                        
                        <div class="profile-form-group">
                            <label for="current-password">Current Password</label>
                            <input type="password" id="current-password" name="current_password" class="form-control" required>
                        </div>
                        
                        <div class="profile-form-group">
                            <label for="new-password">New Password</label>
                            <input type="password" id="new-password" name="new_password" class="form-control" required>
                            <small class="form-text text-muted">Password must be at least 6 characters long.</small>
                        </div>
                        
                        <div class="profile-form-group">
                            <label for="confirm-password">Confirm New Password</label>
                            <input type="password" id="confirm-password" name="confirm_password" class="form-control" required>
                        </div>
                        
                        <div class="profile-actions">
                            <button type="submit" class="btn">Change Password</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
