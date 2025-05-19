<?php
// Include admin header
include '../includes/admin-header.php';

// Check if ID is provided
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    redirect('services.php');
}

$service_id = $_GET['id'];

// Get service details
$sql = "SELECT * FROM services WHERE service_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $service_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    redirect('services.php');
}

$service = $result->fetch_assoc();

// Handle form submission
$error = false;
$success = false;
$image_upload_error = false;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get form data
    $title = sanitize($_POST['title']);
    $description = sanitize($_POST['description']);
    $price = sanitize($_POST['price']);
    $duration = sanitize($_POST['duration']);
    $capacity = sanitize($_POST['capacity']);
    $is_active = isset($_POST['is_active']) ? 1 : 0;
    
    // Keep existing image path by default
    $image_path = $service['image_path'];
    
    // Handle image upload if a new image is provided
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        // Create directory if it doesn't exist
        $upload_dir = '../assets/images/services/';
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        
        $temp_name = $_FILES['image']['tmp_name'];
        $file_name = time() . '_' . $_FILES['image']['name'];
        $target_path = $upload_dir . $file_name;
        
        // Move uploaded file
        if (move_uploaded_file($temp_name, $target_path)) {
            $image_path = 'assets/images/services/' . $file_name;
        } else {
            $image_upload_error = true;
            $error = "Failed to upload image. Please check directory permissions.";
        }
    }
    
    // Validate form data
    if (empty($title) || empty($description) || empty($price) || empty($duration) || empty($capacity)) {
        $error = "Please fill in all required fields.";
    } elseif (!is_numeric($price) || !is_numeric($capacity)) {
        $error = "Price and capacity must be numbers.";
    } elseif ($image_upload_error) {
        // Error already set above
    } else {
        // Update service
        $sql = "UPDATE services SET title = ?, description = ?, price = ?, duration = ?, capacity = ?, image_path = ?, is_active = ? WHERE service_id = ?";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssdsisii", $title, $description, $price, $duration, $capacity, $image_path, $is_active, $service_id);
        
        if ($stmt->execute()) {
            $success = "Service updated successfully.";
            
            // Refresh service data
            $sql = "SELECT * FROM services WHERE service_id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $service_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $service = $result->fetch_assoc();
        } else {
            $error = "Failed to update service: " . $conn->error;
        }
    }
}
?>

<!-- Page Header -->
<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
    <h2>Edit Service</h2>
    <a href="services.php" class="btn btn-outline">Back to Services</a>
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

<!-- Edit Service Form -->
<div class="form-container">
    <form method="POST" enctype="multipart/form-data" class="admin-form">
        <div class="form-grid">
            <div class="form-group">
                <label for="title">Service Title</label>
                <input type="text" id="title" name="title" value="<?php echo $service['title']; ?>" required>
            </div>
            
            <div class="form-group">
                <label for="image">Service Image</label>
                <input type="file" id="image" name="image" class="image-upload" data-preview="image-preview" accept="image/*">
                <div style="margin-top: 0.5rem;">
                    <img id="image-preview" src="<?php echo '../' . $service['image_path']; ?>" alt="Preview" style="max-width: 100%; max-height: 200px; display: block;">
                </div>
                <p class="form-help">Leave empty to keep current image</p>
            </div>
            
            <div class="form-group full-width">
                <label for="description">Description</label>
                <textarea id="description" name="description" required><?php echo $service['description']; ?></textarea>
            </div>
            
            <div class="form-group">
                <label for="price">Price ($)</label>
                <input type="number" id="price" name="price" value="<?php echo $service['price']; ?>" min="0" step="0.01" required>
            </div>
            
            <div class="form-group">
                <label for="duration">Duration (e.g., "3 hours")</label>
                <input type="text" id="duration" name="duration" value="<?php echo $service['duration']; ?>" required>
            </div>
            
            <div class="form-group">
                <label for="capacity">Capacity (max people)</label>
                <input type="number" id="capacity" name="capacity" value="<?php echo $service['capacity']; ?>" min="1" required>
            </div>
            
            <div class="form-group">
                <label for="is_active">Status</label>
                <div style="margin-top: 0.5rem;">
                    <input type="checkbox" id="is_active" name="is_active" <?php echo $service['is_active'] ? 'checked' : ''; ?>>
                    <label for="is_active" style="display: inline; margin-left: 0.5rem;">Active</label>
                </div>
            </div>
        </div>
        
        <div class="form-actions">
            <button type="reset" class="btn btn-outline">Reset</button>
            <button type="submit" class="btn">Update Service</button>
        </div>
    </form>
</div>

<script>
// Image preview functionality
document.addEventListener('DOMContentLoaded', function() {
    const imageUpload = document.querySelector('.image-upload');
    const imagePreview = document.getElementById('image-preview');
    
    if (imageUpload && imagePreview) {
        imageUpload.addEventListener('change', function() {
            if (this.files && this.files[0]) {
                const reader = new FileReader();
                
                reader.onload = function(e) {
                    imagePreview.src = e.target.result;
                    imagePreview.style.display = 'block';
                };
                
                reader.readAsDataURL(this.files[0]);
            }
        });
    }
});
</script>

<?php
// Include admin footer
include '../includes/admin-footer.php';
?>