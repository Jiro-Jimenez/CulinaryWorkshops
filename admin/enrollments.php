<?php
// Include admin header
include '../includes/admin-header.php';

// Get all enrollments
$sql = "SELECT e.*, u.username, s.title FROM enrollments e 
        JOIN users u ON e.user_id = u.user_id 
        JOIN services s ON e.service_id = s.service_id 
        ORDER BY e.enrollment_date DESC";
$result = $conn->query($sql);

$enrollments = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $enrollments[] = $row;
    }
}

// Handle status update
if (isset($_POST['update_status']) && isset($_POST['enrollment_id']) && isset($_POST['status'])) {
    $enrollment_id = sanitize($_POST['enrollment_id']);
    $status = sanitize($_POST['status']);
    
    // Update enrollment status
    $update_sql = "UPDATE enrollments SET status = ? WHERE enrollment_id = ?";
    $update_stmt = $conn->prepare($update_sql);
    $update_stmt->bind_param("si", $status, $enrollment_id);
    
    if ($update_stmt->execute()) {
        // If status is confirmed or completed, ensure user is a client
        if ($status == 'confirmed' || $status == 'completed') {
            // Get user ID from enrollment
            $user_sql = "SELECT user_id FROM enrollments WHERE enrollment_id = ?";
            $user_stmt = $conn->prepare($user_sql);
            $user_stmt->bind_param("i", $enrollment_id);
            $user_stmt->execute();
            $user_result = $user_stmt->get_result();
            
            if ($user_result->num_rows > 0) {
                $user_id = $user_result->fetch_assoc()['user_id'];
                
                // Update user role to client if not already
                $role_sql = "UPDATE users SET role = 'client' WHERE user_id = ? AND role = 'guest'";
                $role_stmt = $conn->prepare($role_sql);
                $role_stmt->bind_param("i", $user_id);
                $role_stmt->execute();
            }
        }
        
        $success_message = "Enrollment status updated successfully.";
        
        // Refresh enrollments list
        $result = $conn->query($sql);
        $enrollments = [];
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $enrollments[] = $row;
            }
        }
    } else {
        $error_message = "Failed to update enrollment status.";
    }
}

// Handle delete enrollment
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $enrollment_id = $_GET['delete'];
    
    // Delete enrollment
    $delete_sql = "DELETE FROM enrollments WHERE enrollment_id = ?";
    $delete_stmt = $conn->prepare($delete_sql);
    $delete_stmt->bind_param("i", $enrollment_id);
    
    if ($delete_stmt->execute()) {
        $success_message = "Enrollment deleted successfully.";
        
        // Refresh enrollments list
        $result = $conn->query($sql);
        $enrollments = [];
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $enrollments[] = $row;
            }
        }
    } else {
        $error_message = "Failed to delete enrollment.";
    }
}
?>

<!-- Page Header -->
<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
    <h2>Enrollment Management</h2>
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

<!-- Enrollments Table -->
<div class="table-container">
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>User</th>
                <th>Service</th>
                <th>Date</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($enrollments)): ?>
                <tr>
                    <td colspan="6" style="text-align: center;">No enrollments found.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($enrollments as $enrollment): ?>
                    <tr>
                        <td><?php echo $enrollment['enrollment_id']; ?></td>
                        <td><?php echo $enrollment['username']; ?></td>
                        <td><?php echo $enrollment['title']; ?></td>
                        <td><?php echo date('M d, Y', strtotime($enrollment['enrollment_date'])); ?></td>
                        <td>
                            <form method="POST" style="display: flex; gap: 10px; align-items: center;">
                                <input type="hidden" name="enrollment_id" value="<?php echo $enrollment['enrollment_id']; ?>">
                                <select name="status" class="status-select" style="padding: 5px; border-radius: 4px;">
                                    <option value="pending" <?php echo ($enrollment['status'] == 'pending') ? 'selected' : ''; ?>>Pending</option>
                                    <option value="confirmed" <?php echo ($enrollment['status'] == 'confirmed') ? 'selected' : ''; ?>>Confirmed</option>
                                    <option value="completed" <?php echo ($enrollment['status'] == 'completed') ? 'selected' : ''; ?>>Completed</option>
                                    <option value="cancelled" <?php echo ($enrollment['status'] == 'cancelled') ? 'selected' : ''; ?>>Cancelled</option>
                                </select>
                                <button type="submit" name="update_status" class="btn btn-sm">Update</button>
                            </form>
                        </td>
                        <td>
                            <div class="action-buttons">
                                <a href="enrollments.php?delete=<?php echo $enrollment['enrollment_id']; ?>" class="btn btn-sm btn-delete" onclick="return confirm('Are you sure you want to delete this enrollment?')">Delete</a>
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