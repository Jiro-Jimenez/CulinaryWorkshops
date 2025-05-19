<?php
// Include config file
require_once 'config.php';
require_once 'functions.php';

// Check if user is logged in
if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    exit;
}

// Handle favorite toggle
if (isset($_POST['action']) && $_POST['action'] == 'toggle_favorite' && isset($_POST['recipe_id'])) {
    $user_id = $_SESSION['user_id'];
    $recipe_id = sanitize($_POST['recipe_id']);
    
    // Check if recipe exists
    $recipe = getRecipeById($recipe_id);
    if (!$recipe) {
        echo json_encode(['success' => false, 'message' => 'Recipe not found']);
        exit;
    }
    
    // Check if already favorited
    $is_favorite = isFavorite($user_id, $recipe_id);
    
    if ($is_favorite) {
        // Remove from favorites
        $sql = "DELETE FROM favorites WHERE user_id = ? AND recipe_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ii", $user_id, $recipe_id);
        
        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'is_favorite' => false, 'message' => 'Removed from favorites']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to remove from favorites']);
        }
    } else {
        // Add to favorites
        $sql = "INSERT INTO favorites (user_id, recipe_id) VALUES (?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ii", $user_id, $recipe_id);
        
        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'is_favorite' => true, 'message' => 'Added to favorites']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to add to favorites']);
        }
    }
    
    exit;
}

// If no valid action is provided
echo json_encode(['success' => false, 'message' => 'Invalid request']);