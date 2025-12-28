<?php
require_once 'includes/db_connection.php';
require_once 'includes/functions.php';
require_once 'includes/auth.php';

// Check if user is logged in
if (!is_logged_in()) {
    // Set redirect URL and redirect to login page
    $_SESSION['redirect_url'] = 'menu.php';
    redirect('login.php');
}

// Check if form was submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get form data
    $menu_item_id = isset($_POST['menu_item_id']) ? (int)$_POST['menu_item_id'] : 0;
    $rating = isset($_POST['rating']) ? (int)$_POST['rating'] : 0;
    $review_text = isset($_POST['review_text']) ? sanitize_input($_POST['review_text']) : '';
    $user_id = $_SESSION['user_id'];
    
    // Validate inputs
    if ($menu_item_id <= 0 || $rating <= 0 || $rating > 5 || empty($review_text)) {
        redirect("item_details.php?id=$menu_item_id&error=1");
    }
    
    // Check if user has already reviewed this item
    $check_query = "SELECT id FROM reviews WHERE user_id = ? AND menu_item_id = ?";
    $check_stmt = $conn->prepare($check_query);
    $check_stmt->bind_param("ii", $user_id, $menu_item_id);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();
    
    if ($check_result->num_rows > 0) {
        // Update existing review
        $review = $check_result->fetch_assoc();
        $review_id = $review['id'];
        
        $update_query = "UPDATE reviews SET rating = ?, review_text = ?, updated_at = NOW() WHERE id = ?";
        $update_stmt = $conn->prepare($update_query);
        $update_stmt->bind_param("isi", $rating, $review_text, $review_id);
        
        if ($update_stmt->execute()) {
            redirect("item_details.php?id=$menu_item_id&review_updated=1");
        } else {
            redirect("item_details.php?id=$menu_item_id&error=2");
        }
    } else {
        // Insert new review
        $insert_query = "INSERT INTO reviews (user_id, menu_item_id, rating, review_text, created_at)
                        VALUES (?, ?, ?, ?, NOW())";
        $insert_stmt = $conn->prepare($insert_query);
        $insert_stmt->bind_param("iiis", $user_id, $menu_item_id, $rating, $review_text);
        
        if ($insert_stmt->execute()) {
            redirect("item_details.php?id=$menu_item_id&review_added=1");
        } else {
            redirect("item_details.php?id=$menu_item_id&error=2");
        }
    }
} else {
    // If not submitted via POST, redirect to home page
    redirect('index.php');
}
?>
