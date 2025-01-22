<?php
session_start();
include 'admin/config.php'; // Your database connection file

if (!isset($_SESSION['userid'])) {
    echo "User ID is not set in session.";
    exit();
}

$userid = $_SESSION['userid'];

// Fetch user data including the image
$sql = "SELECT name, email, password FROM user WHERE id = '$userid'";
$result = mysqli_query($con, $sql);

if ($result) {
    $user = mysqli_fetch_assoc($result);
} else {
    echo "Error: " . mysqli_error($con);
}

// Check if the form was submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['first_name'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? ''; // New password
    $old_password = $_POST['old_password'] ?? ''; // Old password for verification (optional)

    // Check if the old password is provided and validate it
    if (!empty($old_password)) {
        // Verify the old password
        if (!password_verify($old_password, $user['password'])) {
            echo "Old password is incorrect.";
            exit();
        }
    }

    // Prepare the SQL update query
    if (!empty($password)) {
        // Hash the new password before updating
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $sql_update = "UPDATE user SET name = ?, email = ?, password = ? WHERE id = ?";
        $stmt = mysqli_prepare($con, $sql_update);
        mysqli_stmt_bind_param($stmt, 'sssi', $name, $email, $hashed_password, $userid);
    } else {
        // If the password is not provided, do not update it
        $sql_update = "UPDATE user SET name = ?, email = ? WHERE id = ?";
        $stmt = mysqli_prepare($con, $sql_update);
        mysqli_stmt_bind_param($stmt, 'ssi', $name, $email, $userid);
    }

    // Execute the update query
    if (mysqli_stmt_execute($stmt)) {
        echo "Profile updated successfully!";
    } else {
        echo "Error updating profile: " . mysqli_stmt_error($stmt);
    }

    mysqli_stmt_close($stmt);
}

// Close the database connection
mysqli_close($con);
?>



<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Traveler.com</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta content="Free HTML Templates" name="keywords">
    <meta content="Free HTML Templates" name="description">

    <!-- Favicon -->
    <link href="img/favicon.ico" rel="icon">
    <link rel="apple-touch-icon" sizes="180x180" href="img/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="img/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="img/favicon-16x16.png">
    <link rel="manifest" href="img/site.webmanifest">
    <link rel="mask-icon" href="img/safari-pinned-tab.svg" color="#5bbad5">
    <meta name="msapplication-TileColor" content="#da532c">
    <meta name="theme-color" content="#ffffff">
    <link rel="apple-touch-icon" sizes="180x180" href="img/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="img/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="img/favicon-16x16.png">
    <link rel="manifest" href="img/site.webmanifest">
    <link rel="mask-icon" href="img/safari-pinned-tab.svg" color="#5bbad5">
    <meta name="msapplication-TileColor" content="#da532c">
    <meta name="theme-color" content="#ffffff">

    <!-- Google Web Fonts -->
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" rel="stylesheet">

    <!-- Libraries Stylesheet -->
    <link href="lib/owlcarousel/assets/owl.carousel.min.css" rel="stylesheet">
    <link href="lib/tempusdominus/css/tempusdominus-bootstrap-4.min.css" rel="stylesheet" />

    <!-- Customized Bootstrap Stylesheet -->
    <link href="css/style.css" rel="stylesheet">
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
    <!-- Topbar Start -->
    <div class="container-fluid bg-light pt-3 d-none d-lg-block">
        <div class="container">
            <div class="row">
                <div class="col-lg-6 text-center text-lg-left mb-2 mb-lg-0">
                    <div class="d-inline-flex align-items-center">
                        <p><i class="fa fa-envelope mr-2"></i>ubaidsoomro505@gmail.com</p>
                        <p class="text-body px-3">|</p>
                        <p><i class="fa fa-phone-alt mr-2"></i>+92 3188 893 863</p>
                    </div>
                </div>
                <div class="col-lg-6 text-center text-lg-right">
                    <div class="d-inline-flex align-items-center">
                        <a class="text-primary px-3" href="">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                        <a class="text-primary px-3" href="">
                            <i class="fab fa-twitter"></i>
                        </a>
                        <a class="text-primary px-3" href="">
                            <i class="fab fa-linkedin-in"></i>
                        </a>
                        <a class="text-primary px-3" href="">
                            <i class="fab fa-instagram"></i>
                        </a>
                        <a class="text-primary pl-3" href="">
                            <i class="fab fa-youtube"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Topbar End -->


    <!-- Navbar Start -->
    <div class="container-fluid position-relative nav-bar p-0">
        <div class="container-lg position-relative p-0 px-lg-3" style="z-index: 9;">
            <nav class="navbar navbar-expand-lg bg-light navbar-light shadow-lg py-3 py-lg-0 pl-3 pl-lg-5">
                <a href="" class="navbar-brand">
                    <h1 class="text-primary" style="font-size:25px;"><span style="color:black;">Expense</span>Voyage
                    </h1>
                </a>
                <button type="button" class="navbar-toggler" data-toggle="collapse" data-target="#navbarCollapse">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse justify-content-between px-3" id="navbarCollapse">
                    <div class="navbar-nav ml-auto py-0">
                        <a href="index.php" class="nav-item nav-link">Home</a>
                        <a href="about.php" class="nav-item nav-link active">About</a>
                        <a href="service.php" class="nav-item nav-link">Services</a>
                        <a href="package.php" class="nav-item nav-link">Tour Packages</a>
                        <div class="nav-item dropdown">
                            <a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown">Pages</a>
                            <div class="dropdown-menu border-0 rounded-0 m-0">
                                <a href="blog.php" class="dropdown-item">Blog</a>

                                <a href="destination.php" class="dropdown-item">Destination</a>
                                <a href="guide.php" class="dropdown-item">Travel Guides</a>

                            </div>
                        </div>
                        <a href="contact.php" class="nav-item nav-link">Contact</a>

                        <div class="login-register d-flex align-items-center">
                            <?php
if (isset($_SESSION['email'])) {
    // Fetch the user details from the session
    $name = $_SESSION['name'] ?? 'User';

    // Display the dropdown with the user's name
    echo '<div class="nav-item dropdown">
        <a href="#" class="nav-link dropdown-toggle active p-0" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <span>' . htmlspecialchars($name) . '</span>
        </a>
        <div class="dropdown-menu border-0 rounded-0 m-0">
            <a href="user-profile.php" class="dropdown-item">MY Account</a>
            <a href="admin/index.php" class="dropdown-item">Only Admin</a>
            <a href="booking.php" class="dropdown-item">Booking</a>
            <a href="logout.php" class="dropdown-item">Logout</a>
        </div>
    </div>';
} else {
    // Show login/register option if user is not logged in
    echo '<div class="nav-item dropdown">
        <a href="#" class="nav-link dropdown-toggle active p-0" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <span>Login/Register</span>
        </a>
        <div class="dropdown-menu border-0 rounded-0 m-0">
            <a href="login/account.php" class="dropdown-item active">Login/Register</a>
        </div>
    </div>';
}
?>






                        </div>
                    </div>
                </div>
            </nav>
        </div>
    </div>
    <!-- Navbar End -->


    <!-- Header Start -->
    <div class="container-fluid page-header">
        <div class="container">
            <div class="d-flex flex-column align-items-center justify-content-center" style="min-height: 400px">
                <h3 class="display-4 text-white text-uppercase">Profile</h3>
                <div class="d-inline-flex text-white">
                    <p class="m-0 text-uppercase"><a class="text-white" href="index.php">Home</a></p>
                    <i class="fa fa-angle-double-right pt-1 px-3"></i>
                    <p class="m-0 text-uppercase">Update Profile</p>
                </div>
            </div>
        </div>
    </div>
    <!-- Header End -->

    <!-- profiloe form -->

    <div class="container">
        <br>
        <div class="row" id="main">
            <div class="col-md-4 well" id="leftPanel">
                <div class="row">
                    <div class="col-md-12">
                        <div>
                          <br><br>
                            <h2><?php echo htmlspecialchars($user['name']); ?></h2>
                            <br>

                            <b>Thank you for being a part of our community! <br> Regards: <br> EXPENSE VOYAGE 
                             Team</b>


                            <!-- Adjusted for the correct field -->
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-8 well" id="rightPanel">
                <div class="row">
                    <div class="col-md-12">
                    <form role="form" method="POST" action="update_profile.php" enctype="multipart/form-data">
    <h2>Edit your profile.<small>It's always easy</small></h2>
    <hr class="colorgraph">
    <div class="row">
        <div class="col-xs-12 col-sm-6 col-md-6">
            <div class="form-group">
                <input type="text" name="first_name" id="first_name" class="form-control input-lg" placeholder="First Name" tabindex="1" value="<?php echo htmlspecialchars($user['name']); ?>">
            </div>
        </div>
    </div>
    <div class="form-group">
        <input type="email" name="email" id="email" class="form-control input-lg" placeholder="Email Address" tabindex="2" value="<?php echo htmlspecialchars($user['email']); ?>">
    </div>
    
    <!-- Old Password Input Field -->
    <div class="form-group">
        <input type="password" name="old_password" id="old_password" class="form-control input-lg" placeholder="Current Password" tabindex="3">
    </div>
    
    <!-- New Password Input Field -->
    <div class="form-group">
        <input type="password" name="password" id="password" class="form-control input-lg" placeholder="New Password (Leave blank if not changing)" tabindex="4">
    </div>
    
    <hr class="colorgraph">
    <div class="row" style="margin-right:30%;">
        <div class="col-xs-12 col-md-6" style="padding:10%;"></div>
        <div class="col-xs-12 col-md-6">
            <button type="submit" class="btn btn-success btn-block btn-lg">Save</button>
        </div>
    </div>
</form>


                    </div>
                </div>
            </div>
        </div>
    </div>




    <!-- Footer Start -->
    <div class="container-fluid bg-dark text-white-50 py-5 px-sm-3 px-lg-5">
        <div class="row pt-5">
            <div class="col-lg-3 col-md-6 mb-5">
                <a href="" class="navbar-brand">
                    <a href="" class="navbar-brand">
                        <h1 class="text-primary" style="font-size:25px;"><span style="color:white;">Expense</span>Voyage
                        </h1>
                    </a>
                </a>
                <p>Sed ipsum clita tempor ipsum ipsum amet sit ipsum lorem amet labore rebum lorem ipsum dolor. No sed
                    vero lorem dolor dolor</p>
                <h6 class="text-white text-uppercase mt-4 mb-3" style="letter-spacing: 5px;">Follow Us</h6>
                <div class="d-flex justify-content-start">
                    <a class="btn btn-outline-primary btn-square mr-2" href="#"><i class="fab fa-twitter"></i></a>
                    <a class="btn btn-outline-primary btn-square mr-2" href="#"><i class="fab fa-facebook-f"></i></a>
                    <a class="btn btn-outline-primary btn-square mr-2" href="#"><i class="fab fa-linkedin-in"></i></a>
                    <a class="btn btn-outline-primary btn-square" href="#"><i class="fab fa-instagram"></i></a>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-5">
                <h5 class="text-white text-uppercase mb-4" style="letter-spacing: 5px;">Our Services</h5>
                <div class="d-flex flex-column justify-content-start">
                    <a class="text-white-50 mb-2" href="about.php"><i class="fa fa-angle-right mr-2"></i>About</a>
                    <a class="text-white-50 mb-2" href="destination.php"><i
                            class="fa fa-angle-right mr-2"></i>Destination</a>
                    <a class="text-white-50 mb-2" href="service.php"><i class="fa fa-angle-right mr-2"></i>Services</a>
                    <a class="text-white-50 mb-2" href="package.php"><i class="fa fa-angle-right mr-2"></i>Packages</a>
                    <a class="text-white-50 mb-2" href="guide.php"><i class="fa fa-angle-right mr-2"></i>Guides</a>

                    <a class="text-white-50" href="blog.php"><i class="fa fa-angle-right mr-2"></i>Blog</a>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-5">
                <h5 class="text-white text-uppercase mb-4" style="letter-spacing: 5px;">Useful Links</h5>
                <div class="d-flex flex-column justify-content-start">
                    <!-- Social Media Links -->
                    <a class="text-white-50 mb-2" href="#"><i class="fab fa-facebook-f mr-2"></i>Facebook</a>
                    <a class="text-white-50 mb-2" href="#"><i class="fab fa-instagram mr-2"></i>Instagram</a>
                    <a class="text-white-50 mb-2" href="#"><i class="fab fa-twitter mr-2"></i>Twitter</a>
                    <a class="text-white-50 mb-2" href="#"><i class="fab fa-linkedin mr-2"></i>LinkedIn</a>
                    <a class="text-white-50 mb-2" href="#"><i class="fab fa-skype mr-2"></i>Skype</a>
                    <a class="text-white-50" href="mailto:your-email@example.com"><i
                            class="fa fa-envelope mr-2"></i>Gmail</a>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 mb-5">
                <h5 class="text-white text-uppercase mb-4" style="letter-spacing: 5px;">Contact Us</h5>
                <p><i class="fa fa-map-marker-alt mr-2"></i>Aptech Def Hyderabad Sindh Pakistan</p>
                <p><i class="fa fa-phone-alt mr-2"></i>+92 3188 893 8630</p>
                <p><i class="fa fa-envelope mr-2"></i>ubaidsoomro505@gmail.com</p>
                <h6 class="text-white text-uppercase mt-4 mb-3" style="letter-spacing: 5px;">Newsletter</h6>
                <div class="w-100">
                    <div class="input-group">
                        <input type="text" class="form-control border-light" style="padding: 25px;"
                            placeholder="Your Email">
                        <div class="input-group-append">
                            <button class="btn btn-primary px-3">Sign Up</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>



    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.bundle.min.js"></script>
    <script src="lib/easing/easing.min.js"></script>
    <script src="lib/owlcarousel/owl.carousel.min.js"></script>
    <script src="lib/tempusdominus/js/moment.min.js"></script>
    <script src="lib/tempusdominus/js/moment-timezone.min.js"></script>
    <script src="lib/tempusdominus/js/tempusdominus-bootstrap-4.min.js"></script>

    <!-- Contact Javascript File -->
    <script src="mail/jqBootstrapValidation.min.js"></script>
    <script src="mail/contact.js"></script>
</body>

</html>