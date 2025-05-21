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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <style>
        @media (max-width: 768px) {
    nav {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100vh;
        background-color: var(--olive-green); /* Change to your main color */
        color: white;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        z-index: 9999;
        padding: 2rem;
        text-align: center;
    }

    nav.active {
        display: flex;
    }

    nav ul {
        flex-direction: column;
        gap: 1.5rem;
        list-style: none;
        padding: 0;
    }

    nav ul li a {
        color: white;
        font-size: 1.5rem;
        text-decoration: none;
    }

    .mobile-menu-btn {
        display: block;
        z-index: 10000; /* above nav */
        color: white;
        background: none;
        border: none;
        font-size: 2rem;
        cursor: pointer;
        position: relative;
    }
}

@media (min-width: 769px) {
    nav {
        display: flex !important;
        position: static;
        flex-direction: row;
        background-color: transparent;
        height: auto;
        padding: 0;
    }

    .mobile-menu-btn {
        display: none;
    }

    nav ul {
        flex-direction: row;
        gap: 1rem;
    }

    nav ul li a {
        color: var(--white);
        font-size: 1rem;
    }
}
    </style>
</head>
<body>
    <?php include 'loading-screen.php'; ?>
    <header>
    <div class="container header-container" style="position: relative;">
        <div class="logo">
            <a href="index.php"><?php echo SITE_NAME; ?></a>
        </div>

        <button class="mobile-menu-btn" onclick="toggleMenu()" style="background: none; border: none; font-size: 24px; cursor: pointer;">
            <i class="fas fa-bars"></i>
        </button>

        <nav id="main-nav">
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
  <script>
function toggleMenu() {
    const nav = document.getElementById('main-nav');
    nav.classList.toggle('active');
}
</script>
</body>
