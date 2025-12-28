<?php
$page_title = "Discover";
require_once 'includes/db_connection.php';
require_once 'includes/functions.php';

// Include header
include 'includes/header.php';

// Additional styles for gallery
$additional_styles = '<style>
    .gallery {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        gap: 20px;
        margin-top: 30px;
    }
    .gallery-item {
        position: relative;
        overflow: hidden;
        border-radius: 8px;
        height: 250px;
        cursor: pointer;
    }
    .gallery-image {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.5s;
        margin : 15px;
    }
    .gallery-item:hover .gallery-image {
        transform: scale(1.05);
    }
    .gallery-caption {
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        background: rgba(0,0,0,0.7);
        color: white;
        padding: 15px;
        transform: translateY(100%);
        transition: transform 0.3s;
    }
    .gallery-item:hover .gallery-caption {
        transform: translateY(0);
    }
    .lightbox {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0,0,0,0.9);
        display: flex;
        justify-content: center;
        align-items: center;
        z-index: 9999;
        display: none;
        flex-wrap: wrap; /* Allow images to wrap */
        gap: 20px; /* Add spacing between images */
        padding: 20px; /* Add padding around the images */
        overflow-y: auto; /* Allow scrolling if content overflows */
    }
    .lightbox.active {
        display: flex;
    }
    .lightbox-img {
        max-width: 90%;
        max-height: 90%;
        object-fit: contain; /* Ensure images maintain aspect ratio */
        border: 2px solid white;
        border-radius: 8px;
        margin: 20px;
    }
    .lightbox-close {
        position: absolute;
        top: 20px;
        right: 20px;
        color: white;
        font-size: 30px;
        cursor: pointer;
        z-index: 10000;
    }
    .timeline {
        position: relative;
        max-width: 800px;
        margin: 50px auto;
    }
    .timeline::after {
        content: \'\';
        position: absolute;
        width: 6px;
        background-color: var(--primary-color);
        top: 0;
        bottom: 0;
        left: 50%;
        margin-left: -3px;
    }
    .timeline-item {
        padding: 10px 40px;
        position: relative;
        width: 50%;
        box-sizing: border-box;
    }
    .timeline-item:nth-child(odd) {
        left: 0;
    }
    .timeline-item:nth-child(even) {
        left: 50%;
    }
    .timeline-content {
        padding: 20px;
        background-color: white;
        border-radius: 8px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }
    .timeline-content h3 {
        margin-bottom: 10px;
        color: var(--secondary-color);
    }
    .timeline-item::after {
        content: \'\';
        position: absolute;
        width: 25px;
        height: 25px;
        background-color: white;
        border: 4px solid var(--primary-color);
        border-radius: 50%;
        top: 15px;
        z-index: 1;
    }
    .timeline-item:nth-child(odd)::after {
        right: -12px;
    }
    .timeline-item:nth-child(even)::after {
        left: -12px;
    }
    @media screen and (max-width: 768px) {
        .timeline::after {
            left: 31px;
        }
        .timeline-item {
            width: 100%;
            padding-left: 70px;
            padding-right: 25px;
        }
        .timeline-item:nth-child(even) {
            left: 0;
        }
        .timeline-item::after {
            left: 15px;
        }
    }
</style>';

// Gallery images
$gallery_images = [
    [
        'thumbnail' => '',
        'full' => '',
        'title' => 'Elegant Dining Area',
        'description' => 'Our main dining area features comfortable seating in an elegant setting.'
    ],
    [
        'thumbnail' => '',
        'full' => '',
        'title' => 'Master Chefs at Work',
        'description' => 'Our expert chefs prepare each dish with precision and passion.'
    ],
    [
        'thumbnail' => '',
        'full' => '',
        'title' => 'Culinary Artistry',
        'description' => 'Each dish is carefully plated to deliver a feast for the eyes as well as the palate.'
    ],
    [
        'thumbnail' => '',
        'full' => '',
        'title' => 'Signature Cocktails',
        'description' => 'Our expert mixologists create unique cocktails to complement your meal.'
    ],
    [
        'thumbnail' => '',
        'full' => '',
        'title' => 'Decadent Desserts',
        'description' => 'End your meal with one of our carefully crafted desserts.'
    ],
    [
        'thumbnail' => '',
        'full' => '',
        'title' => 'Outdoor Dining',
        'description' => 'Enjoy your meal in our beautiful outdoor patio during the warmer months.'
    ]
];

// History timeline
$history_items = [
    [
        'year' => '2020',
        'title' => 'The Beginning',
        'description' => 'the desires Restaurant was founded by Azeem with a vision to create an exceptional dining experience.'
    ],
    [
        'year' => '2021',
        'title' => 'First Recognition',
        'description' => 'Received our first critical acclaim and was featured in the "Best New Restaurants" list.'
    ],
    [
        'year' => '2022',
        'title' => 'Expansion',
        'description' => 'Expanded our dining area and introduced a private dining room for special events.'
    ],
    [
        'year' => '2023',
        'title' => 'Michelin Star',
        'description' => 'Proudly received our first Michelin star, recognizing our commitment to culinary excellence.'
    ],
    [
        'year' => '2024',
        'title' => 'Online Experience',
        'description' => 'Launched our online ordering system to bring the desires experience to your home.'
    ],
    [
        'year' => '2025',
        'title' => 'Today',
        'description' => 'Continuing our commitment to exceptional food, service, and dining experiences for all our guests.'
    ]
];
?>
<style>
    .lightbox-img {
        max-width: 90%;
        max-height: 90%;
        object-fit: contain;
        /* Ensure images maintain aspect ratio */
        border: 2px solid white;
        border-radius: 8px;
        margin: 15px;
    }
</style>
<!-- Discover Banner -->
<section class="page-banner"
    style="background-image: linear-gradient(rgba(199, 186, 186, 0.6), rgba(0, 0, 0, 0.6)), url('assets/other/discover.jpg');">
    <div class="container">
        <h1>Discover the desires</h1>
        <p>Explore our story, our space, and what makes us a unique dining destination</p>
    </div>
</section>

<!-- Our Story Section -->
<section class="about-section">
    <div class="container">
        <h2 class="section-title">Our Story</h2>
        <p class="section-description">From humble beginnings to culinary excellence, discover the journey of The
            Desires Restaurant.</p>

        <div class="timeline">
            <?php foreach ($history_items as $item): ?>
                <div class="timeline-item">
                    <div class="timeline-content">
                        <h3><?php echo $item['year']; ?> - <?php echo htmlspecialchars($item['title']); ?></h3>
                        <p><?php echo htmlspecialchars($item['description']); ?></p>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Gallery Section -->
<section class="features">
    <div class="container">
        <h2 class="section-title">Photo Gallery</h2>
        <p class="section-description">Take a visual tour of our restaurant, our food, and the experiences we create.
        </p>

        <div class="gallery">
            <?php foreach ($gallery_images as $index => $image): ?>
                <div class="gallery-item">
                    <img src="<?php echo htmlspecialchars($image['thumbnail']); ?>"
                        alt="<?php echo htmlspecialchars($image['title']); ?>" class="gallery-image"
                        data-full="<?php echo htmlspecialchars($image['full']); ?>">
                    <div class="gallery-caption">
                        <h3><?php echo htmlspecialchars($image['title']); ?></h3>
                        <p><?php echo htmlspecialchars($image['description']); ?></p>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Lightbox -->
<div class="lightbox">
    <img src="assets/other/gallery.jpg" alt="Gallery Image" class="lightbox-img">
    <img src="assets/other/gallery2.2.jpg" alt="Gallery Image" class="lightbox-img">
    <img src="assets/other/gallery3.jpg" alt="Gallery Image" class="lightbox-img">
    <div class="lightbox-close">&times;</div>
</div>

<!-- <script>// Open lightbox
const galleryItems = document.querySelectorAll('.gallery-item');
const lightbox = document.querySelector('.lightbox');
const lightboxClose = document.querySelector('.lightbox-close');

galleryItems.forEach(item => {
    item.addEventListener('click', () => {
        lightbox.classList.add('active');
    });
});

// Close lightbox
lightboxClose.addEventListener('click', () => {
    lightbox.classList.remove('active');
});
</script> -->

<!-- Culinary Philosophy -->
<section class="about-section">
    <div class="container">
        <h2 class="section-title">Our Culinary Philosophy</h2>
        <p class="section-description">At the desires, we believe that food is more than just sustenance—it's an
            experience that engages all the senses.</p>

        <div class="about-content">
            <div class="about-text">
                <h3>Farm to Table</h3>
                <p>We source our ingredients from local farmers and producers who share our commitment to quality and
                    sustainability. By working directly with these partners, we ensure that only the freshest,
                    highest-quality ingredients make it to your plate.</p>

                <h3>Culinary Innovation</h3>
                <p>Our chefs are constantly exploring new techniques and flavors to create unique  dishes that surprise
                    and delight. We balance innovation with respect for traditional cooking me- thods to deliver food that
                    is both exciting and satisfying.</p>

                <h3>Seasonal Menus</h3>
                <p>Our menu changes with the seasons to showcase ingredients at their peak. This approach not only
                    ensures the best flavors but also reduces our environmental impact and supports sustainable farming
                    practices.</p>
            </div>
            <div class="about-image">
                <img src="assets/other/chef.jpg" alt="Chef in Kitchen">
            </div>
        </div>
    </div>
</section>

<!-- Testimonials Section -->
<section class="testimonials">
    <div class="container">
        <h2 class="section-title">What Critics Say</h2>
        <p class="section-description">the desires has received acclaim from food critics and publications around the
            world.</p>

        <div class="testimonials-container">
            <div class="testimonial-card">
                <div class="testimonial-rating">
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                </div>
                <p class="testimonial-quote">"the desires delivers an unforgettable dining experience that combines
                    innovative cuisine with impeccable service. A must-visit destination for food enthusiasts."</p>
                <p class="testimonial-author">- Food Magazine</p>
            </div>
            <div class="testimonial-card">
                <div class="testimonial-rating">
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star-half-alt"></i>
                </div>
                <p class="testimonial-quote">"Chef Johnson's creative approach to classic dishes results in flavors that
                    are both familiar and surprising. The attention to detail is evident in every aspect of the
                    restaurant."</p>
                <p class="testimonial-author">- The New York Times</p>
            </div>
            <div class="testimonial-card">
                <div class="testimonial-rating">
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                </div>
                <p class="testimonial-quote">"the desires has successfully created a dining experience that feels both
                    luxurious and welcoming. The tasting menu is a culinary journey worth taking."</p>
                <p class="testimonial-author">- Gourmet Traveler</p>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="cta">
    <div class="container">
        <div class="cta-content">
            <h2>Experience the desires</h2>
            <p>Join us for an unforgettable dining experience that will tantalize your taste buds and create lasting
                memories.</p>
            <div class="hero-buttons">
                <a href="reservation.php" class="btn">Make a Reservation</a>
                <a href="menu.php" class="btn btn-secondary">Explore Our Menu</a>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>