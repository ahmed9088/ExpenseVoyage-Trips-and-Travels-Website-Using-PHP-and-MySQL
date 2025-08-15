<?php
include 'admin/config.php';
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (isset($_GET['viewid'])) {
    $id = intval($_GET['viewid']); // Sanitize the input
    $sql = "SELECT * FROM trips WHERE trip_id = ?";
    $stmt = $con->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc(); // Fetch the trip data
    } else {
        header("Location: package.php"); // Redirect if no trip is found
        exit();
    }
} else {
    header("Location: package.php"); // Redirect if no viewid is set
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Package Details - ExpenseVoyage</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta content="Package Details - ExpenseVoyage" name="keywords">
    <meta content="View detailed information about our travel packages including itinerary, pricing, and inclusions" name="description">
    
    <!-- Favicon -->
    <link href="img/favicon.ico" rel="icon">
    <link rel="apple-touch-icon" sizes="180x180" href="img/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="img/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="img/favicon-16x16.png">
    <link rel="manifest" href="img/site.webmanifest">
    
    <!-- Google Web Fonts -->
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Libraries Stylesheet -->
    <link href="lib/owlcarousel/assets/owl.carousel.min.css" rel="stylesheet">
    <link href="lib/tempusdominus/css/tempusdominus-bootstrap-4.min.css" rel="stylesheet" />
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Animate.css -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    
    <!-- AOS (Animate on Scroll) Library -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.css" rel="stylesheet">
    
    <!-- Particles.js for animated background -->
    <script src="https://cdn.jsdelivr.net/particles.js/2.0.0/particles.min.js"></script>
    
    <!-- Customized Stylesheet -->
    <style>
        :root {
            --primary: #4361ee;
            --secondary: #3f37c9;
            --accent: #4cc9f0;
            --light: #f8f9fa;
            --dark: #212529;
            --success: #06ffa5;
            --gradient: linear-gradient(135deg, var(--primary), var(--secondary));
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            color: var(--dark);
            background-color: #f5f7ff;
            overflow-x: hidden;
        }
        
        /* Animated Background */
        #particles-js {
            position: fixed;
            width: 100%;
            height: 100%;
            top: 0;
            left: 0;
            z-index: -1;
            background: linear-gradient(135deg, #1a1c20, #2d3436);
        }
        
        /* Scrollbar Styling */
        ::-webkit-scrollbar {
            width: 12px;
        }
        
        ::-webkit-scrollbar-track {
            background: #232E33;
            border-radius: 1px;
        }
        
        ::-webkit-scrollbar-thumb {
            background: #7AB730;
            border-radius: 3px;
        }
        
        @supports not selector(::-webkit-scrollbar) {
            body {
                scrollbar-color: #7AB730 #232E33;
            }
        }
        
        /* Typography */
        h1, h2, h3, h4, h5, h6 {
            font-weight: 700;
            margin-bottom: 1rem;
        }
        
        .section-title {
    position: relative;
    display: inline-block;
    margin-bottom: 2.5rem;
    color: white;
}           position: relative;
            display: inline-block;
            margin-bottom: 2.5rem;
        }
        
        .section-title::after {
            content: '';
            position: absolute;
            left: 0;
            bottom: -10px;
            width: 50px;
            height: 4px;
            background: var(--gradient);
            border-radius: 2px;
        }
        
        .text-primary {
            color: var(--primary) !important;
        }
        
        .text-accent {
            color: var(--accent) !important;
        }
        
        /* Buttons */
        .btn {
            padding: 12px 30px;
            font-weight: 600;
            border-radius: 50px;
            transition: all 0.3s ease;
            border: none;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .btn-primary {
            background: var(--gradient);
            color: white;
            box-shadow: 0 4px 15px rgba(67, 97, 238, 0.3);
        }
        
        .btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 7px 20px rgba(67, 97, 238, 0.4);
            color: white;
        }
        
        .btn-outline-primary {
            border: 2px solid var(--primary);
            color: var(--primary);
            background: transparent;
        }
        
        .btn-outline-primary:hover {
            background: var(--primary);
            color: white;
        }
        
        /* Navbar */
        .navbar {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
            padding: 15px 0;
        }
        
        .navbar-brand {
            font-weight: 800;
            font-size: 1.8rem;
            color: var(--primary);
        }
        
        .navbar-brand span {
            color: var(--dark);
        }
        
        .navbar-nav .nav-link {
            font-weight: 500;
            margin: 0 10px;
            color: var(--dark);
            position: relative;
            transition: all 0.3s ease;
        }
        
        .navbar-nav .nav-link:hover, .navbar-nav .nav-link.active {
            color: var(--primary);
        }
        
        .navbar-nav .nav-link::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 0;
            height: 2px;
            background: var(--primary);
            transition: all 0.3s ease;
        }
        
        .navbar-nav .nav-link:hover::after, .navbar-nav .nav-link.active::after {
            width: 100%;
        }
        
        .navbar-toggler {
            border: none;
            padding: 0;
        }
        
        .navbar-toggler:focus {
            box-shadow: none;
        }
        
        .navbar-toggler span {
            display: block;
            width: 25px;
            height: 3px;
            margin: 5px 0;
            background: var(--primary);
            transition: all 0.3s ease;
        }
        
        /* Topbar */
        .topbar {
            background-color: #f8f9fa;
            padding: 10px 0;
            border-bottom: 1px solid #e9ecef;
        }
        
        .topbar a {
            color: #6c757d;
            text-decoration: none;
            transition: all 0.3s ease;
        }
        
        .topbar a:hover {
            color: var(--primary);
        }
        
        /* Page Header */
        .page-header {
            background: linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.6)), url('img/packages-bg.jpg');
            background-size: cover;
            background-position: center;
            padding: 120px 0 80px;
            position: relative;
            overflow: hidden;
        }
        
        .page-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: var(--gradient);
            opacity: 0.2;
            z-index: 1;
        }
        
        .page-header .container {
            position: relative;
            z-index: 2;
        }
        
        .page-header h1 {
            color: white;
            font-weight: 800;
            font-size: 3.5rem;
            margin-bottom: 20px;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
        }
        
        .breadcrumb {
            background: none;
            padding: 0;
            margin: 0;
            justify-content: center;
        }
        
        .breadcrumb-item + .breadcrumb-item::before {
            color: rgba(255, 255, 255, 0.7);
        }
        
        .breadcrumb-item.active {
            color: white;
        }
        
        .breadcrumb-item a {
            color: rgba(255, 255, 255, 0.7);
            text-decoration: none;
            transition: all 0.3s ease;
        }
        
        .breadcrumb-item a:hover {
            color: white;
        }
        
        /* Booking Section */
        .booking {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            padding: 30px;
            margin-top: -80px;
            position: relative;
            z-index: 10;
        }
        
        .booking .form-control {
            border: none;
            border-radius: 50px;
            padding: 15px 25px;
            background: #f8f9fa;
            font-weight: 500;
            height: auto;
        }
        
        .booking .form-control:focus {
            box-shadow: none;
            background: white;
            border: 2px solid var(--primary);
        }
        
        .booking .btn {
            height: auto;
            padding: 15px 30px;
            border-radius: 50px;
        }
        
        /* Package Details */
        .package-details {
            background: white;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
            padding: 50px;
            margin: 50px auto;
            max-width: calc(100% - 40px);
        }
        
        .package-image {
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
            margin-bottom: 30px;
        }
        
        .package-image img {
            width: 100%;
            height: auto;
            transition: all 0.5s ease;
        }
        
        .package-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            margin-bottom: 20px;
        }
        
        .package-meta-item {
            display: flex;
            align-items: center;
            background: #f8f9fa;
            padding: 10px 15px;
            border-radius: 50px;
            font-size: 0.9rem;
        }
        
        .package-meta-item i {
            color: var(--primary);
            margin-right: 8px;
        }
        
        .package-info {
            background: #f8f9fa;
            border-radius: 15px;
            padding: 30px;
            margin-bottom: 30px;
        }
        
        .package-info h3 {
            color: var(--primary);
            margin-bottom: 20px;
            font-size: 1.5rem;
        }
        
        .package-info ul {
            list-style: none;
            padding: 0;
        }
        
        .package-info ul li {
            padding: 10px 0;
            border-bottom: 1px solid #e9ecef;
            display: flex;
            align-items: center;
        }
        
        .package-info ul li:last-child {
            border-bottom: none;
        }
        
        .package-info ul li i {
            color: var(--primary);
            margin-right: 10px;
        }
        
        .package-booking {
            background: var(--gradient);
            border-radius: 15px;
            padding: 30px;
            color: white;
            text-align: center;
        }
        
        .package-booking h3 {
            margin-bottom: 15px;
        }
        
        .package-booking .price {
            font-size: 2.5rem;
            font-weight: 800;
            margin-bottom: 20px;
        }
        
        .package-booking .btn {
            background: white;
            color: var(--primary);
            font-weight: 700;
            padding: 15px 40px;
            margin-top: 10px;
        }
        
        .package-booking .btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
            color: var(--primary);
        }
        
        /* Vehicle Section */
        .vehicle-section {
            background: white;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
            padding: 50px;
            margin: 50px auto;
            max-width: calc(100% - 40px);
        }
        
        .vehicle-image {
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
            margin-bottom: 30px;
        }
        
        .vehicle-image img {
            width: 100%;
            height: auto;
            transition: all 0.5s ease;
        }
        
        .vehicle-details {
            background: #f8f9fa;
            border-radius: 15px;
            padding: 30px;
            height: 100%;
        }
        
        .vehicle-details h3 {
            color: var(--primary);
            margin-bottom: 25px;
            font-size: 1.5rem;
        }
        
        .vehicle-feature {
            display: flex;
            align-items: flex-start;
            margin-bottom: 20px;
        }
        
        .vehicle-feature i {
            background: var(--gradient);
            color: white;
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
            flex-shrink: 0;
        }
        
        .vehicle-feature-content h5 {
            margin-bottom: 5px;
            font-size: 1.1rem;
        }
        
        /* Sidebar */
        .sidebar {
            position: sticky;
            top: 100px;
        }
        
        .author-bio {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
            padding: 30px;
            text-align: center;
            margin-bottom: 30px;
        }
        
        .author-bio img {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            object-fit: cover;
            margin-bottom: 15px;
            border: 4px solid var(--primary);
        }
        
        .author-bio h3 {
            color: var(--primary);
            margin-bottom: 10px;
        }
        
        .author-bio .social-links {
            display: flex;
            justify-content: center;
            gap: 10px;
            margin-top: 15px;
        }
        
        .author-bio .social-links a {
            width: 36px;
            height: 36px;
            background: #f8f9fa;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--primary);
            transition: all 0.3s ease;
        }
        
        .author-bio .social-links a:hover {
            background: var(--primary);
            color: white;
            transform: translateY(-3px);
        }
        
        .search-widget {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
            padding: 30px;
            margin-bottom: 30px;
        }
        
        .search-widget h4 {
            margin-bottom: 20px;
            color: var(--dark);
        }
        
        .search-widget .input-group {
            border-radius: 50px;
            overflow: hidden;
        }
        
        .search-widget .form-control {
            border: none;
            border-radius: 50px 0 0 50px;
            padding: 15px 25px;
        }
        
        .search-widget .btn {
            border-radius: 0 50px 50px 0;
            padding: 0 25px;
        }
        
        .recent-posts {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
            padding: 30px;
        }
        
        .recent-posts h4 {
            margin-bottom: 20px;
            color: var(--dark);
        }
        
        .recent-post-item {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
            padding-bottom: 15px;
            border-bottom: 1px solid #e9ecef;
        }
        
        .recent-post-item:last-child {
            margin-bottom: 0;
            padding-bottom: 0;
            border-bottom: none;
        }
        
        .recent-post-item img {
            width: 70px;
            height: 70px;
            border-radius: 10px;
            object-fit: cover;
            margin-right: 15px;
        }
        
        .recent-post-item-content h6 {
            margin-bottom: 5px;
            font-size: 0.9rem;
        }
        
        .recent-post-item-content small {
            color: #6c757d;
        }
        
        /* Footer */
        footer {
            background: linear-gradient(135deg, #1a1c20, #2d3436);
            color: rgba(255, 255, 255, 0.7);
            padding: 80px 0 0;
            position: relative;
        }
        
        footer::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 5px;
            background: var(--gradient);
        }
        
        .footer-logo {
            font-weight: 800;
            font-size: 1.8rem;
            color: white;
            margin-bottom: 20px;
            display: inline-block;
        }
        
        .footer-logo span {
            color: var(--accent);
        }
        
        .footer-about {
            margin-bottom: 30px;
        }
        
        .footer-title {
            color: white;
            font-weight: 700;
            margin-bottom: 20px;
            position: relative;
            padding-bottom: 10px;
        }
        
        .footer-title::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 40px;
            height: 2px;
            background: var(--accent);
        }
        
        .footer-links a {
            color: rgba(255, 255, 255, 0.7);
            display: block;
            margin-bottom: 10px;
            text-decoration: none;
            transition: all 0.3s ease;
        }
        
        .footer-links a:hover {
            color: var(--accent);
            padding-left: 5px;
        }
        
        .footer-contact p {
            margin-bottom: 10px;
            display: flex;
            align-items: center;
        }
        
        .footer-contact i {
            margin-right: 10px;
            color: var(--accent);
        }
        
        .footer-newsletter .form-control {
            background: rgba(255, 255, 255, 0.1);
            border: none;
            border-radius: 50px;
            padding: 15px 25px;
            color: white;
            margin-bottom: 15px;
        }
        
        .footer-newsletter .form-control::placeholder {
            color: rgba(255, 255, 255, 0.5);
        }
        
        .footer-newsletter .form-control:focus {
            box-shadow: none;
            background: rgba(255, 255, 255, 0.15);
        }
        
        .footer-newsletter .btn {
            width: 100%;
            border-radius: 50px;
        }
        
        .footer-bottom {
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            padding: 20px 0;
            margin-top: 50px;
            text-align: center;
        }
        
        .footer-social a {
            width: 40px;
            height: 40px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin: 0 5px;
            color: white;
            transition: all 0.3s ease;
        }
        
        .footer-social a:hover {
            background: var(--accent);
            transform: translateY(-5px);
        }
        
        /* Back to Top */
        .back-to-top {
            position: fixed;
            bottom: 30px;
            right: 30px;
            width: 50px;
            height: 50px;
            background: var(--gradient);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.2rem;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
            z-index: 99;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
        }
        
        .back-to-top.show {
            opacity: 1;
            visibility: visible;
        }
        
        .back-to-top:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.3);
            color: white;
        }
        
        /* Responsive */
        @media (max-width: 991px) {
            .page-header h1 {
                font-size: 2.5rem;
            }
            
            .package-details, .vehicle-section {
                padding: 30px 20px;
            }
            
            .sidebar {
                position: static;
                margin-top: 30px;
            }
        }
        
        @media (max-width: 767px) {
            .page-header h1 {
                font-size: 2rem;
            }
            
            .booking {
                margin-top: -50px;
                padding: 20px;
            }
            
            .package-meta {
                flex-direction: column;
                gap: 10px;
            }
            
            .package-booking .price {
                font-size: 2rem;
            }
        }
    </style>
</head>
<body>
    <!-- Animated Background -->
    <div id="particles-js"></div>
    
    <!-- Topbar Start -->
    <div class="topbar d-none d-lg-block">
        <div class="container">
            <div class="row">
                <div class="col-md-6 text-center text-md-left mb-2 mb-md-0">
                    <div class="d-inline-flex align-items-center">
                        <p class="mb-0"><i class="fa fa-envelope mr-2 text-primary"></i>ubaidsoomro505@gmail.com</p>
                        <p class="mb-0 px-3">|</p>
                        <p class="mb-0"><i class="fa fa-phone-alt mr-2 text-primary"></i>+92 3188 893 863</p>
                    </div>
                </div>
                <div class="col-md-6 text-center text-md-right">
                    <div class="d-inline-flex align-items-center">
                        <a class="text-primary px-3" href=""><i class="fab fa-facebook-f"></i></a>
                        <a class="text-primary px-3" href=""><i class="fab fa-twitter"></i></a>
                        <a class="text-primary px-3" href=""><i class="fab fa-linkedin-in"></i></a>
                        <a class="text-primary px-3" href=""><i class="fab fa-instagram"></i></a>
                        <a class="text-primary pl-3" href=""><i class="fab fa-youtube"></i></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Topbar End -->
    
    <!-- Navbar Start -->
    <nav class="navbar navbar-expand-lg navbar-light sticky-top">
        <div class="container">
            <a href="index.php" class="navbar-brand animate__animated animate__fadeInLeft">
                <span class="text-primary">Expense</span><span>Voyage</span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarCollapse">
                <span></span>
                <span></span>
                <span></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarCollapse">
                <div class="navbar-nav ms-auto py-0">
                    <a href="index.php" class="nav-item nav-link animate__animated animate__fadeInDown">Home</a>
                    <a href="about.php" class="nav-item nav-link animate__animated animate__fadeInDown">About</a>
                    <a href="service.php" class="nav-item nav-link animate__animated animate__fadeInDown">Services</a>
                    <a href="package.php" class="nav-item nav-link active animate__animated animate__fadeInDown">Tour Packages</a>
                    <div class="nav-item dropdown animate__animated animate__fadeInDown">
                        <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">Pages</a>
                        <div class="dropdown-menu m-0">
                            <a href="blog.php" class="dropdown-item">Blog</a>
                            <a href="destination.php" class="dropdown-item">Destination</a>
                            <a href="guide.php" class="dropdown-item">Travel Guides</a>
                        </div>
                    </div>
                    <a href="contact.php" class="nav-item nav-link animate__animated animate__fadeInDown">Contact</a>
                    <div class="nav-item dropdown animate__animated animate__fadeInRight">
                        <?php
                        if (isset($_SESSION['email'])) {
                            $name = $_SESSION['name'] ?? 'User';
                            echo '<a href="#" class="nav-link dropdown-toggle active" data-bs-toggle="dropdown"><span>' . htmlspecialchars($name) . '</span></a>
                            <div class="dropdown-menu dropdown-menu-end m-0">
                                <a href="user-profile.php" class="dropdown-item">My Account</a>
                                <a href="admin/index.php" class="dropdown-item">Admin Panel</a>
                                <a href="booking.php" class="dropdown-item">Booking</a>
                                <a href="logout.php" class="dropdown-item">Logout</a>
                            </div>';
                        } else {
                            echo '<a href="#" class="nav-link dropdown-toggle active" data-bs-toggle="dropdown"><span>Login/Register</span></a>
                            <div class="dropdown-menu dropdown-menu-end m-0">
                                <a href="login/account.php" class="dropdown-item">Login/Register</a>
                            </div>';
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </nav>
    <!-- Navbar End -->
    
    <!-- Page Header Start -->
    <div class="page-header">
        <div class="container">
            <div class="text-center">
                <h1 class="animate__animated animate__fadeInDown">Package Details</h1>
                <nav aria-label="breadcrumb" class="animate__animated animate__fadeInUp">
                    <ol class="breadcrumb justify-content-center">
                        <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                        <li class="breadcrumb-item"><a href="package.php">Packages</a></li>
                        <li class="breadcrumb-item active">Details</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
    <!-- Page Header End -->
    
    <!-- Booking Start -->
    <div class="container booking animate__animated animate__fadeInUp">
        <form action="package.php" method="get">
            <div class="row g-3 align-items-center">
                <div class="col-md-10">
                    <div class="row g-3">
                        <div class="col-md-12">
                            <input type="text" class="form-control" placeholder="Enter Destination" name="destination">
                        </div>
                    </div>
                </div>
                <div class="col-md-2">
                    <button class="btn btn-primary w-100" type="submit">Search</button>
                </div>
            </div>
        </form>
    </div>
    <!-- Booking End -->
    
    <!-- Package Details Start -->
    <div class="container">
        <div class="row">
            <div class="col-lg-8">
                <div class="package-details" data-aos="fade-up">
                    <div class="package-image">
                        <img src="<?php echo htmlspecialchars($row['trip_image']); ?>" alt="<?php echo htmlspecialchars($row['trip_name']); ?>">
                    </div>
                    
                    <h2 class="mb-4"><?php echo htmlspecialchars($row['trip_name']); ?></h2>
                    
                    <div class="package-meta">
                        <div class="package-meta-item">
                            <i class="fas fa-map-marker-alt"></i>
                            <span><?php echo htmlspecialchars($row['destination']); ?></span>
                        </div>
                        <div class="package-meta-item">
                            <i class="fas fa-calendar-alt"></i>
                            <span><?php echo htmlspecialchars($row['duration_days']); ?> days</span>
                        </div>
                        <div class="package-meta-item">
                            <i class="fas fa-user"></i>
                            <span><?php echo htmlspecialchars($row['persons']); ?> persons</span>
                        </div>
                        <div class="package-meta-item">
                            <i class="fas fa-star"></i>
                            <span><?php echo htmlspecialchars($row['stars']); ?></span>
                        </div>
                    </div>
                    
                    <div class="package-info">
                        <h3>Package Description</h3>
                        <p><?php echo nl2br(htmlspecialchars($row['description'])); ?></p>
                    </div>
                    
                    <div class="package-info">
                        <h3>Package Inclusions</h3>
                        <ul>
                            <li><i class="fas fa-check-circle"></i> Accommodation as per itinerary</li>
                            <li><i class="fas fa-check-circle"></i> Daily breakfast and dinner</li>
                            <li><i class="fas fa-check-circle"></i> All sightseeing as per itinerary</li>
                            <li><i class="fas fa-check-circle"></i> All transfers and sightseeing by AC vehicle</li>
                            <li><i class="fas fa-check-circle"></i> Services of professional tour guide</li>
                            <li><i class="fas fa-check-circle"></i> All toll tax, parking fees, driver allowance</li>
                        </ul>
                    </div>
                </div>
                
                <div class="vehicle-section" data-aos="fade-up" data-aos-delay="100">
        <h2 class="text-center mb-5">Travel with Comfort</h2>
        <div class="row">
            <div class="col-md-6">
                <div class="vehicle-image">
                    <?php if (!empty($row['vehicle_image'])): ?>
                        <img src="<?php echo htmlspecialchars($row['vehicle_image']); ?>" alt="Vehicle">
                    <?php else: ?>
                        <img src="img/no-vehicle-image.jpg" alt="No vehicle image available">
                    <?php endif; ?>
                </div>
            </div>
            <div class="col-md-6">
                <div class="vehicle-details">
                    <h3>Vehicle Details</h3>
                    
                    <?php if (!empty($row['vehicle_type']) || !empty($row['vehicle_capacity']) || !empty($row['vehicle_features']) || !empty($row['driver_details'])): ?>
                        
                        <?php if (!empty($row['vehicle_type'])): ?>
                        <div class="vehicle-feature">
                            <i class="fas fa-bus"></i>
                            <div class="vehicle-feature-content">
                                <h5>Vehicle Type</h5>
                                <p><?php echo htmlspecialchars($row['vehicle_type']); ?></p>
                            </div>
                        </div>
                        <?php endif; ?>
                        
                        <?php if (!empty($row['vehicle_capacity'])): ?>
                        <div class="vehicle-feature">
                            <i class="fas fa-users"></i>
                            <div class="vehicle-feature-content">
                                <h5>Capacity</h5>
                                <p><?php echo htmlspecialchars($row['vehicle_capacity']); ?> Persons</p>
                            </div>
                        </div>
                        <?php endif; ?>
                        
                        <?php if (!empty($row['vehicle_features'])): ?>
                        <div class="vehicle-feature">
                            <i class="fas fa-cogs"></i>
                            <div class="vehicle-feature-content">
                                <h5>Key Features</h5>
                                <p><?php echo nl2br(htmlspecialchars($row['vehicle_features'])); ?></p>
                            </div>
                        </div>
                        <?php endif; ?>
                        
                        <?php if (!empty($row['driver_details'])): ?>
                        <div class="vehicle-feature">
                            <i class="fas fa-user-tie"></i>
                            <div class="vehicle-feature-content">
                                <h5>Driver Details</h5>
                                <p><?php echo nl2br(htmlspecialchars($row['driver_details'])); ?></p>
                            </div>
                        </div>
                        <?php else: ?>
                        <div class="vehicle-feature">
                            <i class="fas fa-user-tie"></i>
                            <div class="vehicle-feature-content">
                                <h5>Driver Details</h5>
                                <p class="text-muted">Driver information will be provided before the trip starts.</p>
                            </div>
                        </div>
                        <?php endif; ?>
                        
                    <?php else: ?>
                        <div class="text-center py-4">
                            <i class="fas fa-car fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">Vehicle Information</h5>
                            <p class="text-muted">Vehicle details will be assigned based on your group size and preferences. Our team will contact you with specific vehicle information before your trip.</p>
                        </div>
                    <?php endif; ?>
                    
                    <div class="text-center mt-4">
                        <a href="booking.php?trip_id=<?php echo $row['trip_id']; ?>" class="btn btn-primary">Book Now</a>
                    </div>
                </div>
            </div>
        </div>
    </div>


            </div>
            
            <div class="col-lg-4">
                <div class="sidebar">
                    <!-- Package Booking Card -->
                    <div class="package-booking" data-aos="fade-up" data-aos-delay="200">
                        <h3>Book This Package</h3>
                        <div class="price">$<?php echo htmlspecialchars($row['budget']); ?></div>
                        <p>Per person for <?php echo htmlspecialchars($row['duration_days']); ?> days</p>
                        <a href="booking.php?trip_id=<?php echo $row['trip_id']; ?>" class="btn btn-block">Book Now</a>
                    </div>
                    
                    <!-- Author Bio -->
                    <div class="author-bio" data-aos="fade-up" data-aos-delay="300">
                        <img src="img/ubaid.jpg" alt="Author">
                        <h3>Ubaidullah</h3>
                        <p>Travel Expert</p>
                        <p>Conset elitr erat vero dolor ipsum et diam, eos dolor lorem, ipsum sit no ut est ipsum erat kasd amet elitr</p>
                        <div class="social-links">
                            <a href=""><i class="fab fa-facebook-f"></i></a>
                            <a href=""><i class="fab fa-twitter"></i></a>
                            <a href=""><i class="fab fa-linkedin-in"></i></a>
                            <a href=""><i class="fab fa-instagram"></i></a>
                            <a href=""><i class="fab fa-youtube"></i></a>
                        </div>
                    </div>
                    
                    <!-- Search Widget -->
                    <div class="search-widget" data-aos="fade-up" data-aos-delay="400">
                        <h4>Search</h4>
                        <div class="input-group">
                            <input type="text" class="form-control" placeholder="Keyword">
                            <button class="btn btn-primary" type="button"><i class="fas fa-search"></i></button>
                        </div>
                    </div>
                    
                    <!-- Recent Posts -->
                    <div class="recent-posts" data-aos="fade-up" data-aos-delay="500">
                        <h4>Recent Posts</h4>
                        <div class="recent-post-item">
                            <img src="img/blog-100x100.jpg" alt="Recent Post">
                            <div class="recent-post-item-content">
                                <h6>Diam lorem dolore justo eirmod lorem dolore</h6>
                                <small>Jan 01, 2023</small>
                            </div>
                        </div>
                        <div class="recent-post-item">
                            <img src="img/blog-100x100.jpg" alt="Recent Post">
                            <div class="recent-post-item-content">
                                <h6>Diam lorem dolore justo eirmod lorem dolore</h6>
                                <small>Jan 01, 2023</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Package Details End -->
    
    <!-- Footer Start -->
    <footer>
        <div class="container">
            <div class="row g-5">
                <div class="col-lg-3 col-md-6">
                    <a href="index.php" class="footer-logo">Expense<span>Voyage</span></a>
                    <div class="footer-about mt-4">
                        <p>Discover the world with ExpenseVoyage - Premium travel experiences tailored to your desires.</p>
                        <div class="footer-social mt-4">
                            <a href=""><i class="fab fa-twitter"></i></a>
                            <a href=""><i class="fab fa-facebook-f"></i></a>
                            <a href=""><i class="fab fa-instagram"></i></a>
                            <a href=""><i class="fab fa-linkedin-in"></i></a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <h5 class="footer-title">Our Services</h5>
                    <div class="footer-links">
                        <a href="about.php">About Us</a>
                        <a href="destination.php">Destinations</a>
                        <a href="service.php">Services</a>
                        <a href="package.php">Packages</a>
                        <a href="guide.php">Travel Guides</a>
                        <a href="blog.php">Blog</a>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <h5 class="footer-title">Quick Links</h5>
                    <div class="footer-links">
                        <a href="">Contact Us</a>
                        <a href="">Terms & Conditions</a>
                        <a href="">Privacy Policy</a>
                        <a href="">FAQs</a>
                        <a href="">Support</a>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <h5 class="footer-title">Contact Us</h5>
                    <div class="footer-contact">
                        <p><i class="fas fa-map-marker-alt"></i> Aptech Def Hyderabad Sindh Pakistan</p>
                        <p><i class="fas fa-phone-alt"></i> +92 3188 893 8630</p>
                        <p><i class="fas fa-envelope"></i> ubaidsoomro505@gmail.com</p>
                    </div>
                    <h5 class="footer-title mt-4">Newsletter</h5>
                    <div class="footer-newsletter">
                        <form>
                            <input type="text" class="form-control" placeholder="Your Email">
                            <button class="btn btn-primary w-100" type="submit">Subscribe</button>
                        </form>
                    </div>
                </div>
            </div>
            <div class="footer-bottom">
                <div class="row">
                    <div class="col-md-6 text-md-start text-center">
                        <p>&copy; <?php echo date('Y'); ?> ExpenseVoyage. All Rights Reserved.</p>
                    </div>
                    <div class="col-md-6 text-md-end text-center">
                        <p>Designed with <i class="fas fa-heart text-danger"></i> by ExpenseVoyage Team</p>
                    </div>
                </div>
            </div>
        </div>
    </footer>
    <!-- Footer End -->
    
    <!-- Back to Top -->
    <a href="#" class="back-to-top"><i class="fas fa-arrow-up"></i></a>
    
    <!-- JavaScript Libraries -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="lib/easing/easing.min.js"></script>
    <script src="lib/owlcarousel/owl.carousel.min.js"></script>
    <script src="lib/tempusdominus/js/moment.min.js"></script>
    <script src="lib/tempusdominus/js/moment-timezone.min.js"></script>
    <script src="lib/tempusdominus/js/tempusdominus-bootstrap-4.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.js"></script>
    
    <!-- Template Javascript -->
    <script>
        // Initialize AOS
        AOS.init({
            duration: 1000,
            once: true
        });
        
        // Initialize particles.js for animated background
        particlesJS('particles-js', {
            particles: {
                number: {
                    value: 80,
                    density: {
                        enable: true,
                        value_area: 800
                    }
                },
                color: {
                    value: '#4cc9f0'
                },
                shape: {
                    type: 'circle'
                },
                opacity: {
                    value: 0.5,
                    random: true
                },
                size: {
                    value: 3,
                    random: true
                },
                line_linked: {
                    enable: true,
                    distance: 150,
                    color: '#4361ee',
                    opacity: 0.4,
                    width: 1
                },
                move: {
                    enable: true,
                    speed: 2,
                    direction: 'none',
                    random: true,
                    straight: false,
                    out_mode: 'out',
                    bounce: false
                }
            },
            interactivity: {
                detect_on: 'canvas',
                events: {
                    onhover: {
                        enable: true,
                        mode: 'grab'
                    },
                    onclick: {
                        enable: true,
                        mode: 'push'
                    },
                    resize: true
                },
                modes: {
                    grab: {
                        distance: 140,
                        line_linked: {
                            opacity: 1
                        }
                    },
                    push: {
                        particles_nb: 4
                    }
                }
            },
            retina_detect: true
        });
        
        // Back to top button
        $(window).scroll(function() {
            if ($(this).scrollTop() > 300) {
                $('.back-to-top').addClass('show');
            } else {
                $('.back-to-top').removeClass('show');
            }
        });
        
        $('.back-to-top').click(function() {
            $('html, body').animate({scrollTop: 0}, 800);
            return false;
        });
    </script>
</body>
</html>