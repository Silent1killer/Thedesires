<?php
$page_title = "Manage Reaches";
require_once '../includes/db_connection.php';
require_once '../includes/functions.php';
require_once '../includes/auth.php';

// Check if user is admin
check_admin();

// Success and error messages
$success_message = '';
$error_message = '';

// if (isset($_GET['id']) && is_numeric($_GET['id'])) {
//     $id = (int) $_GET['id'];

//   // Delete the contact message
//   $query = "DELETE FROM contact_messages WHERE id = ?";
//   $stmt = $conn->prepare($query);
//   $stmt->bind_param("i", $id);

//   if ($stmt->execute()) {
//       header('Location: manage_contacts.php?success=1');
//   } else {
//       header('Location: manage_contacts.php?error=1');
//   }
// } else {
//   header('Location: manage_contacts.php');
// }

// Fetch contact messages
$query = "SELECT * FROM contact_messages ORDER BY created_at DESC";
$result = $conn->query($query);
$contact_messages = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $contact_messages[] = $row;
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
            </div>

            <div class="admin-table">
                <div class="admin-table-header">
                    <h2 class="admin-table-title">Contact Messages</h2>
                </div>
                <div class="admin-table-content">
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Subject</th>
                                <th>Message</th>
                                <th>Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($contact_messages)): ?>
                                <tr>
                                    <td colspan="6" style="text-align: center;">No contact messages found</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($contact_messages as $message): ?>
                                    <tr>
                                        <td><?php echo $message['id']; ?></td>
                                        <td><?php echo htmlspecialchars($message['name']); ?></td>
                                        <td><?php echo htmlspecialchars($message['email']); ?></td>
                                        <td><?php echo htmlspecialchars($message['subject']); ?></td>
                                        <td><?php echo htmlspecialchars($message['message']); ?></td>
                                        <td><?php echo htmlspecialchars($message['created_at']); ?></td>
                                        <td>
                                            <a href="delete_contact.php?id=<?php echo $message['id']; ?>" class="btn btn-danger"
                                                onclick="return confirm('Are you sure you want to delete this message?')">Delete</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</body>

</html>