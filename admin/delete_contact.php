<?php
require_once '../includes/db_connection.php';
require_once '../includes/auth.php';

// Check if user is admin
check_admin();

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = (int) $_GET['id'];

    // Delete the contact message
    $query = "DELETE FROM contact_messages WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        header('Location: manage_contacts.php?success=1');
    } else {
        header('Location: manage_contacts.php?error=1');
    }
} else {
    header('Location: manage_contacts.php');
}
exit;