<?php
$page_title = "Menu Item Details";
require_once 'includes/db_connection.php';
require_once 'includes/functions.php';
require_once 'includes/auth.php';

// Check if item ID is provided
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    redirect('menu.php');
}

$item_id = (int)$_GET['id'];
$item = get_menu_item_by_id($item_id);

// If item doesn't exist, redirect to menu page
if (!$item) {
    redirect('menu.php');
}

// Get item reviews
$reviews = get_reviews_for_item($item_id);

// Check if user has already reviewed this item
$user_review = null;
if (is_logged_in()) {
    $user_id = $_SESSION['user_id'];
    foreach ($reviews as $review) {
        if ($review['user_id'] == $user_id) {
            $user_review = $review;
            break;
        }
    }
}

// Success/error messages
$success_message = '';
$error_message = '';

if (isset($_GET['review_added']) && $_GET['review_added'] == 1) {
    $success_message = 'Your review has been added successfully!';
} elseif (isset($_GET['review_updated']) && $_GET['review_updated'] == 1) {
    $success_message = 'Your review has been updated successfully!';
} elseif (isset($_GET['error'])) {
    if ($_GET['error'] == 1) {
        $error_message = 'Please fill in all fields.';
    } elseif ($_GET['error'] == 2) {
        $error_message = 'An error occurred. Please try again.';
    }
}

// Get related items
$related_items = [];
if ($item['category_id']) {
    $query = "SELECT * FROM menu_items WHERE category_id = {$item['category_id']} AND id != $item_id LIMIT 4";
    $result = $conn->query($query);
    
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $related_items[] = $row;
        }
    }
}

// Get additional images for the item
$item_images = [];
$query = "SELECT image_url FROM menu_items WHERE id = $item_id";
$result = $conn->query($query);

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $item_images[] = $row['image_url'];
    }
}

// Include header
$page_title = $item['name'];
include 'includes/header.php';

// Additional scripts for rating functionality
$additional_scripts = '<script src="js/validation.js"></script>';
?>

<style>
    .item-images {
    margin-bottom: 20px;
}

.carousel-inner img {
    max-height: 400px;
    object-fit: cover;
}
</style>

<!-- Item Details Banner -->
<section class="page-banner" style="background-image: linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.6)), url('<?php echo htmlspecialchars($item['image_url']); ?>');">
    <div class="container">
        <h1><?php echo htmlspecialchars($item['name']); ?></h1>
        <p><?php echo htmlspecialchars($item['category_name']); ?></p>
    </div>
</section>

<!-- Item Details Section -->
<section class="menu-section">
    <div class="container">
        <?php if (!empty($success_message)): ?>
            <div class="alert alert-success"><?php echo $success_message; ?></div>
        <?php endif; ?>
        
        <?php if (!empty($error_message)): ?>
            <div class="alert alert-error"><?php echo $error_message; ?></div>
        <?php endif; ?>
        
        <div class="item-details">
            <div class="item-image" style="background-image: url('<?php echo htmlspecialchars($item['image_url']); ?>')"></div>
            <div class="item-images">
                <div id="item-images-carousel" class="carousel slide" data-ride="carousel">
                    <!-- Indicators -->
                    <ol class="carousel-indicators">
                        <?php foreach ($item_images as $index => $image_url): ?>
                            <li data-target="#item-images-carousel" data-slide-to="<?php echo $index; ?>" class="<?php echo $index === 0 ? 'active' : ''; ?>"></li>
                        <?php endforeach; ?>
                    </ol>

                    <!-- Carousel Items -->
                    <div class="carousel-inner">
                        <?php foreach ($item_images as $index => $image_url): ?>
                            <div class="carousel-item <?php echo $index === 0 ? 'active' : ''; ?>">
                                <img src="<?php echo htmlspecialchars($image_url); ?>" class="d-block w-100" alt="Item Image">
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <!-- Controls -->
                    <a class="carousel-control-prev" href="#item-images-carousel" role="button" data-slide="prev">
                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                        <span class="sr-only">Previous</span>
                    </a>
                    <a class="carousel-control-next" href="#item-images-carousel" role="button" data-slide="next">
                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                        <span class="sr-only">Next</span>
                    </a>
                </div>
            </div>
            <div class="item-info">
                <h2><?php echo htmlspecialchars($item['name']); ?></h2>
                <div class="item-category"><?php echo htmlspecialchars($item['category_name']); ?></div>
                <div class="item-rating">
                    <?php 
                    $rating = round($item['avg_rating'] ?? 0);
                    for ($i = 1; $i <= 5; $i++) {
                        if ($i <= $rating) {
                            echo '<i class="fas fa-star"></i>';
                        } else {
                            echo '<i class="far fa-star"></i>';
                        }
                    }
                    echo ' <span>(' . ((int)$item['review_count']) . ' reviews)</span>';
                    ?>
                </div>
                <div class="item-price"><?php echo format_currency($item['price']); ?></div>
                <div class="item-description">
                    <p><?php echo htmlspecialchars($item['description']); ?></p>
                </div>
                <div class="item-actions">
                    <form action="add_to_cart.php" method="post">
                        <input type="hidden" name="item_id" value="<?php echo $item['id']; ?>">
                        <div class="quantity-selector">
                            <label for="quantity">Quantity:</label>
                            <select id="quantity" name="quantity">
                                <?php for ($i = 1; $i <= 10; $i++): ?>
                                    <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
                                <?php endfor; ?>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-secondary add-to-cart-btn">
                            <i class="fas fa-cart-plus"></i> Add to Cart
                        </button>
                    </form>
                </div>
            </div>
        </div>
        
        <!-- Reviews Section -->
        <div class="reviews-section">
            <h2>Customer Reviews</h2>
            
            <?php if (is_logged_in()): ?>
                <div class="review-form">
                    <h3><?php echo $user_review ? 'Update Your Review' : 'Write a Review'; ?></h3>
                    <form action="process_review.php" method="post" id="review-form">
                        <input type="hidden" name="menu_item_id" value="<?php echo $item['id']; ?>">
                        
                        <div class="form-group">
                            <label for="rating-input">Rating</label>
                            <div class="rating-container">
                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                    <span class="rating-star <?php echo ($user_review && $i <= $user_review['rating']) ? 'active' : ''; ?>" data-rating="<?php echo $i; ?>">
                                        <i class="fas fa-star"></i>
                                    </span>
                                <?php endfor; ?>
                                <input type="hidden" id="rating-input" name="rating" value="<?php echo $user_review ? $user_review['rating'] : ''; ?>">
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="review-text">Your Review</label>
                            <textarea id="review-text" name="review_text" class="form-control" rows="4" required><?php echo $user_review ? htmlspecialchars($user_review['review_text']) : ''; ?></textarea>
                        </div>
                        
                        <div class="form-actions">
                            <button type="submit" class="btn"><?php echo $user_review ? 'Update Review' : 'Submit Review'; ?></button>
                        </div>
                    </form>
                </div>
            <?php else: ?>
                <div class="review-login-prompt">
                    <p>Please <a href="login.php">log in</a> to write a review.</p>
                </div>
            <?php endif; ?>
            
            <div class="reviews-list">
                <?php if (empty($reviews)): ?>
                    <p class="no-reviews">There are no reviews yet. Be the first to write a review!</p>
                <?php else: ?>
                    <?php foreach ($reviews as $review): ?>
                        <div class="review-card">
                            <div class="review-header">
                                <div class="review-user"><?php echo htmlspecialchars($review['user_name']); ?></div>
                                <div class="review-date"><?php echo date('M d, Y', strtotime($review['created_at'])); ?></div>
                            </div>
                            <div class="review-rating">
                                <?php 
                                for ($i = 1; $i <= 5; $i++) {
                                    if ($i <= $review['rating']) {
                                        echo '<i class="fas fa-star"></i>';
                                    } else {
                                        echo '<i class="far fa-star"></i>';
                                    }
                                }
                                ?>
                            </div>
                            <div class="review-text">
                                <?php echo htmlspecialchars($review['review_text']); ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Related Items Section -->
        <?php if (!empty($related_items)): ?>
            <div class="related-items-section">
                <h2>You May Also Like</h2>
                <div class="dishes-container">
                    <?php foreach ($related_items as $related_item): ?>
                        <div class="dish-card">
                            <div class="dish-image" style="background-image: url('<?php echo htmlspecialchars($related_item['image_url']); ?>')"></div>
                            <div class="dish-info">
                                <h3><?php echo htmlspecialchars($related_item['name']); ?></h3>
                                <p class="price"><?php echo format_currency($related_item['price']); ?></p>
                                <div class="dish-actions">
                                    <a href="item_details.php?id=<?php echo $related_item['id']; ?>" class="btn">View Details</a>
                                    <form action="add_to_cart.php" method="post">
                                        <input type="hidden" name="item_id" value="<?php echo $related_item['id']; ?>">
                                        <input type="hidden" name="quantity" value="1">
                                        <button type="submit" class="btn btn-secondary add-to-cart-btn">
                                            <i class="fas fa-cart-plus"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle rating star selection
    const stars = document.querySelectorAll('.rating-star');
    const ratingInput = document.getElementById('rating-input');
    
    stars.forEach((star, index) => {
        star.addEventListener('click', function() {
            const rating = parseInt(this.getAttribute('data-rating'));
            ratingInput.value = rating;
            
            // Update star UI
            stars.forEach((s, i) => {
                if (i < rating) {
                    s.classList.add('active');
                } else {
                    s.classList.remove('active');
                }
            });
        });
    });
});
</script>

<?php include 'includes/footer.php'; ?>
