<?php
require("config.php");
session_start();
if(isset($_POST['insertcity'])){
    $country = $_POST['country'];
    $city = $_POST['city'];
    $file=  $_FILES["image"]["name"];
    $tempname=  $_FILES['image']['tmp_name'];
    $folder = "img/cityimages/".$file;
    $folder2 = "../img/cityimages/".$file;
    $fileType = $_FILES['image']['type'];
    $allowedImageTypes = array("image/jpeg", "image/jpg", "image/png", "image/gif");
    $type=false;
    if (in_array($fileType, $allowedImageTypes)) {
        move_uploaded_file($tempname, $folder2);
            $type = true; 
    } else {
        echo "<script>alert('Only image files (JPEG, PNG, GIF) are allowed.');</script>";
    }

    if($type){
        $sql = "INSERT INTO city(country_name,city_name,cover_image) VALUES('$country','$city','$folder')";
        $result = mysqli_query($con,$sql);
        
        if($result){
            echo "<script>alert('City Added Successfully');
            window.location.href = 'cityadd.php';</script>";
    }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
    <title>Add City - ExpenseVoyage</title>
    
    <!-- Favicon -->
    <link rel="shortcut icon" type="image/x-icon" href="../img/favicon-32x32.png">
    
    <!-- Google Web Fonts -->
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    
    <!-- Datatables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css">
    
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
        
        /* Card Styles */
        .card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
            border: none;
            margin-bottom: 30px;
            overflow: hidden;
        }
        
        .card-header {
            background: white;
            border-bottom: 1px solid rgba(67, 97, 238, 0.1);
            padding: 20px 25px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        
        .card-title {
            margin: 0;
            font-weight: 700;
            color: var(--dark);
            font-size: 1.4rem;
        }
        
        .card-body {
            padding: 25px;
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
        
        .btn-danger {
            background: linear-gradient(135deg, #ff6b6b, #ee5a24);
            color: white;
            box-shadow: 0 4px 15px rgba(255, 107, 107, 0.3);
        }
        
        .btn-danger:hover {
            transform: translateY(-3px);
            box-shadow: 0 7px 20px rgba(255, 107, 107, 0.4);
            color: white;
        }
        
        /* Table Styles */
        .table-responsive {
            border-radius: 10px;
            overflow: hidden;
        }
        
        .table {
            margin-bottom: 0;
        }
        
        .table thead th {
            background: var(--gradient);
            color: white;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.85rem;
            letter-spacing: 0.5px;
            border: none;
            padding: 15px;
        }
        
        .table tbody td {
            padding: 15px;
            vertical-align: middle;
            border-color: rgba(0, 0, 0, 0.05);
        }
        
        .table tbody tr {
            transition: all 0.3s ease;
        }
        
        .table tbody tr:hover {
            background-color: rgba(67, 97, 238, 0.05);
        }
        
        .table img {
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }
        
        .table img:hover {
            transform: scale(1.05);
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
            
            .card-header {
                flex-direction: column;
                align-items: flex-start;
            }
            
            .card-header .btn {
                margin-top: 15px;
                width: 100%;
            }
            
            .header-user-info {
                display: none;
            }
            
            .page-header h1 {
                font-size: 1.8rem;
            }
            
            .table thead th {
                font-size: 0.75rem;
                padding: 10px;
            }
            
            .table tbody td {
                padding: 10px;
                font-size: 0.9rem;
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
                    <li class="submenu open">
                        <a href="#"><i class="fe fe-location"></i> <span>State &amp; City</span> <span class="menu-arrow"><i class="fas fa-chevron-right"></i></span></a>
                        <ul>
                            <li><a href="cityadd.php" class="active"> State / City </a></li>
                        </ul>
                    </li>
                    <li class="menu-title">
                        <span>Trip Management</span>
                    </li>
                    <li class="submenu">
                        <a href="#"><i class="fe fe-map"></i> <span> Trip / Travel</span> <span class="menu-arrow"><i class="fas fa-chevron-right"></i></span></a>
                        <ul>
                            <li><a href="tripadd.php"> Add Trip</a></li>
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
            
          
        </aside>
        
        <!-- Main Content -->
        <div class="main-content" id="mainContent">
            <!-- Top Header -->
            <header class="top-header">
                <button class="menu-toggle" id="menuToggle">
                    <i class="fas fa-bars"></i>
                </button>
                
                <div class="header-user">
                    <img src="../img/Aliza.jpg" alt="Admin">
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
                    <h1>City Management</h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                            <li class="breadcrumb-item active">Add City</li>
                        </ol>
                    </nav>
                </div>
                
                <!-- city add section --> 
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Add City</h4>
                            </div>
                            <form method="post" id="insert product" enctype="multipart/form-data">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-xl-6">
                                            <h5 class="card-title mb-4">City Details</h5>
                                            
                                            <div class="form-group">
                                                <label class="form-label">Country Name</label>
                                                <input type="text" class="form-control" name="country" required>
                                            </div>
                                            
                                            <div class="form-group">
                                                <label class="form-label">City Name</label>
                                                <input type="text" class="form-control" name="city" required>
                                            </div>
                                            
                                            <div class="form-group">
                                                <label class="form-label">Cover Image</label>
                                                <input type="file" class="form-control" name="image" accept="image/*" required>
                                                <small class="text-muted">Supported formats: JPEG, PNG, GIF</small>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="text-left mt-4">
                                        <button type="submit" class="btn btn-primary" name="insertcity">
                                            <i class="fas fa-save me-2"></i> Submit
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <h4 class="header-title mb-4">Cities Added</h4>
                                <div class="table-responsive">
                                    <table id="datatable-buttons" class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>Cover Image</th>
                                                <th>Country Name</th>
                                                <th>City Name</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $sql2 = "SELECT * FROM city";
                                            $result = mysqli_query($con, $sql2);
                                            $rows_num = mysqli_num_rows($result);
                                            if ($rows_num > 0) {
                                                while ($rows = mysqli_fetch_assoc($result)) {
                                                    $id = htmlspecialchars($rows['city_id']);
                                                    echo "<tr>
                                                    <td><img src='../" . htmlspecialchars($rows['cover_image']) . "' alt='' style='height: 80px; border-radius: 8px;'></td>
                                                    <td>" . htmlspecialchars($rows['country_name']) . "</td>
                                                    <td>" . htmlspecialchars($rows['city_name']) . "</td>
                                                        <td>
                                                            <a href='citydelete.php?id=$id' class='btn btn-danger btn-sm' onclick='return confirm(\"Are you sure you want to delete this city?\");'>
                                                                <i class='fas fa-trash-alt'></i> Delete
                                                            </a>
                                                        </td>
                                                    </tr>";
                                                }
                                            } else {
                                                echo "<tr><td colspan='4' class='text-center'>No cities found</td></tr>";
                                            }
                                            ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div> <!-- end card body-->
                        </div> <!-- end card -->
                    </div><!-- end col-->
                </div>
            </div>
        </div>
    </div>
    
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <!-- Bootstrap Core JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Datatables JS -->
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap5.min.js"></script>
    
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
        
        // Initialize DataTable
        $(document).ready(function() {
            $('#datatable-buttons').DataTable({
                responsive: true,
                pageLength: 10,
                language: {
                    search: "_INPUT_",
                    searchPlaceholder: "Search records..."
                }
            });
        });
    </script>
</body>
</html>