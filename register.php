<?php
// Include header
include 'includes/header.php';

// Check if user is already logged in
if (isLoggedIn()) {
    redirect('index.php');
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
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    
    // Validate form data
    if (empty($first_name) || empty($last_name) || empty($username) || empty($email) || empty($password) || empty($confirm_password)) {
        $error = "Please fill in all fields.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Please enter a valid email address.";
    } elseif (strlen($password) < 6) {
        $error = "Password must be at least 6 characters long.";
    } elseif ($password !== $confirm_password) {
        $error = "Passwords do not match.";
    } else {
        // Check if username or email already exists
        global $conn;
        $check_sql = "SELECT * FROM users WHERE username = ? OR email = ?";
        $check_stmt = $conn->prepare($check_sql);
        $check_stmt->bind_param("ss", $username, $email);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();
        
        if ($check_result->num_rows > 0) {
            $error = "Username or email already exists.";
        } else {
            // Register user
            $user_id = registerUser($username, $email, $password, $first_name, $last_name);
            
            if ($user_id) {
                // Auto login
                loginUser($username, $password);
                
                // Redirect to home page
                redirect('index.php');
            } else {
                $error = "Registration failed. Please try again.";
            }
        }
    }
}
?>

<!-- Auth Section -->
<section class="auth-container">
    <h2>Register</h2>
    
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
    
    <form id="register-form" method="POST" class="auth-form">
        <div class="form-group">
            <label for="first_name">First Name</label>
            <input type="text" id="first_name" name="first_name" required>
        </div>
        
        <div class="form-group">
            <label for="last_name">Last Name</label>
            <input type="text" id="last_name" name="last_name" required>
        </div>
        
        <div class="form-group">
            <label for="username">Username</label>
            <input type="text" id="username" name="username" required>
        </div>
        
        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" required>
        </div>
        
        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" required>
        </div>
        
        <div class="form-group">
            <label for="confirm_password">Confirm Password</label>
            <input type="password" id="confirm_password" name="confirm_password" required>
        </div>
        
        <button type="submit" class="btn" style="width: 100%;">Register</button>
    </form>
    
    <div class="auth-links">
        <p>Already have an account? <a href="login.php">Login</a></p>
    </div>
</section>

<?php
// Include footer
include 'includes/footer.php';
?>