<?php
$page_title = "Manage Users";
require_once '../includes/db_connection.php';
require_once '../includes/functions.php';
require_once '../includes/auth.php';

// Check if user is admin
check_admin();

// Success and error messages
$success_message = '';
$error_message = '';

// Get all users
$users = get_all_users();

// View a specific user if requested
$user_details = null;
$user_orders = [];
$user_reservations = [];

if (isset($_GET['view']) && is_numeric($_GET['view'])) {
    $user_id = (int) $_GET['view'];
    $user_details = get_user_by_id($user_id);

    if ($user_details) {
        $user_orders = get_user_orders($user_id);
        $user_reservations = get_user_reservations($user_id);
    }
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
    <style>
        body {
            background-color: #f8f9fa;
            padding: 0;
            margin: 0;
        }
    </style>
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
       
        <!-- Admin Content -->
        <div class="admin-content">
            <div class="admin-header">
                <h1 class="admin-title"><?php echo $page_title; ?></h1>
                <div class="admin-user">
                    <i class="fas fa-user"></i>
                    <span><?php echo htmlspecialchars($_SESSION['user_name']); ?></span>
                </div>
            </div>

            <?php if (!empty($success_message)): ?>
                <div class="alert alert-success"><?php echo $success_message; ?></div>
            <?php endif; ?>

            <?php if (!empty($error_message)): ?>
                <div class="alert alert-error"><?php echo $error_message; ?></div>
            <?php endif; ?>

            <?php if ($user_details): ?>
                <!-- Single User Details -->
                <div class="admin-actions">
                    <a href="manage_users.php" class="btn">Back to All Users</a>
                </div>

                <div class="admin-section">
                    <div class="admin-user-details">
                        <div class="admin-user-profile">
                            <h2>User Profile</h2>
                            <div class="admin-user-info">
                                <p><strong>Name:</strong> <?php echo htmlspecialchars($user_details['name']); ?></p>
                                <p><strong>Email:</strong> <?php echo htmlspecialchars($user_details['email']); ?></p>
                                <p><strong>Phone:</strong>
                                    <?php echo htmlspecialchars($user_details['phone'] ?? 'Not provided'); ?></p>
                                <p><strong>Address:</strong>
                                    <?php echo htmlspecialchars($user_details['address'] ?? 'Not provided'); ?></p>
                                <p><strong>Registered on:</strong>
                                    <?php echo date('F d, Y', strtotime($user_details['created_at'])); ?></p>
                            </div>
                        </div>

                        <div class="admin-user-orders">
                            <h3>Orders</h3>
                            <?php if (empty($user_orders)): ?>
                                <p>This user has not placed any orders yet.</p>
                            <?php else: ?>
                                <table>
                                    <thead>
                                        <tr>
                                            <th>Order #</th>
                                            <th>Date</th>
                                            <th>Amount</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($user_orders as $order): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($order['order_number']); ?></td>
                                                <td><?php echo date('M d, Y', strtotime($order['order_date'])); ?></td>
                                                <td><?php echo format_currency($order['total_amount']); ?></td>
                                                <td>
                                                    <span class="status-badge badge-<?php echo strtolower($order['status']); ?>">
                                                        <?php echo htmlspecialchars($order['status']); ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <a href="manage_orders.php?view=<?php echo $order['id']; ?>"
                                                        class="btn">View</a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            <?php endif; ?>
                        </div>

                        <div class="admin-user-reservations">
                            <h3>Reservations</h3>
                            <?php if (empty($user_reservations)): ?>
                                <p>This user has not made any reservations yet.</p>
                            <?php else: ?>
                                <table>
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Date</th>
                                            <th>Time</th>
                                            <th>Guests</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($user_reservations as $reservation): ?>
                                            <tr>
                                                <td><?php echo $reservation['id']; ?></td>
                                                <td><?php echo date('M d, Y', strtotime($reservation['reservation_date'])); ?></td>
                                                <td><?php echo $reservation['reservation_time']; ?></td>
                                                <td><?php echo $reservation['guests']; ?></td>
                                                <td>
                                                    <span
                                                        class="status-badge badge-<?php echo strtolower($reservation['status']); ?>">
                                                        <?php echo htmlspecialchars($reservation['status']); ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <a href="manage_reservations.php?view=<?php echo $reservation['id']; ?>"
                                                        class="btn">View</a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <!-- Users List -->
                <div class="admin-table">
                    <div class="admin-table-header">
                        <h2 class="admin-table-title">All Users</h2>
                    </div>
                    <div class="admin-table-content">
                        <table>
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Phone</th>
                                    <th>Registered</th>
                                    <th>Orders</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($users)): ?>
                                    <tr>
                                        <td colspan="7" style="text-align: center;">No users found</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($users as $user): ?>
                                        <tr>
                                            <td><?php echo $user['id']; ?></td>
                                            <td><?php echo htmlspecialchars($user['name']); ?></td>
                                            <td><?php echo htmlspecialchars($user['email']); ?></td>
                                            <td><?php echo htmlspecialchars($user['phone'] ?? 'Not provided'); ?></td>
                                            <td><?php echo date('M d, Y', strtotime($user['created_at'])); ?></td>
                                            <td>
                                                <?php
                                                // Count orders for this user
                                                $order_count_query = "SELECT COUNT(*) as count FROM orders WHERE user_id = " . $user['id'];
                                                $order_count_result = $conn->query($order_count_query);
                                                $order_count = 0;
                                                if ($order_count_result && $order_count_row = $order_count_result->fetch_assoc()) {
                                                    $order_count = $order_count_row['count'];
                                                }
                                                echo $order_count;
                                                ?>
                                            </td>
                                            <td class="table-actions">
                                                <a href="manage_users.php?view=<?php echo $user['id']; ?>" class="btn">View</a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script src="../js/main.js"></script>
</body>

</html>