<?php
session_start();
include 'admin/config.php';
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Check if the user is logged in
if (!isset($_SESSION['email'])) {
    // Store the current page in a session variable to redirect after login
    $_SESSION['redirect_after_login'] = 'blog.php'; // Redirect to this page after login
    header("Location: login/account.php"); // Redirect to login page
    exit(); // Stop further execution until the user logs in
}

// Set up pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$perPage = 6; // Number of blogs per page
$offset = ($page - 1) * $perPage;

// Get total number of blogs for pagination
$countQuery = "SELECT COUNT(*) as total FROM blog";
$countResult = $con->query($countQuery);
$totalBlogs = $countResult->fetch_assoc()['total'];
$totalPages = ceil($totalBlogs / $perPage);

// Fetch destination values from the 'trips' table
$query = "SELECT DISTINCT destination FROM trips";
$result = $con->query($query);
$destinations = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $destinations[] = $row['destination'];
    }
}

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
    <title>Blog - ExpenseVoyage</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta content="Premium Travel Experience" name="keywords">
    <meta content="Discover the world with ExpenseVoyage - Read our latest travel stories and tips" name="description">
    
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
            background: linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.6)), url('img/blog-header.jpg');
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
        
        /* Blog Section */
        .blog-section {
            background: white;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
            margin: 0 auto;
            max-width: calc(100% - 40px);
            padding: 60px 40px;
        }
        
        .blog-item {
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
            height: 100%;
            position: relative;
            border: 1px solid rgba(67, 97, 238, 0.1);
        }
        
        .blog-item::before {
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
        
        .blog-item:hover::before {
            transform: scaleX(1);
        }
        
        .blog-item:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
        }
        
        .blog-img {
            position: relative;
            height: 220px;
            overflow: hidden;
        }
        
        .blog-img img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: all 0.5s ease;
        }
        
        .blog-item:hover .blog-img img {
            transform: scale(1.1);
        }
        
        .blog-date {
            position: absolute;
            top: 20px;
            left: 20px;
            background: var(--gradient);
            color: white;
            width: 60px;
            height: 60px;
            border-radius: 10px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }
        
        .blog-date h6 {
            margin: 0;
            font-size: 1.2rem;
            line-height: 1;
        }
        
        .blog-date small {
            font-size: 0.8rem;
            text-transform: uppercase;
        }
        
        .blog-content {
            padding: 25px;
        }
        
        .blog-meta {
            display: flex;
            margin-bottom: 15px;
        }
        
        .blog-meta a {
            color: #6c757d;
            font-size: 0.9rem;
            text-decoration: none;
            margin-right: 15px;
            transition: all 0.3s ease;
        }
        
        .blog-meta a:hover {
            color: var(--primary);
        }
        
        .blog-title {
            font-size: 1.2rem;
            font-weight: 700;
            margin-bottom: 15px;
            display: block;
            color: var(--dark);
            text-decoration: none;
            transition: all 0.3s ease;
        }
        
        .blog-title:hover {
            color: var(--primary);
        }
        
        /* Blog Categories */
        .blog-categories {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
            padding: 30px;
            margin-bottom: 30px;
        }
        
        .blog-categories h3 {
            font-size: 1.3rem;
            margin-bottom: 20px;
            position: relative;
            padding-bottom: 10px;
        }
        
        .blog-categories h3::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 40px;
            height: 2px;
            background: var(--gradient);
        }
        
        .category-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        
        .category-list li {
            margin-bottom: 10px;
            padding-bottom: 10px;
            border-bottom: 1px solid #f1f1f1;
        }
        
        .category-list li:last-child {
            margin-bottom: 0;
            padding-bottom: 0;
            border-bottom: none;
        }
        
        .category-list a {
            color: var(--dark);
            text-decoration: none;
            display: flex;
            justify-content: space-between;
            transition: all 0.3s ease;
        }
        
        .category-list a:hover {
            color: var(--primary);
            padding-left: 5px;
        }
        
        .category-count {
            background: #f8f9fa;
            color: var(--primary);
            border-radius: 20px;
            padding: 2px 10px;
            font-size: 0.8rem;
            font-weight: 600;
        }
        
        /* Recent Posts */
        .recent-posts {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
            padding: 30px;
        }
        
        .recent-posts h3 {
            font-size: 1.3rem;
            margin-bottom: 20px;
            position: relative;
            padding-bottom: 10px;
        }
        
        .recent-posts h3::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 40px;
            height: 2px;
            background: var(--gradient);
        }
        
        .recent-post-item {
            display: flex;
            margin-bottom: 20px;
            padding-bottom: 20px;
            border-bottom: 1px solid #f1f1f1;
        }
        
        .recent-post-item:last-child {
            margin-bottom: 0;
            padding-bottom: 0;
            border-bottom: none;
        }
        
        .recent-post-img {
            width: 80px;
            height: 80px;
            border-radius: 10px;
            overflow: hidden;
            margin-right: 15px;
            flex-shrink: 0;
        }
        
        .recent-post-img img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: all 0.3s ease;
        }
        
        .recent-post-item:hover .recent-post-img img {
            transform: scale(1.1);
        }
        
        .recent-post-content {
            flex-grow: 1;
        }
        
        .recent-post-content h5 {
            font-size: 1rem;
            margin-bottom: 5px;
            line-height: 1.4;
        }
        
        .recent-post-content h5 a {
            color: var(--dark);
            text-decoration: none;
            transition: all 0.3s ease;
        }
        
        .recent-post-content h5 a:hover {
            color: var(--primary);
        }
        
        .recent-post-date {
            font-size: 0.8rem;
            color: #6c757d;
        }
        
        /* Search Widget */
        .search-widget {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
            padding: 30px;
            margin-bottom: 30px;
        }
        
        .search-widget h3 {
            font-size: 1.3rem;
            margin-bottom: 20px;
            position: relative;
            padding-bottom: 10px;
        }
        
        .search-widget h3::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 40px;
            height: 2px;
            background: var(--gradient);
        }
        
        .search-widget .form-control {
            border: 1px solid #e9ecef;
            border-radius: 50px;
            padding: 12px 25px;
            font-weight: 500;
        }
        
        .search-widget .form-control:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 0.2rem rgba(67, 97, 238, 0.25);
        }
        
        .search-widget .btn {
            width: 100%;
            border-radius: 50px;
            margin-top: 10px;
        }
        
        /* Pagination */
        .pagination {
            margin-top: 30px;
        }
        
        .pagination .page-link {
            color: var(--primary);
            border-radius: 50%;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 5px;
            border: 1px solid #e9ecef;
            transition: all 0.3s ease;
        }
        
        .pagination .page-link:hover {
            background-color: var(--primary);
            color: white;
            border-color: var(--primary);
        }
        
        .pagination .page-item.active .page-link {
            background-color: var(--primary);
            border-color: var(--primary);
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
        
        /* Not Found */
        .not-found {
            width: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
            padding: 50px 0;
        }
        
        .not-found h2 {
            margin-top: 20px;
            color: var(--dark);
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
                            <a href="blog.php" class="dropdown-item active">Blog</a>
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
                <h1 class="animate__animated animate__fadeInDown">Our Blog</h1>
                <nav aria-label="breadcrumb" class="animate__animated animate__fadeInUp">
                    <ol class="breadcrumb justify-content-center">
                        <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                        <li class="breadcrumb-item active">Blog</li>
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
                                foreach ($destinations as $destination) {
                                    echo '<option value="' . htmlspecialchars($destination) . '">';
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
    
    <!-- Blog Start -->
    <section class="blog-section">
        <div class="container">
            <div class="row">
                <div class="col-lg-8">
                    <div class="text-center mb-5">
                        <h6 class="text-primary text-uppercase">Our Blog</h6>
                        <h1 class="section-title">Latest Travel Stories</h1>
                    </div>
                    <div class="row g-4">
                        <?php
                        // Check if the created_at column exists in the blog table
                        $checkColumn = mysqli_query($con, "SHOW COLUMNS FROM blog LIKE 'created_at'");
                        $columnExists = mysqli_num_rows($checkColumn) > 0;
                        
                        // Modify the query based on whether created_at column exists
                        if ($columnExists) {
                            $sql = "SELECT * FROM blog ORDER BY created_at DESC LIMIT $offset, $perPage";
                        } else {
                            $sql = "SELECT * FROM blog LIMIT $offset, $perPage";
                        }
                        
                        $result = mysqli_query($con, $sql);
                        $num_rows = mysqli_num_rows($result);
                        
                        if ($num_rows > 0) {
                            while ($row = mysqli_fetch_assoc($result)) {
                                $blog_id =  $row['blog_id'];
                                $image = $row['blog_image'];
                                $title = $row['blog_title'];
                                
                                // Handle the date safely
                                if ($columnExists && isset($row['created_at']) && !empty($row['created_at'])) {
                                    $day = date('d', strtotime($row['created_at']));
                                    $month = date('M', strtotime($row['created_at']));
                                } else {
                                    // Use default values if created_at is not available
                                    $day = '01';
                                    $month = 'Jan';
                                }
                                
                                echo '<div class="col-lg-6 col-md-6 animate__animated animate__fadeInUp" style="animation-delay: 0.1s;">
                                    <div class="blog-item">
                                        <div class="blog-img">
                                            <img src="' . $image . '" class="img-fluid" alt="' . htmlspecialchars($title) . '">
                                            <div class="blog-date">
                                                <h6>' . $day . '</h6>
                                                <small>' . $month . '</small>
                                            </div>
                                        </div>
                                        <div class="blog-content">
                                            <div class="blog-meta">
                                                <a href="">Admin</a>
                                                <a href="">Tours & Travel</a>
                                            </div>
                                            <a href="blog-details.php?id=' . $blog_id . '" class="blog-title">' . htmlspecialchars($title) . '</a>
                                            <a href="blog-details.php?id=' . $blog_id . '" class="btn btn-primary mt-3">Read More</a>
                                        </div>
                                    </div>
                                </div>';
                            }
                        } else {
                            echo '<div class="col-12">
                                <div class="not-found">
                                    <img src="img/not-found.png" alt="" height="190" width="190">
                                    <h2>No Blog Found</h2>
                                </div>
                            </div>';
                        }
                        ?>
                    </div>
                    
                    <?php if ($num_rows > 0): ?>
                    <div class="row">
                        <div class="col-12">
                            <nav aria-label="Page navigation">
                                <ul class="pagination justify-content-center">
                                    <?php
                                    // Previous button
                                    if ($page > 1) {
                                        echo '<li class="page-item">
                                            <a class="page-link" href="?page=' . ($page - 1) . '" aria-label="Previous">
                                                <span aria-hidden="true">&laquo;</span>
                                                <span class="sr-only">Previous</span>
                                            </a>
                                        </li>';
                                    } else {
                                        echo '<li class="page-item disabled">
                                            <a class="page-link" href="#" aria-label="Previous">
                                                <span aria-hidden="true">&laquo;</span>
                                                <span class="sr-only">Previous</span>
                                            </a>
                                        </li>';
                                    }
                                    
                                    // Page numbers
                                    for ($i = 1; $i <= $totalPages; $i++) {
                                        if ($i == $page) {
                                            echo '<li class="page-item active"><a class="page-link" href="#">' . $i . '</a></li>';
                                        } else {
                                            echo '<li class="page-item"><a class="page-link" href="?page=' . $i . '">' . $i . '</a></li>';
                                        }
                                    }
                                    
                                    // Next button
                                    if ($page < $totalPages) {
                                        echo '<li class="page-item">
                                            <a class="page-link" href="?page=' . ($page + 1) . '" aria-label="Next">
                                                <span aria-hidden="true">&raquo;</span>
                                                <span class="sr-only">Next</span>
                                            </a>
                                        </li>';
                                    } else {
                                        echo '<li class="page-item disabled">
                                            <a class="page-link" href="#" aria-label="Next">
                                                <span aria-hidden="true">&raquo;</span>
                                                <span class="sr-only">Next</span>
                                            </a>
                                        </li>';
                                    }
                                    ?>
                                </ul>
                            </nav>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
                
                <div class="col-lg-4">
                    <!-- Search Widget -->
                    <div class="search-widget animate__animated animate__fadeInUp">
                        <h3>Search</h3>
                        <form action="blog.php" method="get">
                            <div class="input-group">
                                <input type="text" class="form-control" placeholder="Search..." name="search">
                                <button class="btn btn-primary" type="submit"><i class="fas fa-search"></i></button>
                            </div>
                        </form>
                    </div>
                    
                    <!-- Categories Widget -->
                    <div class="blog-categories animate__animated animate__fadeInUp">
                        <h3>Categories</h3>
                        <ul class="category-list">
                            <li><a href="#">Adventure Travel <span class="category-count">12</span></a></li>
                            <li><a href="#">Beach Destinations <span class="category-count">8</span></a></li>
                            <li><a href="#">City Breaks <span class="category-count">15</span></a></li>
                            <li><a href="#">Cultural Experiences <span class="category-count">7</span></a></li>
                            <li><a href="#">Food & Cuisine <span class="category-count">9</span></a></li>
                            <li><a href="#">Travel Tips <span class="category-count">14</span></a></li>
                        </ul>
                    </div>
                    
                    <!-- Recent Posts Widget -->
                    <div class="recent-posts animate__animated animate__fadeInUp">
                        <h3>Recent Posts</h3>
                        <?php
                        // Get recent posts for sidebar
                        $recentQuery = "SELECT * FROM blog";
                        if ($columnExists) {
                            $recentQuery .= " ORDER BY created_at DESC LIMIT 3";
                        } else {
                            $recentQuery .= " LIMIT 3";
                        }
                        
                        $recentResult = mysqli_query($con, $recentQuery);
                        
                        if ($recentResult && mysqli_num_rows($recentResult) > 0) {
                            while ($recentRow = mysqli_fetch_assoc($recentResult)) {
                                $recentId = $recentRow['blog_id'];
                                $recentImage = $recentRow['blog_image'];
                                $recentTitle = $recentRow['blog_title'];
                                
                                // Handle date for recent posts
                                if ($columnExists && isset($recentRow['created_at']) && !empty($recentRow['created_at'])) {
                                    $recentDate = date('M d, Y', strtotime($recentRow['created_at']));
                                } else {
                                    $recentDate = 'Recent Post';
                                }
                                
                                echo '<div class="recent-post-item">
                                    <div class="recent-post-img">
                                        <img src="' . $recentImage . '" alt="' . htmlspecialchars($recentTitle) . '">
                                    </div>
                                    <div class="recent-post-content">
                                        <h5><a href="blog-details.php?id=' . $recentId . '">' . htmlspecialchars($recentTitle) . '</a></h5>
                                        <div class="recent-post-date">' . $recentDate . '</div>
                                    </div>
                                </div>';
                            }
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Blog End -->
    
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
                        <p><i class="fas fa-map-marker-alt"></i> Aptech Def Hyderabad Sindh Pakistan</p>
                        <p><i class="fas fa-phone-alt"></i> +92 3188 893 863</p>
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
    </script>
</body>
</html>