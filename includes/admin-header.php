<?php
// Include config file
require_once '../includes/config.php';
require_once '../includes/functions.php';

// Check if user is logged in and is admin
if (!isLoggedIn() || !isAdmin()) {
    redirect('../login.php');
}

// Get current page
$current_page = basename($_SERVER['PHP_SELF'], '.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - <?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body>
    <div class="admin-container">
        <!-- Sidebar -->
        <div class="sidebar">
            <div class="sidebar-header">
                <h2><?php echo SITE_NAME; ?></h2>
            </div>
            
            <div class="sidebar-menu">
                <ul>
                    <li>
                        <a href="index.php" class="<?php echo ($current_page == 'index') ? 'active' : ''; ?>">
                            <i class="fas fa-tachometer-alt"></i>
                            <span>Dashboard</span>
                        </a>
                    </li>
                    <li>
                        <a href="recipes.php" class="<?php echo ($current_page == 'recipes' || $current_page == 'add-recipe' || $current_page == 'edit-recipe') ? 'active' : ''; ?>">
                            <i class="fas fa-utensils"></i>
                            <span>Recipe Organizer</span>
                        </a>
                    </li>
                    <li>
                        <a href="accounts.php" class="<?php echo ($current_page == 'accounts' || $current_page == 'add-user' || $current_page == 'edit-user') ? 'active' : ''; ?>">
                            <i class="fas fa-users"></i>
                            <span>Account Management</span>
                        </a>
                    </li>
                    <li>
                        <a href="services.php" class="<?php echo ($current_page == 'services' || $current_page == 'add-service' || $current_page == 'edit-service') ? 'active' : ''; ?>">
                            <i class="fas fa-concierge-bell"></i>
                            <span>Service Management</span>
                        </a>
                    </li>
                    <li>
                        <a href="enrollments.php" class="<?php echo ($current_page == 'enrollments') ? 'active' : ''; ?>">
                            <i class="fas fa-clipboard-list"></i>
                            <span>Enrollment Management</span>
                        </a>
                    </li>
                    <li>
                        <a href="../index.php" target="_blank">
                            <i class="fas fa-home"></i>
                            <span>View Site</span>
                        </a>
                    </li>
                </ul>
            </div>
            
            <div class="sidebar-footer">
                <a href="../logout.php">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Log Out</span>
                </a>
            </div>
        </div>
        
        <!-- Main Content -->
        <div class="main-content">
            <div class="admin-header">
                <h1><?php echo ucfirst($current_page); ?></h1>
                <div class="user-info">
                    <img src="https://via.placeholder.com/40" alt="Admin">
                    <span><?php echo $_SESSION['first_name'] . ' ' . $_SESSION['last_name']; ?></span>
                </div>
            </div>