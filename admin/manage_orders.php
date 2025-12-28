<?php
$page_title = "Manage Orders";
require_once '../includes/db_connection.php';
require_once '../includes/functions.php';
require_once '../includes/auth.php';

// Check if user is admin
check_admin();

// Success and error messages
$success_message = '';
$error_message = '';

// Handle status updates
if (isset($_POST['update_status'])) {
    $order_id = (int) $_POST['order_id'];
    $status = sanitize_input($_POST['status']);

    $valid_statuses = ['Pending', 'Processing', 'Completed', 'Cancelled'];
    if (!in_array($status, $valid_statuses)) {
        $error_message = "Invalid status.";
    } else {
        $query = "UPDATE orders SET status = ?, updated_at = NOW() WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("si", $status, $order_id);

        if ($stmt->execute()) {
            $success_message = "Order status updated successfully.";
        } else {
            $error_message = "Error updating order status: " . $conn->error;
        }
    }
}

// Get orders based on filter
$status_filter = isset($_GET['status']) ? sanitize_input($_GET['status']) : null;
$orders = get_all_orders($status_filter);

// Get a specific order if requested
$order_details = null;
$order_items = [];
if (isset($_GET['view']) && is_numeric($_GET['view'])) {
    $order_id = (int) $_GET['view'];
    $order_details = get_order_by_id($order_id);

    if ($order_details) {
        $order_items = get_order_items($order_id);
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

            <?php if ($order_details): ?>
                <!-- Single Order Details -->
                <div class="admin-actions">
                    <a href="manage_orders.php" class="btn">Back to All Orders</a>
                </div>

                <div class="admin-section">
                    <div class="admin-order-details">
                        <div class="admin-order-header">
                            <h2>Order #<?php echo htmlspecialchars($order_details['order_number']); ?></h2>
                            <form action="manage_orders.php" method="post" class="order-status-form">
                                <input type="hidden" name="order_id" value="<?php echo $order_details['id']; ?>">
                                <div class="form-group" style="display: flex; align-items: center;">
                                    <label for="status" style="margin-right: 10px;">Status:</label>
                                    <select name="status" id="status" class="form-control">
                                        <option value="Pending" <?php echo ($order_details['status'] == 'Pending') ? 'selected' : ''; ?>>Pending</option>
                                        <option value="Processing" <?php echo ($order_details['status'] == 'Processing') ? 'selected' : ''; ?>>Processing</option>
                                        <option value="Completed" <?php echo ($order_details['status'] == 'Completed') ? 'selected' : ''; ?>>Completed</option>
                                        <option value="Cancelled" <?php echo ($order_details['status'] == 'Cancelled') ? 'selected' : ''; ?>>Cancelled</option>
                                    </select>
                                    <button type="submit" name="update_status" class="btn" style="margin-left: 10px;">Update
                                        Status</button>
                                </div>
                            </form>
                        </div>

                        <div class="admin-order-info">
                            <div class="admin-order-info-section">
                                <h3>Order Information</h3>
                                <p><strong>Order Date:</strong>
                                    <?php echo date('F d, Y H:i', strtotime($order_details['order_date'])); ?></p>
                                <p><strong>Payment Method:</strong>
                                    <?php echo htmlspecialchars($order_details['payment_method']); ?></p>
                                <p><strong>Total Amount:</strong>
                                    <?php echo format_currency($order_details['total_amount']); ?></p>
                            </div>

                            <div class="admin-order-info-section">
                                <h3>Customer Information</h3>
                                <p><strong>Name:</strong> <?php echo htmlspecialchars($order_details['name']); ?></p>
                                <p><strong>Email:</strong> <?php echo htmlspecialchars($order_details['email']); ?></p>
                                <p><strong>Phone:</strong> <?php echo htmlspecialchars($order_details['phone']); ?></p>
                                <p><strong>Delivery Address:</strong>
                                    <?php echo htmlspecialchars($order_details['delivery_address']); ?></p>
                                <?php if (!empty($order_details['special_instructions'])): ?>
                                    <p><strong>Special Instructions:</strong>
                                        <?php echo htmlspecialchars($order_details['special_instructions']); ?></p>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="admin-order-items">
                            <h3>Order Items</h3>
                            <table>
                                <thead>
                                    <tr>
                                        <th>Item</th>
                                        <th>Price</th>
                                        <th>Quantity</th>
                                        <th>Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($order_items as $item): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($item['name']); ?></td>
                                            <td><?php echo format_currency($item['price']); ?></td>
                                            <td><?php echo $item['quantity']; ?></td>
                                            <td><?php echo format_currency($item['price'] * $item['quantity']); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="3" class="text-right"><strong>Subtotal:</strong></td>
                                        <td><?php echo format_currency($order_details['subtotal']); ?></td>
                                    </tr>
                                    <tr>
                                        <td colspan="3" class="text-right"><strong>Delivery Fee:</strong></td>
                                        <td><?php echo format_currency($order_details['delivery_fee']); ?></td>
                                    </tr>
                                    <tr>
                                        <td colspan="3" class="text-right"><strong>Tax:</strong></td>
                                        <td><?php echo format_currency($order_details['tax']); ?></td>
                                    </tr>
                                    <tr>
                                        <td colspan="3" class="text-right"><strong>Total:</strong></td>
                                        <td><strong><?php echo format_currency($order_details['total_amount']); ?></strong>
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <!-- Orders List -->
                <div class="admin-actions">
                    <a href="manage_orders.php"
                        class="btn <?php echo !isset($_GET['status']) ? 'btn-secondary' : ''; ?>">All Orders</a>
                    <a href="manage_orders.php?status=pending"
                        class="btn <?php echo (isset($_GET['status']) && $_GET['status'] == 'pending') ? 'btn-secondary' : ''; ?>">Pending</a>
                    <a href="manage_orders.php?status=processing"
                        class="btn <?php echo (isset($_GET['status']) && $_GET['status'] == 'processing') ? 'btn-secondary' : ''; ?>">Processing</a>
                    <a href="manage_orders.php?status=completed"
                        class="btn <?php echo (isset($_GET['status']) && $_GET['status'] == 'completed') ? 'btn-secondary' : ''; ?>">Completed</a>
                    <a href="manage_orders.php?status=cancelled"
                        class="btn <?php echo (isset($_GET['status']) && $_GET['status'] == 'cancelled') ? 'btn-secondary' : ''; ?>">Cancelled</a>
                </div>

                <div class="admin-table">
                    <div class="admin-table-header">
                        <h2 class="admin-table-title">
                            <?php
                            if (isset($_GET['status'])) {
                                echo ucfirst($_GET['status']) . ' Orders';
                            } else {
                                echo 'All Orders';
                            }
                            ?>
                        </h2>
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
                                <?php if (empty($orders)): ?>
                                    <tr>
                                        <td colspan="6" style="text-align: center;">No orders found</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($orders as $order): ?>
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
            <?php endif; ?>
        </div>
    </div>

    <script src="../js/main.js"></script>
</body>

</html>