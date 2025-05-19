<?php
// Include admin header
include '../includes/admin-header.php';

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

// Handle form submission
$error = false;
$success = false;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get form data
    $title = sanitize($_POST['title']);
    $description = sanitize($_POST['description']);
    $ingredients = sanitize($_POST['ingredients']);
    $instructions = sanitize($_POST['instructions']);
    $prep_time = sanitize($_POST['prep_time']);
    $cook_time = sanitize($_POST['cook_time']);
    $servings = sanitize($_POST['servings']);
    $difficulty = sanitize($_POST['difficulty']);
    $is_premium = isset($_POST['is_premium']) ? 1 : 0;
    
    // Handle image upload
    $image_path = $recipe['image_path']; // Keep existing image by default
    
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $upload_dir = '../assets/images/recipes/';
        $temp_name = $_FILES['image']['tmp_name'];
        $file_name = time() . '_' . $_FILES['image']['name'];
        
        // Move uploaded file
        if (move_uploaded_file($temp_name, $upload_dir . $file_name)) {
            $image_path = 'assets/images/recipes/' . $file_name;
        } else {
            $error = "Failed to upload image.";
        }
    }
    
    // Validate form data
    if (empty($title) || empty($description) || empty($ingredients) || empty($instructions) || empty($prep_time) || empty($cook_time) || empty($servings) || empty($difficulty)) {
        $error = "Please fill in all required fields.";
    } elseif (!is_numeric($prep_time) || !is_numeric($cook_time) || !is_numeric($servings)) {
        $error = "Prep time, cook time, and servings must be numbers.";
    } else {
        // Update recipe
        if (updateRecipe($recipe_id, $title, $description, $ingredients, $instructions, $prep_time, $cook_time, $servings, $difficulty, $image_path, $is_premium)) {
            $success = "Recipe updated successfully.";
            
            // Refresh recipe data
            $recipe = getRecipeById($recipe_id);
        } else {
            $error = "Failed to update recipe.";
        }
    }
}
?>

<!-- Page Header -->
<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
    <h2>Edit Recipe</h2>
    <a href="recipes.php" class="btn btn-outline">Back to Recipes</a>
</div>

<?php if ($error): ?>
    <div class="alert alert-danger">
        <?php echo $error; ?>
    </div>
<?php endif; ?>

<?php if ($success): ?>
    <div class="alert alert-success">
        <?php echo $success; ?>
    </div>
<?php endif; ?>

<!-- Edit Recipe Form -->
<div class="form-container">
    <form method="POST" enctype="multipart/form-data" class="admin-form">
        <div class="form-grid">
            <div class="form-group">
                <label for="title">Recipe Title</label>
                <input type="text" id="title" name="title" value="<?php echo $recipe['title']; ?>" required>
            </div>
            
            <div class="form-group">
                <label for="image">Recipe Image</label>
                <input type="file" id="image" name="image" class="image-upload" data-preview="image-preview" accept="image/*">
                <div style="margin-top: 0.5rem;">
                    <img id="image-preview" src="<?php echo '../' . $recipe['image_path']; ?>" alt="Preview" style="max-width: 100%; max-height: 200px; display: block;">
                </div>
            </div>
            
            <div class="form-group full-width">
                <label for="description">Description</label>
                <textarea id="description" name="description" required><?php echo $recipe['description']; ?></textarea>
            </div>
            
            <div class="form-group">
                <label for="prep_time">Prep Time (minutes)</label>
                <input type="number" id="prep_time" name="prep_time" value="<?php echo $recipe['prep_time']; ?>" min="0" required>
            </div>
            
            <div class="form-group">
                <label for="cook_time">Cook Time (minutes)</label>
                <input type="number" id="cook_time" name="cook_time" value="<?php echo $recipe['cook_time']; ?>" min="0" required>
            </div>
            
            <div class="form-group">
                <label for="servings">Servings</label>
                <input type="number" id="servings" name="servings" value="<?php echo $recipe['servings']; ?>" min="1" required>
            </div>
            
            <div class="form-group">
                <label for="difficulty">Difficulty</label>
                <select id="difficulty" name="difficulty" required>
                    <option value="">Select Difficulty</option>
                    <option value="easy" <?php echo ($recipe['difficulty'] == 'easy') ? 'selected' : ''; ?>>Easy</option>
                    <option value="medium" <?php echo ($recipe['difficulty'] == 'medium') ? 'selected' : ''; ?>>Medium</option>
                    <option value="hard" <?php echo ($recipe['difficulty'] == 'hard') ? 'selected' : ''; ?>>Hard</option>
                </select>
            </div>
            
            <div class="form-group">
                <label for="is_premium">Premium Recipe</label>
                <div style="margin-top: 0.5rem;">
                    <input type="checkbox" id="is_premium" name="is_premium" <?php echo ($recipe['is_premium']) ? 'checked' : ''; ?>>
                    <label for="is_premium" style="display: inline; margin-left: 0.5rem;">Mark as premium (only available to clients)</label>
                </div>
            </div>
            
            <div class="form-group full-width">
                <label for="ingredients">Ingredients (comma separated)</label>
                <textarea id="ingredients" name="ingredients" required><?php echo $recipe['ingredients']; ?></textarea>
            </div>
            
            <div class="form-group full-width">
                <label for="instructions">Instructions (one step per line)</label>
                <textarea id="instructions" name="instructions" required><?php echo $recipe['instructions']; ?></textarea>
            </div>
        </div>
        
        <div class="form-actions">
            <a href="recipes.php" class="btn btn-outline">Cancel</a>
            <button type="submit" class="btn">Update Recipe</button>
        </div>
    </form>
</div>

<?php
// Include admin footer
include '../includes/admin-footer.php';
?>