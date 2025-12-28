<?php
$page_title = "Admin Dashboard";
require_once '../includes/db_connection.php';
require_once '../includes/functions.php';
require_once '../includes/auth.php';

// Check if user is admin
check_admin();

// Get statistics for dashboard
$total_menu_items = get_total_menu_items();
$total_users = get_total_users();
$total_orders = get_total_orders();
$total_revenue = get_total_revenue();

// Get recent orders
$recent_orders = get_all_orders();
if (count($recent_orders) > 5) {
    $recent_orders = array_slice($recent_orders, 0, 5);
}

// Include header
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?> - the desires</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../css/style.css">
</head>

<body>
    <div class="admin-container">
        <!-- Admin Sidebar -->
        <div class="admin-sidebar">
            <div class="admin-logo">
                <a href="../index.php">
                    <img src="../assets/logo.svg" alt="the desires Restaurant">
                </a>
            </div>
            <div class="admin-menu">
                <a href="index.php" class="admin-menu-item">
                    <i class="fas fa-tachometer-alt"></i> Dashboard
                </a>
                <a href="manage_menu.php" class="admin-menu-item">
                    <i class="fas fa-utensils"></i> Manage Menu
                </a>
                <a href="manage_orders.php" class="admin-menu-item">
                    <i class="fas fa-shopping-cart"></i> Manage Orders
                </a>
                <a href="manage_reservations.php" class="admin-menu-item">
                    <i class="fas fa-calendar-alt"></i> Manage Reservations
                </a>
                <a href="manage_users.php" class="admin-menu-item">
                    <i class="fas fa-users"></i> Manage Users
                </a>
                <a href="manage_reviews.php" class="admin-menu-item">
                    <i class="fas fa-star"></i> Manage Reviews
                </a>
                <a href="manage_contacts.php" class="admin-menu-item">
                    <i class="fas fa-envelope"></i> Manage Reaches
                </a>
                <a href="../index.php" class="admin-menu-item">
                    <i class="fas fa-home"></i> Visit Website
                </a>
                <a href="../logout.php" class="admin-menu-item">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </div>
        </div>
       
        <!-- <div class="admin-mobile-menu">
            <a href="index.php" class="admin-menu-item">
                <i class="fas fa-tachometer-alt"></i> Dashboard
            </a>
            <a href="manage_menu.php" class="admin-menu-item">
                <i class="fas fa-utensils"></i> Manage Menu
            </a>
            <a href="manage_orders.php" class="admin-menu-item">
                <i class="fas fa-shopping-cart"></i> Manage Orders
            </a>
            <a href="manage_reservations.php" class="admin-menu-item">
                <i class="fas fa-calendar-alt"></i> Manage Reservations
            </a>
            <a href="manage_users.php" class="admin-menu-item">
                <i class="fas fa-users"></i> Manage Users
            </a>
            <a href="manage_reviews.php" class="admin-menu-item">
                <i class="fas fa-star"></i> Manage Reviews
            </a>
            <a href="manage_contacts.php" class="admin-menu-item">
                <i class="fas fa-envelope"></i> Manage Reaches
            </a>
            <a href="../index.php" class="admin-menu-item">
                <i class="fas fa-home"></i> Visit Website
            </a>
            <a href="../logout.php" class="admin-menu-item">
                <i class="fas fa-sign-out-alt"></i> Logout
            </a> 
         </div> -->
    <!-- Admin Content -->
    <div class="admin-content">
        <div class="admin-header">
            <h1 class="admin-title">Dashboard</h1>
            <div class="admin-user">
                <i class="fas fa-user"></i>
                <span><?php echo htmlspecialchars($_SESSION['user_name']); ?></span>
            </div>
        </div>

        <!-- Dashboard Cards -->
        <div class="admin-cards">
            <div class="admin-card">
                <div class="admin-card-icon">
                    <i class="fas fa-utensils"></i>
                </div>
                <div class="admin-card-content">
                    <h3><?php echo $total_menu_items; ?></h3>
                    <p>Menu Items</p>
                </div>
            </div>

            <div class="admin-card">
                <div class="admin-card-icon">
                    <i class="fas fa-users"></i>
                </div>
                <div class="admin-card-content">
                    <h3><?php echo $total_users; ?></h3>
                    <p>Registered Users</p>
                </div>
            </div>

            <div class="admin-card">
                <div class="admin-card-icon">
                    <i class="fas fa-shopping-cart"></i>
                </div>
                <div class="admin-card-content">
                    <h3><?php echo $total_orders; ?></h3>
                    <p>Total Orders</p>
                </div>
            </div>

            <div class="admin-card">
                <div class="admin-card-icon">
                    <i class="fas fa-dollar-sign"></i>
                </div>
                <div class="admin-card-content">
                    <h3><?php echo format_currency($total_revenue); ?></h3>
                    <p>Total Revenue</p>
                </div>
            </div>
        </div>

        <!-- Recent Orders -->
        <div class="admin-table">
            <div class="admin-table-header">
                <h2 class="admin-table-title">Recent Orders</h2>
                <div class="admin-table-actions">
                    <a href="manage_orders.php" class="btn btn-secondary">View All</a>
                </div>
            </div>
            <div class="admin-table-content">
                <table>
                    <thead>
                        <tr>
                            <th>Order #</th>
                            <th>Customer</th>
                            <th>Date</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($recent_orders)): ?>
                            <tr>
                                <td colspan="6" style="text-align: center;">No orders found</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($recent_orders as $order): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($order['order_number']); ?></td>
                                    <td><?php echo htmlspecialchars($order['user_name']); ?></td>
                                    <td><?php echo date('M d, Y', strtotime($order['order_date'])); ?></td>
                                    <td><?php echo format_currency($order['total_amount']); ?></td>
                                    <td>
                                        <span class="status-badge badge-<?php echo strtolower($order['status']); ?>">
                                            <?php echo htmlspecialchars($order['status']); ?>
                                        </span>
                                    </td>
                                    <td class="table-actions">
                                        <a href="manage_orders.php?view=<?php echo $order['id']; ?>" class="btn">View</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Quick Links -->
        <div class="admin-quick-links">
            <h2>Quick Links</h2>
            <div class="admin-quick-links-container">
                <a href="manage_menu.php?action=add" class="admin-quick-link">
                    <i class="fas fa-plus"></i>
                    <span>Add Menu Item</span>
                </a>
                <a href="manage_orders.php?status=pending" class="admin-quick-link">
                    <i class="fas fa-clock"></i>
                    <span>Pending Orders</span>
                </a>
                <a href="manage_reservations.php?status=pending" class="admin-quick-link">
                    <i class="fas fa-calendar-check"></i>
                    <span>Pending Reservations</span>
                </a>
                <a href="manage_reviews.php" class="admin-quick-link">
                    <i class="fas fa-star"></i>
                    <span>Recent Reviews</span>
                </a>
            </div>
        </div>
    </div>
    </div>

    <script src="../js/main.js"></script>
</body>

</html>