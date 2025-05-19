<?php
// Include header
include 'includes/header.php';

// Check if service ID is provided
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    redirect('services.php');
}

// Get service details
$service_id = $_GET['id'];
$service = getServiceById($service_id);

// If service not found, redirect to services page
if (!$service) {
    redirect('services.php');
}

// Handle enrollment
$error = false;
$success = false;

if (isset($_POST['enroll']) && isLoggedIn()) {
    $user_id = $_SESSION['user_id'];
    
    // Check if user is already enrolled
    $check_sql = "SELECT * FROM enrollments WHERE user_id = ? AND service_id = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("ii", $user_id, $service_id);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();
    
    if ($check_result->num_rows > 0) {
        $error = "You are already enrolled in this service.";
    } else {
        // Process enrollment using the new function
        if (processEnrollment($user_id, $service_id)) {
            $success = "You have successfully enrolled in this service. You now have premium access!";
        } else {
            $error = "Failed to enroll in this service. Please try again.";
        }
    }
}
?>

<!-- Service Detail Section -->
<section class="service-detail">
    <div class="container">
        <div class="service-header">
            <h1><?php echo $service['title']; ?></h1>
            <div class="service-meta">
                <span><i class="fas fa-clock"></i> Duration: <?php echo $service['duration']; ?></span>
                <span><i class="fas fa-dollar-sign"></i> Price: $<?php echo number_format($service['price'], 2); ?></span>
                <span><i class="fas fa-users"></i> Capacity: <?php echo $service['capacity']; ?> people</span>
            </div>
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
        
        <div class="service-content">
            <div class="service-image">
                <img src="<?php echo $service['image_path']; ?>" alt="<?php echo $service['title']; ?>">
            </div>
            
            <div class="service-description">
                <h2>Description</h2>
                <p><?php echo $service['description']; ?></p>
                
                <div class="service-actions">
                    <?php if (isLoggedIn()): ?>
                        <?php
                        // Check if user is already enrolled
                        $check_sql = "SELECT * FROM enrollments WHERE user_id = ? AND service_id = ?";
                        $check_stmt = $conn->prepare($check_sql);
                        $check_stmt->bind_param("ii", $_SESSION['user_id'], $service_id);
                        $check_stmt->execute();
                        $check_result = $check_stmt->get_result();
                        $already_enrolled = $check_result->num_rows > 0;
                        
                        if ($already_enrolled) {
                            $enrollment = $check_result->fetch_assoc();
                        }
                        ?>
                        
                        <?php if ($already_enrolled): ?>
                            <div class="alert alert-info">
                                You are enrolled in this service. 
                                Status: <span class="status status-<?php echo $enrollment['status']; ?>"><?php echo ucfirst($enrollment['status']); ?></span>
                            </div>
                        <?php else: ?>
                            <form method="POST">
                                <button type="submit" name="enroll" class="btn">Enroll Now</button>
                            </form>
                        <?php endif; ?>
                    <?php else: ?>
                        <div class="alert alert-info">
                            Please <a href="login.php">login</a> to enroll in this service.
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <div class="service-actions">
            <a href="services.php" class="btn btn-outline">Back to Services</a>
        </div>
    </div>
</section>

<?php
// Include footer
include 'includes/footer.php';
?>