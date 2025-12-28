<?php
$page_title = "Contact Us";
require_once 'includes/db_connection.php';
require_once 'includes/functions.php';
require_once 'includes/auth.php';

// Check if user is logged in
if (!is_logged_in()) {
    header('Location: login.php'); // Redirect to login page
    exit;
}

// Include header
include 'includes/header.php';

// Success message if contact form was submitted
$success_message = '';
if (isset($_GET['success']) && $_GET['success'] == 1) {
    $success_message = 'Thank you for contacting us! We have received your message and will get back to you shortly.';
}
?>

<!-- Contact Banner -->
<section class="page-banner"
    style="background-image: linear-gradient(rgba(206, 180, 180, 0.81), rgba(75, 62, 62, 0.6)), url('assets/other/contact.jpg');">
    <div class="container">
        <h1>Contact Us</h1>
        <p>We'd love to hear from you. Reach out with any questions, feedback, or inquiries.</p>
    </div>
</section>

<!-- Contact Section -->
<section class="contact-section">
    <div class="container">
        <?php if (!empty($success_message)): ?>
            <div class="alert alert-success">
                <?php echo $success_message; ?>
            </div>
        <?php endif; ?>

        <div class="contact-container">
            <div class="contact-info">
                <h3>Get in Touch</h3>
                <div class="contact-info-item">
                    <i class="fas fa-map-marker-alt"></i>
                    <div class="content">
                        <h4>Location</h4>
                        <p>Kcroad,manglore -575022</p>
                    </div>
                </div>
                <div class="contact-info-item">
                    <i class="fas fa-phone"></i>
                    <div class="content">
                        <h4>Phone</h4>
                        <p>+91 7411712661</p>
                    </div>
                </div>
                <div class="contact-info-item">
                    <i class="fas fa-envelope"></i>
                    <div class="content">
                        <h4>Email</h4>
                        <p>info@thedesires.com</p>
                    </div>
                </div>
                <div class="contact-info-item">
                    <i class="fas fa-clock"></i>
                    <div class="content">
                        <h4>Hours</h4>
                        <p>Monday: Closed</p>
                        <p>Tuesday - Friday: 11am - 10pm</p>
                        <p>Saturday - Sunday: 10am - 11pm</p>
                    </div>
                </div>
                <div class="contact-info-item">
                    <i class="fas fa-share-alt"></i>
                    <div class="content">
                        <h4>Follow Us</h4>
                        <div class="social-icons">
                            <a href="#"><i class="fab fa-facebook-f"></i></a>
                            <a href="#"><i class="fab fa-twitter"></i></a>
                            <a href="#"><i class="fab fa-instagram"></i></a>
                            <a href="#"><i class="fab fa-linkedin-in"></i></a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="contact-form">
                <h3>Send us a Message</h3>
                <form action="process_contact.php" method="post" id="contact-form">
                    <div class="form-group">
                        <label for="name">Your Name</label>
                        <input type="text" id="name" name="name" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label for="email">Email Address</label>
                        <input type="email" id="email" name="email" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label for="subject">Subject</label>
                        <input type="text" id="subject" name="subject" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label for="message">Your Message</label>
                        <textarea id="message" name="message" class="form-control" rows="5" required></textarea>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn">Send Message</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Map Container -->
        <div class="map-container">
            <iframe
                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3737.901825003423!2d74.83837487489087!3d12.853024287451568!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3ba350a13025ca43%3A0xff493840cb0e889b!2sSrinivas%20College%20Pandeshwar!5e1!3m2!1sen!2sin!4v1743218607773!5m2!1sen!2sin"
                width="100%" height="100%" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
        </div>
    </div>
</section>

<!-- FAQ Section -->
<section class="features">
    <div class="container">
        <h2 class="section-title">Frequently Asked Questions</h2>
        <div class="features-container">
            <div class="feature-box">
                <i class="fas fa-question-circle"></i>
                <h3>Do you offer delivery?</h3>
                <p>Yes, we offer delivery within a 5-mile radius of the restaurant. Orders can be placed online or by
                    phone.</p>
            </div>
            <div class="feature-box">
                <i class="fas fa-calendar-alt"></i>
                <h3>How far in advance should I make a reservation?</h3>
                <p>We recommend booking at least 2-3 days in advance, especially for weekend dining.</p>
            </div>
            <div class="feature-box">
                <i class="fas fa-utensils"></i>
                <h3>Do you accommodate dietary restrictions?</h3>
                <p>Absolutely! We offer vegetarian, vegan, and gluten-free options. Please inform us of any allergies or
                    dietary needs.</p>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>