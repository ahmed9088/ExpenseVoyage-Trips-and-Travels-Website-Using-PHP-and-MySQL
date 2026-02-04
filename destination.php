<?php
session_start();
include 'admin/config.php';
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
// Check if the user is logged in
if (!isset($_SESSION['email'])) {
    // Store the current page in a session variable to redirect after login
    $_SESSION['redirect_after_login'] = 'destination.php'; // Redirect to this page after login
    header("Location: login/account.php"); // Redirect to login page
    exit(); // Stop further execution until the user logs in
}
// Fetch destination values from the 'trips' table
$query = "SELECT DISTINCT destination FROM trips";
$result = $con->query($query);

// Fetch review data
$reviewQuery = "SELECT * FROM review";
$reviewResult = mysqli_query($con, $reviewQuery);
if (!$reviewResult) {
    die("Query failed: " . mysqli_error($con));
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Destination - ExpenseVoyage</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta content="Premium Travel Experience" name="keywords">
    <meta content="Discover amazing destinations with ExpenseVoyage - Your gateway to unforgettable journeys" name="description">
    
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
            width: 10px;
        }
        
        ::-webkit-scrollbar-track {
            background: #f1f1f1;
        }
        
        ::-webkit-scrollbar-thumb {
            background: var(--primary);
            border-radius: 5px;
        }
        
        ::-webkit-scrollbar-thumb:hover {
            background: var(--secondary);
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
        
        /* Page Header */
        .page-header {
            background: linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.6)), url('img/destination-header.jpg');
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
        
        /* Section Styling */
        section {
            padding: 100px 0;
            position: relative;
        }
        
        /* Destination Section */
        .destination-section {
            background: white;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
            margin: 0 auto;
            max-width: calc(100% - 40px);
            padding: 60px 40px;
        }
        
        .destination-item {
            position: relative;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
            height: 350px;
            margin-bottom: 30px;
            transition: all 0.3s ease;
            border: 1px solid rgba(67, 97, 238, 0.1);
        }
        
        .destination-item::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 5px;
            background: var(--gradient);
            transform: scaleX(0);
            transform-origin: left;
            transition: transform 0.3s ease;
        }
        
        .destination-item:hover::before {
            transform: scaleX(1);
        }
        
        .destination-item:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
        }
        
        .destination-item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: all 0.5s ease;
        }
        
        .destination-item:hover img {
            transform: scale(1.1);
        }
        
        .destination-overlay {
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            padding: 30px;
            background: linear-gradient(to top, rgba(0, 0, 0, 0.8), transparent);
            color: white;
            text-decoration: none;
            transition: all 0.3s ease;
        }
        
        .destination-overlay:hover {
            text-decoration: none;
            color: white;
        }
        
        .destination-overlay h5 {
            font-weight: 700;
            margin-bottom: 5px;
            font-size: 1.5rem;
        }
        
        .destination-overlay span {
            font-size: 1rem;
            opacity: 0.9;
        }
        
        /* Destination Filter */
        .destination-filter {
            margin-bottom: 40px;
            text-align: center;
        }
        
        .filter-btn {
            background: #f8f9fa;
            border: 1px solid #e9ecef;
            color: var(--dark);
            padding: 10px 25px;
            border-radius: 50px;
            margin: 0 5px 10px;
            font-weight: 500;
            transition: all 0.3s ease;
            cursor: pointer;
        }
        
        .filter-btn:hover, .filter-btn.active {
            background: var(--gradient);
            color: white;
            border-color: transparent;
        }
        
        /* Testimonial Section */
        .testimonial-item {
            background: white;
            border-radius: 15px;
            padding: 40px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
            margin-bottom: 30px;
            position: relative;
            transition: all 0.3s ease;
        }
        
        .testimonial-item:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
        }
        
        .testimonial-item::before {
            content: '\f10d';
            font-family: 'Font Awesome 5 Free';
            font-weight: 900;
            position: absolute;
            top: 20px;
            left: 20px;
            font-size: 2rem;
            color: rgba(67, 97, 238, 0.1);
        }
        
        .testimonial-text {
            margin-bottom: 20px;
            font-style: italic;
            color: #555;
        }
        
        .testimonial-author {
            display: flex;
            align-items: center;
        }
        
        .testimonial-author img {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            object-fit: cover;
            margin-right: 15px;
            border: 2px solid var(--primary);
        }
        
        .testimonial-author h5 {
            margin: 0;
            font-weight: 700;
        }
        
        .testimonial-author p {
            margin: 0;
            color: #6c757d;
            font-size: 0.9rem;
        }
        
        /* Call to Action */
        .cta-section {
            background: var(--gradient);
            border-radius: 20px;
            padding: 60px 40px;
            color: white;
            text-align: center;
            margin: 80px auto 0;
            max-width: calc(100% - 40px);
            box-shadow: 0 10px 30px rgba(67, 97, 238, 0.3);
            position: relative;
            overflow: hidden;
        }
        
        .cta-section::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, rgba(255,255,255,0) 70%);
            animation: pulse 4s infinite alternate;
        }
        
        @keyframes pulse {
            0% { transform: scale(1); opacity: 0.5; }
            100% { transform: scale(1.1); opacity: 0.2; }
        }
        
        .cta-content {
            position: relative;
            z-index: 2;
        }
        
        .cta-section h2 {
            font-size: 2.5rem;
            margin-bottom: 20px;
            font-weight: 800;
        }
        
        .cta-section p {
            font-size: 1.1rem;
            margin-bottom: 30px;
            max-width: 700px;
            margin-left: auto;
            margin-right: auto;
        }
        
        .cta-section .btn {
            background: white;
            color: var(--primary);
            padding: 15px 40px;
            font-weight: 700;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }
        
        .cta-section .btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
            color: var(--primary);
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
        
        .email-wrapper {
    position: relative;
    width: 300px; /* adjust */
}

.email-wrapper input {
    width: 100%;
    padding: 12px 50px 12px 15px;
    border-radius: 50px;
    border: 1px solid #ccc;
    outline: none;
}

.email-wrapper button {
    position: absolute;
    top: 50%;
    right: 5px;
    transform: translateY(-50%);
    height: 40px;
    width: 40px;
    border-radius: 50%;
    border: none;
    background: #0d6efd;
    color: #fff;
    cursor: pointer;
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
              bottom: 100px;
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
        
        /* Animations */
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        /* Responsive */
        @media (max-width: 991px) {
            .page-header h1 {
                font-size: 2.5rem;
            }
            
            section {
                padding: 70px 0;
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
            
            .cta-section {
                padding: 40px 20px;
            }
            
            .cta-section h2 {
                font-size: 2rem;
            }
        }
    </style>
</head>
<body>
    <!-- Animated Background -->
    <div id="particles-js"></div>
    

    
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
                    <a href="package.php" class="nav-item nav-link animate__animated animate__fadeInDown">Tour Packages</a>
                    <div class="nav-item dropdown animate__animated animate__fadeInDown">
                        <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">Pages</a>
                        <div class="dropdown-menu m-0">
                            <a href="blog.php" class="dropdown-item">Blog</a>
                            <a href="destination.php" class="dropdown-item active">Destination</a>
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
                <h1 class="animate__animated animate__fadeInDown">Destinations</h1>
                <nav aria-label="breadcrumb" class="animate__animated animate__fadeInUp">
                    <ol class="breadcrumb justify-content-center">
                        <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                        <li class="breadcrumb-item active">Destinations</li>
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
                            <input type="text" class="form-control" placeholder="Enter Destination" name="destination" list="destinations">
                            <datalist id="destinations">
                                <?php
                                if ($result) {
                                    while ($row = $result->fetch_assoc()) {
                                        echo '<option value="' . htmlspecialchars($row['destination']) . '">';
                                    }
                                }
                                ?>
                            </datalist>
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
    
    <!-- Destination Start -->
    <section class="destination-section">
        <div class="container">
            <div class="text-center mb-5">
                <h6 class="text-primary text-uppercase">Popular Destinations</h6>
                <h1 class="section-title">Explore Top Destinations</h1>
                <p class="lead">Discover amazing places around the world with our curated travel experiences</p>
            </div>
            
            <div class="destination-filter">
                <button class="filter-btn active" data-filter="all">All</button>
                <button class="filter-btn" data-filter="europe">Europe</button>
                <button class="filter-btn" data-filter="asia">Asia</button>
                <button class="filter-btn" data-filter="america">America</button>
                <button class="filter-btn" data-filter="africa">Africa</button>
                <button class="filter-btn" data-filter="oceania">Oceania</button>
            </div>
            
            <div class="row g-4 destination-container">
                <div class="col-lg-4 col-md-6 animate__animated animate__fadeInUp" style="animation-delay: 0.1s;" data-category="europe">
                    <div class="destination-item">
                        <img src="img/destination-2.jpg" class="img-fluid" alt="United Kingdom">
                        <a href="destination-details.php?country=United Kingdom" class="destination-overlay">
                            <h5>United Kingdom</h5>
                            <span>100+ Cities</span>
                        </a>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 animate__animated animate__fadeInUp" style="animation-delay: 0.2s;" data-category="oceania">
                    <div class="destination-item">
                        <img src="img/destination-3.jpg" class="img-fluid" alt="Australia">
                        <a href="destination-details.php?country=Australia" class="destination-overlay">
                            <h5>Australia</h5>
                            <span>80+ Cities</span>
                        </a>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 animate__animated animate__fadeInUp" style="animation-delay: 0.3s;" data-category="africa">
                    <div class="destination-item">
                        <img src="img/destination-5.jpg" class="img-fluid" alt="South Africa">
                        <a href="destination-details.php?country=South Africa" class="destination-overlay">
                            <h5>South Africa</h5>
                            <span>60+ Cities</span>
                        </a>
                    </div>
                </div>
                
                <?php
                // Fetch data from the city table
                $query = "SELECT country_name, city_name, cover_image FROM city";
                $result = $con->query($query);
                
                // Display the data
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        // Determine category based on country
                        $category = 'all'; // Default category
                        $countryName = strtolower($row['country_name']);
                        
                        if (strpos($countryName, 'europe') !== false || in_array($row['country_name'], ['United Kingdom', 'France', 'Germany', 'Italy', 'Spain'])) {
                            $category = 'europe';
                        } elseif (strpos($countryName, 'asia') !== false || in_array($row['country_name'], ['China', 'Japan', 'India', 'Thailand', 'Singapore'])) {
                            $category = 'asia';
                        } elseif (strpos($countryName, 'america') !== false || in_array($row['country_name'], ['United States', 'Canada', 'Mexico', 'Brazil', 'Argentina'])) {
                            $category = 'america';
                        } elseif (strpos($countryName, 'africa') !== false || in_array($row['country_name'], ['South Africa', 'Egypt', 'Kenya', 'Morocco', 'Tanzania'])) {
                            $category = 'africa';
                        } elseif (strpos($countryName, 'australia') !== false || in_array($row['country_name'], ['Australia', 'New Zealand', 'Fiji'])) {
                            $category = 'oceania';
                        }
                        
                        echo '
                        <div class="col-lg-4 col-md-6 animate__animated animate__fadeInUp" style="animation-delay: 0.1s;" data-category="'.$category.'">
                            <div class="destination-item">
                                <img class="img-fluid" src="'.$row['cover_image'].'" style="height:350px; object-fit:cover;" alt="'.htmlspecialchars($row['country_name']).'">
                                <a href="destination-details.php?country='.urlencode($row['country_name']).'&city='.urlencode($row['city_name']).'" class="destination-overlay">
                                    <h5>'.htmlspecialchars($row['country_name']).'</h5>
                                    <span>City: '.htmlspecialchars($row['city_name']).'</span>
                                </a>
                            </div>
                        </div>';
                    }
                }
                ?>
            </div>
        </div>
    </section>
    <!-- Destination End -->
    
    <!-- Call to Action -->
    <div class="container">
        <div class="cta-section animate__animated animate__fadeInUp">
            <div class="cta-content">
                <h2>Ready to Start Your Journey?</h2>
                <p>Let our travel experts create the perfect itinerary tailored just for you. Experience the world like never before with ExpenseVoyage.</p>
                <a href="contact.php" class="btn">Contact Us Today</a>
            </div>
        </div>
    </div>
    
    <!-- Testimonial Start -->
    <section class="testimonial">
        <div class="container">
            <div class="text-center mb-5">
                <h6 class="text-primary text-uppercase">Testimonials</h6>
                <h1 class="section-title">What Our Clients Say</h1>
            </div>
            <div class="row">
                <?php
                if ($reviewResult && mysqli_num_rows($reviewResult) > 0) {
                    while ($rows = mysqli_fetch_assoc($reviewResult)) {
                        $reviewer_image = htmlspecialchars($rows['image']);
                        $reviewer_name = htmlspecialchars($rows['username']);
                        echo '<div class="col-md-4 animate__animated animate__fadeInUp" style="animation-delay: 0.1s;">
                            <div class="testimonial-item">
                                <p class="testimonial-text">"' . htmlspecialchars($rows['usermessage']) . '"</p>
                                <div class="testimonial-author">
                                    <img src="img/reviewerimages/' . $reviewer_image . '" alt="' . $reviewer_name . '">
                                    <div>
                                        <h5>' . $reviewer_name . '</h5>
                                        <p>Traveler</p>
                                    </div>
                                </div>
                            </div>
                        </div>';
                    }
                } else {
                    echo '<div class="col-12 text-center"><p>No reviews found.</p></div>';
                }
                ?>
            </div>
        </div>
    </section>
    <!-- Testimonial End -->
    
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
                         <p><i class="fas fa-map-marker-alt"></i> Quaid-e-Awam University Nawabshah</p>
                        <p><i class="fas fa-phone-alt"></i> +92 3188 893 8630</p>
                        <p><i class="fas fa-envelope"></i> alizamemonnn@gmail.com</p>
                    </div>
                    <h5 class="footer-title mt-4">Newsletter</h5>
                    <div class="footer-newsletter">
    <form class="email-wrapper">
        <input type="email" placeholder="Your Email" required>
        <button type="submit">→</button>
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
                        <p>Designed by Aliza 23BSCS84 <i class="fas fa-heart text-danger"></i></p>                    </div>
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
    
    <!-- Template Javascript -->
    <script>
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
        
        // Destination filtering functionality
        $(document).ready(function() {
            $('.filter-btn').click(function() {
                // Remove active class from all buttons
                $('.filter-btn').removeClass('active');
                
                // Add active class to clicked button
                $(this).addClass('active');
                
                // Get filter value
                var filterValue = $(this).data('filter');
                
                // Show/hide destinations based on filter
                if (filterValue === 'all') {
                    $('.destination-container > div').show();
                } else {
                    $('.destination-container > div').hide();
                    $('.destination-container > div[data-category="' + filterValue + '"]').show();
                }
            });
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
        
        // Animation on scroll
        $(window).on('load', function() {
            $('.animate__animated').css('opacity', '0');
            
            $(window).scroll(function() {
                var windowBottom = $(this).scrollTop() + $(this).innerHeight();
                
                $('.animate__animated').each(function() {
                    var objectBottom = $(this).offset().top + $(this).outerHeight() / 2;
                    
                    if (objectBottom < windowBottom) {
                        $(this).animate({'opacity': '1'}, 500);
                    }
                });
            }).scroll();
        });
    </script>
</body>
</html>