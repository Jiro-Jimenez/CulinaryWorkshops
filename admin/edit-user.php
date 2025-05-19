<?php
// Include admin header
include '../includes/admin-header.php';

// Check if user ID is provided
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    redirect('accounts.php');
}

// Get user details
$user_id = $_GET['id'];
$user = getUserById($user_id);

// If user not found, redirect to accounts page
if (!$user) {
    redirect('accounts.php');
}

// Handle form submission
$error = false;
$success = false;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get form data
    $first_name = sanitize($_POST['first_name']);
    $last_name = sanitize($_POST['last_name']);
    $username = sanitize($_POST['username']);
    $email = sanitize($_POST['email']);
    $role = sanitize($_POST['role']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    
    // Validate form data
    if (empty($first_name) || empty($last_name) || empty($username) || empty($email) || empty($role)) {
        $error = "Please fill in all required fields.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Please enter a valid email address.";
    } else {
        // Check if username or email already exists (excluding current user)
        global $conn;
        $check_sql = "SELECT * FROM users WHERE (username = ? OR email = ?) AND user_id != ?";
        $check_stmt = $conn->prepare($check_sql);
        $check_stmt->bind_param("ssi", $username, $email, $user_id);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();
        
        if ($check_result->num_rows > 0) {
            $error = "Username or email already exists.";
        } else {
            // Update user
            if (updateUser($user_id, $username, $email, $first_name, $last_name, $role)) {
                $success = "User updated successfully.";
                
                // Update password if provided
                if (!empty($password)) {
                    if (strlen($password) < 6) {
                        $error = "Password must be at least 6 characters long.";
                    } elseif ($password !== $confirm_password) {
                        $error = "Passwords do not match.";
                    } else {
                        // Hash password
                        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                        
                        // Update password
                        $password_sql = "UPDATE users SET password = ? WHERE user_id = ?";
                        $password_stmt = $conn->prepare($password_sql);
                        $password_stmt->bind_param("si", $hashed_password, $user_id);
                        
                        if ($password_stmt->execute()) {
                            $success = "User updated successfully with new password.";
                        } else {
                            $error = "Failed to update password.";
                        }
                    }
                }
                
                // Refresh user data
                $user = getUserById($user_id);
            } else {
                $error = "Failed to update user.";
            }
        }
    }
}
?>

<!-- Page Header -->
<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
    <h2>Edit User</h2>
    <a href="accounts.php" class="btn btn-outline">Back to Accounts</a>
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

<!-- Edit User Form -->
<div class="form-container">
    <form method="POST" class="admin-form">
        <div class="form-grid">
            <div class="form-group">
                <label for="first_name">First Name</label>
                <input type="text" id="first_name" name="first_name" value="<?php echo $user['first_name']; ?>" required>
            </div>
            
            <div class="form-group">
                <label for="last_name">Last Name</label>
                <input type="text" id="last_name" name="last_name" value="<?php echo $user['last_name']; ?>" required>
            </div>
            
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" value="<?php echo $user['username']; ?>" required>
            </div>
            
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" value="<?php echo $user['email']; ?>" required>
            </div>
            
            <div class="form-group">
                <label for="password">Password (leave blank to keep current)</label>
                <input type="password" id="password" name="password">
            </div>
            
            <div class="form-group">
                <label for="confirm_password">Confirm Password</label>
                <input type="password" id="confirm_password" name="confirm_password">
            </div>
            
            <div class="form-group">
                <label for="role">Role</label>
                <select id="role" name="role" required>
                    <option value="">Select Role</option>
                    <option value="admin" <?php echo ($user['role'] == 'admin') ? 'selected' : ''; ?>>Admin</option>
                    <option value="client" <?php echo ($user['role'] == 'client') ? 'selected' : ''; ?>>Client</option>
                    <option value="guest" <?php echo ($user['role'] == 'guest') ? 'selected' : ''; ?>>Guest</option>
                </select>
            </div>
            
            <div class="form-group">
                <label>Account Created</label>
                <input type="text" value="<?php echo date('M d, Y', strtotime($user['created_at'])); ?>" readonly>
            </div>
        </div>
        
        <div class="form-actions">
            <a href="accounts.php" class="btn btn-outline">Cancel</a>
            <button type="submit" class="btn">Update User</button>
        </div>
    </form>
</div>

<?php
// Include admin footer
include '../includes/admin-footer.php';
?>