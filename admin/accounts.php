<?php
// Include admin header
include '../includes/admin-header.php';

// Get all users
$users = getAllUsers();

// Handle delete user
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $user_id = $_GET['delete'];
    
    // Don't allow deleting self
    if ($user_id == $_SESSION['user_id']) {
        $error_message = "You cannot delete your own account.";
    } else {
        // Delete user
        $delete_sql = "DELETE FROM users WHERE user_id = ?";
        $delete_stmt = $conn->prepare($delete_sql);
        $delete_stmt->bind_param("i", $user_id);
        
        if ($delete_stmt->execute()) {
            $success_message = "User deleted successfully.";
            // Refresh users list
            $users = getAllUsers();
        } else {
            $error_message = "Failed to delete user.";
        }
    }
}
?>

<!-- Page Header -->
<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
    <h2>Account Management</h2>
    <a href="add-user.php" class="btn">Add New User</a>
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

<!-- Users Table -->
<div class="table-container">
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Username</th>
                <th>Name</th>
                <th>Email</th>
                <th>Role</th>
                <th>Created</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($users)): ?>
                <tr>
                    <td colspan="7" style="text-align: center;">No users found.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($users as $user): ?>
                    <tr>
                        <td><?php echo $user['user_id']; ?></td>
                        <td><?php echo $user['username']; ?></td>
                        <td><?php echo $user['first_name'] . ' ' . $user['last_name']; ?></td>
                        <td><?php echo $user['email']; ?></td>
                        <td>
                            <span class="status <?php echo ($user['role'] == 'admin') ? 'status-active' : (($user['role'] == 'client') ? 'status-pending' : 'status-inactive'); ?>">
                                <?php echo ucfirst($user['role']); ?>
                            </span>
                        </td>
                        <td><?php echo date('M d, Y', strtotime($user['created_at'])); ?></td>
                        <td>
                            <div class="action-buttons">
                                <a href="edit-user.php?id=<?php echo $user['user_id']; ?>" class="btn btn-sm btn-edit">Edit</a>
                                <?php if ($user['user_id'] != $_SESSION['user_id']): ?>
                                    <a href="accounts.php?delete=<?php echo $user['user_id']; ?>" class="btn btn-sm btn-delete" onclick="return confirm('Are you sure you want to delete this user?')">Delete</a>
                                <?php endif; ?>
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