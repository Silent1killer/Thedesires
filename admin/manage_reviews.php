<?php
$page_title = "Manage Reviews";
require_once '../includes/db_connection.php';
require_once '../includes/functions.php';
require_once '../includes/auth.php';

// Check if user is admin
check_admin();

// Success and error messages
$success_message = '';
$error_message = '';

// Handle delete review
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $review_id = (int) $_GET['delete'];

    $query = "DELETE FROM reviews WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $review_id);

    if ($stmt->execute()) {
        $success_message = "Review deleted successfully.";
    } else {
        $error_message = "Error deleting review: " . $conn->error;
    }
}

// Get all reviews
$query = "SELECT r.*, u.name as user_name, m.name as menu_item_name
          FROM reviews r
          LEFT JOIN users u ON r.user_id = u.id
          LEFT JOIN menu_items m ON r.menu_item_id = m.id
          ORDER BY r.created_at DESC";
$result = $conn->query($query);

$reviews = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $reviews[] = $row;
    }
}

// Get a specific review if requested
$review_details = null;
if (isset($_GET['view']) && is_numeric($_GET['view'])) {
    $review_id = (int) $_GET['view'];

    $query = "SELECT r.*, u.name as user_name, u.email, m.name as menu_item_name, m.image_url
              FROM reviews r
              LEFT JOIN users u ON r.user_id = u.id
              LEFT JOIN menu_items m ON r.menu_item_id = m.id
              WHERE r.id = $review_id";

    $result = $conn->query($query);

    if ($result && $result->num_rows > 0) {
        $review_details = $result->fetch_assoc();
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

            <?php if ($review_details): ?>
                <!-- Single Review Details -->
                <div class="admin-actions">
                    <a href="manage_reviews.php" class="btn">Back to All Reviews</a>
                </div>

                <div class="admin-section">
                    <div class="admin-review-details">
                        <div class="admin-review-header">
                            <h2>Review for <?php echo htmlspecialchars($review_details['menu_item_name']); ?></h2>
                            <div class="review-rating">
                                <?php
                                for ($i = 1; $i <= 5; $i++) {
                                    if ($i <= $review_details['rating']) {
                                        echo '<i class="fas fa-star"></i>';
                                    } else {
                                        echo '<i class="far fa-star"></i>';
                                    }
                                }
                                echo ' ' . $review_details['rating'] . '/5';
                                ?>
                            </div>
                        </div>

                        <div class="admin-review-content">
                            <div class="admin-review-item">
                                <div class="admin-review-item-image"
                                    style="width: 100px; height: 100px; background-image: url('<?php echo htmlspecialchars($review_details['image_url']); ?>'); background-size: cover; background-position: center;">
                                </div>
                                <div class="admin-review-item-info">
                                    <h3><?php echo htmlspecialchars($review_details['menu_item_name']); ?></h3>
                                    <a href="../item_details.php?id=<?php echo $review_details['menu_item_id']; ?>"
                                        class="btn btn-secondary">View Item</a>
                                </div>
                            </div>

                            <div class="admin-review-text">
                                <h3>Review</h3>
                                <p><?php echo htmlspecialchars($review_details['review_text']); ?></p>
                                <p class="review-date">Posted on:
                                    <?php echo date('F d, Y', strtotime($review_details['created_at'])); ?>
                                </p>
                            </div>

                            <div class="admin-review-user">
                                <h3>User Information</h3>
                                <p><strong>Name:</strong> <?php echo htmlspecialchars($review_details['user_name']); ?></p>
                                <p><strong>Email:</strong> <?php echo htmlspecialchars($review_details['email']); ?></p>
                                <a href="manage_users.php?view=<?php echo $review_details['user_id']; ?>" class="btn">View
                                    User Profile</a>
                            </div>

                            <div class="admin-review-actions">
                                <a href="manage_reviews.php?delete=<?php echo $review_details['id']; ?>"
                                    class="btn btn-danger"
                                    onclick="return confirm('Are you sure you want to delete this review?')">Delete
                                    Review</a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <!-- Reviews List -->
                <div class="admin-table">
                    <div class="admin-table-header">
                        <h2 class="admin-table-title">All Reviews</h2>
                    </div>
                    <div class="admin-table-content">
                        <table>
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Menu Item</th>
                                    <th>User</th>
                                    <th>Rating</th>
                                    <th>Review</th>
                                    <th>Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($reviews)): ?>
                                    <tr>
                                        <td colspan="7" style="text-align: center;">No reviews found</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($reviews as $review): ?>
                                        <tr>
                                            <td><?php echo $review['id']; ?></td>
                                            <td><?php echo htmlspecialchars($review['menu_item_name']); ?></td>
                                            <td><?php echo htmlspecialchars($review['user_name']); ?></td>
                                            <td>
                                                <?php
                                                for ($i = 1; $i <= 5; $i++) {
                                                    if ($i <= $review['rating']) {
                                                        echo '<i class="fas fa-star"></i>';
                                                    } else {
                                                        echo '<i class="far fa-star"></i>';
                                                    }
                                                }
                                                ?>
                                            </td>
                                            <td>
                                                <?php
                                                $review_excerpt = strlen($review['review_text']) > 50 ?
                                                    substr($review['review_text'], 0, 50) . '...' :
                                                    $review['review_text'];
                                                echo htmlspecialchars($review_excerpt);
                                                ?>
                                            </td>
                                            <td><?php echo date('M d, Y', strtotime($review['created_at'])); ?></td>
                                            <td class="table-actions">
                                                <a href="manage_reviews.php?view=<?php echo $review['id']; ?>" class="btn">View</a>
                                                <a href="manage_reviews.php?delete=<?php echo $review['id']; ?>"
                                                    class="btn btn-danger"
                                                    onclick="return confirm('Are you sure you want to delete this review?')">Delete</a>
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