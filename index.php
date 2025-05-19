<?php
// Include header
include 'includes/header.php';

// Get services for homepage
$services = getAllServices();

// Get public recipes
$recipes = getPublicRecipes();
?>

<!-- Hero Section -->
<section class="hero">
    <div class="container">
        <h1>Welcome to Culinary Workshop</h1>
        <p>Discover the joy of cooking with our expert-led classes and premium recipes. Whether you're a beginner or a seasoned chef, we have something for everyone.</p>
        <a href="services.php" class="btn">Explore Our Classes</a>
    </div>
</section>

<!-- Services Summary Section -->
<section class="services">
    <div class="container">
        <div class="section-title">
            <h2>Our Services</h2>
            <p>Join our hands-on cooking classes and workshops led by professional chefs.</p>
        </div>
        
        <div class="services-grid">
            <?php foreach ($services as $service): ?>
                <div class="service-card">
                    <div class="service-img">
                        <img src="<?php echo $service['image_path']; ?>" alt="<?php echo $service['title']; ?>">
                    </div>
                    <div class="service-content">
                        <h3><?php echo $service['title']; ?></h3>
                        <p><?php echo substr($service['description'], 0, 100) . '...'; ?></p>
                        <div class="service-meta">
                            <span><i class="far fa-clock"></i> <?php echo $service['duration']; ?></span>
                            <span><i class="fas fa-users"></i> Max <?php echo $service['capacity']; ?> people</span>
                        </div>
                        <a href="service-detail.php?id=<?php echo $service['service_id']; ?>" class="btn btn-outline">Learn More</a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        
        <div style="text-align: center; margin-top: 2rem;">
            <a href="services.php" class="btn">View All Services</a>
        </div>
    </div>
</section>

<!-- Featured Recipes Section -->
<section class="recipes">
    <div class="container">
        <div class="section-title">
            <h2>Featured Recipes</h2>
            <p>Explore our collection of delicious recipes from around the world.</p>
        </div>
        
        <div class="recipe-grid">
            <?php foreach ($recipes as $recipe): ?>
                <div class="recipe-card">
                    <div class="recipe-img">
                        <img src="<?php echo $recipe['image_path']; ?>" alt="<?php echo $recipe['title']; ?>">
                    </div>
                    <div class="recipe-content">
                        <h3><?php echo $recipe['title']; ?></h3>
                        <p><?php echo substr($recipe['description'], 0, 80) . '...'; ?></p>
                        <div class="recipe-meta">
                            <span><i class="far fa-clock"></i> <?php echo $recipe['prep_time'] + $recipe['cook_time']; ?> mins</span>
                            <span><i class="fas fa-utensils"></i> <?php echo $recipe['servings']; ?> servings</span>
                        </div>
                        <span class="recipe-difficulty difficulty-<?php echo $recipe['difficulty']; ?>">
                            <?php echo ucfirst($recipe['difficulty']); ?>
                        </span>
                        <a href="recipe-detail.php?id=<?php echo $recipe['recipe_id']; ?>" class="btn btn-outline">View Recipe</a>
                    </div>
                    <?php if (isLoggedIn() && isClient()): ?>
                        <button class="favorite-btn <?php echo isFavorite($_SESSION['user_id'], $recipe['recipe_id']) ? 'active' : ''; ?>" data-recipe-id="<?php echo $recipe['recipe_id']; ?>">
                            <i class="<?php echo isFavorite($_SESSION['user_id'], $recipe['recipe_id']) ? 'fas' : 'far'; ?> fa-heart"></i>
                        </button>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
        
        <div style="text-align: center; margin-top: 2rem;">
            <a href="recipes.php" class="btn">View All Recipes</a>
        </div>
    </div>
</section>

<?php
// Include footer
include 'includes/footer.php';
?>