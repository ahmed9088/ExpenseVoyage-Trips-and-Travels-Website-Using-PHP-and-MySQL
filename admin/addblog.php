<?php
session_start();
require("config.php");
 

if(isset($_POST['addblog'])){
    $blogtitle = $_POST['title'];
    $blogdesc = $_POST['description'];
    $file=  $_FILES["image"]["name"];
    $tempname=  $_FILES['image']['tmp_name'];
    $folder = "img/blogimg/".$file;
    $folder2 = "../img/blogimg/".$file;
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
        $sql = "INSERT INTO blog(blog_title,blog_image,blog_text) VALUES('$blogtitle','$folder','$blogdesc')";
        $result = mysqli_query($con,$sql);


        if($result){
        echo "<script>alert('Blog Added Successfully');</script>";

        }
    }

}
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
                        <h3 class="page-title">Blog</h3>
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                            <li class="breadcrumb-item active">Blog</li>
                        </ul>
                    </div>
                </div>
            </div>
            <!-- /Page Header -->

            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Add Blog Details</h4>
                        </div>
                        <form method="post" enctype="multipart/form-data">
                            <div class="card-body">
                                <h5 class="card-title">Blog Detail</h5>

                                <div class="row">
                                    <div class="col-xl-12">
                                        <div class="form-group row">
                                            <label class="col-lg-2 col-form-label">Blog Title</label>
                                            <div class="col-lg-9">
                                                <input type="text" class="form-control" name="title" required
                                                    placeholder="Enter Title">
                                            </div>
                                        </div>
										<div class="form-group row">
                                            <label class="col-lg-2 col-form-label">Blog Description</label>
                                            <div class="col-lg-9">
                                                <textarea class="form-control" name="description" rows="5"></textarea>
                                            </div>
                                        </div>

                                    </div>
                                  
                                    <div class="col-xl-6">
                                    
                                        
                                        <div class="form-group row">
                                            <label class="col-lg-3 col-form-label"> Blog Image</label>
                                            <div class="col-lg-9">
                                                <input type="file" class="form-control" name="image" required
                                                    >
                                            </div>
                                        </div>

                                    </div>
                                </div>
                              

                                <div class="form-group row ">
							


                                <input type="submit" value="Add Trip" class="btn btn-primary" name="addblog"
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