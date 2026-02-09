<?php
// Global Header Component
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include_once 'admin/config.php';

// Verify Database Connection
if (!$con) {
    die("Database connection failed. Please check your settings.");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title><?php echo $pageTitle ?? 'ExpenseVoyage | Travel Website'; ?></title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">

    <!-- External Libraries -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">

    <!-- CSS -->
    <link href="css/custom.css" rel="stylesheet">
</head>
<body>
    <!-- Trending Cinematic Layers -->
    <div class="grain-overlay"></div>
    <div class="luxury-cursor"></div>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg fixed-top" id="mainNav">
        <div class="container px-3 px-lg-0">
            <div class="nav-container d-flex align-items-center w-100 py-2 py-lg-0">
                <a href="index.php" class="navbar-brand serif-font fs-3 text-white">
                    Expense<span class="text-gold">Voyage</span>
                </a>
                
                <div class="ms-auto d-flex align-items-center">
                    <!-- Theme Toggle -->
                    <button id="theme-toggle" class="btn text-white me-3 fs-5 p-0" title="Switch Light/Dark Mode">
                        <i class="fas fa-sun"></i>
                    </button>

                    <button class="navbar-toggler border-0 shadow-none p-0" type="button" data-bs-toggle="collapse" data-bs-target="#nav">
                        <span class="navbar-toggler-icon" style="filter: invert(1);"></span>
                    </button>
                </div>
                
                <div class="collapse navbar-collapse" id="nav">
                    <ul class="navbar-nav ms-auto mb-2 mb-lg-0 align-items-center">
                        <li class="nav-item"><a class="nav-link <?php echo $currentPage == 'index' ? 'active' : ''; ?>" href="index.php">Home</a></li>
                        <li class="nav-item"><a class="nav-link <?php echo $currentPage == 'package' ? 'active' : ''; ?>" href="package.php">Packages</a></li>
                        <li class="nav-item"><a class="nav-link <?php echo $currentPage == 'about' ? 'active' : ''; ?>" href="about.php">About Us</a></li>
                        <li class="nav-item"><a class="nav-link <?php echo $currentPage == 'contact' ? 'active' : ''; ?>" href="contact.php">Contact</a></li>
                        
                        <?php if (isset($_SESSION['email'])): ?>
                            <li class="nav-item dropdown ms-lg-4">
                                <a class="nav-link dropdown-toggle btn-luxe-outline px-4 py-2" href="#" data-bs-toggle="dropdown">
                                    <span class="small"><?php echo htmlspecialchars($_SESSION['name'] ?? 'Guest'); ?></span>
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end mt-4">
                                    <li><a class="dropdown-item" href="user-profile.php"><i class="fas fa-user-circle me-3"></i> Profile</a></li>
                                    <?php if(($_SESSION['role'] ?? '') == 'admin'): ?>
                                        <li><a class="dropdown-item" href="admin/index.php"><i class="fas fa-user-shield me-3"></i> Admin Panel</a></li>
                                    <?php endif; ?>
                                    <li><hr class="dropdown-divider opacity-10"></li>
                                    <li><a class="dropdown-item text-danger" href="logout.php"><i class="fas fa-sign-out-alt me-3"></i> Logout</a></li>
                                </ul>
                            </li>
                        <?php else: ?>
                            <li class="nav-item ms-lg-4 mt-3 mt-lg-0">
                                <a href="login/account.php" class="btn-luxe btn-luxe-gold py-2 px-4">Login</a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
        </div>
    </nav>

