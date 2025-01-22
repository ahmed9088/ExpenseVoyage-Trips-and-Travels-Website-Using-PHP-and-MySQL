<?php
session_start();
require("config.php");
 

if(isset($_POST['addtrip'])){
    $triptitle = $_POST['title'];
    $tripdesc = $_POST['description'];
    $startdate = $_POST['startdate'];
    $destination = $_POST['destination'];
    $persons = $_POST['persons'];
    $durationday = $_POST['durationday'];
    $enddate = $_POST['enddate'];
    $budget = $_POST['budget'];
    $stars = $_POST['stars'];
    $file=  $_FILES["image"]["name"];
    $tempname=  $_FILES['image']['tmp_name'];
    $folder = "img/tripimages/".$file;
    $folder2 = "../img/tripimages/".$file;
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
        $sql = "INSERT INTO trips(trip_image,trip_name,description,starts_date,end_date,destination,budget,persons,stars,duration_days) VALUES('$folder','$triptitle','$tripdesc','$startdate','$enddate','$destination','$budget','$persons','$stars','$durationday')";
        $result = mysqli_query($con,$sql);


        if($result){
        echo "<script>alert('Trip Added Successfully');</script>";

        }
    }

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
<title>Traveler</title>

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
                        <h3 class="page-title">Trip</h3>
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                            <li class="breadcrumb-item active">Property</li>
                        </ul>
                    </div>
                </div>
            </div>
            <!-- /Page Header -->

            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Add Trip Details</h4>
                        </div>
                        <form method="post" enctype="multipart/form-data">
                            <div class="card-body">
                                <h5 class="card-title">Trip Detail</h5>

                                <div class="row">
                                    <div class="col-xl-12">
                                        <div class="form-group row">
                                            <label class="col-lg-2 col-form-label">Trip Title</label>
                                            <div class="col-lg-9">
                                                <input type="text" class="form-control" name="title" required
                                                    placeholder="Enter Title">
                                            </div>
                                        </div>
										<div class="form-group row">
                                            <label class="col-lg-2 col-form-label">Trip Description</label>
                                            <div class="col-lg-9">
                                                <textarea class="form-control" name="description" rows="5"></textarea>
                                            </div>
                                        </div>

                                    </div>
                                    <div class="col-xl-6">
                                       
                                        <div class="form-group row">
                                            <label class="col-lg-3 col-form-label">Start Date</label>
                                            <div class="col-lg-9">
                                                <input type="date" class="form-control" name="startdate" required
                                                    placeholder="">
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-lg-3 col-form-label">Destination</label>
                                            <div class="col-lg-9">
                                                <input type="text" class="form-control" name="destination" required
                                                    placeholder="Enter Destination">
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-lg-3 col-form-label">Persons</label>
                                            <div class="col-lg-9">
                                                <input type="number" class="form-control" name="persons" required
                                                    placeholder="Enter Max Persons">
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-lg-3 col-form-label">Duration Days</label>
                                            <div class="col-lg-9">
                                                <input type="text" class="form-control" name="durationday" required
                                                    placeholder="Enter Duration  (only no 1 to 60)">
                                            </div>
                                        </div>

                                    </div>
                                    <div class="col-xl-6">
                                    <div class="form-group row">
                                            <label class="col-lg-3 col-form-label">End Date</label>
                                            <div class="col-lg-9">
                                                <input type="date" class="form-control" name="enddate" required
                                                    placeholder="">
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-lg-3 col-form-label">Budget</label>
                                            <div class="col-lg-9">
                                                <input type="number" class="form-control" name="budget" required
                                                    placeholder="Enter Budget">
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-lg-3 col-form-label">Stars</label>
                                            <div class="col-lg-9">
                                                <input type="number" class="form-control" name="stars" required
                                                    placeholder="Enter Stars  (only no 1 to 5)">
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-lg-3 col-form-label"> Trip Image</label>
                                            <div class="col-lg-9">
                                                <input type="file" class="form-control" name="image" required
                                                    >
                                            </div>
                                        </div>

                                    </div>
                                </div>
                              

                                <div class="form-group row ">
							


                                <input type="submit" value="Add Trip" class="btn btn-primary" name="addtrip"
                                   >

                            </div>
                        </form>
                    </div>
                </div>
            </div>

        </div>
    </div>
    <!-- /Main Wrapper -->


    <!-- jQuery -->
    <script src="assets/js/jquery-3.2.1.min.js"></script>
    <script src="assets/plugins/tinymce/tinymce.min.js"></script>
    <script src="assets/plugins/tinymce/init-tinymce.min.js"></script>
    <!-- Bootstrap Core JS -->
    <script src="assets/js/popper.min.js"></script>
    <script src="assets/js/bootstrap.min.js"></script>

    <!-- Slimscroll JS -->
    <script src="assets/plugins/slimscroll/jquery.slimscroll.min.js"></script>

    <!-- Custom JS -->
    <script src="assets/js/script.js"></script>

</body>

</html>