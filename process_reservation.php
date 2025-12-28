<?php
require_once 'includes/db_connection.php';
require_once 'includes/functions.php';
require_once 'includes/auth.php';

// Check if form was submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get form data
    $name = isset($_POST['name']) ? sanitize_input($_POST['name']) : '';
    $email = isset($_POST['email']) ? sanitize_input($_POST['email']) : '';
    $phone = isset($_POST['phone']) ? sanitize_input($_POST['phone']) : '';
    $date = isset($_POST['date']) ? sanitize_input($_POST['date']) : '';
    $time = isset($_POST['time']) ? sanitize_input($_POST['time']) : '';
    $guests = isset($_POST['guests']) ? (int)$_POST['guests'] : 0;
    $special_requests = isset($_POST['special_requests']) ? sanitize_input($_POST['special_requests']) : '';
    $user_id = is_logged_in() ? $_SESSION['user_id'] : null;
    
    // Validate inputs
    if (empty($name) || empty($email) || empty($phone) || empty($date) || empty($time) || $guests <= 0) {
        redirect('reservation.php?error=1');
    }
    
    // Validate date (must be in the future)
    $reservation_date = new DateTime($date);
    $today = new DateTime();
    $today->setTime(0, 0, 0); // Set time to midnight
    
    if ($reservation_date < $today) {
        redirect('reservation.php?error=2');
    }
    
    // Insert reservation into database
    $query = "INSERT INTO reservations (user_id, name, email, phone, reservation_date, reservation_time, guests, special_requests, status, created_at)
              VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'Pending', NOW())";
    
    $stmt = $conn->prepare($query);
    $stmt->bind_param("isssssss", $user_id, $name, $email, $phone, $date, $time, $guests, $special_requests);
    
    if ($stmt->execute()) {
        // Redirect to reservation page with success message
        redirect('reservation.php?success=1');
    } else {
        // Redirect back to reservation page with error
        redirect('reservation.php?error=3');
    }
} else {
    // If not submitted via POST, redirect to reservation page
    redirect('reservation.php');
}
?>
