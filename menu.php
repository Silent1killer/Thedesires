<?php
$page_title = "Menu";
require_once 'includes/db_connection.php';
require_once 'includes/functions.php';

// Get categories
$categories = get_menu_categories();

// Get all menu items
$menu_items = get_all_menu_items();

// Group items by category
$items_by_category = [];
foreach ($menu_items as $item) {
    $category_id = $item['category_id'];
    if (!isset($items_by_category[$category_id])) {
        $items_by_category[$category_id] = [];
    }
    $items_by_category[$category_id][] = $item;
}

// Include header
include 'includes/header.php';
?>

<!-- Menu Banner -->
<section class="page-banner" style="background-image: linear-gradient(rgba(188, 180, 180, 0.6), rgba(77, 71, 71, 0.6)), url('assets/other/menu.jpg');">
    <div class="container">
        <h1>Our Menu</h1>
        <p>Discover our exquisite culinary creations crafted with passion and the finest ingredients</p>
    </div>
</section>

<!-- Menu Section -->
<section class="menu-section">
    <div class="container">
        <!-- Categories Filter -->
        <div class="menu-categories">
            <button class="category-btn active" data-category="all">All</button>
            <?php foreach ($categories as $category): ?>
                <button class="category-btn" data-category="<?php echo $category['id']; ?>">
                    <?php echo htmlspecialchars($category['name']); ?>
                </button>
            <?php endforeach; ?>
        </div>
        
        <!-- Menu Items -->
        <div class="menu-items">
            <?php foreach ($menu_items as $item): ?>
                <div class="menu-item" id="dish-<?php echo $item['id']; ?>" data-category="<?php echo $item['category_id']; ?>">
                    <div class="menu-item-image" style="background-image: url('<?php echo htmlspecialchars($item['image_url']); ?>')"></div>
                    <div class="menu-item-info">
                        <h3><?php echo htmlspecialchars($item['name']); ?></h3>
                        <p class="description"><?php echo htmlspecialchars($item['description']); ?></p>
                        <div class="price"><?php echo format_currency($item['price']); ?></div>
                        <div class="rating">
                            <?php 
                            $rating = round($item['avg_rating'] ?? 0);
                            for ($i = 1; $i <= 5; $i++) {
                                if ($i <= $rating) {
                                    echo '<i class="fas fa-star"></i>';
                                } else {
                                    echo '<i class="far fa-star"></i>';
                                }
                            }
                            ?>
                            <span>(<?php echo (int)($item['review_count'] ?? 0); ?>)</span>
                        </div>
                        <div class="menu-item-actions">
                            <a href="item_details.php?id=<?php echo $item['id']; ?>" class="btn">Details</a>
                            <form action="add_to_cart.php" method="post">
                                <input type="hidden" name="item_id" value="<?php echo $item['id']; ?>">
                                <input type="hidden" name="quantity" value="1">
                                <button type="submit" class="btn btn-secondary add-to-cart-btn">
                                    <i class="fas fa-cart-plus"></i> Add to Cart
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Daily Specials -->
<section class="features" style="padding-top: 0;">
    <div class="container">
        <h2 class="section-title">Daily Specials</h2>
        <p class="section-description">Our chef's special creations that change daily to bring you new flavors and experiences.</p>
        
        <div class="features-container">
            <div class="feature-box">
                <i class="fas fa-coffee"></i>
                <h3>Monday - Coffee Special</h3>
                <p>Buy any main course and get a premium coffee for free.</p>
            </div>
            <div class="feature-box">
                <i class="fas fa-pizza-slice"></i>
                <h3>Tuesday - Pizza Night</h3>
                <p>20% off on all our artisanal pizzas.</p>
            </div>
            <div class="feature-box">
                <i class="fas fa-burger"></i>
                <h3>Wednesday - Special Day</h3>
                <p>Special Dishes From our Chefs</p>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="cta">
    <div class="container">
        <div class="cta-content">
            <h2>Ready to Order?</h2>
            <p>Enjoy our delicious food from the comfort of your home. Order online now for pickup or delivery.</p>
            <div class="hero-buttons">
                <a href="cart.php" class="btn">View Cart</a>
                <a href="reservation.php" class="btn btn-secondary">Make a Reservation</a>
            </div>
        </div>
    </div>
</section>

<!-- Special Dietary Options -->
<section class="about-section">
    <div class="container">
        <h2 class="section-title">Special Dietary Options</h2>
        <p class="section-description">We cater to various dietary requirements to ensure everyone can enjoy our culinary creations.</p>
        
        <div class="about-features">
            <div class="about-feature">
                <i class="fas fa-seedling"></i>
                <div>
                    <h3>Vegetarian</h3>
                    <p>We offer a variety of vegetarian options that don't compromise on flavor.</p>
                </div>
            </div>
            <div class="about-feature">
                <i class="fas fa-leaf"></i>
                <div>
                    <h3>Vegan</h3>
                    <p>Our vegan dishes are prepared with plant-based ingredients only.</p>
                </div>
            </div>
            <div class="about-feature">
                <i class="fas fa-bread-slice"></i>
                <div>
                    <h3>Gluten-Free</h3>
                    <p>Many of our dishes can be prepared gluten-free upon request.</p>
                </div>
            </div>
            <div class="about-feature">
                <i class="fas fa-pepper-hot"></i>
                <div>
                    <h3>Spice Level</h3>
                    <p>We can adjust the spice level of dishes according to your preference.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
