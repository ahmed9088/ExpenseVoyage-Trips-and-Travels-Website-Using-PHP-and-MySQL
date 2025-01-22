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
       <title>Traveler.com</title>
		
		<!-- Favicon -->
		 
        <link rel="shortcut icon" type="image/x-icon" href="assets/img/favicon-32x32.png">
		
		<!-- Bootstrap CSS -->
        <link rel="stylesheet" href="assets/css/bootstrap.min.css">
		
		<!-- Fontawesome CSS -->
        <link rel="stylesheet" href="assets/css/font-awesome.min.css">
		
		<!-- Feathericon CSS -->
        <link rel="stylesheet" href="assets/css/feathericon.min.css">
		
		<!-- Datatables CSS -->
		<link rel="stylesheet" href="assets/plugins/datatables/dataTables.bootstrap4.min.css">
		<link rel="stylesheet" href="assets/plugins/datatables/responsive.bootstrap4.min.css">
		<link rel="stylesheet" href="assets/plugins/datatables/select.bootstrap4.min.css">
		<link rel="stylesheet" href="assets/plugins/datatables/buttons.bootstrap4.min.css">
		
		<!-- Main CSS -->
        <link rel="stylesheet" href="assets/css/style.css">
		
		<!--[if lt IE 9]>
			<script src="assets/js/html5shiv.min.js"></script>
			<script src="assets/js/respond.min.js"></script>
		<![endif]-->
    </head>
    <body>
	
		<!-- Main Wrapper -->

		
			<!-- Header -->
				<?php include("header.php"); ?>
			<!-- /Header -->
			
			<!-- Page Wrapper -->
            <div class="page-wrapper">
			
                <div class="content container-fluid">
					
					<!-- Page Header -->
					<div class="page-header">
						<div class="row">
							<div class="col-sm-12">
								<h3 class="page-title">Welcome Admin!</h3>
								<p></p>
								<ul class="breadcrumb">
									<li class="breadcrumb-item active">Dashboard</li>
								</ul>
							</div>
						</div>
					</div>
					<!-- /Page Header -->

					<div class="row">
    <!-- Total Users -->
    <div class="col-xl-3 col-sm-6 col-12">
        <div class="card">
            <div class="card-body">
                <div class="dash-widget-header">
                    <span class="dash-widget-icon bg-primary">
                        <i class="fe fe-users"></i>
                    </span>
                </div>
                <div class="dash-widget-info">
                    <h3><?php echo $totalUsers; ?></h3>
                    <h6 class="text-muted">Total Users</h6>
                </div>
            </div>
        </div>
    </div>

    <!-- Total Admins -->
    <div class="col-xl-3 col-sm-6 col-12">
        <div class="card">
            <div class="card-body">
                <div class="dash-widget-header">
                    <span class="dash-widget-icon bg-warning">
                        <i class="fe fe-user"></i>
                    </span>
                </div>
                <div class="dash-widget-info">
                    <h3><?php echo $totalAdmins; ?></h3>
                    <h6 class="text-muted">Total Admins</h6>
                </div>
            </div>
        </div>
    </div>

    <!-- Total Agents -->
    <div class="col-xl-3 col-sm-6 col-12">
        <div class="card">
            <div class="card-body">
                <div class="dash-widget-header">
                    <span class="dash-widget-icon bg-success">
                        <i class="fe fe-users"></i>
                    </span>
                </div>
                <div class="dash-widget-info">
                    <h3><?php echo $totalAgents; ?></h3>
                    <h6 class="text-muted">Total Agents</h6>
                </div>
            </div>
        </div>
    </div>

    <!-- Total Trips -->
    <div class="col-xl-3 col-sm-6 col-12">
        <div class="card">
            <div class="card-body">
                <div class="dash-widget-header">
                    <span class="dash-widget-icon bg-info">
                        <i class="fe fe-map"></i>
                    </span>
                </div>
                <div class="dash-widget-info">
                    <h3><?php echo $totalTrips; ?></h3>
                    <h6 class="text-muted">Total Trips</h6>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Total Contacts -->
<div class="row"> 
<div class="col-xl-3 col-sm-6 col-12">
    <div class="card">
        <div class="card-body">
            <div class="dash-widget-header">
                <span class="dash-widget-icon bg-warning">
                    <i class="fe fe-phone"></i>
                </span>
            </div>
            <div class="dash-widget-info">
                <h3><?php echo $totalContacts; ?></h3>
                <h6 class="text-muted">Contact Us Messages</h6>
            </div>
        </div>
    </div>
</div>

<!-- Total Reviews -->
<div class="col-xl-3 col-sm-6 col-12">
    <div class="card">
        <div class="card-body">
            <div class="dash-widget-header">
                <span class="dash-widget-icon bg-primary">
                    <i class="fe fe-star"></i>
                </span>
            </div>
            <div class="dash-widget-info">
                <h3><?php echo $totalReviews; ?></h3>
                <h6 class="text-muted">Total Reviews</h6>
            </div>
        </div>
    </div>
</div>

<!-- Total Blogs -->
<div class="col-xl-3 col-sm-6 col-12">
    <div class="card">
        <div class="card-body">
            <div class="dash-widget-header">
                <span class="dash-widget-icon bg-success">
                    <i class="fe fe-book"></i>
                </span>
            </div>
            <div class="dash-widget-info">
                <h3><?php echo $totalBlogs; ?></h3>
                <h6 class="text-muted">Total Blogs</h6>
            </div>
        </div>
    </div>
</div>
</div>


					<!-- <div class="row">
						<div class="col-md-12 col-lg-6">
						
							
							<div class="card card-chart">
								<div class="card-header">
									<h4 class="card-title">Sales Overview</h4>
								</div>
								<div class="card-body">
									<div id="morrisArea"></div>
								</div>
							</div>
							
							
						</div>
						<div class="col-md-12 col-lg-6">
						
							
							<div class="card card-chart">
								<div class="card-header">
									<h4 class="card-title">Order Status</h4>
								</div>
								<div class="card-body">
									<div id="morrisLine"></div>
								</div>
							</div>
							
							
						</div>	
					</div> -->
				</div>			
			</div>
			<!-- /Page Wrapper -->
		

		<!-- /Main Wrapper -->
		
		<!-- jQuery -->
        <script src="assets/js/jquery-3.2.1.min.js"></script>
		
		<!-- Bootstrap Core JS -->
        <script src="assets/js/popper.min.js"></script>
        <script src="assets/js/bootstrap.min.js"></script>
		
		<!-- Slimscroll JS -->
        <script src="assets/plugins/slimscroll/jquery.slimscroll.min.js"></script>
		
		<script src="assets/plugins/raphael/raphael.min.js"></script>    
		<script src="assets/plugins/morris/morris.min.js"></script>  
		<script src="assets/js/chart.morris.js"></script>
		
		<!-- Custom JS -->
		<script  src="assets/js/script.js"></script>
		
    </body>

</html>
