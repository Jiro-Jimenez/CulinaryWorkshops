<?php
// Include header
include 'includes/header.php';

// Check if recipe ID is provided
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    redirect('recipes.php');
}

// Get recipe details
$recipe_id = $_GET['id'];
$recipe = getRecipeById($recipe_id);

// If recipe not found, redirect to recipes page
if (!$recipe) {
    redirect('recipes.php');
}

// Check if recipe is premium and user has access
if ($recipe['is_premium'] && (!isLoggedIn() || (!hasPremiumAccess($_SESSION['user_id']) && !isAdmin()))) {
    // Redirect to recipes page with message
    $_SESSION['message'] = "This is a premium recipe. Please enroll in our services to access it.";
    redirect('recipes.php');
}

// Parse ingredients and instructions
$ingredients = explode(',', $recipe['ingredients']);
$instructions = explode("\n", $recipe['instructions']);
?>

<!-- Recipe Detail Section -->
<section class="recipe-detail">
    <div class="container">
        <div class="recipe-header">
            <h1><?php echo $recipe['title']; ?></h1>
            
            <?php if (!empty($recipe['category'])): ?>
            <div style="margin: 1rem 0;">
                <?php foreach (explode(',', $recipe['category']) as $cat): ?>
                <span style="display: inline-block; background-color: var(--light-olive); color: var(--white); padding: 0.3rem 0.7rem; border-radius: 4px; font-size: 0.9rem; margin-right: 0.5rem; margin-bottom: 0.5rem;"><?php echo trim($cat); ?></span>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
            
            <div class="recipe-meta">
                <span><i class="far fa-clock"></i> Prep: <?php echo $recipe['prep_time']; ?> mins</span>
                <span><i class="fas fa-fire"></i> Cook: <?php echo $recipe['cook_time']; ?> mins</span>
                <span><i class="fas fa-utensils"></i> Servings: <?php echo $recipe['servings']; ?></span>
                <span class="recipe-difficulty difficulty-<?php echo $recipe['difficulty']; ?>">
                    <?php echo ucfirst($recipe['difficulty']); ?>
                </span>
                <?php if ($recipe['is_premium']): ?>
                <span class="status status-active" style="margin-left: 0.5rem; padding: 0.3rem 0.7rem; font-size: 0.9rem;">Premium</span>
                <?php endif; ?>
            </div>
            
            <?php if (isLoggedIn()): ?>
            <button class="favorite-btn large <?php echo isFavorite($_SESSION['user_id'], $recipe['recipe_id']) ? 'active' : ''; ?>" data-recipe-id="<?php echo $recipe['recipe_id']; ?>">
                <i class="<?php echo isFavorite($_SESSION['user_id'], $recipe['recipe_id']) ? 'fas' : 'far'; ?> fa-heart"></i>
                <span><?php echo isFavorite($_SESSION['user_id'], $recipe['recipe_id']) ? 'Favorited' : 'Add to Favorites'; ?></span>
            </button>
            <?php endif; ?>
        </div>
        
        <div class="recipe-content">
            <div class="recipe-image">
                <img src="<?php echo $recipe['image_path']; ?>" alt="<?php echo $recipe['title']; ?>">
            </div>
            
            <div class="recipe-description">
                <h2>Description</h2>
                <p><?php echo $recipe['description']; ?></p>
            </div>
            
            <div class="recipe-ingredients">
                <h2>Ingredients</h2>
                <ul>
                    <?php foreach ($ingredients as $ingredient): ?>
                        <li><?php echo trim($ingredient); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
            
            <div class="recipe-instructions">
                <h2>Instructions</h2>
                <ol>
                    <?php foreach ($instructions as $instruction): ?>
                        <?php if (trim($instruction) !== ''): ?>
                            <li><?php echo trim($instruction); ?></li>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </ol>
            </div>
        </div>
        
        <div class="recipe-actions">
            <a href="recipes.php" class="btn btn-outline">Back to Recipes</a>
            <button class="btn" onclick="window.print()">Print Recipe</button>
        </div>
    </div>
</section>

<?php
// Include footer
include 'includes/footer.php';
?>