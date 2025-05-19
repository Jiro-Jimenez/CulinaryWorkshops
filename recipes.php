<?php
// Include header
include 'includes/header.php';

// Get all recipes
$sql = "SELECT * FROM recipes";

// If user is not logged in or doesn't have premium access, only show non-premium recipes
if (!isLoggedIn() || (!hasPremiumAccess($_SESSION['user_id']) && !isAdmin())) {
    $sql .= " WHERE is_premium = 0";
}

$sql .= " ORDER BY created_at DESC";
$result = $conn->query($sql);

$recipes = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $recipes[] = $row;
    }
}

// Get categories
$categories = [];
foreach ($recipes as $recipe) {
    if (!empty($recipe['category'])) {
        $recipe_categories = explode(',', $recipe['category']);
        foreach ($recipe_categories as $cat) {
            $cat = trim($cat);
            if (!empty($cat) && !in_array($cat, $categories)) {
                $categories[] = $cat;
            }
        }
    }
}
sort($categories);
?>

<!-- Hero Section -->
<section class="hero">
    <div class="container">
        <h1>Our Recipes</h1>
        <p>Discover our collection of delicious recipes for every occasion.</p>
        <?php if (!isLoggedIn()): ?>
            <p><a href="login.php" class="btn">Login</a> to access premium recipes!</p>
        <?php elseif (!hasPremiumAccess($_SESSION['user_id']) && !isAdmin()): ?>
            <p>Enroll in our services to unlock premium recipes!</p>
        <?php endif; ?>
    </div>
</section>


<!-- Recipes Section -->
<section class="recipes">
    <div class="container">
        <!-- Category Filter -->
        <?php if (!empty($categories)): ?>
        <div style="margin-bottom: 2rem;">
            <h2>Filter by Category</h2>




<!-- Search and Filter -->
<div class="search-container" style="margin-bottom: 1.5rem;">
    <div class="search-box">
        <input type="text" id="recipe-search" placeholder="Search recipes...">
        <button type="button" id="search-btn">
            <i class="fas fa-search"></i>
        </button>
    </div>
    <div class="sort-options">
        <label for="sort-by">Sort by:</label>
        <select id="sort-by">
            <option value="newest">Newest First</option>
            <option value="oldest">Oldest First</option>
            <option value="a-z">A-Z</option>
            <option value="z-a">Z-A</option>
            <option value="difficulty">Difficulty</option>
        </select>
    </div>
</div>



            <div style="display: flex; flex-wrap: wrap; gap: 0.5rem; margin-top: 1rem;">
                <button class="btn btn-sm category-filter active" data-category="all">All</button>
                <?php foreach ($categories as $category): ?>
                <button class="btn btn-sm btn-outline category-filter" data-category="<?php echo strtolower(str_replace(' ', '-', $category)); ?>"><?php echo $category; ?></button>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
        
        <div class="recipe-grid">
            <?php if (empty($recipes)): ?>
                <div style="text-align: center; padding: 3rem 0; grid-column: 1 / -1;">
                    <h3>No Recipes Found</h3>
                    <p>Check back later for new recipes!</p>
                </div>
            <?php else: ?>
                <?php foreach ($recipes as $recipe): ?>
                    <?php
                    $recipe_categories = !empty($recipe['category']) ? explode(',', $recipe['category']) : [];
                    $category_classes = '';
                    foreach ($recipe_categories as $cat) {
                        $cat = trim($cat);
                        if (!empty($cat)) {
                            $category_classes .= ' category-' . strtolower(str_replace(' ', '-', $cat));
                        }
                    }
                    ?>
                    <div class="recipe-card<?php echo $category_classes; ?>">
                        <div class="recipe-img">
                            <img src="<?php echo $recipe['image_path']; ?>" alt="<?php echo $recipe['title']; ?>">
                        </div>
                        <div class="recipe-content">
                            <h3><?php echo $recipe['title']; ?></h3>
                            <?php if (!empty($recipe['category'])): ?>
                            <div style="margin-bottom: 0.5rem;">
                                <?php foreach (explode(',', $recipe['category']) as $cat): ?>
                                <span style="display: inline-block; background-color: var(--light-olive); color: var(--white); padding: 0.2rem 0.5rem; border-radius: 4px; font-size: 0.8rem; margin-right: 0.3rem; margin-bottom: 0.3rem;"><?php echo trim($cat); ?></span>
                                <?php endforeach; ?>
                            </div>
                            <?php endif; ?>
                            <p><?php echo substr($recipe['description'], 0, 100) . '...'; ?></p>
                            <div class="recipe-meta">
                                <span><i class="far fa-clock"></i> <?php echo $recipe['prep_time'] + $recipe['cook_time']; ?> mins</span>
                                <span><i class="fas fa-utensils"></i> <?php echo $recipe['servings']; ?> servings</span>
                            </div>
                            <span class="recipe-difficulty difficulty-<?php echo $recipe['difficulty']; ?>">
                                <?php echo ucfirst($recipe['difficulty']); ?>
                            </span>
                            <?php if ($recipe['is_premium']): ?>
                            <span class="status status-active" style="margin-left: 0.5rem; padding: 0.2rem 0.5rem; font-size: 0.8rem;">Premium</span>
                            <?php endif; ?>
                            <a href="recipe-detail.php?id=<?php echo $recipe['recipe_id']; ?>" class="btn btn-outline">View Recipe</a>
                        </div>
                        <?php if (isLoggedIn()): ?>
                        <button class="favorite-btn <?php echo isFavorite($_SESSION['user_id'], $recipe['recipe_id']) ? 'active' : ''; ?>" data-recipe-id="<?php echo $recipe['recipe_id']; ?>">
                            <i class="<?php echo isFavorite($_SESSION['user_id'], $recipe['recipe_id']) ? 'fas' : 'far'; ?> fa-heart"></i>
                        </button>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</section>

<script>
// Category filter functionality
document.addEventListener('DOMContentLoaded', function() {
    const categoryButtons = document.querySelectorAll('.category-filter');
    
    categoryButtons.forEach(button => {
        button.addEventListener('click', function() {
            const category = this.getAttribute('data-category');
            
            // Update active button
            categoryButtons.forEach(btn => {
                btn.classList.remove('active');
                btn.classList.add('btn-outline');
            });
            this.classList.add('active');
            this.classList.remove('btn-outline');
            
            // Filter recipes
            const recipes = document.querySelectorAll('.recipe-card');
            
            if (category === 'all') {
                recipes.forEach(recipe => {
                    recipe.style.display = 'block';
                });
            } else {
                recipes.forEach(recipe => {
                    if (recipe.classList.contains('category-' + category)) {
                        recipe.style.display = 'block';
                    } else {
                        recipe.style.display = 'none';
                    }
                });
            }
        });
    });
});
</script>

<?php
// Include footer
include 'includes/footer.php';
?>