<?php
session_start();
include 'admin/config.php';
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Check if user is logged in
if (!isset($_SESSION['email'])) {
    $_SESSION['redirect_after_login'] = 'booking.php?trip_id=' . $_GET['trip_id'];
    header("Location: login/account.php");
    exit();
}

// Fetch the trip data using the trip_id from the URL
if (isset($_GET['trip_id']) && !empty($_GET['trip_id'])) {
    $trip_id = intval($_GET['trip_id']); // Sanitize the input
    // Prepare the SQL query to fetch trip details
    $sql = "SELECT * FROM trips WHERE trip_id = ?";
    $stmt = $con->prepare($sql);
    $stmt->bind_param('i', $trip_id);
    $stmt->execute();
    $result = $stmt->get_result();
    // If trip found, fetch the data
    if ($result->num_rows > 0) {
        $trip = $result->fetch_assoc();
    } else {
        header("Location: package.php"); // Redirect if trip not found
        exit();
    }
} else {
    header("Location: package.php"); // Redirect if trip_id is missing
    exit();
}
?>  
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Book Your Trip - ExpenseVoyage</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta content="Book your dream trip with ExpenseVoyage - Secure and easy booking process" name="description">
    
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
            background: linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.6)), url('img/booking-bg.jpg');
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
        
        /* Booking Form */
        .booking-container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
            padding: 50px;
            margin: 50px auto;
            max-width: 800px;
        }
        
        .booking-header {
            text-align: center;
            margin-bottom: 40px;
        }
        
        .booking-header h2 {
            color: var(--primary);
            font-size: 2.5rem;
            margin-bottom: 10px;
        }
        
        .booking-header p {
            color: #6c757d;
            font-size: 1.1rem;
        }
        
        .trip-summary {
            background: #f8f9fa;
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 30px;
            border-left: 5px solid var(--primary);
        }
        
        .trip-summary h4 {
            color: var(--primary);
            margin-bottom: 15px;
            font-size: 1.3rem;
        }
        
        .trip-summary-item {
            display: flex;
            align-items: center;
            margin-bottom: 10px;
        }
        
        .trip-summary-item i {
            color: var(--primary);
            margin-right: 10px;
            width: 20px;
            text-align: center;
        }
        
        .form-group {
            margin-bottom: 25px;
        }
        
        .form-group label {
            font-weight: 600;
            color: var(--dark);
            margin-bottom: 8px;
            display: block;
        }
        
        .form-control {
            border: 1px solid #e9ecef;
            border-radius: 10px;
            padding: 15px;
            font-size: 1rem;
            transition: all 0.3s ease;
        }
        
        .form-control:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 0.2rem rgba(67, 97, 238, 0.25);
        }
        
        .form-control[readonly] {
            background-color: #f8f9fa;
        }
        
        textarea.form-control {
            min-height: 120px;
            resize: vertical;
        }
        
        .booking-footer {
            text-align: center;
            margin-top: 30px;
        }
        
        .booking-footer p {
            color: #6c757d;
            margin-bottom: 15px;
            font-size: 0.9rem;
        }
        
        /* Success Message */
        .success-message {
            background-color: #d4edda;
            color: #155724;
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 20px;
            text-align: center;
            display: none;
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
            
            .booking-container {
                padding: 30px 20px;
                margin: 30px auto;
            }
        }
        
        @media (max-width: 767px) {
            .page-header h1 {
                font-size: 2rem;
            }
            
            .booking-container {
                padding: 20px;
                margin: 20px 15px;
            }
            
            .booking-header h2 {
                font-size: 2rem;
            }
        }
    </style>
</head>
<body>
    <!-- Animated Background -->
    <div id="particles-js"></div>
    
    <!-- Topbar Section -->
    <div class="topbar d-none d-lg-block">
        <div class="container">
            <div class="row">
                <div class="col-lg-6 text-center text-lg-left">
                    <p class="mb-0"><i class="fa fa-envelope mr-2 text-primary"></i>ubaidsoomro505@gmail.com | <i class="fa fa-phone-alt mr-2 text-primary"></i>+92 3188 893 863</p>
                </div>
                <div class="col-lg-6 text-center text-lg-right">
                    <div class="d-inline-flex align-items-center">
                        <a class="text-primary px-3" href=""><i class="fab fa-facebook-f"></i></a>
                        <a class="text-primary px-3" href=""><i class="fab fa-twitter"></i></a>
                        <a class="text-primary px-3" href=""><i class="fab fa-linkedin-in"></i></a>
                        <a class="text-primary px-3" href=""><i class="fab fa-instagram"></i></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Topbar End -->
    
    <!-- Navbar Section -->
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
                <h1 class="animate__animated animate__fadeInDown">Book Your Trip</h1>
                <nav aria-label="breadcrumb" class="animate__animated animate__fadeInUp">
                    <ol class="breadcrumb justify-content-center">
                        <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                        <li class="breadcrumb-item"><a href="package.php">Packages</a></li>
                        <li class="breadcrumb-item active">Booking</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
    <!-- Page Header End -->
    
    <!-- Booking Form Section -->
    <div class="container">
        <div class="booking-container animate__animated animate__fadeInUp">
            <div class="booking-header">
                <h2>Complete Your Booking</h2>
                <p>Fill in your details to book your dream trip</p>
            </div>
            
            <div class="success-message" id="successMessage">
                <i class="fas fa-check-circle me-2"></i>
                Your booking has been submitted successfully! Redirecting to payment...
            </div>
            
            <form method="GET" action="payment.php" id="bookingForm">
                <input type="hidden" name="trip_id" value="<?php echo htmlspecialchars($trip['trip_id']); ?>">
                
                <!-- Trip Summary -->
                <div class="trip-summary">
                    <h4><i class="fas fa-info-circle me-2"></i>Trip Details</h4>
                    <div class="trip-summary-item">
                        <i class="fas fa-map-marker-alt"></i>
                        <span><strong>Destination:</strong> <?php echo htmlspecialchars($trip['destination']); ?></span>
                    </div>
                    <div class="trip-summary-item">
                        <i class="fas fa-calendar-alt"></i>
                        <span><strong>Duration:</strong> <?php echo htmlspecialchars($trip['duration_days']); ?> days</span>
                    </div>
                    <div class="trip-summary-item">
                        <i class="fas fa-users"></i>
                        <span><strong>Persons:</strong> <?php echo htmlspecialchars($trip['persons']); ?></span>
                    </div>
                    <div class="trip-summary-item">
                        <i class="fas fa-dollar-sign"></i>
                        <span><strong>Price:</strong> $<?php echo htmlspecialchars($trip['budget']); ?> per person</span>
                    </div>
                </div>
                
                <!-- User Details -->
                <div class="form-group">
                    <label for="name">Full Name</label>
                    <input type="text" name="name" id="name" class="form-control" placeholder="Enter your full name" required>
                </div>
                
                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" name="email" id="email" class="form-control" placeholder="Enter your email" required>
                </div>
                
                <div class="form-group">
                    <label for="phone">Phone Number</label>
                    <input type="tel" name="phone" id="phone" class="form-control" placeholder="Enter your phone number" required>
                </div>
                
                <div class="form-group">
                    <label for="start_date">Preferred Start Date</label>
                    <input type="date" name="start_date" id="start_date" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label for="travelers">Number of Travelers</label>
                    <select name="travelers" id="travelers" class="form-control" required>
                        <option value="">Select number of travelers</option>
                        <option value="1">1 Person</option>
                        <option value="2">2 Persons</option>
                        <option value="3">3 Persons</option>
                        <option value="4">4 Persons</option>
                        <option value="5">5 Persons</option>
                        <option value="6">6+ Persons</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="message">Special Requests</label>
                    <textarea name="message" id="message" class="form-control" placeholder="Any special requests or messages? (Optional)"></textarea>
                </div>
                
                <div class="booking-footer">
                    <p>By clicking "Book Now", you agree to our Terms and Conditions.</p>
                    <button type="submit" class="btn btn-primary">Book Now</button>
                </div>
            </form>
        </div>
    </div>
    <!-- Booking Form End -->
    
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
        
        // Form submission handling
        $('#bookingForm').on('submit', function(e) {
            e.preventDefault();
            
            // Show success message
            $('#successMessage').slideDown();
            
            // Disable submit button to prevent multiple submissions
            $('button[type="submit"]').prop('disabled', true);
            
            // Redirect to payment page after a short delay
            setTimeout(function() {
                window.location.href = 'payment.php?' + $(this).serialize();
            }.bind(this), 1500);
        });
        
        // Set minimum date for start date to today
        var today = new Date().toISOString().split('T')[0];
        document.getElementById('start_date').setAttribute('min', today);
    </script>
</body>
</html>