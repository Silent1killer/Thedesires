<?php
require_once 'includes/db_connection.php';
require_once 'includes/functions.php';
require_once 'includes/auth.php';

// Check if cart is empty
$cart_items = get_cart_items();
if (empty($cart_items)) {
    redirect('cart.php');
}

// Check if form was submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get form data
    $name = isset($_POST['name']) ? sanitize_input($_POST['name']) : '';
    $email = isset($_POST['email']) ? sanitize_input($_POST['email']) : '';
    $phone = isset($_POST['phone']) ? sanitize_input($_POST['phone']) : '';
    $address = isset($_POST['address']) ? sanitize_input($_POST['address']) : '';
    $city = isset($_POST['city']) ? sanitize_input($_POST['city']) : '';
    $zip = isset($_POST['zip']) ? sanitize_input($_POST['zip']) : '';
    $special_instructions = isset($_POST['special_instructions']) ? sanitize_input($_POST['special_instructions']) : '';
    $payment_method = isset($_POST['payment_method']) ? sanitize_input($_POST['payment_method']) : 'cash';
    
    // Format full address
    $delivery_address = "$address, $city, $zip";
    
    // Validate inputs
    if (empty($name) || empty($email) || empty($phone) || empty($address) || empty($city) || empty($zip)) {
        redirect('checkout.php?error=1');
    }
    
    // If payment method is credit card, validate card details (simplified for demo)
    if ($payment_method == 'credit_card') {
        $card_number = isset($_POST['card_number']) ? sanitize_input($_POST['card_number']) : '';
        $card_name = isset($_POST['card_name']) ? sanitize_input($_POST['card_name']) : '';
        $expiry_date = isset($_POST['expiry_date']) ? sanitize_input($_POST['expiry_date']) : '';
        $cvv = isset($_POST['cvv']) ? sanitize_input($_POST['cvv']) : '';
        
        if (empty($card_number) || empty($card_name) || empty($expiry_date) || empty($cvv)) {
            redirect('checkout.php?error=2');
        }
    }
    
    // Get user ID if logged in
    $user_id = is_logged_in() ? $_SESSION['user_id'] : null;
    
    // Calculate totals
    $subtotal = get_cart_total();
    $delivery_fee = 80.00;
    $tax = $subtotal * 0.08;
    $total = $subtotal + $delivery_fee + $tax;
    
    // Generate order number
    $order_number = generate_order_number();
    
    // Start transaction
    $conn->begin_transaction();
    
    try {
        // Insert order into database
        $order_query = "INSERT INTO orders (user_id, order_number, name, email, phone, delivery_address, 
                        special_instructions, payment_method, subtotal, delivery_fee, tax, total_amount, status, order_date)
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'Pending', NOW())";
        
        $order_stmt = $conn->prepare($order_query);
        $order_stmt->bind_param("isssssssdddd", $user_id, $order_number, $name, $email, $phone, $delivery_address, 
                              $special_instructions, $payment_method, $subtotal, $delivery_fee, $tax, $total);
        $order_stmt->execute();
        
        // Get the order ID
        $order_id = $conn->insert_id;
        
        // Insert order items
        $item_query = "INSERT INTO order_items (order_id, menu_item_id, quantity, price)
                      VALUES (?, ?, ?, ?)";
        $item_stmt = $conn->prepare($item_query);
        
        foreach ($cart_items as $item) {
            $item_stmt->bind_param("iiid", $order_id, $item['id'], $item['quantity'], $item['price']);
            $item_stmt->execute();
        }
        
        // If user is not logged in but provided email exists in database, link order to user
        if (!$user_id) {
            $user_check_query = "SELECT id FROM users WHERE email = ?";
            $user_check_stmt = $conn->prepare($user_check_query);
            $user_check_stmt->bind_param("s", $email);
            $user_check_stmt->execute();
            $user_check_result = $user_check_stmt->get_result();
            
            if ($user_check_result->num_rows > 0) {
                $user_row = $user_check_result->fetch_assoc();
                $found_user_id = $user_row['id'];
                
                $update_order_query = "UPDATE orders SET user_id = ? WHERE id = ?";
                $update_order_stmt = $conn->prepare($update_order_query);
                $update_order_stmt->bind_param("ii", $found_user_id, $order_id);
                $update_order_stmt->execute();
            }
        }
        
        // Commit the transaction
        $conn->commit();
        
        // Clear the cart
        $_SESSION['cart'] = [];
        
        // Redirect to order confirmation page
        redirect("order_confirmation.php?order_id=$order_id");
        
    } catch (Exception $e) {
        // Roll back the transaction if something failed
        $conn->rollback();
        redirect('checkout.php?error=3');
    }
    
} else {
    // If not submitted via POST, redirect to checkout page
    redirect('checkout.php');
}
?>
