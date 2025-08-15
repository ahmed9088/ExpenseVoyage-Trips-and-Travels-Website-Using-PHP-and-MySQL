<?php
session_start();
if (isset($_POST['addtrip'])) {
    // Database connection
    include 'config.php'; // Adjust the path to your db connection file
    
    // Check if admin is logged in
    if(!isset($_SESSION['auser'])) {
        header("location:index.php");
        exit();
    }
    
    // Retrieve form data and escape to prevent SQL injection
    $trip_name = mysqli_real_escape_string($con, $_POST['trip_name']);
    $description = mysqli_real_escape_string($con, $_POST['description']);
    $start_date = mysqli_real_escape_string($con, $_POST['starts_date']);
    $end_date = mysqli_real_escape_string($con, $_POST['end_date']);
    $destination = mysqli_real_escape_string($con, $_POST['destination']);
    $budget = mysqli_real_escape_string($con, $_POST['budget']);
    $persons = mysqli_real_escape_string($con, $_POST['persons']);
    $stars = mysqli_real_escape_string($con, $_POST['stars']);
    $duration_days = mysqli_real_escape_string($con, $_POST['duration_days']);
    $distance_km = mysqli_real_escape_string($con, $_POST['distance_km']);
    $vehicle_type = mysqli_real_escape_string($con, $_POST['vehicle_type']);
    $vehicle_capacity = mysqli_real_escape_string($con, $_POST['vehicle_capacity']);
    $vehicle_features = mysqli_real_escape_string($con, $_POST['vehicle_features']);
    $driver_details = mysqli_real_escape_string($con, $_POST['driver_details']);
    $seats_available = mysqli_real_escape_string($con, $_POST['seats_available']);
    $departure = mysqli_real_escape_string($con, $_POST['departure']);
    
    // Function to handle image upload
    function upload_image($file, $target_dir, $input_name)
    {
        $image_name = $_FILES[$input_name]['name'];
        $target_file = $target_dir . basename($image_name);
        if (move_uploaded_file($_FILES[$input_name]['tmp_name'], $target_file)) {
            return $target_file; // Return the file path if successful
        } else {
            return false; // Return false if upload failed
        }
    }
    
    // Handle trip image upload
    $trip_image = upload_image($_FILES, "img/tripimages/", 'trip_image');
    if (!$trip_image) {
        echo "<script>alert('Error uploading trip image.');</script>";
    }
    
    // Handle vehicle image upload
    $vehicle_image = upload_image($_FILES, "img/vehicleimages/", 'vehicle_image');
    if (!$vehicle_image) {
        echo "<script>alert('Error uploading vehicle image.');</script>";
    }
    
    // Handle driver image upload
    $driver_image = upload_image($_FILES, "img/driverimages/", 'driver_image');
    if (!$driver_image) {
        echo "<script>alert('Error uploading driver image.');</script>";
    }
    
    // Insert form data into the 'trips' table
    $sql = "INSERT INTO trips (trip_name, trip_image, description, starts_date, end_date, destination, budget, persons, stars, duration_days, distance_km, vehicle_type, vehicle_capacity, vehicle_features, driver_details, vehicle_image, driver_image, seats_available, departure) 
            VALUES ('$trip_name', '$trip_image', '$description', '$start_date', '$end_date', '$destination', '$budget', '$persons', '$stars', '$duration_days', '$distance_km', '$vehicle_type', '$vehicle_capacity', '$vehicle_features', '$driver_details', '$vehicle_image', '$driver_image', '$seats_available', '$departure')";
    
    // Execute the query
    if (mysqli_query($con, $sql)) {
        echo "<script>alert('New trip added successfully.');
        window.location.href = 'tripadd.php';</script>";
    } else {
        echo "Error: " . $sql . "<br>" . mysqli_error($con);
    }
    
    // Close the database connection
    mysqli_close($con);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
    <title>Add Trip - ExpenseVoyage</title>
    
    <!-- Favicon -->
    <link rel="shortcut icon" type="image/x-icon" href="../img/favicon-32x32.png">
    
    <!-- Google Web Fonts -->
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    
    <!-- Animate.css -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    
    <!-- Custom CSS -->
    <style>
        :root {
            --primary: #4361ee;
            --secondary: #3f37c9;
            --accent: #4cc9f0;
            --light: #f8f9fa;
            --dark: #212529;
            --gradient: linear-gradient(135deg, var(--primary), var(--secondary));
            --sidebar-width: 280px;
            --sidebar-collapsed-width: 80px;
            --header-height: 70px;
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
        
        /* Dashboard Layout */
        .dashboard-wrapper {
            display: flex;
            min-height: 100vh;
        }
        
        /* Sidebar */
        .sidebar {
            width: var(--sidebar-width);
            background: linear-gradient(180deg, #1a1c20, #2d3436);
            color: white;
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            z-index: 1000;
            transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
            overflow-x: hidden;
            overflow-y: auto;
            box-shadow: 0 0 30px rgba(0, 0, 0, 0.2);
        }
        
        .sidebar.collapsed {
            width: var(--sidebar-collapsed-width);
        }
        
        .sidebar-header {
            padding: 25px 20px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: rgba(0, 0, 0, 0.2);
        }
        
        .sidebar-logo {
            font-weight: 800;
            font-size: 1.5rem;
            color: white;
            text-decoration: none;
            display: flex;
            align-items: center;
            transition: all 0.3s ease;
        }
        
        .sidebar.collapsed .sidebar-logo {
            justify-content: center;
        }
        
        .sidebar-logo i {
            font-size: 1.8rem;
            margin-right: 12px;
            color: var(--accent);
            transition: all 0.3s ease;
        }
        
        .sidebar.collapsed .sidebar-logo i {
            margin-right: 0;
        }
        
        .sidebar-logo span {
            color: var(--primary);
            transition: all 0.3s ease;
        }
        
        .sidebar.collapsed .sidebar-logo span {
            display: none;
        }
        
        .sidebar-toggle {
            width: 36px;
            height: 36px;
            background: var(--gradient);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            cursor: pointer;
            box-shadow: 0 4px 12px rgba(67, 97, 238, 0.4);
            transition: all 0.3s ease;
            border: none;
        }
        
        .sidebar-toggle:hover {
            transform: scale(1.1);
            box-shadow: 0 6px 20px rgba(67, 97, 238, 0.6);
        }
        
        .sidebar.collapsed .sidebar-toggle i {
            transform: rotate(180deg);
        }
        
        .sidebar-menu {
            padding: 20px 0;
        }
        
        .sidebar-menu ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        
        .menu-title {
            padding: 10px 25px;
            font-size: 0.75rem;
            text-transform: uppercase;
            color: rgba(255, 255, 255, 0.4);
            font-weight: 700;
            letter-spacing: 1px;
            transition: all 0.3s ease;
        }
        
        .sidebar.collapsed .menu-title {
            display: none;
        }
        
        .sidebar-menu > ul > li {
            position: relative;
            margin: 2px 0;
        }
        
        .sidebar-menu > ul > li > a {
            display: flex;
            align-items: center;
            padding: 14px 25px;
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        
        .sidebar-menu > ul > li > a::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            height: 100%;
            width: 0;
            background: var(--gradient);
            transition: all 0.3s ease;
            z-index: -1;
        }
        
        .sidebar-menu > ul > li > a:hover::before,
        .sidebar-menu > ul > li > a.active::before {
            width: 100%;
        }
        
        .sidebar-menu > ul > li > a:hover,
        .sidebar-menu > ul > li > a.active {
            color: white;
        }
        
        .sidebar-menu > ul > li > a i {
            font-size: 1.2rem;
            margin-right: 15px;
            width: 24px;
            text-align: center;
            transition: all 0.3s ease;
        }
        
        .sidebar.collapsed .sidebar-menu > ul > li > a i {
            margin-right: 0;
        }
        
        .sidebar-menu > ul > li > a span {
            transition: all 0.3s ease;
            font-weight: 500;
        }
        
        .sidebar.collapsed .sidebar-menu > ul > li > a span {
            display: none;
        }
        
        /* Submenu Styles */
        .submenu {
            position: relative;
        }
        
        .submenu > a {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 14px 25px;
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        
        .submenu > a::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            height: 100%;
            width: 0;
            background: var(--gradient);
            transition: all 0.3s ease;
            z-index: -1;
        }
        
        .submenu > a:hover::before {
            width: 100%;
        }
        
        .submenu > a:hover {
            color: white;
        }
        
        .submenu > a i:first-child {
            font-size: 1.2rem;
            margin-right: 15px;
            width: 24px;
            text-align: center;
            transition: all 0.3s ease;
        }
        
        .sidebar.collapsed .submenu > a i:first-child {
            margin-right: 0;
        }
        
        .submenu > a span {
            transition: all 0.3s ease;
            font-weight: 500;
        }
        
        .sidebar.collapsed .submenu > a span {
            display: none;
        }
        
        .menu-arrow {
            transition: transform 0.3s ease;
            font-size: 0.8rem;
        }
        
        .sidebar.collapsed .menu-arrow {
            display: none;
        }
        
        .submenu.open > a .menu-arrow {
            transform: rotate(90deg);
        }
        
        .submenu ul {
            max-height: 0;
            overflow: hidden;
            background: rgba(0, 0, 0, 0.2);
            transition: all 0.3s ease;
        }
        
        .submenu.open ul {
            max-height: 500px;
        }
        
        .sidebar.collapsed .submenu ul {
            position: absolute;
            left: 100%;
            top: 0;
            width: 220px;
            background: linear-gradient(180deg, #1a1c20, #2d3436);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
            border-radius: 0 10px 10px 0;
            max-height: none;
            opacity: 0;
            visibility: hidden;
            transform: translateX(-10px);
            transition: all 0.3s ease;
        }
        
        .sidebar.collapsed .submenu.open ul {
            opacity: 1;
            visibility: visible;
            transform: translateX(0);
        }
        
        .submenu ul li {
            margin: 0;
        }
        
        .submenu ul li a {
            display: block;
            padding: 12px 25px;
            color: rgba(255, 255, 255, 0.7);
            text-decoration: none;
            transition: all 0.3s ease;
            font-size: 0.9rem;
        }
        
        .submenu ul li a:hover {
            color: white;
            background: rgba(255, 255, 255, 0.05);
            padding-left: 30px;
        }
        
        .sidebar-footer {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            padding: 20px;
            text-align: center;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            background: rgba(0, 0, 0, 0.2);
            transition: all 0.3s ease;
        }
        
        .sidebar.collapsed .sidebar-footer {
            display: none;
        }
        
        .sidebar-footer p {
            margin: 0;
            font-size: 0.8rem;
            color: rgba(255, 255, 255, 0.5);
        }
        
        /* Main Content */
        .main-content {
            flex: 1;
            margin-left: var(--sidebar-width);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
        }
        
        .main-content.expanded {
            margin-left: var(--sidebar-collapsed-width);
        }
        
        /* Top Header */
        .top-header {
            background: white;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            height: var(--header-height);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 30px;
            position: sticky;
            top: 0;
            z-index: 999;
        }
        
        .menu-toggle {
            display: none;
            background: none;
            border: none;
            font-size: 1.5rem;
            color: var(--primary);
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .menu-toggle:hover {
            transform: scale(1.1);
        }
        
        .header-user {
            display: flex;
            align-items: center;
        }
        
        .header-user img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
            margin-right: 15px;
            border: 2px solid var(--primary);
        }
        
        .header-user-info h5 {
            margin: 0;
            font-weight: 600;
            color: var(--dark);
        }
        
        .header-user-info p {
            margin: 0;
            font-size: 0.9rem;
            color: #6c757d;
        }
        
        .logout-btn {
            background: var(--gradient);
            color: white;
            border: none;
            padding: 10px 25px;
            border-radius: 50px;
            font-weight: 600;
            transition: all 0.3s ease;
            margin-left: 20px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-size: 0.9rem;
        }
        
        .logout-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(67, 97, 238, 0.4);
            color: white;
        }
        
        /* Dashboard Content */
        .dashboard-content {
            flex: 1;
            padding: 30px;
            background-color: #f5f7ff;
        }
        
        .page-header {
            margin-bottom: 30px;
        }
        
        .page-header h1 {
            font-weight: 800;
            color: var(--dark);
            margin-bottom: 10px;
            font-size: 2.2rem;
        }
        
        .breadcrumb {
            background: none;
            padding: 0;
            margin: 0;
            display: flex;
            align-items: center;
        }
        
        .breadcrumb-item {
            font-size: 0.9rem;
        }
        
        .breadcrumb-item a {
            color: var(--primary);
            text-decoration: none;
            transition: all 0.3s ease;
        }
        
        .breadcrumb-item a:hover {
            color: var(--secondary);
        }
        
        .breadcrumb-item.active {
            color: #6c757d;
        }
        
        .breadcrumb-item + .breadcrumb-item::before {
            content: ">";
            color: #6c757d;
            padding: 0 10px;
        }
        
        /* Form Section */
        .form-section {
            background: white;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
            padding: 30px;
            margin-bottom: 30px;
        }
        
        .form-header {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .form-header h6 {
            color: var(--primary);
            font-weight: 600;
            letter-spacing: 2px;
            text-transform: uppercase;
            margin-bottom: 10px;
        }
        
        .form-header h1 {
            font-weight: 800;
            color: var(--dark);
            font-size: 2.2rem;
        }
        
        /* Form Styles */
        .form-group {
            margin-bottom: 25px;
        }
        
        .form-label {
            font-weight: 600;
            color: var(--dark);
            margin-bottom: 10px;
            font-size: 0.95rem;
        }
        
        .form-control {
            border: 1px solid #e9ecef;
            border-radius: 10px;
            padding: 12px 15px;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        
        .form-control:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 0.25rem rgba(67, 97, 238, 0.1);
        }
        
        .form-control-file {
            border: 1px solid #e9ecef;
            border-radius: 10px;
            padding: 10px 15px;
        }
        
        /* Button Styles */
        .btn {
            padding: 12px 30px;
            font-weight: 600;
            border-radius: 50px;
            transition: all 0.3s ease;
            border: none;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-size: 0.9rem;
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
        
        /* Alert Styles */
        .alert {
            border-radius: 10px;
            border: none;
            padding: 15px 20px;
            margin-bottom: 20px;
        }
        
        .alert-success {
            background-color: rgba(6, 255, 165, 0.1);
            color: #00d68f;
        }
        
        .alert-danger {
            background-color: rgba(255, 107, 107, 0.1);
            color: #ff6b6b;
        }
        
        /* Responsive */
        @media (max-width: 991px) {
            .sidebar {
                transform: translateX(-100%);
            }
            
            .sidebar.active {
                transform: translateX(0);
            }
            
            .sidebar.collapsed {
                width: var(--sidebar-width);
                transform: translateX(-100%);
            }
            
            .sidebar.collapsed.active {
                transform: translateX(0);
            }
            
            .main-content {
                margin-left: 0;
            }
            
            .main-content.expanded {
                margin-left: 0;
            }
            
            .menu-toggle {
                display: block;
            }
            
            .sidebar-toggle {
                display: none;
            }
        }
        
        @media (max-width: 767px) {
            .dashboard-content {
                padding: 20px;
            }
            
            .form-section {
                padding: 20px;
            }
            
            .header-user-info {
                display: none;
            }
            
            .page-header h1 {
                font-size: 1.8rem;
            }
            
            .form-header h1 {
                font-size: 1.8rem;
            }
        }
    </style>
</head>
<body>
    <div class="dashboard-wrapper">
        <!-- Sidebar -->
        <aside class="sidebar" id="sidebar">
            <div class="sidebar-header">
                <a href="dashboard.php" class="sidebar-logo">
                    <i class="fas fa-globe-americas"></i>
                    <span>Expense<span>Voyage</span></span>
                </a>
                <button class="sidebar-toggle" id="sidebarToggle">
                    <i class="fas fa-chevron-left"></i>
                </button>
            </div>
            
            <div class="sidebar-menu">
                <ul>
                    <li class="menu-title">
                        <span>Main</span>
                    </li>
                    <li>
                        <a href="dashboard.php"><i class="fe fe-home"></i> <span>Dashboard</span></a>
                    </li>
                    <li class="menu-title">
                        <span>All Users</span>
                    </li>
                    <li class="submenu">
                        <a href="#"><i class="fe fe-user"></i> <span> All Users </span> <span class="menu-arrow"><i class="fas fa-chevron-right"></i></span></a>
                        <ul>
                            <li><a href="adminlist.php"> Admin </a></li>
                            <li><a href="userlist.php"> Users </a></li>
                        </ul>
                    </li>
                    <li class="menu-title">
                        <span>State &amp; City</span>
                    </li>
                    <li class="submenu">
                        <a href="#"><i class="fe fe-location"></i> <span>State &amp; City</span> <span class="menu-arrow"><i class="fas fa-chevron-right"></i></span></a>
                        <ul>
                            <li><a href="cityadd.php"> State / City </a></li>
                        </ul>
                    </li>
                    <li class="menu-title">
                        <span>Trip Management</span>
                    </li>
                    <li class="submenu open">
                        <a href="#"><i class="fe fe-map"></i> <span> Trip / Travel</span> <span class="menu-arrow"><i class="fas fa-chevron-right"></i></span></a>
                        <ul>
                            <li><a href="tripadd.php" class="active"> Add Trip</a></li>
                            <li><a href="tripview.php"> View Trip </a></li>
                            <li><a href="addblog.php"> Add Blog </a></li>
                            <li><a href="addagent.php"> Add agent </a></li>
                        </ul>
                    </li>

                    <li class="menu-title">
                        <span>Query</span>
                    </li>
                    <li class="submenu">
                        <a href="#"><i class="fe fe-comment"></i> <span> Contact,Feedback </span> <span class="menu-arrow"><i class="fas fa-chevron-right"></i></span></a>
                        <ul>
                            <li><a href="contactview.php"> Contact </a></li>
                            <li><a href="feedbackview.php"> Feedback </a></li>
                        </ul>
                    </li>
                </ul>
            </div>
            
            <div class="sidebar-footer">
                <p>&copy; <?php echo date('Y'); ?> ExpenseVoyage</p>
            </div>
        </aside>
        
        <!-- Main Content -->
        <div class="main-content" id="mainContent">
            <!-- Top Header -->
            <header class="top-header">
                <button class="menu-toggle" id="menuToggle">
                    <i class="fas fa-bars"></i>
                </button>
                
                <div class="header-user">
                    <img src="../img/user-avatar.jpg" alt="Admin">
                    <div class="header-user-info">
                        <h5><?php echo htmlspecialchars($_SESSION['auser']); ?></h5>
                        <p>Administrator</p>
                    </div>
                    <a href="logout.php" class="logout-btn">Logout</a>
                </div>
            </header>
            
            <!-- Dashboard Content -->
            <div class="dashboard-content">
                <div class="page-header">
                    <h1>Trip Management</h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                            <li class="breadcrumb-item active">Add Trip</li>
                        </ol>
                    </nav>
                </div>
                
                <!-- Form Section -->
                <div class="form-section animate__animated animate__fadeInUp">
                    <div class="form-header">
                        <h6 class="text-primary text-uppercase" style="letter-spacing: 5px;">Travel with Comfort</h6>
                        <h1>Add Trip Form</h1>
                    </div>
                    
                    <form action="" method="POST" enctype="multipart/form-data">
                        <div class="row">
                            <div class="col-md-6">
                                <!-- Trip Name -->
                                <div class="form-group">
                                    <label for="trip_name" class="form-label">Trip Name</label>
                                    <input type="text" class="form-control" id="trip_name" name="trip_name" required>
                                </div>
                                
                                <!-- Departure -->
                                <div class="form-group">
                                    <label for="departure" class="form-label">Departure Location</label>
                                    <input type="text" class="form-control" id="departure" name="departure" required>
                                </div>
                                
                                <!-- Start Date -->
                                <div class="form-group">
                                    <label for="starts_date" class="form-label">Start Date</label>
                                    <input type="date" class="form-control" id="starts_date" name="starts_date" required>
                                </div>
                                
                                <!-- End Date -->
                                <div class="form-group">
                                    <label for="end_date" class="form-label">End Date</label>
                                    <input type="date" class="form-control" id="end_date" name="end_date" required>
                                </div>
                                
                                <!-- Destination -->
                                <div class="form-group">
                                    <label for="destination" class="form-label">Destination</label>
                                    <input type="text" class="form-control" id="destination" name="destination" required>
                                </div>
                                
                                <!-- Budget -->
                                <div class="form-group">
                                    <label for="budget" class="form-label">Budget</label>
                                    <input type="number" class="form-control" id="budget" name="budget" required>
                                </div>
                                
                                <!-- Persons -->
                                <div class="form-group">
                                    <label for="persons" class="form-label">Persons</label>
                                    <input type="number" class="form-control" id="persons" name="persons" required>
                                </div>
                                
                                <!-- Stars -->
                                <div class="form-group">
                                    <label for="stars" class="form-label">Stars</label>
                                    <input type="number" class="form-control" id="stars" name="stars" min="1" max="5" required>
                                </div>
                                
                                <!-- Trip Image -->
                                <div class="form-group">
                                    <label for="trip_image" class="form-label">Trip Image</label>
                                    <input type="file" class="form-control" id="trip_image" name="trip_image" accept="image/*" required>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <!-- Duration Days -->
                                <div class="form-group">
                                    <label for="duration_days" class="form-label">Duration (Days)</label>
                                    <input type="number" class="form-control" id="duration_days" name="duration_days" required>
                                </div>
                                
                                <!-- Distance KM -->
                                <div class="form-group">
                                    <label for="distance_km" class="form-label">Distance (KM)</label>
                                    <input type="number" class="form-control" id="distance_km" name="distance_km" required>
                                </div>
                                
                                <!-- Vehicle Type -->
                                <div class="form-group">
                                    <label for="vehicle_type" class="form-label">Vehicle Type</label>
                                    <input type="text" class="form-control" id="vehicle_type" name="vehicle_type" required>
                                </div>
                                
                                <!-- Vehicle Capacity -->
                                <div class="form-group">
                                    <label for="vehicle_capacity" class="form-label">Vehicle Capacity</label>
                                    <input type="number" class="form-control" id="vehicle_capacity" name="vehicle_capacity" required>
                                </div>
                                
                                <!-- Vehicle Features -->
                                <div class="form-group">
                                    <label for="vehicle_features" class="form-label">Vehicle Features</label>
                                    <textarea class="form-control" id="vehicle_features" name="vehicle_features" rows="3" required></textarea>
                                </div>
                                
                                <!-- Driver Details -->
                                <div class="form-group">
                                    <label for="driver_details" class="form-label">Driver Details</label>
                                    <textarea class="form-control" id="driver_details" name="driver_details" rows="3" required></textarea>
                                </div>
                                
                                <!-- Seats Available -->
                                <div class="form-group">
                                    <label for="seats_available" class="form-label">Seats Available</label>
                                    <input type="number" class="form-control" id="seats_available" name="seats_available" required>
                                </div>
                                
                                <!-- Vehicle Image -->
                                <div class="form-group">
                                    <label for="vehicle_image" class="form-label">Vehicle Image</label>
                                    <input type="file" class="form-control" id="vehicle_image" name="vehicle_image" accept="image/*" required>
                                </div>
                                
                                <!-- Driver Image -->
                                <div class="form-group">
                                    <label for="driver_image" class="form-label">Driver Image</label>
                                    <input type="file" class="form-control" id="driver_image" name="driver_image" accept="image/*" required>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Description -->
                        <div class="form-group">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control" id="description" name="description" rows="4" required></textarea>
                        </div>
                        
                        <!-- Submit Button -->
                        <div class="text-center mt-4">
                            <button type="submit" name="addtrip" class="btn btn-primary">
                                <i class="fas fa-plus-circle me-2"></i> Add Trip
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <!-- Bootstrap Core JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Custom JavaScript -->
    <script>
        // Desktop Sidebar Toggle
        document.getElementById('sidebarToggle').addEventListener('click', function() {
            document.getElementById('sidebar').classList.toggle('collapsed');
            document.getElementById('mainContent').classList.toggle('expanded');
        });
        
        // Mobile Sidebar Toggle
        document.getElementById('menuToggle').addEventListener('click', function() {
            document.getElementById('sidebar').classList.toggle('active');
        });
        
        // Submenu Toggle
        document.querySelectorAll('.submenu > a').forEach(function(element) {
            element.addEventListener('click', function(e) {
                e.preventDefault();
                let parent = this.parentElement;
                parent.classList.toggle('open');
            });
        });
        
        // Close sidebar when clicking outside on mobile
        $(document).click(function(event) {
            var $target = $(event.target);
            if(!$target.closest('.sidebar').length && 
               !$target.closest('.menu-toggle').length && 
               $('.sidebar').hasClass('active') && 
               $(window).width() <= 991) {
                $('.sidebar').removeClass('active');
            }
        });
    </script>
</body>
</html>