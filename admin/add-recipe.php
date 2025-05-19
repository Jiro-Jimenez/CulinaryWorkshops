<?php
// Include admin header
include '../includes/admin-header.php';

// Handle form submission
$error = false;
$success = false;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get form data
    $title = sanitize($_POST['title']);
    $category = sanitize($_POST['category']);
    $description = sanitize($_POST['description']);
    $ingredients = sanitize($_POST['ingredients']);
    $instructions = sanitize($_POST['instructions']);
    $prep_time = sanitize($_POST['prep_time']);
    $cook_time = sanitize($_POST['cook_time']);
    $servings = sanitize($_POST['servings']);
    $difficulty = sanitize($_POST['difficulty']);
    $is_premium = isset($_POST['is_premium']) ? 1 : 0;
    
    // Handle image upload
    $image_path = 'assets/images/recipes/default.jpg'; // Default image
    
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        // Create directory if it doesn't exist
        $upload_dir = '../assets/images/recipes/';
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        
        $temp_name = $_FILES['image']['tmp_name'];
        $file_name = time() . '_' . $_FILES['image']['name'];
        $target_path = $upload_dir . $file_name;
        
        // Move uploaded file
        if (move_uploaded_file($temp_name, $target_path)) {
            $image_path = 'assets/images/recipes/' . $file_name;
        } else {
            $error = "Failed to upload image. Error: " . error_get_last()['message'];
        }
    }
    
    // Validate form data
    if (empty($title) || empty($description) || empty($ingredients) || empty($instructions) || empty($prep_time) || empty($cook_time) || empty($servings) || empty($difficulty)) {
        $error = "Please fill in all required fields.";
    } elseif (!is_numeric($prep_time) || !is_numeric($cook_time) || !is_numeric($servings)) {
        $error = "Prep time, cook time, and servings must be numbers.";
    } else {
        // Add recipe with category
        $sql = "INSERT INTO recipes (title, category, description, ingredients, instructions, prep_time, cook_time, servings, difficulty, image_path, is_premium) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssssiiissi", $title, $category, $description, $ingredients, $instructions, $prep_time, $cook_time, $servings, $difficulty, $image_path, $is_premium);
        
        if ($stmt->execute()) {
            $success = "Recipe added successfully.";
            
            // Clear form data
            $title = $category = $description = $ingredients = $instructions = $prep_time = $cook_time = $servings = $difficulty = '';
            $is_premium = 0;
        } else {
            $error = "Failed to add recipe: " . $conn->error;
        }
    }
}
?>

<!-- Page Header -->
<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
    <h2>Add New Recipe</h2>
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

<!-- Add Recipe Form -->
<div class="form-container">
    <form method="POST" enctype="multipart/form-data" class="admin-form">
        <div class="form-grid">
            <div class="form-group">
                <label for="title">Recipe Title</label>
                <input type="text" id="title" name="title" value="<?php echo isset($title) ? $title : ''; ?>" required>
            </div>
            
            <div class="form-group">
                <label for="category">Category (comma separated)</label>
                <input type="text" id="category" name="category" value="<?php echo isset($category) ? $category : ''; ?>" placeholder="e.g. Italian, Pasta, Dinner">
            </div>
            
            <div class="form-group">
                <label for="image">Recipe Image</label>
                <input type="file" id="image" name="image" class="image-upload" data-preview="image-preview" accept="image/*">
                <div style="margin-top: 0.5rem;">
                    <img id="image-preview" src="#" alt="Preview" style="max-width: 100%; max-height: 200px; display: none;">
                </div>
            </div>
            
            <div class="form-group full-width">
                <label for="description">Description</label>
                <textarea id="description" name="description" required><?php echo isset($description) ? $description : ''; ?></textarea>
            </div>
            
            <div class="form-group">
                <label for="prep_time">Prep Time (minutes)</label>
                <input type="number" id="prep_time" name="prep_time" value="<?php echo isset($prep_time) ? $prep_time : ''; ?>" min="0" required>
            </div>
            
            <div class="form-group">
                <label for="cook_time">Cook Time (minutes)</label>
                <input type="number" id="cook_time" name="cook_time" value="<?php echo isset($cook_time) ? $cook_time : ''; ?>" min="0" required>
            </div>
            
            <div class="form-group">
                <label for="servings">Servings</label>
                <input type="number" id="servings" name="servings" value="<?php echo isset($servings) ? $servings : ''; ?>" min="1" required>
            </div>
            
            <div class="form-group">
                <label for="difficulty">Difficulty</label>
                <select id="difficulty" name="difficulty" required>
                    <option value="">Select Difficulty</option>
                    <option value="easy" <?php echo (isset($difficulty) && $difficulty == 'easy') ? 'selected' : ''; ?>>Easy</option>
                    <option value="medium" <?php echo (isset($difficulty) && $difficulty == 'medium') ? 'selected' : ''; ?>>Medium</option>
                    <option value="hard" <?php echo (isset($difficulty) && $difficulty == 'hard') ? 'selected' : ''; ?>>Hard</option>
                </select>
            </div>
            
            <div class="form-group">
                <label for="is_premium">Premium Recipe</label>
                <div style="margin-top: 0.5rem;">
                    <input type="checkbox" id="is_premium" name="is_premium" <?php echo (isset($is_premium) && $is_premium) ? 'checked' : ''; ?>>
                    <label for="is_premium" style="display: inline; margin-left: 0.5rem;">Mark as premium (only available to clients)</label>
                </div>
            </div>
            
            <div class="form-group full-width">
                <label for="ingredients">Ingredients (comma separated)</label>
                <textarea id="ingredients" name="ingredients" required><?php echo isset($ingredients) ? $ingredients : ''; ?></textarea>
            </div>
            
            <div class="form-group full-width">
                <label for="instructions">Instructions (one step per line)</label>
                <textarea id="instructions" name="instructions" required><?php echo isset($instructions) ? $instructions : ''; ?></textarea>
            </div>
        </div>
        
        <div class="form-actions">
            <button type="reset" class="btn btn-outline">Reset</button>
            <button type="submit" class="btn">Add Recipe</button>
        </div>
    </form>
</div>

<?php
// Include admin footer
include '../includes/admin-footer.php';
?>