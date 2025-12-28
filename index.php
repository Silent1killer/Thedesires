<?php
$page_title = "Home";
require_once 'includes/db_connection.php';
require_once 'includes/functions.php';

// Get popular dishes
$popular_dishes = get_popular_menu_items(4);

// Get testimonials (for demonstration, hardcoded values)
$testimonials = [
    [
        'name' => 'John D.',
        'rating' => 5,
        'comment' => 'The food was absolutely delicious! I will definitely be coming back again.',
        'avatar' => 'https://randomuser.me/api/portraits/men/1.jpg'
    ],
    [
        'name' => 'Sarah M.',
        'rating' => 4,
        'comment' => 'Great atmosphere and excellent service. The pasta dishes are exceptional.',
        'avatar' => 'https://randomuser.me/api/portraits/women/2.jpg'
    ],
    [
        'name' => 'Robert K.',
        'rating' => 5,
        'comment' => 'One of the best dining experiences I\'ve had in years. Highly recommended!',
        'avatar' => 'https://randomuser.me/api/portraits/men/3.jpg'
    ]
];

// Include header
include 'includes/header.php';
?>
<style>
    .hero {
        background-image: linear-gradient(rgba(60, 48, 48, 0), rgb(55, 51, 51)), url('assets/other/discover.jpg');
        max-width: 8000px;
        background-size: cover;
        background-position: center;
        color: rgb(4, 4, 4);
        text-align: center;
        padding: 150px 0;
    }

    .hero-content {
        color: white;
        background: none;
    }

    .hero-content {
        animation: fadeIn 1s ease-in-out;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
        }

        to {
            opacity: 1;
        }
    }
</style>
<!-- Hero Section -->
<section class="hero">
    <div class="hero-content">
        <h1>Welcome to the Desires Restaurant</h1>
        <p>Experience the finest dining with our exquisite menu curated by world-class chefs. We bring you authentic
            flavors with a modern twist.</p>
        <div class="hero-buttons">
            <a href="menu.php" class="btn">View Menu</a>
            <a href="reservation.php" class="btn btn-secondary">Book a Table</a>
        </div>
    </div>
</section>

<!-- Features Section -->
<section class="features">
    <div class="container">
        <h2 class="section-title">Why Choose Us</h2>
        <p class="section-description">At the desires, we are committed to providing an exceptional dining experience
            with high-quality ingredients and impeccable service.</p>
        <div class="features-container">
            <div class="feature-box">
                <i class="fas fa-utensils"></i>
                <h3>Exquisite Cuisine</h3>
                <p>Our menu features a variety of culinary delights prepared by award-winning chefs using the freshest
                    ingredients.</p>
            </div>
            <div class="feature-box">
                <i class="fas fa-burger"></i>
                <h3>Fine Dining</h3>
                <p>Enjoy an elegant atmosphere with comfortable seating, stylish décor, and attentive service.</p>
            </div>
            <div class="feature-box">
                <i class="fas fa-truck"></i>
                <h3>Online Ordering</h3>
                <p>Can't dine in? Order your favorite dishes online for delivery or pickup at your convenience.</p>
            </div>
        </div>
    </div>
</section>

<!-- Popular Dishes Section -->
<section class="popular-dishes">
    <div class="container">
        <h2 class="section-title">Popular Dishes</h2>
        <p class="section-description">Discover our most loved dishes that keep our customers coming back for more.</p>
        <div class="dishes-container">
            <?php foreach ($popular_dishes as $dish): ?>
                <div class="dish-card">
                    <div class="dish-image"
                        style="background-image: url('<?php echo htmlspecialchars($dish['image_url']); ?>')"></div>
                    <div class="dish-info">
                        <h3><?php echo htmlspecialchars($dish['name']); ?></h3>
                        <div class="rating">
                            <?php
                            $rating = round($dish['avg_rating'] ?? 0);
                            for ($i = 1; $i <= 5; $i++) {
                                if ($i <= $rating) {
                                    echo '<i class="fas fa-star"></i>';
                                } else {
                                    echo '<i class="far fa-star"></i>';
                                }
                            }
                            ?>
                        </div>
                        <p class="price"><?php echo format_currency($dish['price']); ?></p>
                        <div class="dish-actions">
                            <a href="menu.php#dish-<?php echo $dish['id']; ?>" class="btn">View Details</a>
                            <form action="add_to_cart.php" method="post">
                                <input type="hidden" name="item_id" value="<?php echo $dish['id']; ?>">
                                <input type="hidden" name="quantity" value="1">
                                <button type="submit" class="btn btn-secondary add-to-cart-btn">
                                    <i class="fas fa-shopping-cart"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <div style="text-align: center; margin-top: 30px;">
            <a href="menu.php" class="btn">View Full Menu</a>
        </div>
    </div>
</section>

<!-- Testimonials Section -->
<section class="testimonials">
    <div class="container">
        <h2 class="section-title">Customer Testimonials</h2>
        <p class="section-description">Don't just take our word for it. See what our satisfied customers have to say
            about their dining experience.</p>
        <div class="testimonials-container">
            <?php foreach ($testimonials as $testimonial): ?>
                <div class="testimonial-card">
                    <img src="<?php echo htmlspecialchars($testimonial['avatar']); ?>"
                        alt="<?php echo htmlspecialchars($testimonial['name']); ?>" class="testimonial-avatar">
                    <div class="testimonial-rating">
                        <?php
                        for ($i = 1; $i <= 5; $i++) {
                            if ($i <= $testimonial['rating']) {
                                echo '<i class="fas fa-star"></i>';
                            } else {
                                echo '<i class="far fa-star"></i>';
                            }
                        }
                        ?>
                    </div>
                    <p class="testimonial-quote">"<?php echo htmlspecialchars($testimonial['comment']); ?>"</p>
                    <p class="testimonial-author"><?php echo htmlspecialchars($testimonial['name']); ?></p>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="cta">
    <div class="container">
        <div class="cta-content">
            <h2>Reserve Your Table Today</h2>
            <p>Experience the culinary excellence and impeccable service at the desires. Book your table now to avoid
                disappointment.</p>
            <a href="reservation.php" class="btn">Make a Reservation</a>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>