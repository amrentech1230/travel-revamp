<?php
require_once __DIR__ . '/../config/config.php';
$currentPage = basename($_SERVER['PHP_SELF'], '.php');
$base = BASE_PATH;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="TravenzoTravel - Book cheap flights online. Compare & get lowest airfare on domestic & international flights.">
    <meta name="keywords" content="flights, cheap flights, air tickets, flight booking, TravenzoTravel, airline tickets">
    <title><?php echo isset($pageTitle) ? $pageTitle . ' | ' . SITE_NAME : SITE_NAME . ' | Book Flights at Lowest Airfare'; ?></title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Font Awesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <!-- AOS Animate On Scroll -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.css">

    <!-- Main CSS -->
    <link rel="stylesheet" href="<?php echo $base; ?>/assets/css/style.css">

    <!-- Pass PHP constants to JavaScript -->
    <script>const BASE_PATH = '<?php echo $base; ?>';</script>
</head>
<body class="page-<?php echo $currentPage; ?>">

<!-- ═══ Page Preloader ═══ -->
<!-- <div class="preloader" id="preloader">
    <div class="preloader-inner">
        <div class="plane-loader">
            <i class="fas fa-plane"></i>
        </div>
        <div class="loader-trail"></div>
        <p class="loader-text">TravenzoTravel</p>
        <span class="loader-sub">Finding best flights for you...</span>
    </div>
</div> -->

<!-- ═══ Top Info Bar ═══ -->
<div class="topbar">
    <div class="container topbar-inner">
        <div class="topbar-left">
            <span><i class="fas fa-phone-alt"></i> <?php echo SITE_PHONE; ?></span>
            <span class="topbar-divider">|</span>
            <span><i class="fas fa-envelope"></i> <?php echo SITE_EMAIL; ?></span>
        </div>
        <div class="topbar-right">
            <a href="<?php echo $base; ?>/about.php">About Us</a>
            <a href="<?php echo $base; ?>/contact.php">Support 24/7</a>
        </div>
    </div>
</div>

<!-- ═══ Main Header / Navbar ═══ -->
<header class="site-header <?php echo $currentPage === 'index' ? 'header-transparent' : 'header-solid'; ?>">
    <div class="container header-inner">
        <!-- Logo -->
        <a href="<?php echo $base; ?>/" class="logo">
            <span class="logo-primary">Travenzo</span><span class="logo-accent">Travel</span>
        </a>

        <!-- Desktop Nav -->
        <nav class="main-nav" id="mainNav">
            <ul>
                <li><a href="<?php echo $base; ?>/" class="<?php echo $currentPage === 'index' ? 'active' : ''; ?>"><i class="fas fa-plane"></i> Flights</a></li>
                <li><a href="<?php echo $base; ?>/about.php" class="<?php echo $currentPage === 'about' ? 'active' : ''; ?>">About</a></li>
                <li><a href="<?php echo $base; ?>/contact.php" class="<?php echo $currentPage === 'contact' ? 'active' : ''; ?>">Contact</a></li>
            </ul>
        </nav>

        <!-- Auth Buttons -->
        <div class="header-actions">
            <?php if (isLoggedIn()): ?>
                <div class="user-menu">
                    <button class="user-menu-btn" id="userMenuBtn">
                        <i class="fas fa-user-circle"></i>
                        <span><?php echo sanitize($_SESSION['user_name']); ?></span>
                        <i class="fas fa-chevron-down"></i>
                    </button>
                    <div class="user-dropdown" id="userDropdown">
                        <a href="<?php echo $base; ?>/my-bookings.php"><i class="fas fa-ticket-alt"></i> My Bookings</a>
                        <a href="<?php echo $base; ?>/profile.php"><i class="fas fa-user-edit"></i> Profile</a>
                        <hr>
                        <a href="<?php echo $base; ?>/logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
                    </div>
                </div>
            <?php else: ?>
                <a href="<?php echo $base; ?>/login.php" class="btn-header-login"><i class="fas fa-user"></i> Login</a>
                <a href="<?php echo $base; ?>/register.php" class="btn-header-signup">Sign Up</a>
            <?php endif; ?>
        </div>

        <!-- Mobile Toggle -->
        <button class="mobile-toggle" id="mobileToggle" aria-label="Toggle menu">
            <span></span><span></span><span></span>
        </button>
    </div>
</header>

<!-- ═══ Mobile Sidebar ═══ -->
<div class="mobile-sidebar" id="mobileSidebar">
    <div class="mobile-sidebar-header">
        <span class="logo-primary">Travenzo</span><span class="logo-accent">Travel</span>
        <button class="mobile-close" id="mobileClose"><i class="fas fa-times"></i></button>
    </div>
    <nav class="mobile-nav">
        <a href="<?php echo $base; ?>/"><i class="fas fa-plane"></i> Flights</a>
        <a href="<?php echo $base; ?>/about.php"><i class="fas fa-building"></i> About Us</a>
        <a href="<?php echo $base; ?>/contact.php"><i class="fas fa-headset"></i> Contact</a>
        <hr>
        <?php if (isLoggedIn()): ?>
            <a href="<?php echo $base; ?>/my-bookings.php"><i class="fas fa-ticket-alt"></i> My Bookings</a>
            <a href="<?php echo $base; ?>/logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
        <?php else: ?>
            <a href="<?php echo $base; ?>/login.php"><i class="fas fa-sign-in-alt"></i> Login</a>
            <a href="<?php echo $base; ?>/register.php"><i class="fas fa-user-plus"></i> Sign Up</a>
        <?php endif; ?>
    </nav>
</div>
<div class="mobile-overlay" id="mobileOverlay"></div>

<!-- ═══ Flash Messages ═══ -->
<?php $flash = getFlash(); if ($flash): ?>
<div class="flash flash-<?php echo $flash['type']; ?>" id="flashMsg">
    <div class="container">
        <span><i class="fas fa-<?php echo $flash['type'] === 'success' ? 'check-circle' : 'exclamation-circle'; ?>"></i> <?php echo $flash['message']; ?></span>
        <button class="flash-close" onclick="this.parentElement.parentElement.remove()"><i class="fas fa-times"></i></button>
    </div>
</div>
<?php endif; ?>

<!-- ═══ Main Content Begin ═══ -->
<main id="mainContent">
