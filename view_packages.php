<?php
include 'admin/config.php';
session_start();

if (isset($_GET['viewid'])) {
    $id = intval($_GET['viewid']); // Sanitize the input
    $sql = "SELECT * FROM trips WHERE trip_id = ?";
    $stmt = $con->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc(); // Fetch the trip data
    } else {
        header("Location: package.php"); // Redirect if no trip is found
        exit();
    }
} else {
    header("Location: package.php"); // Redirect if no viewid is set
    exit();
}
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
    <!-- Favicon -->
    <link rel="apple-touch-icon" sizes="180x180" href="img/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="img/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="img/favicon-16x16.png">
    <link rel="manifest" href="img/site.webmanifest">
    <link rel="mask-icon" href="img/safari-pinned-tab.svg" color="#5bbad5">
    <meta name="msapplication-TileColor" content="#da532c">
    <meta name="theme-color" content="#ffffff">
    <!-- Favicon -->

    
<!-- AOS (Animate on Scroll) Library for Fade Effect -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.css" rel="stylesheet">
<script src="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.js"></script>

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
                        <p><i class="fa fa-envelope mr-2"></i>info@example.com</p>
                        <p class="text-body px-3">|</p>
                        <p><i class="fa fa-phone-alt mr-2"></i>+012 345 6789</p>
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
                    <h1 class="m-0 text-primary" style="font-size:25px;"><span style="color:black;">Expense</span>Voyage</h1>
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
    // Check if 'name' is set in the session, otherwise display a default name
    $name = isset($_SESSION['name']) ? $_SESSION['name'] : 'User';
    
    echo ' <div class="nav-item dropdown">
        <a href="#" class="nav-link dropdown-toggle active p-0" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <span>' . htmlspecialchars($name) . '</span> <!-- Display users name -->
        </a>
        <div class="dropdown-menu border-0 rounded-0 m-0">
            <a href="user-profile.php" class="dropdown-item">MY Account</a>
            <a href="admin/index.php" class="dropdown-item">Only Admin</a>
            <a href="booking.php" class="dropdown-item">Booking</a>
            <a href="logout.php" class="dropdown-item">Logout</a>
        </div>
    </div>';
} else {
    echo ' <div class="nav-item dropdown">
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
                <h3 class="display-4 text-white text-uppercase">View Details</h3>
                <div class="d-inline-flex text-white">
                    <p class="m-0 text-uppercase"><a class="text-white" href="">Home</a></p>
                    <i class="fa fa-angle-double-right pt-1 px-3"></i>
                    <p class="m-0 text-uppercase">View Details</p>
                </div>
            </div>
        </div>
    </div>
    <!-- Header End -->


    <!-- Booking Start -->
    <div class="container-fluid booking mt-5 pb-5">
        <div class="container pb-5">
            <div class="bg-light shadow" style="padding: 30px;">
                <form action="package.php" method="get">
                    <div class="row align-items-center" style="min-height: 60px;">
                        <div class="col-md-10">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="mb-3 mb-md-0">
                                        <input type="text" class="form-control p-4" placeholder="Enter Destination" name="destination">
                                    </div>
                                </div>



                            </div>
                        </div>
                        <div class="col-md-2">
                            <button class="btn btn-primary btn-block" type="submit"
                                style="height: 47px; margin-top: -2px;">Submit</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- Booking End -->

<!-- Blog Start -->
<div class="container-fluid py-5">
    <div class="container py-5">
        <div class="row">
            <div class="col-lg-8">
                <!-- Blog Detail Start -->
                <div class="pb-3">
                    <div class="blog-item">
                        <div class="position-relative">
                            <img class="img-fluid w-100" src="<?php echo $row['trip_image'] ?>" alt="">
                        </div>
                    </div>
                    <div class="bg-white mb-3" style="padding: 30px;">
                        <div class="d-flex mb-3">
                            <a class="text-primary text-uppercase text-decoration-none" href="">Admin</a>
                            <span class="text-primary px-2">|</span>
                            <a class="text-primary text-uppercase text-decoration-none" href="">Tours & Travel</a>
                        </div>
                        <h2 class="mb-3"><?php echo $row['trip_name'] ?></h2>
                        <p><?php echo $row['description'] ?></p>

                        <div class="col-lg-4 col-md-6 mb-4">
                            <div class="package-item bg-white mb-2" style="width:300%; margin-left:30px;">
                                <div class="p-4">
                                    <div class="d-flex justify-content-between mb-3">
                                        <small class="m-0"><i class="fa fa-map-marker-alt text-primary mr-2"></i><?php echo $row['destination'] ?></small>
                                        <small class="m-0"><i class="fa fa-calendar-alt text-primary mr-2"></i><?php echo $row['duration_days'] ?> days</small>
                                        <small class="m-0"><i class="fa fa-user text-primary mr-2"></i><?php echo $row['persons'] ?> persons</small>
                                    </div>
                                    <a class="h5 text-decoration-none" href="#"><?php echo $row['trip_name'] ?></a>
                                    <a href="booking.php?trip_id=<?php echo $row['trip_id']; ?>">
                                        <button class="btn btn-primary btn-block" type="submit" style="height: 35px; margin-top: 10px;">Book Now</button>
                                    </a>

                                    <div class="border-top mt-4 pt-4">
                                        <div class="d-flex justify-content-between">
                                            <h6 class="m-0"><i class="fa fa-star text-primary mr-2"></i><?php echo $row['stars'] ?></h6>
                                            <h5 class="m-0">$<?php echo $row['budget'] ?></h5>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Blog Detail End -->
            </div>

            <div class="col-lg-4 mt-5 mt-lg-0">
                <!-- Author Bio -->
                <div class="d-flex flex-column text-center bg-white mb-5 py-5 px-4">
                    <img src="img/ubaid.jpg" class="img-fluid mx-auto mb-3" style="width: 100px;">
                    <h3 class="text-primary mb-3">Ubaidullah</h3>
                    <p>Conset elitr erat vero dolor ipsum et diam, eos dolor lorem, ipsum sit no ut est ipsum erat kasd amet elitr</p>
                    <div class="d-flex justify-content-center">
                        <a class="text-primary px-2" href="">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                        <a class="text-primary px-2" href="">
                            <i class="fab fa-twitter"></i>
                        </a>
                        <a class="text-primary px-2" href="">
                            <i class="fab fa-linkedin-in"></i>
                        </a>
                        <a class="text-primary px-2" href="">
                            <i class="fab fa-instagram"></i>
                        </a>
                        <a class="text-primary px-2" href="">
                            <i class="fab fa-youtube"></i>
                        </a>
                    </div>
                </div>

                <!-- Search Form -->
                <div class="mb-5">
                    <div class="bg-white" style="padding: 30px;">
                        <div class="input-group">
                            <input type="text" class="form-control p-4" placeholder="Keyword">
                            <div class="input-group-append">
                                <span class="input-group-text bg-primary border-primary text-white"><i class="fa fa-search"></i></span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Post -->
                <div class="mb-5">
                    <h4 class="text-uppercase mb-4" style="letter-spacing: 5px;">Recent Post</h4>
                    <a class="d-flex align-items-center text-decoration-none bg-white mb-3" href="">
                        <img class="img-fluid" src="img/blog-100x100.jpg" alt="">
                        <div class="pl-3">
                            <h6 class="m-1">Diam lorem dolore justo eirmod lorem dolore</h6>
                            <small>Jan 01, 2050</small>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Blog End -->

<div class="container-fluid py-5" style="background-color: #f9f9f9; margin: 90px 0;">
    <div class="container py-5">
        <div class="text-center mb-5">
            <h6 class="text-primary text-uppercase" style="letter-spacing: 5px;">Travel with Comfort</h6>
            <h1 class="display-4">Vehicle Details</h1>
        </div>
        <div class="row">
            <!-- Vehicle Image Section -->
            <div class="col-lg-6 col-md-12 mb-4" data-aos="fade-right">
                <div class="card border-0 shadow-lg">
                    <h3 class="text-center mb-3">Vehicle Image</h3>
                    <img src="<?php echo $row['vehicle_image'] ?>" class="img-fluid rounded-top" alt="Luxury Vehicle" style="width: 100%; height: auto;">
                </div>
            </div>

            <!-- Vehicle Details Section -->
            <div class="col-lg-6 col-md-12 mb-4 d-flex" data-aos="fade-left">
                <div class="card border-0 shadow-lg flex-fill">
                    <div class="card-body p-4">
                        <h3 class="text-center mb-3">Vehicle Details</h3>
                        <!-- Vehicle Type -->
                        <div class="d-flex align-items-center mb-4">
                            <div class="mr-3">
                                <i class="fas fa-bus fa-2x text-primary"></i>
                            </div>
                            <div>
                                <h5 class="card-title mb-1">Vehicle Type</h5>
                                <p class="card-text"><?php echo $row['vehicle_type'] ?></p>
                            </div>
                        </div>

                        <!-- Vehicle Capacity -->
                        <div class="d-flex align-items-center mb-4">
                            <div class="mr-3">
                                <i class="fas fa-users fa-2x text-primary"></i>
                            </div>
                            <div>
                                <h5 class="card-title mb-1">Capacity</h5>
                                <p class="card-text"><?php echo $row['vehicle_capacity'] ?> Persons</p>
                            </div>
                        </div>

                        <!-- Key Features -->
                        <div class="d-flex align-items-center mb-4">
                            <div class="mr-3">
                                <i class="fas fa-cog fa-2x text-primary"></i>
                            </div>
                            <div>
                                <h5 class="card-title mb-1">Key Features</h5>
                                <p class="card-text"><?php echo $row['vehicle_features'] ?></p>
                            </div>
                        </div>

                        <!-- Driver Details -->
                        <div class="d-flex align-items-center mb-4">
                            <div class="mr-3">
                                <i class="fas fa-user-tie fa-2x text-primary"></i>
                            </div>
                            <div>
                                <h5 class="card-title mb-1">Driver Details</h5>
                                <p class="card-text"><?php echo $row['driver_details'] ?></p>
                            </div>
                        </div>

                        <!-- Button -->
                        <div class="text-center mt-5">
                            <a href="booking.php?trip_id=<?php echo $row['trip_id']; ?>">
                                <button class="btn btn-primary btn-block py-3" style="border-radius: 50px;">Book Now</button>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>


<script>
  AOS.init();
</script>

   <!-- Footer Start -->
    <div class="container-fluid bg-dark text-white-50 py-5 px-sm-3 px-lg-5 " >
        <div class="row pt-5">
            <div class="col-lg-3 col-md-6 mb-5">
                <a href="" class="navbar-brand">
                 <a href="" class="navbar-brand">
          <h1 class="text-primary" style="font-size:25px;"><span style="color:white;">Expense</span>Voyage</h1>
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

    <!-- Footer End -->


    <!-- Back to Top -->
    <a href="#" class="btn btn-lg btn-primary btn-lg-square back-to-top"><i class="fa fa-angle-double-up"></i></a>


    <!-- JavaScript Libraries -->
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

    <!-- Template Javascript -->
    <script src="js/main.js"></script>
</body>

</html>