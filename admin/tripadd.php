<?php
if (isset($_POST['addtrip'])) {
    // Database connection
    include 'config.php'; // Adjust the path to your db connection file

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
    $seats_available = mysqli_real_escape_string($con, $_POST['departure']);

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
        echo "Error uploading trip image.";
        exit;
    }

    // Handle vehicle image upload
    $vehicle_image = upload_image($_FILES, "img/vehicleimages/", 'vehicle_image');
    if (!$vehicle_image) {
        echo "Error uploading vehicle image.";
        exit;
    }

    // Handle driver image upload
    $driver_image = upload_image($_FILES, "img/driverimages/", 'driver_image');
    if (!$driver_image) {
        echo "Error uploading driver image.";
        exit;
    }

    // Insert form data into the 'trips' table
    $sql = "INSERT INTO trips (trip_name, trip_image, description, starts_date, end_date, destination, budget, persons, stars, duration_days, distance_km, vehicle_type, vehicle_capacity, vehicle_features, driver_details, vehicle_image, driver_image, seats_available, departure) 
            VALUES ('$trip_name', '$trip_image', '$description', '$start_date', '$end_date', '$destination', '$budget', '$persons', '$stars', '$duration_days', '$distance_km', '$vehicle_type', '$vehicle_capacity', '$vehicle_features', '$driver_details', '$vehicle_image', '$driver_image', '$seats_available', 'departure')";

    // Execute the query
    if (mysqli_query($con, $sql)) {
        echo "New trip added successfully.";
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

            <div class="container-fluid py-5" style="margin: 90px 0; background-attachment: fixed !important; margin-top:20px;">
    <div class="container py-5">
        <div class="text-center mb-5">
            <h6 class="text-primary text-uppercase" style="letter-spacing: 5px;">Travel with Comfort</h6>
            <h1 class="display-4">Add Trip Form</h1>
        </div>
        <div class="row">
            <!-- Form Section -->
            <div class="col-lg-12 col-md-12" data-aos="fade-left">
                <div class="card border-0 shadow-lg">
                    <div class="card-body p-5">
                    <form action="" method="POST" enctype="multipart/form-data">
    <!-- Trip Name -->
    <div class="form-group">
        <label for="trip_name">Trip Name:</label>
        <input type="text" class="form-control" id="trip_name" name="trip_name" required>
    </div>

    <!-- Departure -->
<div class="form-group">
    <label for="departure">Departure Location:</label>
    <input type="text" class="form-control" id="departure" name="departure" required>
</div>

    <!-- Description -->
    <div class="form-group">
        <label for="description">Description:</label>
        <textarea class="form-control" id="description" name="description" rows="3" required></textarea>
    </div>

    <!-- Start Date -->
    <div class="form-group">
        <label for="starts_date">Start Date:</label>
        <input type="date" class="form-control" id="starts_date" name="starts_date" required>
    </div>

    <!-- End Date -->
    <div class="form-group">
        <label for="end_date">End Date:</label>
        <input type="date" class="form-control" id="end_date" name="end_date" required>
    </div>

    <!-- Destination -->
    <div class="form-group">
        <label for="destination">Destination:</label>
        <input type="text" class="form-control" id="destination" name="destination" required>
    </div>

    <!-- Budget -->
    <div class="form-group">
        <label for="budget">Budget:</label>
        <input type="number" class="form-control" id="budget" name="budget" required>
    </div>

    <!-- Persons -->
    <div class="form-group">
        <label for="persons">Persons:</label>
        <input type="number" class="form-control" id="persons" name="persons" required>
    </div>

    <!-- Stars -->
    <div class="form-group">
        <label for="stars">Stars:</label>
        <input type="number" class="form-control" id="stars" name="stars" required>
    </div>

    <!-- Duration Days -->
    <div class="form-group">
        <label for="duration_days">Duration (Days):</label>
        <input type="number" class="form-control" id="duration_days" name="duration_days" required>
    </div>

    <!-- Distance KM -->
    <div class="form-group">
        <label for="distance_km">Distance (KM):</label>
        <input type="number" class="form-control" id="distance_km" name="distance_km" required>
    </div>

    <!-- Vehicle Type -->
    <div class="form-group">
        <label for="vehicle_type">Vehicle Type:</label>
        <input type="text" class="form-control" id="vehicle_type" name="vehicle_type" required>
    </div>

    <!-- Vehicle Capacity -->
    <div class="form-group">
        <label for="vehicle_capacity">Vehicle Capacity:</label>
        <input type="number" class="form-control" id="vehicle_capacity" name="vehicle_capacity" required>
    </div>

    <!-- Vehicle Features -->
    <div class="form-group">
        <label for="vehicle_features">Vehicle Features:</label>
        <textarea class="form-control" id="vehicle_features" name="vehicle_features" rows="3" required></textarea>
    </div>

    <!-- Driver Details -->
    <div class="form-group">
        <label for="driver_details">Driver Details:</label>
        <textarea class="form-control" id="driver_details" name="driver_details" rows="3" required></textarea>
    </div>

    <!-- Seats Available -->
    <div class="form-group">
        <label for="seats_available">Seats Available:</label>
        <input type="number" class="form-control" id="seats_available" name="seats_available" required>
    </div>

    <!-- Trip Image -->
    <div class="form-group">
        <label for="trip_image">Trip Image:</label>
        <input type="file" class="form-control-file" id="trip_image" name="trip_image" required>
    </div>

    <!-- Vehicle Image -->
    <div class="form-group">
        <label for="vehicle_image">Vehicle Image:</label>
        <input type="file" class="form-control-file" id="vehicle_image" name="vehicle_image" required>
    </div>

    <!-- Driver Image -->
    <div class="form-group">
        <label for="driver_image">Driver Image:</label>
        <input type="file" class="form-control-file" id="driver_image" name="driver_image" required>
    </div>

    <!-- Submit Button -->
    <button type="submit" name="addtrip" class="btn btn-primary">Add Trip</button>
</form>

<?php
if (isset($_POST['addtrip'])) {
    // Add your trip handling code here...

    // Display success message after form submission
    echo "<div class='alert alert-success mt-3'>Your details have been updated successfully.</div>";
}
?>

                    </div>
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