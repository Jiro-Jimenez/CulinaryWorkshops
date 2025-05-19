<?php
// Include header
include 'includes/header.php';

// Check if user is already logged in
if (isLoggedIn()) {
    redirectBasedOnRole();
}

// Handle form submission
$error = false;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get form data
    $username = sanitize($_POST['username']);
    $password = $_POST['password'];
    
    // Validate form data
    if (empty($username) || empty($password)) {
        $error = "Please enter both username and password.";
    } else {
        // Attempt to login
        if (loginUser($username, $password)) {
            // Redirect based on role
            redirectBasedOnRole();
        } else {
            $error = "Invalid username or password.";
        }
    }
};
?>

<!-- Auth Section -->
<section class="auth-container">
    <h2>Login</h2>
    
    <?php if ($error): ?>
        <div class="alert alert-danger">
            <?php echo $error; ?>
        </div>
    <?php endif; ?>
    
    <form id="login-form" method="POST" class="auth-form">
        <div class="form-group">
            <label for="username">Username</label>
            <input type="text" id="username" name="username" required>
        </div>
        
        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" required>
        </div>
        
        <button type="submit" class="btn" style="width: 100%;">Login</button>
    </form>
    
    <div class="auth-links">
        <p>Don't have an account? <a href="register.php">Register</a></p>
    </div>
</section>

<?php
// Include footer
include 'includes/footer.php';
?>