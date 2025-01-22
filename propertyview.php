<?php
session_start();
require("config.php");
////code

if (!isset($_SESSION['auser'])) {
	header("location:index.php");
}
?>
<?php
// Database connection (update with your actual database credentials)
$servername = "localhost"; // Replace with your server details
$username = "root";        // Replace with your database username
$password = "";            // Replace with your database password
$dbname = "trip_travel"; // Replace with your actual database name

// Create connection
$con = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($con->connect_error) {
    die("Connection failed: " . $con->connect_error);
}

// Fetch destination values from the 'trips' table
$query = "SELECT DISTINCT destination FROM trips";
$result = $con->query($query);
?>
<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
	<title>Tameer.com</title>
	<title>Ventura - Data Tables</title>

	<!-- Favicon -->
	<link rel="shortcut icon" type="image/x-icon" href="assets/img/favicon.png">

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
<style>
    
body {
  --sb-track-color: #232E33;
  --sb-thumb-color: #7AB730;
  --sb-size: 14px;
}

body::-webkit-scrollbar {
  width: 12px;
}

body::-webkit-scrollbar-track {
  background: var(--sb-track-color);
  border-radius: 1px;
}

body::-webkit-scrollbar-thumb {
  background: var(--sb-thumb-color);
  border-radius: 3px;
  
}

@supports not selector(::-webkit-scrollbar) {
  body {
    scrollbar-color: var(--sb-thumb-color)
                     var(--sb-track-color);
  }
}
</style>
	<!-- Main Wrapper -->


	<!-- Header -->
	<?php include("header.php"); ?>
	<!-- /Sidebar -->

	<!-- Page Wrapper -->
	<div class="page-wrapper">
		<div class="content container-fluid">

			<!-- Page Header -->
			<div class="page-header">
				<div class="row">
					<div class="col">
						<h3 class="page-title">Property</h3>
						<ul class="breadcrumb">
							<li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
							<li class="breadcrumb-item active">Property</li>
						</ul>
					</div>
				</div>
			</div>
			<!-- /Page Header -->




			<div class="row">
				<div class="col-12">
					<div class="card">
						<div class="card-body">

							<h4 class="header-title mt-0 mb-4">Property View</h4>

							<table id="datatable-buttons" class="table table-striped dt-responsive nowrap">
								<thead>
									<tr>
										<!-- <th>P ID</th> -->
										<th>Trip Name</th>
										<th>Trip Description</th>
										<th>Start Date</th>
										<th>End Date</th>

										<th>Destination</th>
										<th>Budget</th>
										<th>Persons</th>


										<th>Stars</th>
										<th>Duration Days</th>
										<th>Actions</th>

									</tr>
								</thead>


								<tbody>

									<?php

									$sql2 = "SELECT * FROM trips";
									$result = mysqli_query($con, $sql2);

									$rows_num = mysqli_num_rows($result);

									if ($rows_num > 0) {
										while ($rows = mysqli_fetch_assoc($result)) {
											$id = htmlspecialchars($rows['trip_id']);
											echo "<tr>
												<td>" . htmlspecialchars($rows['trip_name']) . "</td>
												<td>" . htmlspecialchars($rows['description']) . "</td>
												<td>" . htmlspecialchars($rows['starts_date']) . "</td>
												<td>" . htmlspecialchars($rows['end_date']) . "</td>
												<td>" . htmlspecialchars($rows['destination']) . "</td>
												<td>" . htmlspecialchars($rows['budget']) . "</td>
												<td>" . htmlspecialchars($rows['persons']) . "</td>
												<td>" . htmlspecialchars($rows['stars']) . "</td>
												<td>" . htmlspecialchars($rows['duration_days']) . "</td>
												<td>
													<div class='d-grid gap-2 d-md-block'>
														<a href='#' onclick='confirmDelete($id)' class='btn btn-danger btn-sm mb-2'>Delete</a>
													</div>
												</td>
											</tr>";
										}
										
									}
									?>



								</tbody>
							</table>

						</div> <!-- end card body-->
					</div> <!-- end card -->
				</div><!-- end col-->
			</div>
			<!-- end row-->

		</div>
	</div>
	<!-- /Main Wrapper -->


	<!-- jQuery -->
	<script src="assets/js/jquery-3.2.1.min.js"></script>

	<!-- Bootstrap Core JS -->
	<script src="assets/js/popper.min.js"></script>
	<script src="assets/js/bootstrap.min.js"></script>

	<!-- Slimscroll JS -->
	<script src="assets/plugins/slimscroll/jquery.slimscroll.min.js"></script>

	<!-- Datatables JS -->
	<script src="assets/plugins/datatables/jquery.dataTables.min.js"></script>
	<script src="assets/plugins/datatables/dataTables.bootstrap4.min.js"></script>
	<script src="assets/plugins/datatables/dataTables.responsive.min.js"></script>
	<script src="assets/plugins/datatables/responsive.bootstrap4.min.js"></script>

	<script src="assets/plugins/datatables/dataTables.select.min.js"></script>

	<script src="assets/plugins/datatables/dataTables.buttons.min.js"></script>
	<script src="assets/plugins/datatables/buttons.bootstrap4.min.js"></script>
	<script src="assets/plugins/datatables/buttons.html5.min.js"></script>
	<script src="assets/plugins/datatables/buttons.flash.min.js"></script>
	<script src="assets/plugins/datatables/buttons.print.min.js"></script>

	<!-- Custom JS -->
	<script src="assets/js/script.js"></script>
	
	<script>
	function confirmDelete(id) {
		if (confirm('Are you sure you want to delete this trip?')) {
			window.location.href = 'deletetrip.php?deleteid=' + id; // Adjust the URL as needed
		}
	}
	</script>
</body>

</html>