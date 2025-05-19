<?php
// Include header
include 'includes/header.php';

// Check if user is logged in and is a client
if (!isLoggedIn() || !isClient()) {
    redirect('login.php');
}

// Get user's favorite recipes
$user_id = $_SESSION['user_id'];
$favorites = getUserFavorites($user_id);
?>

<!-- Hero Section -->
<section class="hero">
    <div class="container">
        <h1>My Favorite Recipes</h1>
        <p>Your personal collection of favorite recipes.</p>
    </div>
</section>

<!-- Favorites Section -->
<section class="recipes">
    <div class="container">
        <?php if (empty($favorites)): ?>
            <div style="text-align: center; padding: 3rem 0;">
                <h2>No Favorites Yet</h2>
                <p>You haven't added any recipes to your favorites yet.</p>
                <a href="recipes.php" class="btn" style="margin-top: 1rem;">Browse Recipes</a>
            </div>
        <?php else: ?>
            <div class="recipe-grid">
                <?php foreach ($favorites as $recipe): ?>
                    <div class="recipe-card">
                        <div class="recipe-img">
                            <img src="<?php echo $recipe['image_path']; ?>" alt="<?php echo $recipe['title']; ?>">
                        </div>
                        <div class="recipe-content">
                            <h3><?php echo $recipe['title']; ?></h3>
                            <p><?php echo substr($recipe['description'], 0, 100) . '...'; ?></p>
                            <div class="recipe-meta">
                                <span><i class="far fa-clock"></i> <?php echo $recipe['prep_time'] + $recipe['cook_time']; ?> mins</span>
                                <span><i class="fas fa-utensils"></i> <?php echo $recipe['servings']; ?> servings</span>
                            </div>
                            <span class="recipe-difficulty difficulty-<?php echo $recipe['difficulty']; ?>">
                                <?php echo ucfirst($recipe['difficulty']); ?>
                            </span>
                            <a href="recipe-detail.php?id=<?php echo $recipe['recipe_id']; ?>" class="btn btn-outline">View Recipe</a>
                        </div>
                        <button class="favorite-btn active" data-recipe-id="<?php echo $recipe['recipe_id']; ?>">
                            <i class="fas fa-heart"></i>
                        </button>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php
// Include footer
include 'includes/footer.php';
?>