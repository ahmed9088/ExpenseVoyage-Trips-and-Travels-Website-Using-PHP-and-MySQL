<?php
session_start();
include 'admin/config.php';
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Check if the user is logged in
if (!isset($_SESSION['email'])) {
    // Store the current page in a session variable to redirect after login
    $_SESSION['redirect_after_login'] = 'guide.php'; // Redirect to this page after login
    header("Location: login/account.php"); // Redirect to login page
    exit(); // Stop further execution until the user logs in
}

// Fetch destination values from the 'trips' table
$query = "SELECT DISTINCT destination FROM trips";
$result = $con->query($query);
$destinations = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $destinations[] = $row['destination'];
    }
}

// Fetch review data from the 'review' table
$reviewQuery = "SELECT * FROM review";
$reviewResult = mysqli_query($con, $reviewQuery);
if (!$reviewResult) {
    die("Query failed: " . mysqli_error($con));
}

// Fetch agent data from the 'agent' table
$agentQuery = "SELECT id, a_name, a_profetion, a_image, date_time FROM agent";
$agentResult = mysqli_query($con, $agentQuery);
if (!$agentResult) {
    die("Query failed: " . mysqli_error($con));
}

// Close the database connection
mysqli_close($con);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Travel Guides - ExpenseVoyage</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta content="Premium Travel Guides by ExpenseVoyage" name="keywords">
    <meta content="Meet our expert travel guides at ExpenseVoyage - Your companions for unforgettable journeys" name="description">
    
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
            background: linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.6)), url('img/guides-header.jpg');
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
        
        /* Team/Guides Section */
        .team {
            background: white;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
            margin: 0 auto;
            max-width: calc(100% - 40px);
        }
        
        .team-item {
            background: white;
            border-radius: 15px;
            padding: 40px 30px;
            text-align: center;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
            height: 100%;
            position: relative;
            overflow: hidden;
            z-index: 1;
            border: 1px solid rgba(67, 97, 238, 0.1);
        }
        
        .team-item::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 5px;
            background: var(--gradient);
            z-index: -1;
            transform: scaleX(0);
            transform-origin: left;
            transition: transform 0.3s ease;
        }
        
        .team-item:hover::before {
            transform: scaleX(1);
        }
        
        .team-item:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
        }
        
        .team-img {
            width: 100px;
            height: 100px;
            background: var(--gradient);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 25px;
            color: white;
            font-size: 2.2rem;
            transition: all 0.3s ease;
            box-shadow: 0 5px 15px rgba(67, 97, 238, 0.3);
            overflow: hidden;
        }
        
        .team-img img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        .team-item:hover .team-img {
            transform: scale(1.1) rotate(5deg);
            box-shadow: 0 10px 25px rgba(67, 97, 238, 0.4);
        }
        
        .team-info h5 {
            font-weight: 700;
            margin-bottom: 15px;
            font-size: 1.3rem;
            color: var(--dark);
        }
        
        .team-info p {
            color: #6c757d;
            margin: 0;
            line-height: 1.6;
        }
        
        .team-social {
            margin-top: 20px;
        }
        
        .team-social a {
            width: 36px;
            height: 36px;
            background: rgba(67, 97, 238, 0.1);
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin: 0 5px;
            color: var(--primary);
            transition: all 0.3s ease;
        }
        
        .team-social a:hover {
            background: var(--primary);
            color: white;
            transform: translateY(-3px);
        }
        
        /* Guide Process */
        .guide-process {
            margin-top: 80px;
        }
        
        .process-step {
            text-align: center;
            position: relative;
            padding: 0 15px;
        }
        
        .process-step:not(:last-child)::after {
            content: '';
            position: absolute;
            top: 50px;
            right: -30px;
            width: 60px;
            height: 2px;
            background: var(--gradient);
            z-index: 1;
        }
        
        .process-icon {
            width: 100px;
            height: 100px;
            background: var(--gradient);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            color: white;
            font-size: 2rem;
            position: relative;
            z-index: 2;
            transition: all 0.3s ease;
        }
        
        .process-step:hover .process-icon {
            transform: scale(1.1);
            box-shadow: 0 10px 25px rgba(67, 97, 238, 0.4);
        }
        
        .process-number {
            position: absolute;
            top: -10px;
            right: -10px;
            width: 30px;
            height: 30px;
            background: var(--accent);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 700;
            font-size: 0.9rem;
            box-shadow: 0 3px 10px rgba(76, 201, 240, 0.5);
        }
        
        .process-title {
            font-weight: 700;
            margin-bottom: 10px;
            color: var(--dark);
        }
        
        /* Testimonial Section */
        .testimonial {
            background-color: #f5f7ff;
        }
        
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
            
            .process-step:not(:last-child)::after {
                display: none;
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
    
    <!-- Topbar Start -->
    <div class="container-fluid bg-light py-3 d-none d-lg-block">
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
                    <a href="package.php" class="nav-item nav-link animate__animated animate__fadeInDown">Tour Packages</a>
                    <div class="nav-item dropdown animate__animated animate__fadeInDown">
                        <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">Pages</a>
                        <div class="dropdown-menu m-0">
                            <a href="blog.php" class="dropdown-item">Blog</a>
                            <a href="destination.php" class="dropdown-item">Destination</a>
                            <a href="guide.php" class="dropdown-item active">Travel Guides</a>
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
                <h1 class="animate__animated animate__fadeInDown">Travel Guides</h1>
                <nav aria-label="breadcrumb" class="animate__animated animate__fadeInUp">
                    <ol class="breadcrumb justify-content-center">
                        <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                        <li class="breadcrumb-item active">Travel Guides</li>
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
    
    <!-- Team/Guides Start -->
    <section class="team">
        <div class="container">
            <div class="text-center mb-5">
                <h6 class="text-primary text-uppercase">Travel Experts</h6>
                <h1 class="section-title">Meet Our Guides</h1>
                <p class="lead">Our experienced guides will make your journey unforgettable</p>
            </div>
            
            <div class="row g-4">
                <?php
                if ($agentResult && mysqli_num_rows($agentResult) > 0) {
                    while ($agent = mysqli_fetch_assoc($agentResult)) {
                        $basePath = 'admin/upload/agents/';
                        $imageFilename = htmlspecialchars($agent['a_image']);
                        $imagePath = $basePath . $imageFilename;
                        $agentName = htmlspecialchars($agent['a_name']);
                        $agentProfession = htmlspecialchars($agent['a_profetion']);
                        
                        echo '
                        <div class="col-lg-3 col-md-6 animate__animated animate__fadeInUp" style="animation-delay: 0.1s;">
                            <div class="team-item">
                                <div class="team-img">
                                    <img src="' . $imagePath . '" alt="' . $agentName . '" onerror="this.src=\'img/placeholder.jpg\';">
                                </div>
                                <div class="team-info">
                                    <h5>' . $agentName . '</h5>
                                    <p>' . $agentProfession . '</p>
                                    <div class="team-social">
                                        <a href=""><i class="fab fa-twitter"></i></a>
                                        <a href=""><i class="fab fa-facebook-f"></i></a>
                                        <a href=""><i class="fab fa-instagram"></i></a>
                                        <a href=""><i class="fab fa-linkedin-in"></i></a>
                                    </div>
                                </div>
                            </div>
                        </div>';
                    }
                } else {
                    echo '<div class="col-12 text-center"><p>No guides found.</p></div>';
                }
                ?>
            </div>
            
            <!-- Guide Process -->
            <div class="guide-process">
                <div class="text-center mb-5">
                    <h6 class="text-primary text-uppercase">How It Works</h6>
                    <h1 class="section-title">Our Guide Process</h1>
                </div>
                
                <div class="row g-4">
                    <div class="col-md-3 animate__animated animate__fadeInUp" style="animation-delay: 0.1s;">
                        <div class="process-step">
                            <div class="process-icon">
                                <i class="fas fa-user-check"></i>
                                <div class="process-number">1</div>
                            </div>
                            <h5 class="process-title">Choose Your Guide</h5>
                            <p>Select from our expert guides based on your destination and preferences.</p>
                        </div>
                    </div>
                    
                    <div class="col-md-3 animate__animated animate__fadeInUp" style="animation-delay: 0.2s;">
                        <div class="process-step">
                            <div class="process-icon">
                                <i class="fas fa-calendar-check"></i>
                                <div class="process-number">2</div>
                            </div>
                            <h5 class="process-title">Schedule Your Trip</h5>
                            <p>Work with your guide to create the perfect itinerary for your journey.</p>
                        </div>
                    </div>
                    
                    <div class="col-md-3 animate__animated animate__fadeInUp" style="animation-delay: 0.3s;">
                        <div class="process-step">
                            <div class="process-icon">
                                <i class="fas fa-comments"></i>
                                <div class="process-number">3</div>
                            </div>
                            <h5 class="process-title">Pre-Trip Consultation</h5>
                            <p>Discuss your expectations and get valuable insights before you travel.</p>
                        </div>
                    </div>
                    
                    <div class="col-md-3 animate__animated animate__fadeInUp" style="animation-delay: 0.4s;">
                        <div class="process-step">
                            <div class="process-icon">
                                <i class="fas fa-hiking"></i>
                                <div class="process-number">4</div>
                            </div>
                            <h5 class="process-title">Enjoy Your Journey</h5>
                            <p>Experience your destination with the expertise of a local guide.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Team/Guides End -->
    
    <!-- Call to Action -->
    <div class="container">
        <div class="cta-section animate__animated animate__fadeInUp">
            <div class="cta-content">
                <h2>Ready for an Unforgettable Journey?</h2>
                <p>Book one of our expert travel guides today and discover destinations like never before. Create memories that will last a lifetime.</p>
                <a href="contact.php" class="btn">Book a Guide Now</a>
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
                        <a href="contact.php">Contact Us</a>
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
        
        // Testimonial carousel
        $('.testimonial-carousel').owlCarousel({
            autoplay: true,
            smartSpeed: 1000,
            margin: 30,
            dots: false,
            loop: true,
            nav: true,
            navText: [
                '<i class="fas fa-chevron-left"></i>',
                '<i class="fas fa-chevron-right"></i>'
            ],
            responsive: {
                0: {
                    items: 1
                },
                768: {
                    items: 2
                },
                992: {
                    items: 3
                }
            }
        });
    </script>
</body>
</html>