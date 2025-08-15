<?php
session_start();
include 'admin/config.php';
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Check if user is logged in
if (!isset($_SESSION['email'])) {
    $_SESSION['redirect_after_login'] = 'index.php';
    header("Location: login/account.php");
    exit();
}

// Handle review submission
if (isset($_POST['sendreview'])) {
    if (isset($_SESSION['email'])) {
        $useremail = mysqli_real_escape_string($con, $_POST['useremail']);
        $usermessage = mysqli_real_escape_string($con, $_POST['usermessage']);
        $userid = $_SESSION['userid'];
        
        // Fetch user's name using prepared statement
        $stmt = $con->prepare("SELECT name FROM user WHERE id = ?");
        $stmt->bind_param("i", $userid);
        $stmt->execute();
        $result = $stmt->get_result();
        $userRow = $result->fetch_assoc();
        $username = $userRow['name'];
        $stmt->close();
        
        // Handle file upload
        if (isset($_FILES['profile']) && $_FILES['profile']['error'] == UPLOAD_ERR_OK) {
            $fileTmpPath = $_FILES['profile']['tmp_name'];
            $fileName = $_FILES['profile']['name'];
            $fileSize = $_FILES['profile']['size'];
            $fileType = $_FILES['profile']['type'];
            $fileNameCmps = explode(".", $fileName);
            $fileExtension = strtolower(end($fileNameCmps));
            
            $allowedfileExtensions = ['jpg', 'gif', 'png', 'jpeg', 'pdf'];
            $maxFileSize = 5 * 1024 * 1024; // 5MB
            
            if (in_array($fileExtension, $allowedfileExtensions) && $fileSize <= $maxFileSize) {
                $newFileName = uniqid() . '.' . $fileExtension;
                $uploadFileDir = 'img/reviewerimages/';
                
                // Create directory if it doesn't exist
                if (!file_exists($uploadFileDir)) {
                    mkdir($uploadFileDir, 0755, true);
                }
                
                $dest_path = $uploadFileDir . $newFileName;
                
                if (move_uploaded_file($fileTmpPath, $dest_path)) {
                    // Insert using prepared statement
                    $stmt = $con->prepare("INSERT INTO review (userid, email, image, usermessage, date_time, username) 
                                         VALUES (?, ?, ?, ?, NOW(), ?)");
                    $stmt->bind_param("issss", $userid, $useremail, $newFileName, $usermessage, $username);
                    $stmt->execute();
                    $stmt->close();
                    
                    $_SESSION['review_submitted'] = true;
                    header("Location: index.php");
                    exit();
                }
            }
        }
    }
}

// Check for review submission success
$reviewSuccess = false;
if (isset($_SESSION['review_submitted'])) {
    $reviewSuccess = true;
    unset($_SESSION['review_submitted']);
}

// Fetch trip data
$query = "SELECT * FROM trips";
$result = mysqli_query($con, $query);

if (!$result) {
    die("Query failed: " . mysqli_error($con));
}

// Fetch agent data
$agentQuery = "SELECT * FROM agent";
$agentResult = mysqli_query($con, $agentQuery);

if (!$agentResult) {
    die("Query failed: " . mysqli_error($con));
}

// Fetch review data
$reviewQuery = "SELECT * FROM review";
$reviewResult = mysqli_query($con, $reviewQuery);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>ExpenseVoyage - Premium Travel Experience</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta content="Premium Travel Experience" name="keywords">
    <meta content="Discover the world with ExpenseVoyage - Premium travel experiences tailored to your desires" name="description">
    
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
        
        /* Hero Section */
        .hero {
            height: 100vh;
            min-height: 600px;
            position: relative;
            overflow: hidden;
            display: flex;
            align-items: center;
        }
        
        .hero-content {
            position: relative;
            z-index: 2;
            text-align: center;
            color: white;
            max-width: 800px;
            margin: 0 auto;
            padding: 0 20px;
        }
        
        .hero-content h1 {
            font-size: 3.5rem;
            font-weight: 800;
            margin-bottom: 1.5rem;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
        }
        
        .hero-content p {
            font-size: 1.2rem;
            margin-bottom: 2rem;
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.5);
        }
        
        .hero-background {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, rgba(67, 97, 238, 0.8), rgba(63, 55, 201, 0.8)), 
                        url('img/carousel-1.jpg');
            background-size: cover;
            background-position: center;
            z-index: 1;
            animation: backgroundPan 20s infinite alternate;
        }
        
        @keyframes backgroundPan {
            0% { background-position: 0% 50%; }
            100% { background-position: 100% 50%; }
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
        
        .section-bg {
            background: white;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
            padding: 50px;
            margin-bottom: 30px;
        }
        
        /* About Section */
        .about-img {
            position: relative;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
        }
        
        .about-img img {
            transition: all 0.5s ease;
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        .about-img:hover img {
            transform: scale(1.05);
        }
        
        .about-text {
            padding: 40px;
            background: white;
            border-radius: 20px;
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.05);
            height: 100%;
        }
        
        /* Feature Section */
        .feature-item {
            background: white;
            border-radius: 15px;
            padding: 30px;
            text-align: center;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
            height: 100%;
        }
        
        .feature-item:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
        }
        
        .feature-icon {
            width: 80px;
            height: 80px;
            background: var(--gradient);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            color: white;
            font-size: 1.8rem;
        }
        
        /* Destination Section */
        .destination-item {
            position: relative;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            height: 350px;
            margin-bottom: 30px;
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
        }
        
        /* Service Section */
        .service-item {
            background: white;
            border-radius: 15px;
            padding: 40px 30px;
            text-align: center;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
            height: 100%;
        }
        
        .service-item:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
        }
        
        .service-icon {
            width: 80px;
            height: 80px;
            background: var(--gradient);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            color: white;
            font-size: 1.8rem;
        }
        
        /* Trip Package Section */
        .package-item {
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
            height: 100%;
        }
        
        .package-item:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
        }
        
        .package-img {
            height: 220px;
            overflow: hidden;
        }
        
        .package-img img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: all 0.5s ease;
        }
        
        .package-item:hover .package-img img {
            transform: scale(1.1);
        }
        
        .package-content {
            padding: 25px;
        }
        
        .package-meta {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
        }
        
        .package-meta small {
            display: flex;
            align-items: center;
            color: #6c757d;
        }
        
        .package-meta i {
            margin-right: 5px;
            color: var(--primary);
        }
        
        .package-title {
            font-size: 1.2rem;
            font-weight: 700;
            margin-bottom: 15px;
            display: block;
            color: var(--dark);
            text-decoration: none;
            transition: all 0.3s ease;
        }
        
        .package-title:hover {
            color: var(--primary);
            text-decoration: none;
        }
        
        .package-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-top: 1px solid #eee;
            padding-top: 15px;
            margin-top: 15px;
        }
        
        .package-price {
            font-weight: 700;
            color: var(--primary);
            font-size: 1.3rem;
        }
        
        /* Review Section */
        .review-form {
            background: white;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
        }
        
        .review-header {
            background: var(--gradient);
            color: white;
            padding: 25px;
            text-align: center;
        }
        
        .review-header h1 {
            margin: 0;
            font-size: 1.8rem;
        }
        
        .review-body {
            padding: 40px;
        }
        
        .review-body .form-control {
            border: 1px solid #eee;
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 20px;
            font-weight: 500;
        }
        
        .review-body .form-control:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 0.2rem rgba(67, 97, 238, 0.25);
        }
        
        /* Success Message */
        .success-message {
            background-color: #d4edda;
            color: #155724;
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 20px;
            text-align: center;
            animation: fadeIn 0.5s ease-in-out;
        }
        
        /* Team Section */
        .team-item {
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
            height: 100%;
        }
        
        .team-item:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
        }
        
        .team-img {
            height: 250px;
            position: relative;
            overflow: hidden;
        }
        
        .team-img img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: all 0.5s ease;
        }
        
        .team-item:hover .team-img img {
            transform: scale(1.1);
        }
        
        .team-social {
            position: absolute;
            bottom: 20px;
            left: 0;
            width: 100%;
            display: flex;
            justify-content: center;
            opacity: 0;
            transition: all 0.3s ease;
        }
        
        .team-item:hover .team-social {
            opacity: 1;
        }
        
        .team-social a {
            width: 40px;
            height: 40px;
            background: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 5px;
            color: var(--primary);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }
        
        .team-social a:hover {
            background: var(--primary);
            color: white;
            transform: translateY(-5px);
        }
        
        .team-info {
            padding: 25px;
            text-align: center;
        }
        
        .team-info h5 {
            font-weight: 700;
            margin-bottom: 5px;
        }
        
        .team-info p {
            color: #6c757d;
            margin: 0;
        }
        
        /* Testimonial Section */
        .testimonial-item {
            background: white;
            border-radius: 15px;
            padding: 40px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
            margin-bottom: 30px;
            position: relative;
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
        
        /* Blog Section */
        .blog-item {
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
            height: 100%;
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
            text-decoration: none;
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
            .hero-content h1 {
                font-size: 2.5rem;
            }
            
            section {
                padding: 70px 0;
            }
        }
        
        @media (max-width: 767px) {
            .hero-content h1 {
                font-size: 2rem;
            }
            
            .booking {
                margin-top: -50px;
                padding: 20px;
            }
            
            .about-text {
                padding: 30px 20px;
                margin-top: 30px;
            }
            
            .section-bg {
                padding: 30px 20px;
            }
            
            .review-body {
                padding: 30px 20px;
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
                        <p class="mb-0"><i class="fa fa-envelope mr-2 text-primary"></i>memon1ahmed@gmail.com</p>
                        <p class="mb-0 px-3">|</p>
                        <p class="mb-0"><i class="fa fa-phone-alt mr-2 text-primary"></i>+92 3073 762 276</p>
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
                    <a href="index.php" class="nav-item nav-link active animate__animated animate__fadeInDown">Home</a>
                    <a href="about.php" class="nav-item nav-link animate__animated animate__fadeInDown">About</a>
                    <a href="service.php" class="nav-item nav-link animate__animated animate__fadeInDown">Services</a>
                    <a href="package.php" class="nav-item nav-link animate__animated animate__fadeInDown">Tour Packages</a>
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
    
    <!-- Hero Section Start -->
    <div class="hero">
        <div class="hero-background"></div>
        <div class="hero-content animate__animated animate__fadeIn">
            <h1 class="animate__animated animate__fadeInDown">Discover The World With Us</h1>
            <p class="animate__animated animate__fadeInUp">Experience unforgettable journeys to the most breathtaking destinations on Earth</p>
            <a href="package.php" class="btn btn-primary animate__animated animate__zoomIn">Explore Tours</a>
        </div>
    </div>
    <!-- Hero Section End -->
    
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
    
    <!-- About Start -->
    <section class="about">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6 animate__animated animate__fadeInLeft">
                    <div class="about-img">
                        <img src="img/about.jpg" class="img-fluid" alt="About Us">
                    </div>
                </div>
                <div class="col-lg-6 animate__animated animate__fadeInRight">
                    <div class="about-text">
                        <h6 class="text-primary text-uppercase">About Us</h6>
                        <h1 class="section-title">We Provide Premium Travel Experiences</h1>
                        <p>At ExpenseVoyage, we believe that every journey should be unforgettable. As passionate travel enthusiasts, our goal is to help you plan your dream trips with ease and confidence.</p>
                        <p>Whether you're seeking adventure, relaxation, or cultural exploration, we curate personalized itineraries tailored to your preferences and budget.</p>
                        <div class="row g-3 pt-3">
                            <div class="col-6">
                                <img src="img/about-1.jpg" class="img-fluid rounded" alt="Travel">
                            </div>
                            <div class="col-6">
                                <img src="img/about-2.jpg" class="img-fluid rounded" alt="Adventure">
                            </div>
                        </div>
                        <a href="about.php" class="btn btn-primary mt-4">Learn More</a>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- About End -->
    
    <!-- Features Start -->
    <section class="features">
        <div class="container">
            <div class="text-center mb-5">
                <h6 class="text-primary text-uppercase">Why Choose Us</h6>
                <h1 class="section-title">Our Special Features</h1>
            </div>
            <div class="row g-4">
                <div class="col-md-4 animate__animated animate__fadeInUp" style="animation-delay: 0.1s;">
                    <div class="feature-item">
                        <div class="feature-icon">
                            <i class="fas fa-money-bill-wave"></i>
                        </div>
                        <h5>Competitive Pricing</h5>
                        <p>We believe that amazing travel experiences shouldn't come with a hefty price tag. Our packages offer exceptional value.</p>
                    </div>
                </div>
                <div class="col-md-4 animate__animated animate__fadeInUp" style="animation-delay: 0.2s;">
                    <div class="feature-item">
                        <div class="feature-icon">
                            <i class="fas fa-award"></i>
                        </div>
                        <h5>Best Services</h5>
                        <p>We pride ourselves on delivering the best services to make your travel experience seamless and enjoyable from start to finish.</p>
                    </div>
                </div>
                <div class="col-md-4 animate__animated animate__fadeInUp" style="animation-delay: 0.3s;">
                    <div class="feature-item">
                        <div class="feature-icon">
                            <i class="fas fa-globe-americas"></i>
                        </div>
                        <h5>Worldwide Coverage</h5>
                        <p>We offer worldwide coverage, bringing the world to your fingertips with destinations across all continents.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Features End -->
    
    <!-- Destination Start -->
    <section class="destinations">
        <div class="container">
            <div class="text-center mb-5">
                <h6 class="text-primary text-uppercase">Popular Destinations</h6>
                <h1 class="section-title">Explore Top Destinations</h1>
            </div>
            <div class="row g-4">
                <div class="col-md-4 animate__animated animate__fadeInUp" style="animation-delay: 0.1s;">
                    <div class="destination-item">
                        <img src="img/destination-2.jpg" class="img-fluid" alt="United Kingdom">
                        <a href="destination.php" class="destination-overlay">
                            <h5>United Kingdom</h5>
                            <span>100+ Cities</span>
                        </a>
                    </div>
                </div>
                <div class="col-md-4 animate__animated animate__fadeInUp" style="animation-delay: 0.2s;">
                    <div class="destination-item">
                        <img src="img/destination-3.jpg" class="img-fluid" alt="Australia">
                        <a href="destination.php" class="destination-overlay">
                            <h5>Australia</h5>
                            <span>80+ Cities</span>
                        </a>
                    </div>
                </div>
                <div class="col-md-4 animate__animated animate__fadeInUp" style="animation-delay: 0.3s;">
                    <div class="destination-item">
                        <img src="img/destination-5.jpg" class="img-fluid" alt="South Africa">
                        <a href="destination.php" class="destination-overlay">
                            <h5>South Africa</h5>
                            <span>60+ Cities</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Destination End -->
    
    <!-- Services Start -->
    <section class="services">
        <div class="container">
            <div class="text-center mb-5">
                <h6 class="text-primary text-uppercase">Our Services</h6>
                <h1 class="section-title">Premium Travel Services</h1>
            </div>
            <div class="row g-4">
                <div class="col-md-4 animate__animated animate__fadeInUp" style="animation-delay: 0.1s;">
                    <div class="service-item">
                        <div class="service-icon">
                            <i class="fas fa-map-marked-alt"></i>
                        </div>
                        <h5>Travel Guide</h5>
                        <p>Expert local guides who provide authentic insights and hidden gems at every destination.</p>
                    </div>
                </div>
                <div class="col-md-4 animate__animated animate__fadeInUp" style="animation-delay: 0.2s;">
                    <div class="service-item">
                        <div class="service-icon">
                            <i class="fas fa-ticket-alt"></i>
                        </div>
                        <h5>Ticket Booking</h5>
                        <p>Hassle-free booking for flights, trains, buses, and attractions with the best prices.</p>
                    </div>
                </div>
                <div class="col-md-4 animate__animated animate__fadeInUp" style="animation-delay: 0.3s;">
                    <div class="service-item">
                        <div class="service-icon">
                            <i class="fas fa-hotel"></i>
                        </div>
                        <h5>Hotel Booking</h5>
                        <p>Curated selection of accommodations from luxury resorts to boutique hotels and unique stays.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Services End -->
    
    <!-- Trips Start -->
    <section class="trips">
        <div class="container">
            <div class="text-center mb-5">
                <h6 class="text-primary text-uppercase">Featured Tours</h6>
                <h1 class="section-title">Perfect Tour Packages</h1>
            </div>
            <div class="row g-4">
                <?php while ($trip = mysqli_fetch_assoc($result)): ?>
                    <div class="col-lg-4 col-md-6 animate__animated animate__fadeInUp" style="animation-delay: 0.1s;">
                        <div class="package-item">
                            <div class="package-img">
                                <img src="<?php echo htmlspecialchars($trip['trip_image']); ?>" class="img-fluid" alt="<?php echo htmlspecialchars($trip['trip_name']); ?>">
                            </div>
                            <div class="package-content">
                                <div class="package-meta">
                                    <small><i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($trip['destination']); ?></small>
                                    <small><i class="fas fa-calendar-alt"></i> <?php echo htmlspecialchars($trip['duration_days']); ?> days</small>
                                    <small><i class="fas fa-user"></i> <?php echo htmlspecialchars($trip['persons']); ?> Person</small>
                                </div>
                                <a href="trip_details.php?id=<?php echo htmlspecialchars($trip['trip_id']); ?>" class="package-title"><?php echo htmlspecialchars($trip['trip_name']); ?></a>
                                <div class="package-footer">
                                    <div>
                                        <i class="fas fa-star text-warning"></i>
                                        <i class="fas fa-star text-warning"></i>
                                        <i class="fas fa-star text-warning"></i>
                                        <i class="fas fa-star text-warning"></i>
                                        <i class="fas fa-star-half-alt text-warning"></i>
                                        <span class="ms-1"><?php echo htmlspecialchars($trip['stars']); ?></span>
                                    </div>
                                    <div class="package-price">$<?php echo htmlspecialchars($trip['budget']); ?></div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        </div>
    </section>
    <!-- Trips End -->
    
    <!-- Review Start -->
    <section class="review">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-6 animate__animated animate__fadeInUp">
                    <div class="review-form">
                        <div class="review-header">
                            <h1>Share Your Experience</h1>
                        </div>
                        <div class="review-body">
                            <?php if ($reviewSuccess): ?>
                                <div class="success-message">
                                    Thank you for your review! It has been submitted successfully.
                                </div>
                            <?php endif; ?>
                            
                            <form method="POST" action="index.php" enctype="multipart/form-data" onsubmit="return validateMessage()">
                                <div class="mb-3">
                                    <input type="email" name="useremail" class="form-control" placeholder="Your Email" required>
                                </div>
                                <div class="mb-3">
                                    <input type="file" name="profile" class="form-control" required>
                                </div>
                                <div class="mb-3">
                                    <textarea id="usermessage" name="usermessage" class="form-control" rows="4" placeholder="Share your experience (max 10 words)" required></textarea>
                                    <small id="wordCount" class="form-text text-muted">Words: 0/10</small>
                                </div>
                                <div>
                                    <button class="btn btn-primary w-100" type="submit" name="sendreview">Submit Review</button>
                                </div>
                            </form>
                            <script>
                                function validateMessage() {
                                    const messageInput = document.getElementById('usermessage');
                                    const message = messageInput.value.trim();
                                    const wordCount = message.split(/\s+/).filter(word => word.length > 0).length;
                                    
                                    if (wordCount > 10) {
                                        alert('Please enter no more than 10 words.');
                                        return false;
                                    }
                                    return true;
                                }
                                
                                document.getElementById('usermessage').addEventListener('input', function() {
                                    const message = this.value.trim();
                                    const wordCount = message.split(/\s+/).filter(word => word.length > 0).length;
                                    document.getElementById('wordCount').textContent = `Words: ${wordCount}/10`;
                                    
                                    if (wordCount > 10) {
                                        const words = message.split(/\s+/).slice(0, 10).join(" ");
                                        this.value = words;
                                    }
                                });
                            </script>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Review End -->
    
    <!-- Team Start -->
    <section class="team">
        <div class="container">
            <div class="text-center mb-5">
                <h6 class="text-primary text-uppercase">Travel Experts</h6>
                <h1 class="section-title">Meet Our Guides</h1>
            </div>
            <div class="row g-4">
                <?php
                $basePath = 'admin/upload/agents/';
                
                while ($agent = mysqli_fetch_assoc($agentResult)):
                    $imageFilename = htmlspecialchars($agent['a_image']);
                    $imagePath = $basePath . $imageFilename;
                    $agentName = htmlspecialchars($agent['a_name']);
                    $agentProfession = htmlspecialchars($agent['a_profetion']);
                ?>
                    <div class="col-lg-3 col-md-6 animate__animated animate__fadeInUp" style="animation-delay: 0.1s;">
                        <div class="team-item">
                            <div class="team-img">
                                <img src="<?php echo $imagePath; ?>" class="img-fluid" alt="<?php echo $agentName; ?>" onerror="this.src='img/placeholder.jpg';">
                                <div class="team-social">
                                    <a href=""><i class="fab fa-twitter"></i></a>
                                    <a href=""><i class="fab fa-facebook-f"></i></a>
                                    <a href=""><i class="fab fa-instagram"></i></a>
                                    <a href=""><i class="fab fa-linkedin-in"></i></a>
                                </div>
                            </div>
                            <div class="team-info">
                                <h5><?php echo $agentName; ?></h5>
                                <p><?php echo $agentProfession; ?></p>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        </div>
    </section>
    <!-- Team End -->
    
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
    
    <!-- Blog Start -->
    <section class="blog">
        <div class="container">
            <div class="text-center mb-5">
                <h6 class="text-primary text-uppercase">Our Blog</h6>
                <h1 class="section-title">Latest Travel Stories</h1>
            </div>
            <div class="row g-4">
                <div class="col-lg-4 col-md-6 animate__animated animate__fadeInUp" style="animation-delay: 0.1s;">
                    <div class="blog-item">
                        <div class="blog-img">
                            <img src="img/blog-1.jpg" class="img-fluid" alt="Blog Post">
                            <div class="blog-date">
                                <h6>01</h6>
                                <small>Jan</small>
                            </div>
                        </div>
                        <div class="blog-content">
                            <div class="blog-meta">
                                <a href="">Admin</a>
                                <a href="">Tours & Travel</a>
                            </div>
                            <a href="blog.php" class="blog-title">Discover Hidden Gems in Europe</a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 animate__animated animate__fadeInUp" style="animation-delay: 0.2s;">
                    <div class="blog-item">
                        <div class="blog-img">
                            <img src="img/blog-2.jpg" class="img-fluid" alt="Blog Post">
                            <div class="blog-date">
                                <h6>15</h6>
                                <small>Jan</small>
                            </div>
                        </div>
                        <div class="blog-content">
                            <div class="blog-meta">
                                <a href="">Admin</a>
                                <a href="">Adventure</a>
                            </div>
                            <a href="blog.php" class="blog-title">Ultimate Adventure Travel Guide</a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 animate__animated animate__fadeInUp" style="animation-delay: 0.3s;">
                    <div class="blog-item">
                        <div class="blog-img">
                            <img src="img/blog-3.jpg" class="img-fluid" alt="Blog Post">
                            <div class="blog-date">
                                <h6>28</h6>
                                <small>Jan</small>
                            </div>
                        </div>
                        <div class="blog-content">
                            <div class="blog-meta">
                                <a href="">Admin</a>
                                <a href="#">Luxury Travel</a>
                            </div>
                            <a href="blog.php" class="blog-title">Luxury Resorts You Must Visit</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Blog End -->
    
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