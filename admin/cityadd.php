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
								<h3 class="page-title">State</h3>
								<ul class="breadcrumb">
									<li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
									<li class="breadcrumb-item active">State</li>
								</ul>
							</div>
						</div>
					</div>
					<!-- /Page Header -->
					
				<!-- city add section --> 
					<div class="row">
						<div class="col-md-12">
							<div class="card">
								<div class="card-header">
									<h1 class="card-title">Add City</h1>
									
								</div>
								<form method="post" id="insert product" enctype="multipart/form-data">
									<div class="card-body">
											<div class="row">
												<div class="col-xl-6">
													<h5 class="card-title">City Details</h5>
													
													<div class="form-group row">
														<label class="col-lg-3 col-form-label">Country Name</label>
														<div class="col-lg-9">
															<input type="text" class="form-control" name="country">
														</div>
													</div>
                                                    <div class="form-group row">
														<label class="col-lg-3 col-form-label">City Name</label>
														<div class="col-lg-9">
															<input type="text" class="form-control" name="city">
														</div>
													</div>
                                                    <div class="form-group row">
														<label class="col-lg-3 col-form-label">Cover Image</label>
														<div class="col-lg-9">
															<input type="file" class="form-control" name="image">
														</div>
													</div>
												</div>
											</div>
											<div class="text-left">
												<input type="submit" class="btn btn-primary"  value="Submit" name="insertcity" style="margin-left:200px;">
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

							<h4 class="header-title mt-0 mb-4">Citiese Added <h4>

							<table id="datatable-buttons" class="table table-striped dt-responsive nowrap">
								<thead>
									<tr>
										<!-- <th>P ID</th> -->
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
                                            <td><img  src='../" . htmlspecialchars($rows['cover_image']) . "' alt='' style='height: 100px;'></td>
											<td>" . htmlspecialchars($rows['country_name']) . "</td>
											<td>" . htmlspecialchars($rows['city_name']) . "</td>
												<td>
													<div class='d-grid gap-2 d-md-block'>
                        <a href='citydelete.php?id=$id' class='btn btn-danger btn-sm mb-2'>Delete</a>
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