<?php
$page_title = "About Us";
require_once 'includes/db_connection.php';
require_once 'includes/functions.php';

// Include header
include 'includes/header.php';

// Team members data
$team_members = [
    [
        'name' => 'Azeem',
        'role' => 'Executive Chef & Founder',
        'bio' => 'With over 20 years of culinary experience, Chef Michael brings his passion for innovative cuisine to the desires.',
        'image' => 'assets/other/dp.jpg'
    ],
    [
        'name' => 'Sabeel',
        'role' => 'Head Chef',
        'bio' => 'Specializing in fusion cuisine, Chef member 1 creates unique dishes that blend traditional and modern techniques.',
        'image' => 'assets/other/chebbi1.jpg'
    ],
    [
        'name' => 'member 2',
        'role' => 'Pastry Chef',
        'bio' => 'A master of sweet creations, member 2 designs desserts that are as beautiful as they are delicious.',
        'image' => 'assets/other/chef2.jpg'
    ],
    [
        'name' => 'member 3',
        'role' => 'Restaurant Manager',
        'bio' => 'member 3 ensures that each guest receives exceptional service from the moment they enter until they leave.',
        'image' => 'assets/other/manager.jpeg'
    ]
];
?>

<!-- About Banner -->
<section class="page-banner"
    style="background-image: linear-gradient(rgba(249, 249, 249, 0.6), rgba(46, 35, 35, 0.97)), url('assets/other/dining.jpg');">
    <div class="container">
        <h1>About the desires</h1>
        <p>Discover our story, our values, and the people who make us special</p>
    </div>
</section>

<!-- Our Story Section -->
<section class="about-section">
    <div class="container">
        <h2 class="section-title">Our Story</h2>
        <div class="about-content">
            <div class="about-text">
                <p>The Desires Restaurant was founded in 2020 by Azeem,  who had a vision to create a dining 
                     establishment  that would combine exceptional cuisine with a warm, inviting atmosphere.</p>

                <p>What began as a small bistro with just a handful of tables has grown into one of the city's most
                    beloved dining destinations. Throughout our journey, we've remained committed to our founding
                    principles: using the finest ingredients, embracing culinary creativity, and providing impeccable
                    service.</p>

                <p>Over the years, the desires has received numerous accolades, including a Michelin star in 2023, but
                    our greatest reward is the satisfaction of our guests who return time and again to experience our
                    evolving menu and consistent quality.</p>

                <div class="about-features">
                    <div class="about-feature">
                        <i class="fas fa-check-circle"></i>
                        <span>Locally sourced, fresh ingredients</span>
                    </div>
                    <div class="about-feature">
                        <i class="fas fa-check-circle"></i>
                        <span>Innovative culinary techniques</span>
                    </div>
                    <div class="about-feature">
                        <i class="fas fa-check-circle"></i>
                        <span>Warm, attentive service</span>
                    </div>
                    <div class="about-feature">
                        <i class="fas fa-check-circle"></i>
                        <span>Elegant yet comfortable atmosphere</span>
                    </div>
                </div>
            </div>
            <div class="about-image">
                <img src="assets/other/inter.jpg"
                    alt="the desires Restaurant Interior">
            </div>
        </div>
    </div>
</section>

<!-- Our Values Section -->
<section class="features">
    <div class="container">
        <h2 class="section-title">Our Values</h2>
        <p class="section-description">The principles that guide everything we do at the desires Restaurant.</p>

        <div class="features-container">
            <div class="feature-box">
                <i class="fas fa-leaf"></i>
                <h3>Sustainability</h3>
                <p>We are committed to sustainable practices, from sourcing ingredients to reducing waste and minimizing
                    our environmental footprint.</p>
            </div>
            <div class="feature-box">
                <i class="fas fa-star"></i>
                <h3>Excellence</h3>
                <p>We strive for excellence in every aspect of our restaurant, from the food we serve to the experience
                    we create for our guests.</p>
            </div>
            <div class="feature-box">
                <i class="fas fa-heart"></i>
                <h3>Passion</h3>
                <p>Our team shares a passion for food, hospitality, and creating memorable experiences for every guest
                    who walks through our doors.</p>
            </div>
            <div class="feature-box">
                <i class="fas fa-users"></i>
                <h3>Community</h3>
                <p>We believe in giving back to our community and supporting local producers, farmers, and artisans.</p>
            </div>
        </div>
    </div>
</section>

<!-- Meet the Team Section -->
<section class="team-section">
    <div class="container">
        <h2 class="section-title">Meet Our Team</h2>
        <p class="section-description">The talented individuals who bring the desires experience to life every day.</p>

        <div class="team-members">
            <?php foreach ($team_members as $member): ?>
                <div class="team-member">
                    <div class="team-member-image"
                        style="background-image: url('<?php echo htmlspecialchars($member['image']); ?>')"></div>
                    <div class="team-member-info">
                        <h3><?php echo htmlspecialchars($member['name']); ?></h3>
                        <p class="role"><?php echo htmlspecialchars($member['role']); ?></p>
                        <p><?php echo htmlspecialchars($member['bio']); ?></p>
                        <div class="team-member-social">
                            <a href="#"><i class="fab fa-twitter"></i></a>
                            <a href="#"><i class="fab fa-instagram"></i></a>
                            <a href="#"><i class="fab fa-linkedin-in"></i></a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Our Approach Section -->
<section class="about-section">
    <div class="container">
        <h2 class="section-title">Our Culinary Approach</h2>
        <div class="about-content">
            <div class="about-image">
                <img src="assets/other/create.jpg" alt="Culinary Creation">
            </div>
            <div class="about-text">
                <p>At the desires, we believe that exceptional food begins with exceptional ingredients. We work closely
                    with local farmers and producers to source the freshest, highest-quality ingredients available.</p>

                <p>Our menu is a reflection of both tradition and innovation. We respect classic techniques while
                    embracing modern approaches to create dishes that are familiar yet surprising, comforting yet
                    exciting.</p>

                <p>Seasonality plays a crucial role in our culinary philosophy. Our menu evolves throughout the year to
                    showcase ingredients at their peak, ensuring that each dish we serve represents the best of what's
                    available.</p>

                <p>Every plate that leaves our kitchen is a work of art, crafted with precision and passion by our
                    talented culinary team. We believe that dining is a multi-sensory experience, and we pay careful
                    attention to flavor, texture, aroma, and presentation.</p>
            </div>
        </div>
    </div>
</section>

<!-- Achievements Section -->
<section class="features">
    <div class="container">
        <h2 class="section-title">Our Achievements</h2>
        <p class="section-description">Recognition of our commitment to culinary excellence and exceptional dining
            experiences.</p>

        <div class="features-container">
            <div class="feature-box">
                <i class="fas fa-award"></i>
                <h3>Michelin Star</h3>
                <p>Awarded our first Michelin star in 2023, recognizing our outstanding cuisine and service.</p>
            </div>
            <div class="feature-box">
                <i class="fas fa-trophy"></i>
                <h3>Best Fine Dining</h3>
                <p>Named "Best Fine Dining Restaurant" by City Dining Guide for three consecutive years.</p>
            </div>
            <div class="feature-box">
                <i class="fas fa-thumbs-up"></i>
                <h3>Customer Choice</h3>
                <p>Voted "Customer Favorite" in the annual Dining Excellence Awards since 20XX.</p>
            </div>
        </div>
    </div>
</section>

<!-- Join Us Section -->
<section class="cta">
    <div class="container">
        <div class="cta-content">
            <h2>Join the desires Family</h2>
            <p>Interested in a career with us? We're always looking for passionate, talented individuals to join our
                team.</p>
            <a href="contact.php" class="btn">Contact Us</a>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>