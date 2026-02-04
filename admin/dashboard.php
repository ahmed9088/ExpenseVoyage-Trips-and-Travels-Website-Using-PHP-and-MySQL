<?php
session_start();
require("config.php");
// Check if admin is logged in
if(!isset($_SESSION['auser'])) {
    header("location:index.php");
}
// Fetch data from the database with error handling
// Total users
$sqlUsers = "SELECT COUNT(*) AS total_users FROM user";
$resultUsers = $con->query($sqlUsers);
if (!$resultUsers) {
    die("Query Failed: " . $con->error);  // Error handling for debugging
}
$totalUsers = $resultUsers->fetch_assoc()['total_users'];
// Total admins
$sqlAdmins = "SELECT COUNT(*) AS total_admins FROM admin";
$resultAdmins = $con->query($sqlAdmins);
if (!$resultAdmins) {
    die("Query Failed: " . $con->error);  // Error handling for debugging
}
$totalAdmins = $resultAdmins->fetch_assoc()['total_admins'];
// Total agents
$sqlAgents = "SELECT COUNT(*) AS total_agents FROM agent";
$resultAgents = $con->query($sqlAgents);
if (!$resultAgents) {
    die("Query Failed: " . $con->error);  // Error handling for debugging
}
$totalAgents = $resultAgents->fetch_assoc()['total_agents'];
// Total trips
$sqlTrips = "SELECT COUNT(*) AS total_trips FROM trips";
$resultTrips = $con->query($sqlTrips);
if (!$resultTrips) {
    die("Query Failed: " . $con->error);  // Error handling for debugging
}
$totalTrips = $resultTrips->fetch_assoc()['total_trips'];
// Fetch total trips
$sqlTrips = "SELECT COUNT(*) AS total_trips FROM trips";
$resultTrips = $con->query($sqlTrips);
if (!$resultTrips) {
    die("Query Failed: " . $con->error);
}
$totalTrips = $resultTrips->fetch_assoc()['total_trips'];
// Fetch total contacts
$sqlContacts = "SELECT COUNT(*) AS total_contacts FROM contactus";
$resultContacts = $con->query($sqlContacts);
if (!$resultContacts) {
    die("Query Failed: " . $con->error);
}
$totalContacts = $resultContacts->fetch_assoc()['total_contacts'];
// Fetch total reviews
$sqlReviews = "SELECT COUNT(*) AS total_reviews FROM review";
$resultReviews = $con->query($sqlReviews);
if (!$resultReviews) {
    die("Query Failed: " . $con->error);
}
$totalReviews = $resultReviews->fetch_assoc()['total_reviews'];
// Fetch total blogs
$sqlBlogs = "SELECT COUNT(*) AS total_blogs FROM blog";
$resultBlogs = $con->query($sqlBlogs);
if (!$resultBlogs) {
    die("Query Failed: " . $con->error);
}
$totalBlogs = $resultBlogs->fetch_assoc()['total_blogs'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
    <title>Admin Dashboard - ExpenseVoyage</title>
    
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
    padding-bottom: 80px; /* Add this line for spacing */
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
    position: fixed;
    bottom: 0;
    left: 0;
    right: 0;
    padding: 20px;
    text-align: center;
    border-top: 1px solid rgba(255, 255, 255, 0.1);
    background: linear-gradient(180deg, #1a1c20, #2d3436);
    transition: all 0.3s ease;
    z-index: 1001;
    width: var(--sidebar-width);
}

.sidebar.collapsed .sidebar-footer {
    width: var(--sidebar-collapsed-width);
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
        
        .page-header p {
            color: #6c757d;
            font-size: 1.1rem;
        }
        
        /* Dashboard Cards */
        .stats-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
            padding: 30px;
            margin-bottom: 30px;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
            border: 1px solid rgba(67, 97, 238, 0.1);
        }
        
        .stats-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
        }
        
        .stats-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 6px;
            height: 100%;
            background: var(--gradient);
        }
        
        .stats-icon {
            width: 80px;
            height: 80px;
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 20px;
            font-size: 2rem;
            color: white;
        }
        
        .stats-icon.primary {
            background: var(--gradient);
        }
        
        .stats-icon.warning {
            background: linear-gradient(135deg, #f9a826, #f0932b);
        }
        
        .stats-icon.success {
            background: linear-gradient(135deg, #06ffa5, #00d68f);
        }
        
        .stats-icon.info {
            background: linear-gradient(135deg, #4cc9f0, #3a86ff);
        }
        
        .stats-number {
            font-size: 2.8rem;
            font-weight: 800;
            color: var(--dark);
            margin-bottom: 5px;
        }
        
        .stats-title {
            color: #6c757d;
            font-weight: 600;
            font-size: 1.1rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
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
            
            .stats-card {
                padding: 20px;
            }
            
            .stats-number {
                font-size: 2rem;
            }
            
            .header-user-info {
                display: none;
            }
            
            .page-header h1 {
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
                        <a href="dashboard.php" class="active"><i class="fe fe-home"></i> <span>Dashboard</span></a>
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
                    <h1>Admin Dashboard</h1>
                    <p>Welcome back, <?php echo htmlspecialchars($_SESSION['auser']); ?>! Here's what's happening with your travel business today.</p>
                </div>
                
                <!-- Stats Cards -->
                <div class="row">
                    <!-- Total Users -->
                    <div class="col-xl-3 col-sm-6 col-12">
                        <div class="stats-card">
                            <div class="stats-icon primary">
                                <i class="fas fa-users"></i>
                            </div>
                            <div class="stats-number"><?php echo $totalUsers; ?></div>
                            <div class="stats-title">Total Users</div>
                        </div>
                    </div>
                    
                    <!-- Total Admins -->
                    <div class="col-xl-3 col-sm-6 col-12">
                        <div class="stats-card">
                            <div class="stats-icon warning">
                                <i class="fas fa-user-shield"></i>
                            </div>
                            <div class="stats-number"><?php echo $totalAdmins; ?></div>
                            <div class="stats-title">Total Admins</div>
                        </div>
                    </div>
                    
                    <!-- Total Agents -->
                    <div class="col-xl-3 col-sm-6 col-12">
                        <div class="stats-card">
                            <div class="stats-icon success">
                                <i class="fas fa-user-tie"></i>
                            </div>
                            <div class="stats-number"><?php echo $totalAgents; ?></div>
                            <div class="stats-title">Total Agents</div>
                        </div>
                    </div>
                    
                    <!-- Total Trips -->
                    <div class="col-xl-3 col-sm-6 col-12">
                        <div class="stats-card">
                            <div class="stats-icon info">
                                <i class="fas fa-map-marked-alt"></i>
                            </div>
                            <div class="stats-number"><?php echo $totalTrips; ?></div>
                            <div class="stats-title">Total Trips</div>
                        </div>
                    </div>
                </div>
                
                <!-- Second Row of Stats Cards -->
                <div class="row">
                    <!-- Total Contacts -->
                    <div class="col-xl-3 col-sm-6 col-12">
                        <div class="stats-card">
                            <div class="stats-icon warning">
                                <i class="fas fa-envelope"></i>
                            </div>
                            <div class="stats-number"><?php echo $totalContacts; ?></div>
                            <div class="stats-title">Contact </div>
                        </div>
                    </div>
                    
                    <!-- Total Reviews -->
                    <div class="col-xl-3 col-sm-6 col-12">
                        <div class="stats-card">
                            <div class="stats-icon primary">
                                <i class="fas fa-star"></i>
                            </div>
                            <div class="stats-number"><?php echo $totalReviews; ?></div>
                            <div class="stats-title">Total Reviews</div>
                        </div>
                    </div>
                    
                    <!-- Total Blogs -->
                    <div class="col-xl-3 col-sm-6 col-12">
                        <div class="stats-card">
                            <div class="stats-icon success">
                                <i class="fas fa-blog"></i>
                            </div>
                            <div class="stats-number"><?php echo $totalBlogs; ?></div>
                            <div class="stats-title">Total Blogs</div>
                        </div>
                    </div>
                    
                    <!-- Quick Actions -->
                    <div class="col-xl-3 col-sm-6 col-12">
                        <div class="stats-card">
                            <div class="stats-icon info">
                                <i class="fas fa-bolt"></i>
                            </div>
                            <div class="stats-number">24/7</div>
                            <div class="stats-title">Support</div>
                        </div>
                    </div>
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