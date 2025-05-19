<?php
// Include header
include 'includes/header.php';

// Get all services
$services = getAllServices();
?>

<!-- Hero Section -->
<section class="hero">
    <div class="container">
        <h1>Our Services</h1>
        <p>Discover our range of cooking classes and workshops led by professional chefs.</p>
    </div>
</section>

<!-- Services Section -->
<section class="services">
    <div class="container">
        <div class="section-title">
            <h2>Cooking Classes & Workshops</h2>
            <p>Join our hands-on cooking classes and workshops to learn new skills and recipes in a fun and interactive environment.</p>
        </div>
        
        <div class="services-grid">
            <?php foreach ($services as $service): ?>
                <div class="service-card">
                    <div class="service-img">
                        <img src="<?php echo $service['image_path']; ?>" alt="<?php echo $service['title']; ?>">
                    </div>
                    <div class="service-content">
                        <h3><?php echo $service['title']; ?></h3>
                        <p><?php echo substr($service['description'], 0, 150) . '...'; ?></p>
                        <div class="service-meta">
                            <span><i class="far fa-clock"></i> <?php echo $service['duration']; ?></span>
                            <span><i class="fas fa-users"></i> Max <?php echo $service['capacity']; ?> people</span>
                        </div>
                        <div class="service-meta">
                            <span><i class="fas fa-tag"></i> $<?php echo number_format($service['price'], 2); ?></span>
                        </div>
                        <a href="service-detail.php?id=<?php echo $service['service_id']; ?>" class="btn btn-outline">Learn More</a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Why Choose Us Section -->
<section class="about">
    <div class="container">
        <div class="section-title">
            <h2>Why Choose Our Classes</h2>
            <p>Here's why our cooking classes and workshops stand out from the rest.</p>
        </div>
        
        <div class="about-content">
            <div class="about-text">
                <h3>Expert Instructors</h3>
                <p>All our classes are taught by professional chefs with years of experience in the culinary industry.</p>
                
                <h3>Small Class Sizes</h3>
                <p>We keep our class sizes small to ensure that each participant gets personalized attention and guidance.</p>
                
                <h3>Hands-On Experience</h3>
                <p>Our classes are hands-on, allowing you to practice and perfect your skills under the guidance of our expert chefs.</p>
                
                <h3>Quality Ingredients</h3>
                <p>We use only the finest and freshest ingredients in our classes, ensuring that you learn to cook with the best.</p>
                
                <h3>Take-Home Recipes</h3>
                <p>After each class, you'll receive detailed recipes to take home, so you can recreate the dishes on your own.</p>
                
                <h3>Fun and Interactive</h3>
                <p>Our classes are designed to be fun and interactive, creating a relaxed and enjoyable learning environment.</p>
            </div>
            <div class="about-image">
                <img src="assets/images/cooking-class.jpg" alt="Cooking Class">
            </div>
        </div>
    </div>
</section>

<?php
// Include footer
include 'includes/footer.php';
?>