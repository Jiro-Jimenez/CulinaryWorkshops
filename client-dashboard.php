<?php
// Include header
include 'includes/header.php';

// Check if user is logged in
if (!isLoggedIn()) {
    redirect('login.php');
}

// Get user's favorite recipes
$user_id = $_SESSION['user_id'];
$favorites = getUserFavorites($user_id);

// Get user's enrollments
$sql = "SELECT e.*, s.title, s.image_path, s.description FROM enrollments e 
        JOIN services s ON e.service_id = s.service_id 
        WHERE e.user_id = ? ORDER BY e.enrollment_date DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$enrollments = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $enrollments[] = $row;
    }
}

// Check if user has premium access
$has_premium_access = hasPremiumAccess($user_id);

// Determine which tab to show (default to favorites)
$active_tab = 'favorites';
if (isset($_GET['tab'])) {
    $active_tab = sanitize($_GET['tab']);
}
?>

<!-- Hero Section -->
<section class="hero">
    <div class="container">
        <h1>Welcome, <?php echo $_SESSION['first_name']; ?>!</h1>
        <p>Manage your favorite recipes and enrolled services.</p>
    </div>
</section>

<!-- Client Dashboard Tabs -->
<section class="recipes">
    <div class="container">
        <div style="display: flex; justify-content: space-between; margin-bottom: 2rem;">
            <div>
                <a href="?tab=favorites" id="favorites-tab" class="btn <?php echo ($active_tab == 'favorites') ? '' : 'btn-outline'; ?>">My Favorites</a>
                <a href="?tab=enrollments" id="enrollments-tab" class="btn <?php echo ($active_tab == 'enrollments') ? '' : 'btn-outline'; ?>">My Enrollments</a>
                <?php if ($has_premium_access): ?>
                <a href="?tab=all-recipes" id="all-recipes-tab" class="btn <?php echo ($active_tab == 'all-recipes') ? '' : 'btn-outline'; ?>">All Recipes</a>
                <?php endif; ?>
            </div>
            <div>
                <?php if ($has_premium_access): ?>
                <span class="status status-active" style="padding: 0.5rem 1rem;">Premium Access</span>
                <?php else: ?>
                <span class="status status-inactive" style="padding: 0.5rem 1rem;">Standard Access</span>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Favorites Tab -->
        <div id="favorites" class="dashboard-tab" style="display: <?php echo ($active_tab == 'favorites') ? 'block' : 'none'; ?>">
            <h2>My Favorite Recipes</h2>
            


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



            <?php if (empty($favorites)): ?>
                <div style="text-align: center; padding: 3rem 0;">
                    <h3>No Favorites Yet</h3>
                    <p>You haven't added any recipes to your favorites yet.</p>
                    <a href="recipes.php" class="btn" style="margin-top: 1rem;">Browse Recipes</a>
                </div>
            <?php else: ?>
                <div class="recipe-grid">
                    <?php foreach ($favorites as $recipe): ?>
                        <div class="recipe-card" style="transition: opacity 0.3s ease;">
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
        
        <!-- Enrollments Tab -->
        <div id="enrollments" class="dashboard-tab" style="display: <?php echo ($active_tab == 'enrollments') ? 'block' : 'none'; ?>">
            <h2>My Enrollments</h2>
            
            <?php if (empty($enrollments)): ?>
                <div style="text-align: center; padding: 3rem 0;">
                    <h3>No Enrollments Yet</h3>
                    <p>You haven't enrolled in any services yet.</p>
                    <a href="services.php" class="btn" style="margin-top: 1rem;">Browse Services</a>
                </div>
            <?php else: ?>
                <div class="services-grid">
                    <?php foreach ($enrollments as $enrollment): ?>
                        <div class="service-card">
                            <div class="service-img">
                                <img src="<?php echo $enrollment['image_path']; ?>" alt="<?php echo $enrollment['title']; ?>">
                            </div>
                            <div class="service-content">
                                <h3><?php echo $enrollment['title']; ?></h3>
                                <p><?php echo substr($enrollment['description'], 0, 100) . '...'; ?></p>
                                <div class="service-meta">
                                    <span><i class="far fa-calendar"></i> <?php echo date('M d, Y', strtotime($enrollment['enrollment_date'])); ?></span>
                                </div>
                                <div class="service-meta">
                                    <span>
                                        <i class="fas fa-info-circle"></i> Status: 
                                        <span class="status status-<?php echo strtolower($enrollment['status']); ?>" style="padding: 0.2rem 0.5rem; font-size: 0.8rem;">
                                            <?php echo ucfirst($enrollment['status']); ?>
                                        </span>
                                    </span>
                                </div>
                                <a href="service-detail.php?id=<?php echo $enrollment['service_id']; ?>" class="btn btn-outline">View Service</a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>




        <!-- All Recipes Tab (Premium Only) -->
        <?php if ($has_premium_access): ?>
        <div id="all-recipes" class="dashboard-tab" style="display: <?php echo ($active_tab == 'all-recipes') ? 'block' : 'none'; ?>">
            <h2>All Recipes</h2>
            



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




            <?php
            // Get all recipes
            $all_recipes = getAllRecipes();
            
            // Get categories
            $categories = [];
            foreach ($all_recipes as $recipe) {
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
            
            <!-- Category Filter -->
            <?php if (!empty($categories)): ?>
            <div style="margin-bottom: 2rem;">
                <h3>Filter by Category</h3>
                <div style="display: flex; flex-wrap: wrap; gap: 0.5rem; margin-top: 1rem;">
                    <button class="btn btn-sm category-filter active" data-category="all">All</button>
                    <?php foreach ($categories as $category): ?>
                    <button class="btn btn-sm btn-outline category-filter" data-category="<?php echo strtolower(str_replace(' ', '-', $category)); ?>"><?php echo $category; ?></button>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>
            
            <div class="recipe-grid">
                <?php foreach ($all_recipes as $recipe): ?>
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
                        <button class="favorite-btn <?php echo isFavorite($_SESSION['user_id'], $recipe['recipe_id']) ? 'active' : ''; ?>" data-recipe-id="<?php echo $recipe['recipe_id']; ?>">
                            <i class="<?php echo isFavorite($_SESSION['user_id'], $recipe['recipe_id']) ? 'fas' : 'far'; ?> fa-heart"></i>
                        </button>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>
</section>

<script>
// Handle tab changes via URL
document.addEventListener('DOMContentLoaded', function() {
    const tabLinks = document.querySelectorAll('.container > div:first-child > a');
    tabLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const tabId = this.getAttribute('href').substring(5); // Remove '?tab='
            window.location.href = 'client-dashboard.php?tab=' + tabId;
        });
    });
});
</script>

<?php
// Include footer
include 'includes/footer.php';
?>