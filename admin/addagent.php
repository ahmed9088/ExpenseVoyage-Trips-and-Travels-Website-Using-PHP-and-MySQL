<?php
// Include database connection
include 'config.php';

if (isset($_POST['submit'])) {
    // Get form data
    $a_name = $_POST['a_name'];
    $a_profetion = $_POST['a_profetion'];

    // Handle file upload
    if (isset($_FILES['a_image']) && $_FILES['a_image']['error'] === 0) {
        $image = $_FILES['a_image'];
        $imageName = time() . '_' . $image['name']; // Unique name
        $imagePath = 'upload/agents/' . $imageName;

        // Move uploaded file to the server
        move_uploaded_file($image['tmp_name'], $imagePath);
    } else {
        $imageName = null; // No image uploaded
    }

    // Insert into the agent table
    $sql = "INSERT INTO agent (a_name, a_profetion, a_image, date_time) VALUES (?, ?, ?, NOW())";
    $stmt = $con->prepare($sql);

    if ($stmt === false) {
        die("Prepare failed: (" . $con->errno . ") " . $con->error);
    }

    $stmt->bind_param('sss', $a_name, $a_profetion, $imageName);

    if ($stmt->execute()) {
        echo "Agent added successfully!";
    } else {
        echo "Error executing query: " . $stmt->error;
    }

    // Close the statement
    $stmt->close();
}

// Ensure connection is set before closing
if (isset($con)) {
    $con->close();
}
?>



<!DOCTYPE html>
<html lang="en">
    
<!-- Mirrored from dreamguys.co.in/demo/ventura/form-basic-inputs.html by HTTrack Website Copier/3.x [XR&CO'2014], Sun, 25 Aug 2019 04:41:04 GMT -->
<head>
        <meta charset="utf-8">
          <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
        <title>Tameer.com</title>
        <title>Traveler.com</title>
		
		<!-- Favicon -->
       <link rel="shortcut icon" type="image/x-icon" href="assets/img/favicon-32x32.png">
		
		<!-- Bootstrap CSS -->
        <link rel="stylesheet" href="assets/css/bootstrap.min.css">
		
		<!-- Fontawesome CSS -->
        <link rel="stylesheet" href="assets/css/font-awesome.min.css">
		
		<!-- Feathericon CSS -->
        <link rel="stylesheet" href="assets/css/feathericon.min.css">
		
		<!-- Main CSS -->
        <link rel="stylesheet" href="assets/css/style.css">
		
		<!--[if lt IE 9]>
			<script src="assets/js/html5shiv.min.js"></script>
			<script src="assets/js/respond.min.js"></script>
		<![endif]-->
    </head>
    <body>

		
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
								<h3 class="page-title">Basic Inputs</h3>
								<ul class="breadcrumb">
									<li class="breadcrumb-item"><a href="index.html">Dashboard</a></li>
									<li class="breadcrumb-item active">Basic Inputs</li>
								</ul>
							</div>
						</div>
					</div>
					<!-- /Page Header -->
					
					<form action="addagent.php" method="post" enctype="multipart/form-data">
    <div class="form-group row">
        <label class="col-form-label col-md-2">Agent Name</label>
        <div class="col-md-10">
            <input type="text" name="a_name" class="form-control" required>
        </div>
    </div>
    <div class="form-group row">
        <label class="col-form-label col-md-2">Agent Profession</label>
        <div class="col-md-10">
            <input type="text" name="a_profetion" class="form-control" required>
        </div>
    </div>
    <div class="form-group row">
        <label class="col-form-label col-md-2">Profile</label>
        <div class="col-md-10">
            <input class="form-control" type="file" name="a_image" required>
            <br>
            <button class="btn btn-primary" name="submit" style="width: 300px;" type="submit">Add Agent</button>
        </div>
    </div>
</form>
<?php
// Include database connection
include 'config.php';

// Check if there's a request to delete an agent
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];
    $delete_sql = "DELETE FROM agent WHERE id = ?";
    $stmt = $con->prepare($delete_sql);
    $stmt->bind_param('i', $delete_id);
    if ($stmt->execute()) {
        echo "<script>alert('Agent deleted successfully');</script>";
        echo "<script>window.location.href='addagent.php';</script>";
    } else {
        echo "<script>alert('Error deleting agent');</script>";
    }
}
?>

<!-- HTML table structure for displaying agents -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <h4 class="header-title mt-0 mb-4">Agents List</h4>

                <table id="datatable-buttons" class="table table-striped dt-responsive nowrap">
                    <thead>
                        <tr>
                            <th>Agent Name</th>
                            <th>Agent Profession</th>
                            <th>Profile</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Fetch all agents from the database
                        $sql = "SELECT id, a_name, a_profetion, a_image FROM agent";
                        $result = $con->query($sql);

                        if ($result->num_rows > 0) {
                            // Output data of each row
                            while ($row = $result->fetch_assoc()) {
                                echo "<tr>";
                                echo "<td>" . $row['a_name'] . "</td>";
                                echo "<td>" . $row['a_profetion'] . "</td>";
                                echo "<td><img src='upload/agents/" . $row['a_image'] . "' width='50' height='50'></td>";
                                echo "<td>
                                    <a href='addagent.php?delete_id=" . $row['id'] . "' onclick='return confirm(\"Are you sure you want to delete this agent?\")' class='btn btn-danger btn-sm'>Delete</a>
                                    </td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='4'>No agents found</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>

            </div> <!-- end card body-->
        </div> <!-- end card -->
    </div> <!-- end col-12 -->
</div> <!-- end row -->

<!-- End of HTML and PHP code -->


			<!-- /Main Wrapper -->
			 

		
		<!-- jQuery -->
        <script src="assets/js/jquery-3.2.1.min.js"></script>
		
		<!-- Bootstrap Core JS -->
        <script src="assets/js/popper.min.js"></script>
        <script src="assets/js/bootstrap.min.js"></script>
		
		<!-- Slimscroll JS -->
        <script src="assets/plugins/slimscroll/jquery.slimscroll.min.js"></script>
		
		<!-- Custom JS -->
		<script  src="assets/js/script.js"></script>
		
    </body>

</html>