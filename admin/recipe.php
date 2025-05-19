<?php
// Include admin header
include '../includes/admin-header.php';

// Get all recipes
$recipes = getAllRecipes();

// Handle delete recipe
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $recipe_id = $_GET['delete'];
    
    // Delete recipe
    $delete_sql = "DELETE FROM recipes WHERE recipe_id = ?";
    $delete_stmt = $conn->prepare($delete_sql);
    $delete_stmt->bind_param("i", $recipe_id);
    
    if ($delete_stmt->execute()) {
        $success_message = "Recipe deleted successfully.";
        // Refresh recipes list
        $recipes = getAllRecipes();
    } else {
        $error_message = "Failed to delete recipe.";
    }
}
?>

<!-- Page Header -->
<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
    <h2>Recipe Management</h2>
    <a href="add-recipe.php" class="btn">Add New Recipe</a>
</div>

<?php if (isset($success_message)): ?>
    <div class="alert alert-success">
        <?php echo $success_message; ?>
    </div>
<?php endif; ?>

<?php if (isset($error_message)): ?>
    <div class="alert alert-danger">
        <?php echo $error_message; ?>
    </div>
<?php endif; ?>

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

<!-- Recipes Table -->
<div class="table-container">
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Image</th>
                <th>Title</th>
                <th>Difficulty</th>
                <th>Premium</th>
                <th>Created</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody id="recipes-tbody">
            <?php if (empty($recipes)): ?>
                <tr>
                    <td colspan="7" style="text-align: center;">No recipes found.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($recipes as $recipe): ?>
                    <tr data-title="<?php echo strtolower($recipe['title']); ?>" data-date="<?php echo $recipe['created_at']; ?>" data-difficulty="<?php echo $recipe['difficulty']; ?>">
                        <td><?php echo $recipe['recipe_id']; ?></td>
                        <td>
                            <img src="<?php echo '../' . $recipe['image_path']; ?>" alt="<?php echo $recipe['title']; ?>" style="width: 50px; height: 50px; object-fit: cover; border-radius: 4px;">
                        </td>
                        <td><?php echo $recipe['title']; ?></td>
                        <td>
                            <span class="recipe-difficulty difficulty-<?php echo $recipe['difficulty']; ?>">
                                <?php echo ucfirst($recipe['difficulty']); ?>
                            </span>
                        </td>
                        <td>
                            <span class="status <?php echo $recipe['is_premium'] ? 'status-active' : 'status-inactive'; ?>">
                                <?php echo $recipe['is_premium'] ? 'Premium' : 'Free'; ?>
                            </span>
                        </td>
                        <td><?php echo date('M d, Y', strtotime($recipe['created_at'])); ?></td>
                        <td>
                            <div class="action-buttons">
                                <a href="../recipe-detail.php?id=<?php echo $recipe['recipe_id']; ?>" class="btn btn-sm btn-view" target="_blank">View</a>
                                <a href="edit-recipe.php?id=<?php echo $recipe['recipe_id']; ?>" class="btn btn-sm btn-edit">Edit</a>
                                <a href="recipes.php?delete=<?php echo $recipe['recipe_id']; ?>" class="btn btn-sm btn-delete" onclick="return confirm('Are you sure you want to delete this recipe?')">Delete</a>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Search functionality
    const searchInput = document.getElementById('recipe-search');
    const sortSelect = document.getElementById('sort-by');
    const tbody = document.getElementById('recipes-tbody');
    const rows = Array.from(tbody.querySelectorAll('tr'));
    
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            
            let hasResults = false;
            rows.forEach(row => {
                const title = row.querySelector('td:nth-child(3)').textContent.toLowerCase();
                
                if (title.includes(searchTerm)) {
                    row.style.display = '';
                    hasResults = true;
                } else {
                    row.style.display = 'none';
                }
            });
            
            // Show no results message
            if (!hasResults && rows.length > 0) {
                const noResultsRow = document.getElementById('no-results-row');
                if (!noResultsRow) {
                    const newRow = document.createElement('tr');
                    newRow.id = 'no-results-row';
                    newRow.innerHTML = '<td colspan="7" style="text-align: center;">No recipes found matching your search.</td>';
                    tbody.appendChild(newRow);
                }
            } else {
                const noResultsRow = document.getElementById('no-results-row');
                if (noResultsRow) {
                    noResultsRow.remove();
                }
            }
        });
    }
    
    // Sort functionality
    if (sortSelect) {
        sortSelect.addEventListener('change', function() {
            const sortValue = this.value;
            
            rows.sort((a, b) => {
                if (sortValue === 'newest' || sortValue === 'oldest') {
                    const dateA = new Date(a.dataset.date || 0);
                    const dateB = new Date(b.dataset.date || 0);
                    return sortValue === 'newest' ? dateB - dateA : dateA - dateB;
                } 
                else if (sortValue === 'a-z' || sortValue === 'z-a') {
                    const titleA = a.dataset.title;
                    const titleB = b.dataset.title;
                    return sortValue === 'a-z' 
                        ? titleA.localeCompare(titleB) 
                        : titleB.localeCompare(titleA);
                }
                else if (sortValue === 'difficulty') {
                    const diffMap = { 'easy': 1, 'medium': 2, 'hard': 3 };
                    const diffA = diffMap[a.dataset.difficulty] || 0;
                    const diffB = diffMap[b.dataset.difficulty] || 0;
                    return diffA - diffB;
                }
                return 0;
            });
            
            // Reappend sorted rows
            rows.forEach(row => {
                tbody.appendChild(row);
            });
        });
    }
});
</script>

<?php
// Include admin footer
include '../includes/admin-footer.php';
?>