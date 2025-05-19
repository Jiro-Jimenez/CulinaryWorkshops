<?php
// Include admin header
include '../includes/admin-header.php';

// Get all services
$services = getAllServices();

// Handle delete service
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $service_id = $_GET['delete'];
    
    // Delete service
    $delete_sql = "DELETE FROM services WHERE service_id = ?";
    $delete_stmt = $conn->prepare($delete_sql);
    $delete_stmt->bind_param("i", $service_id);
    
    if ($delete_stmt->execute()) {
        $success_message = "Service deleted successfully.";
        // Refresh services list
        $services = getAllServices();
    } else {
        $error_message = "Failed to delete service.";
    }
}
?>

<!-- Page Header -->
<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
    <h2>Service Management</h2>
    <a href="add-service.php" class="btn">Add New Service</a>
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

<!-- Services Table -->
<div class="table-container">
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Image</th>
                <th>Title</th>
                <th>Price</th>
                <th>Duration</th>
                <th>Capacity</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($services)): ?>
                <tr>
                    <td colspan="8" style="text-align: center;">No services found.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($services as $service): ?>
                    <tr>
                        <td><?php echo $service['service_id']; ?></td>
                        <td>
                            <img src="<?php echo '../' . $service['image_path']; ?>" alt="<?php echo $service['title']; ?>" style="width: 50px; height: 50px; object-fit: cover; border-radius: 4px;">
                        </td>
                        <td><?php echo $service['title']; ?></td>
                        <td>$<?php echo number_format($service['price'], 2); ?></td>
                        <td><?php echo $service['duration']; ?></td>
                        <td><?php echo $service['capacity']; ?></td>
                        <td>
                            <span class="status <?php echo $service['is_active'] ? 'status-active' : 'status-inactive'; ?>">
                                <?php echo $service['is_active'] ? 'Active' : 'Inactive'; ?>
                            </span>
                        </td>
                        <td>
                            <div class="action-buttons">
                                <a href="../service-detail.php?id=<?php echo $service['service_id']; ?>" class="btn btn-sm btn-view" target="_blank">View</a>
                                <a href="edit-service.php?id=<?php echo $service['service_id']; ?>" class="btn btn-sm btn-edit">Edit</a>
                                <a href="services.php?delete=<?php echo $service['service_id']; ?>" class="btn btn-sm btn-delete" onclick="return confirm('Are you sure you want to delete this service?')">Delete</a>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php
// Include admin footer
include '../includes/admin-footer.php';
?>