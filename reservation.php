<?php
$page_title = "Reservation";
require_once 'includes/db_connection.php';
require_once 'includes/functions.php';
require_once 'includes/auth.php';

// Check if user is logged in
$user = null;
if (is_logged_in()) {
    $user = get_user_by_id($_SESSION['user_id']);
}else{
        redirect('login.php');
    }


// Set available time slots
$time_slots = [
    '11:00', '11:30', '12:00', '12:30', '13:00', '13:30', 
    '18:00', '18:30', '19:00', '19:30', '20:00', '20:30', '21:00'
];

// Include header
include 'includes/header.php';

// Additional styles for time slots
$additional_styles = '<style>
    .time-slots {
        display: flex;
        flex-wrap: wrap;
        margin-bottom: 20px;
        curson: pointer;
    }
    .time-slot {
        padding: 8px 12px;
        margin: 5px;
        border: 1px solid #ddd;
        border-radius: 4px;
        cursor: pointer;
        transition: all 0.3s;
    }
    .time-slot:hover, .time-slot.active {
        background-color: var(--primary-color);
        color: white;
        border-color: var(--primary-color);
         cursor: pointer;
    }
</style>';


// Success message if reservation was completed
$success_message = '';
if (isset($_GET['success']) && $_GET['success'] == 1) {
    $success_message = 'Your reservation has been successfully submitted! We will contact you shortly to confirm your booking.';
}
?>

<!-- Reservation Banner -->
<section class="page-banner" style="background-image: linear-gradient(rgba(255, 255, 255, 0.6), rgba(99, 84, 84, 0.6)), url('assets/other/reserv.jpg');
color">
    <div class="container">
        <h1>Make a Reservation</h1>
        <p>Book your table in advance to ensure availability and enjoy a seamless dining experience</p>
    </div>
</section>

<!-- Reservation Section -->
<section class="reservation-section">
    <div class="container">
        <?php if (!empty($success_message)): ?>
            <div class="alert alert-success">
                <?php echo $success_message; ?>
            </div>
        <?php endif; ?>
        
        <div class="reservation-container">
            <div class="reservation-info">
                <h2>Reserve Your Table</h2>
                <p>At the desires, we want to make your dining experience as enjoyable as possible. Reserving a table in advance ensures you won't have to wait when you arrive.</p>
                <p>For parties larger than 8 people, special events, or any specific requirements, please contact us directly.</p>
                
                <div class="reservation-details">
                    <div class="reservation-detail">
                        <i class="fas fa-phone"></i>
                        <div>
                            <h4>Phone Reservation</h4>
                            <p>+91 7411712661</p>
                        </div>
                    </div>
                    <div class="reservation-detail">
                        <i class="fas fa-clock"></i>
                        <div>
                            <h4>Opening Hours</h4>
                            <p>Lunch: 11:00 AM - 2:30 PM</p>
                            <p>Dinner: 6:00 PM - 10:00 PM</p>
                        </div>
                    </div>
                    <div class="reservation-detail">
                        <i class="fas fa-calendar-alt"></i>
                        <div>
                            <h4>Closed On</h4>
                            <p>We are closed on Mondays</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="reservation-form">
                <h3>Book Your Table</h3>
                <form action="process_reservation.php" method="post" id="reservation-form">
                    <?php if (is_logged_in()): ?>
                        <input type="hidden" name="user_id" value="<?php echo $_SESSION['user_id']; ?>">
                    <?php endif; ?>
                    
                    <div class="form-group">
                        <label for="name">Full Name</label>
                        <input type="text" id="name" name="name" class="form-control" value="<?php echo is_logged_in() ? htmlspecialchars($user['name']) : ''; ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="email">Email Address</label>
                        <input type="email" id="email" name="email" class="form-control" value="<?php echo is_logged_in() ? htmlspecialchars($user['email']) : ''; ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="phone">Phone Number</label>
                        <input type="tel" id="phone" name="phone" class="form-control" value="<?php echo is_logged_in() && isset($user['phone']) ? htmlspecialchars($user['phone']) : ''; ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="date">Reservation Date</label>
                        <input type="date" id="date" name="date" class="form-control" required min="<?php echo date('Y-m-d'); ?>">
                    </div>
                    
                    <div class="form-group">
                        <label>Preferred Time</label>
                        <div class="time-slots">
                            <?php foreach ($time_slots as $time): ?>
                                <div class="time-slot" data-time="<?php echo $time; ?>">
                                    <?php echo $time; ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <input type="hidden" id="reservation-time" name="time" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="guests">Number of Guests</label>
                        <input type="number" id="guests" name="guests" class="form-control" min="1" max="8" value="2" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="special_requests">Special Requests (Optional)</label>
                        <textarea id="special_requests" name="special_requests" class="form-control" rows="3"></textarea>
                    </div>
                    
                    <div class="form-actions">
                        <button type="submit" class="btn">Reserve Now</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>

<!-- Policies Section -->
<section class="features">
    <div class="container">
        <h2 class="section-title">Reservation Policies</h2>
        <div class="features-container">
            <div class="feature-box">
                <i class="fas fa-clock"></i>
                <h3>Late Arrival</h3>
                <p>We hold reservations for 15 minutes past the scheduled time. After that, tables may be given to waiting customers.</p>
            </div>
            <div class="feature-box">
                <i class="fas fa-ban"></i>
                <h3>Cancellation</h3>
                <p>Please notify us at least 4 hours in advance if you need to cancel or modify your reservation.</p>
            </div>
            <div class="feature-box">
                <i class="fas fa-users"></i>
                <h3>Large Groups</h3>
                <p>For parties of 9 or more, please contact us directly to arrange your reservation.</p>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
