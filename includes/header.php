<?php
// Include config file
require_once 'includes/config.php';
require_once 'includes/functions.php';

// Get current page
$current_page = basename($_SERVER['PHP_SELF'], '.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo SITE_NAME; ?> - <?php echo ucfirst($current_page); ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <header>
        <div class="container header-container">
            <div class="logo">
                <a href="index.php"><?php echo SITE_NAME; ?></a>
            </div>
            <button class="mobile-menu-btn">
                <i class="fas fa-bars"></i>
            </button>
            <nav>
                <ul>
                    <li><a href="index.php" class="<?php echo ($current_page == 'index') ? 'active' : ''; ?>">Home</a></li>
                    <li><a href="about.php" class="<?php echo ($current_page == 'about') ? 'active' : ''; ?>">About Us</a></li>
                    <li><a href="services.php" class="<?php echo ($current_page == 'services') ? 'active' : ''; ?>">Services</a></li>
                    <li><a href="recipes.php" class="<?php echo ($current_page == 'recipes') ? 'active' : ''; ?>">Recipes</a></li>
                    <li><a href="contact.php" class="<?php echo ($current_page == 'contact') ? 'active' : ''; ?>">Contact</a></li>
                    
                    <?php if (isLoggedIn()): ?>
                        <?php if (isClient()): ?>
                            <li><a href="client-dashboard.php" class="<?php echo ($current_page == 'client-dashboard') ? 'active' : ''; ?>">My Dashboard</a></li>
                        <?php endif; ?>
                        <?php if (isAdmin()): ?>
                            <li><a href="admin/index.php">Admin</a></li>
                        <?php endif; ?>
                        <li><a href="logout.php">Logout</a></li>
                    <?php else: ?>
                        <li><a href="login.php" class="<?php echo ($current_page == 'login') ? 'active' : ''; ?>">Login</a></li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>
    </header>