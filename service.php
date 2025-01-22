<?php
session_start();

include 'admin/config.php';
// Fetch destination values from the 'trips' table
$query = "SELECT DISTINCT destination FROM trips";
$result = $con->query($query);
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
          <a href="" class="navbar-brand">
          <h1 class="text-primary" style="font-size:25px;"><span style="color:black;">Expense</span>Voyage</h1>
                </a>
                </a>
                <button type="button" class="navbar-toggler" data-toggle="collapse" data-target="#navbarCollapse">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse justify-content-between px-3" id="navbarCollapse">
                    <div class="navbar-nav ml-auto py-0">
                        <a href="index.php" class="nav-item nav-link">Home</a>
                        <a href="about.php" class="nav-item nav-link">About</a>
                        <a href="service.php" class="nav-item nav-link active">Services</a>
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
                <h3 class="display-4 text-white text-uppercase">Services</h3>
                <div class="d-inline-flex text-white">
                    <p class="m-0 text-uppercase"><a class="text-white" href="index.php">Home</a></p>
                    <i class="fa fa-angle-double-right pt-1 px-3"></i>
                    <p class="m-0 text-uppercase">Services</p>
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


    <!-- Service Start -->
    <div class="container">
        <div class="row">
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="service-item bg-white text-center mb-2 py-5 px-4">
                    <i class="fa fa-2x fa-passport mx-auto mb-4"></i> <!-- Visa Assistance Icon -->
                    <h5 class="mb-2">Visa Assistance</h5>
                    <p class="m-0">We help you with the visa application process, ensuring smooth travel across borders.</p>
                </div>
            </div>
    
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="service-item bg-white text-center mb-2 py-5 px-4">
                    <i class="fa fa-2x fa-plane-departure mx-auto mb-4"></i> <!-- Flight Services Icon -->
                    <h5 class="mb-2">Flight Services</h5>
                    <p class="m-0">Book your flights with ease and enjoy competitive pricing for both domestic and international trips.</p>
                </div>
            </div>
    
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="service-item bg-white text-center mb-2 py-5 px-4">
                    <i class="fa fa-2x fa-map-signs mx-auto mb-4"></i> <!-- Guided Tours Icon -->
                    <h5 class="mb-2">Guided Tours</h5>
                    <p class="m-0">Explore new destinations with our expert-guided tours, designed for a memorable experience.</p>
                </div>
            </div>
        </div>
    
        <div class="row">
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="service-item bg-white text-center mb-2 py-5 px-4">
                    <i class="fa fa-2x fa-hotel mx-auto mb-4"></i> <!-- Hotel Booking Icon -->
                    <h5 class="mb-2">Hotel Booking</h5>
                    <p class="m-0">Stay at the best hotels at affordable rates. We handle all your accommodation needs.</p>
                </div>
            </div>
    
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="service-item bg-white text-center mb-2 py-5 px-4">
                    <i class="fa fa-2x fa-umbrella-beach mx-auto mb-4"></i> <!-- Vacation Packages Icon -->
                    <h5 class="mb-2">Vacation Packages</h5>
                    <p class="m-0">Tailor-made vacation packages that provide great value and unforgettable experiences.</p>
                </div>
            </div>
    
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="service-item bg-white text-center mb-2 py-5 px-4">
                    <i class="fa fa-2x fa-shield-alt mx-auto mb-4"></i> <!-- Travel Insurance Icon -->
                    <h5 class="mb-2">Travel Insurance</h5>
                    <p class="m-0">Comprehensive travel insurance to protect you against unforeseen travel issues.</p>
                </div>
            </div>
        </div>
    </div>
    
    
    <!-- Service End -->


    <!-- Testimonial Start -->
    <div class="container-fluid py-5">
        <div class="container py-5">
            <div class="text-center mb-3 pb-3">
                <h6 class="text-primary text-uppercase" style="letter-spacing: 5px;">Testimonial</h6>
                <h1>What Say Our Clients</h1>
            </div>

            <?php
            $sql = "SELECT * FROM review";
            $result = mysqli_query($con, $sql);

            // Check if there are results
            if ($result && mysqli_num_rows($result) > 0) {
                echo '<div class="owl-carousel testimonial-carousel">'; // Start carousel
                while ($rows = mysqli_fetch_assoc($result)) {
                    // Use the image from the 'review' table instead of fetching it from 'user'
                    $reviewer_image = $rows['image']; // Assuming 'image' is the column in 'review' table
                    $reviewer_name = $rows['username']; // Assuming 'name' is also in the 'review' table

                    echo '<div class="text-center pb-4">
                <img class="img-fluid mx-auto" src="img/reviewerimages/' . htmlspecialchars($reviewer_image) . '" style="width: 100px; height: 100px;">
                <div class="testimonial-text bg-white p-4 mt-n5">
                    <p class="mt-5">' . htmlspecialchars($rows['usermessage']) . '</p>
                    <h5 class="text-truncate">' . htmlspecialchars($reviewer_name) . '</h5>
                </div>
            </div>';
                }
                echo '</div>'; // End carousel
            } else {
                echo '<p>No reviews found.</p>'; // Handle case when no reviews exist
            }
            ?>



        </div>
    </div>
    <!-- Testimonial End -->


    <!-- Footer Start -->
     <div class="container-fluid bg-dark text-white-50 py-5 px-sm-3 px-lg-5" style="margin-top: 90px;">
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