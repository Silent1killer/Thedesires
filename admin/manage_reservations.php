<?php
$page_title = "Manage Reservations";
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
    $reservation_id = (int) $_POST['reservation_id'];
    $status = sanitize_input($_POST['status']);

    $valid_statuses = ['Pending', 'Confirmed', 'Completed', 'Cancelled'];
    if (!in_array($status, $valid_statuses)) {
        $error_message = "Invalid status.";
    } else {
        $query = "UPDATE reservations SET status = ?, updated_at = NOW() WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("si", $status, $reservation_id);

        if ($stmt->execute()) {
            $success_message = "Reservation status updated successfully.";
        } else {
            $error_message = "Error updating reservation status: " . $conn->error;
        }
    }
}

// Get all reservations
$status_filter = isset($_GET['status']) ? sanitize_input($_GET['status']) : null;
$query = "SELECT r.*, u.name as user_name, u.email, u.phone as user_phone
          FROM reservations r
          LEFT JOIN users u ON r.user_id = u.id";

if ($status_filter) {
    $query .= " WHERE r.status = '" . $conn->real_escape_string(ucfirst($status_filter)) . "'";
}

$query .= " ORDER BY r.reservation_date DESC, r.reservation_time DESC";
$result = $conn->query($query);

$reservations = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $reservations[] = $row;
    }
}

// Get a specific reservation if requested
$reservation_details = null;
if (isset($_GET['view']) && is_numeric($_GET['view'])) {
    $reservation_id = (int) $_GET['view'];

    $query = "SELECT r.*, u.name as user_name, u.email, u.phone as user_phone
              FROM reservations r
              LEFT JOIN users u ON r.user_id = u.id
              WHERE r.id = $reservation_id";

    $result = $conn->query($query);

    if ($result && $result->num_rows > 0) {
        $reservation_details = $result->fetch_assoc();
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
                <a href="manage_reservations.php" class="admin-menu-item active">
                    <i class="fas fa-calendar-alt"></i> Manage Reservations
                </a>
                <a href="manage_users.php" class="admin-menu-item">
                    <i class="fas fa-users"></i> Manage Users
                </a>
                <a href="manage_reviews.php" class="admin-menu-item">
                    <i class="fas fa-star"></i> Manage Reviews
                </a>
                <a href="manage_contacts.php" class="admin-menu-item">
                    <i class="fas fa-star"></i> Manage Reaches
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

            <?php if ($reservation_details): ?>
                <!-- Single Reservation Details -->
                <div class="admin-actions">
                    <a href="manage_reservations.php" class="btn">Back to All Reservations</a>
                </div>

                <div class="admin-section">
                    <div class="admin-reservation-details">
                        <div class="admin-reservation-header">
                            <h2>Reservation #<?php echo $reservation_details['id']; ?></h2>
                            <form action="manage_reservations.php" method="post" class="reservation-status-form">
                                <input type="hidden" name="reservation_id"
                                    value="<?php echo $reservation_details['id']; ?>">
                                <div class="form-group" style="display: flex; align-items: center;">
                                    <label for="status" style="margin-right: 10px;">Status:</label>
                                    <select name="status" id="status" class="form-control">
                                        <option value="Pending" <?php echo ($reservation_details['status'] == 'Pending') ? 'selected' : ''; ?>>Pending</option>
                                        <option value="Confirmed" <?php echo ($reservation_details['status'] == 'Confirmed') ? 'selected' : ''; ?>>Confirmed</option>
                                        <option value="Completed" <?php echo ($reservation_details['status'] == 'Completed') ? 'selected' : ''; ?>>Completed</option>
                                        <option value="Cancelled" <?php echo ($reservation_details['status'] == 'Cancelled') ? 'selected' : ''; ?>>Cancelled</option>
                                    </select>
                                    <button type="submit" name="update_status" class="btn" style="margin-left: 10px;">Update
                                        Status</button>
                                </div>
                            </form>
                        </div>

                        <div class="admin-reservation-info">
                            <div class="admin-reservation-info-section">
                                <h3>Reservation Information</h3>
                                <p><strong>Date:</strong>
                                    <?php echo date('F d, Y', strtotime($reservation_details['reservation_date'])); ?></p>
                                <p><strong>Time:</strong> <?php echo $reservation_details['reservation_time']; ?></p>
                                <p><strong>Number of Guests:</strong> <?php echo $reservation_details['guests']; ?></p>
                                <?php if (!empty($reservation_details['special_requests'])): ?>
                                    <p><strong>Special Requests:</strong>
                                        <?php echo htmlspecialchars($reservation_details['special_requests']); ?></p>
                                <?php endif; ?>
                                <p><strong>Reservation Made:</strong>
                                    <?php echo date('F d, Y H:i', strtotime($reservation_details['created_at'])); ?></p>
                            </div>

                            <div class="admin-reservation-info-section">
                                <h3>Customer Information</h3>
                                <p><strong>Name:</strong> <?php echo htmlspecialchars($reservation_details['name']); ?></p>
                                <p><strong>Email:</strong> <?php echo htmlspecialchars($reservation_details['email']); ?>
                                </p>
                                <p><strong>Phone:</strong> <?php echo htmlspecialchars($reservation_details['phone']); ?>
                                </p>
                                <?php if ($reservation_details['user_id']): ?>
                                    <p><strong>Registered User:</strong> Yes</p>
                                    <p><a href="manage_users.php?view=<?php echo $reservation_details['user_id']; ?>"
                                            class="btn">View User Profile</a></p>
                                <?php else: ?>
                                    <p><strong>Registered User:</strong> No</p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <!-- Reservations List -->
                <div class="admin-actions">
                    <a href="manage_reservations.php"
                        class="btn <?php echo !isset($_GET['status']) ? 'btn-secondary' : ''; ?>">All Reservations</a>
                    <a href="manage_reservations.php?status=pending"
                        class="btn <?php echo (isset($_GET['status']) && $_GET['status'] == 'pending') ? 'btn-secondary' : ''; ?>">Pending</a>
                    <a href="manage_reservations.php?status=confirmed"
                        class="btn <?php echo (isset($_GET['status']) && $_GET['status'] == 'confirmed') ? 'btn-secondary' : ''; ?>">Confirmed</a>
                    <a href="manage_reservations.php?status=completed"
                        class="btn <?php echo (isset($_GET['status']) && $_GET['status'] == 'completed') ? 'btn-secondary' : ''; ?>">Completed</a>
                    <a href="manage_reservations.php?status=cancelled"
                        class="btn <?php echo (isset($_GET['status']) && $_GET['status'] == 'cancelled') ? 'btn-secondary' : ''; ?>">Cancelled</a>
                </div>

                <div class="admin-table">
                    <div class="admin-table-header">
                        <h2 class="admin-table-title">
                            <?php
                            if (isset($_GET['status'])) {
                                echo ucfirst($_GET['status']) . ' Reservations';
                            } else {
                                echo 'All Reservations';
                            }
                            ?>
                        </h2>
                    </div>
                    <div class="admin-table-content">
                        <table>
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Customer</th>
                                    <th>Date</th>
                                    <th>Time</th>
                                    <th>Guests</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($reservations)): ?>
                                    <tr>
                                        <td colspan="7" style="text-align: center;">No reservations found</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($reservations as $reservation): ?>
                                        <tr>
                                            <td><?php echo $reservation['id']; ?></td>
                                            <td><?php echo htmlspecialchars($reservation['name']); ?></td>
                                            <td><?php echo date('M d, Y', strtotime($reservation['reservation_date'])); ?></td>
                                            <td><?php echo $reservation['reservation_time']; ?></td>
                                            <td><?php echo $reservation['guests']; ?></td>
                                            <td>
                                                <span class="status-badge badge-<?php echo strtolower($reservation['status']); ?>">
                                                    <?php echo htmlspecialchars($reservation['status']); ?>
                                                </span>
                                            </td>
                                            <td class="table-actions">
                                                <a href="manage_reservations.php?view=<?php echo $reservation['id']; ?>"
                                                    class="btn">View</a>
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