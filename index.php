<?php
session_start();
include 'admin/config.php';
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Check if the user is logged in
if (!isset($_SESSION['email'])) {
    // Store the current page in a session variable to redirect after login
    $_SESSION['redirect_after_login'] = 'index.php'; // Redirect to this page after login
    header("Location: login/account.php"); // Redirect to login page
    exit(); // Stop further execution until the user logs in
}

if (isset($_POST['sendreview'])) {
    if (isset($_SESSION['email'])) {
        $useremail = $_POST['useremail'];
        $usermessage = $_POST['usermessage'];
        $userid = $_SESSION['userid'];

        // Fetch the user's name from the user table
        $userQuery = mysqli_query($con, "SELECT name FROM user WHERE id = '$userid'");
        $userRow = mysqli_fetch_assoc($userQuery);
        $username = $userRow['name']; // Get the user's name

        // Handle file upload
        if (isset($_FILES['profile']) && $_FILES['profile']['error'] == UPLOAD_ERR_OK) {
            $fileTmpPath = $_FILES['profile']['tmp_name'];
            $fileName = $_FILES['profile']['name'];
            $fileSize = $_FILES['profile']['size'];
            $fileType = $_FILES['profile']['type'];
            $fileNameCmps = explode(".", $fileName);
            $fileExtension = strtolower(end($fileNameCmps));

            // Specify allowed file types
            $allowedfileExtensions = ['jpg', 'gif', 'png', 'jpeg', 'pdf'];
            if (in_array($fileExtension, $allowedfileExtensions)) {
                // Set the new file name and destination
                $newFileName = uniqid() . '.' . $fileExtension;
                $uploadFileDir = 'img/reviewerimages/'; // Ensure this directory exists and is writable
                $dest_path = $uploadFileDir . $newFileName;

                // Move the file to the specified directory
                if (move_uploaded_file($fileTmpPath, $dest_path)) {
                    // Insert into database including the username
                    $sql = "INSERT INTO review (userid, email, image, usermessage, date_time, username) 
                            VALUES ('$userid', '$useremail', '$newFileName', '$usermessage', NOW(), '$username')";
                    $result = mysqli_query($con, $sql);

                    if ($result) {
                        // Set a session variable to indicate successful submission
                        $_SESSION['review_submitted'] = true;
                        // Redirect to the same page to prevent resubmission
                        header("Location: index.php");
                        exit();
                    }
                }
            }
        }
    }
}

// Check for review submission success message
if (isset($_SESSION['review_submitted'])) {
    unset($_SESSION['review_submitted']); // Clear the session variable
}

// Fetch trip data from the database
$query = "SELECT * FROM trips"; // Adjust the query based on your table structure
$result = mysqli_query($con, $query);

// Check for query execution errors
if (!$result) {
    die("Query failed: " . mysqli_error($con)); // This will output the error
}

// The rest of your code...
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

    <!-- Google Web Fonts -->
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" rel="stylesheet">

    <!-- Libraries Stylesheet -->
    <link href="lib/owlcarousel/assets/owl.carousel.min.css" rel="stylesheet">
    <link href="lib/tempusdominus/css/tempusdominus-bootstrap-4.min.css" rel="stylesheet" />

    <!-- Bootstrap CSS (optional, for styling) -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">

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
                        <p><i class="fa fa-envelope mr-2"></i>memon1ahmed@gmail.com</p>
                        <p class="text-body px-3">|</p>
                        <p><i class="fa fa-phone-alt mr-2"></i>+92 3073 762 276</p>
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

    <style>
    /* Ensures the navbar spans full screen width and is responsive */
    .nav-bar {
        position: relative;
        width: 100%; /* Ensures full width */
        transition: all 0.3s ease;
        z-index: 9;
    }

    /* Sticky Class (Applied on Scroll) */
    .sticky-nav {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%; /* Ensures sticky navbar is full width */
        z-index: 999;
        background-color: #ffffff;
        box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
        animation: fadeInDown 0.5s ease;
    }

    /* Keyframe for smooth fade-in effect */
    @keyframes fadeInDown {
        from {
            opacity: 0;
            transform: translateY(-20px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* Ensures the container inside the navbar is full-width */
    .container-fluid {
        max-width: 100%;
        padding: 0; /* Removes extra padding to span the screen */
    }

    /* Adjusting padding for better alignment */
    .container-lg {
        padding: 0 15px; /* Adjust this padding for alignment */
    }
</style>

<script>
    // Wait for the DOM to be fully loaded
    document.addEventListener("DOMContentLoaded", function () {
        const navbar = document.querySelector('.nav-bar');

        // Function to toggle the sticky class based on scroll
        window.addEventListener('scroll', function () {
            if (window.scrollY > 100) { // When user scrolls 100px down
                navbar.classList.add('sticky-nav');
            } else {
                navbar.classList.remove('sticky-nav');
            }
        });
    });
</script>

<!-- Navbar Start -->
<div class="container-fluid nav-bar p-0">
    <div class="container-lg p-0 px-lg-3">
        <nav class="navbar navbar-expand-lg bg-light navbar-light shadow-lg py-3 py-lg-0 pl-3 pl-lg-5">
            <a href="" class="navbar-brand">
                <h1 class="m-0 text-primary" style="font-size:25px;">
                    <span style="color:black;">Expense</span>Voyage
                </h1>
            </a>
            <button type="button" class="navbar-toggler" data-toggle="collapse" data-target="#navbarCollapse">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse justify-content-between px-3" id="navbarCollapse">
                <div class="navbar-nav ml-auto py-0">
                    <a href="index.php" class="nav-item nav-link active">Home</a>
                    <a href="about.php" class="nav-item nav-link">About</a>
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


    <!-- Carousel Start -->
    <div class="container-fluid p-0">
        <div id="header-carousel" class="carousel slide" data-ride="carousel">
            <div class="carousel-inner">
                <div class="carousel-item active">
                    <img class="w-100" src="img/carousel-1.jpg" alt="Image">
                    <div class="carousel-caption d-flex flex-column align-items-center justify-content-center">
                        <div class="p-3" style="max-width: 900px;">
                            <h4 class="text-white text-uppercase mb-md-3">Tours & Travel</h4>
                            <h1 class="display-3 text-white mb-md-4">Let's Discover The World Together</h1>
                            <a href="" class="btn btn-primary py-md-3 px-md-5 mt-2">Book Now</a>
                        </div>
                    </div>
                </div>
                <div class="carousel-item">
                    <img class="w-100" src="img/carousel-2.jpg" alt="Image">
                    <div class="carousel-caption d-flex flex-column align-items-center justify-content-center">
                        <div class="p-3" style="max-width: 900px;">
                            <h4 class="text-white text-uppercase mb-md-3">Tours & Travel</h4>
                            <h1 class="display-3 text-white mb-md-4">Discover Amazing Places With Us</h1>
                            <a href="" class="btn btn-primary py-md-3 px-md-5 mt-2">Book Now</a>
                        </div>
                    </div>
                </div>
            </div>
            <a class="carousel-control-prev" href="#header-carousel" data-slide="prev">
                <div class="btn btn-dark" style="width: 45px; height: 45px;">
                    <span class="carousel-control-prev-icon mb-n2"></span>
                </div>
            </a>
            <a class="carousel-control-next" href="#header-carousel" data-slide="next">
                <div class="btn btn-dark" style="width: 45px; height: 45px;">
                    <span class="carousel-control-next-icon mb-n2"></span>
                </div>
            </a>
        </div>
    </div>
    <!-- Carousel End -->



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
                                        <input type="text" class="form-control p-4" placeholder="Enter Destination"
                                            name="destination">
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
    <!-- Booking End -->


    <!-- About Start -->
    <div class="container-fluid py-5">
        <div class="container pt-5">
            <div class="row">
                <div class="col-lg-6" style="min-height: 500px;">
                    <div class="position-relative h-100">
                        <img class="position-absolute w-100 h-100" src="img/about.jpg" style="object-fit: cover;">
                    </div>
                </div>
                <div class="col-lg-6 pt-5 pb-lg-5">
                    <div class="about-text bg-white p-4 p-lg-5 my-lg-5">
                        <h6 class="text-primary text-uppercase" style="letter-spacing: 5px;">About Us</h6>
                        <h1 class="mb-3">We Provide Best Tour Packages In Your Budget</h1>
                        <p>we believe that every journey should be unforgettable. As passionate travel enthusiasts, our
                            goal is to help you plan your dream trips with ease and confidence. Whether you're seeking
                            adventure, relaxation, or cultural exploration, we curate personalized itineraries tailored
                            to your preferences and budget.</p>
                        <div class="row mb-4">
                            <div class="col-6">
                                <img class="img-fluid" src="img/about-1.jpg" alt="">
                            </div>
                            <div class="col-6">
                                <img class="img-fluid" src="img/about-2.jpg" alt="">
                            </div>
                        </div>
                        <a href="" class="btn btn-primary mt-1">Book Now</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- About End -->


    <!-- Feature Start -->
    <div class="container-fluid pb-5">
        <div class="container pb-5">
            <div class="row">
                <div class="col-md-4">
                    <div class="d-flex mb-4 mb-lg-0">
                        <div class="d-flex flex-shrink-0 align-items-center justify-content-center bg-primary mr-3"
                            style="height: 100px; width: 100px;">
                            <i class="fa fa-2x fa-money-check-alt text-white"></i>
                        </div>
                        <div class="d-flex flex-column">
                            <h5 class="">Competitive Pricing</h5>
                            <p class="m-0">We believe that amazing travel experiences shouldn’t come with a hefty price
                                tag.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="d-flex mb-4 mb-lg-0">
                        <div class="d-flex flex-shrink-0 align-items-center justify-content-center bg-primary mr-3"
                            style="height: 100px; width: 100px;">
                            <i class="fa fa-2x fa-award text-white"></i>
                        </div>
                        <div class="d-flex flex-column">
                            <h5 class="">Best Services</h5>
                            <p class="m-0">We pride ourselves on delivering the best services to make your travel. </p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="d-flex mb-4 mb-lg-0">
                        <div class="d-flex flex-shrink-0 align-items-center justify-content-center bg-primary mr-3"
                            style="height: 100px; width: 100px;">
                            <i class="fa fa-2x fa-globe text-white"></i>
                        </div>
                        <div class="d-flex flex-column">
                            <h5 class="">Worldwide Coverage</h5>
                            <p class="m-0">We offer worldwide coverage, bringing the world to your fingertips.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Feature End -->


    <!-- Destination Start -->
    <div class="container-fluid py-5">
        <div class="container pt-5 pb-3">
            <div class="text-center mb-3 pb-3">
                <h6 class="text-primary text-uppercase" style="letter-spacing: 5px;">Destination</h6>
                <h1>Explore Top Destination</h1>
            </div>
            <div class="row">

                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="destination-item position-relative overflow-hidden mb-2">
                        <img class="img-fluid" src="img/destination-2.jpg" alt="">
                        <a class="destination-overlay text-white text-decoration-none" href="destination.php">
                            <h5 class="text-white">United Kingdom</h5>
                            <span>100 Cities</span>
                        </a>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="destination-item position-relative overflow-hidden mb-2">
                        <img class="img-fluid" src="img/destination-3.jpg" alt="">
                        <a class="destination-overlay text-white text-decoration-none" href="destination.php">
                            <h5 class="text-white">Australia</h5>
                            <span>100 Cities</span>
                        </a>
                    </div>
                </div>

                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="destination-item position-relative overflow-hidden mb-2">
                        <img class="img-fluid" src="img/destination-5.jpg" alt="">
                        <a class="destination-overlay text-white text-decoration-none" href="destination.php">
                            <h5 class="text-white">South Africa</h5>
                            <span>100 Cities</span>
                        </a>
                    </div>
                </div>

            </div>
        </div>
    </div>
    <!-- Destination Start -->


    <!-- Service Start -->
    <div class="container-fluid py-5">
        <div class="container pt-5 pb-3">
            <div class="text-center mb-3 pb-3">
                <h6 class="text-primary text-uppercase" style="letter-spacing: 5px;">Services</h6>
                <h1>Tours & Travel Services</h1>
            </div>
            <div class="row">
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="service-item bg-white text-center mb-2 py-5 px-4">
                        <i class="fa fa-2x fa-route mx-auto mb-4"></i>
                        <h5 class="mb-2">Travel Guide</h5>
                        <p class="m-0">Justo sit justo eos amet tempor amet clita amet ipsum eos elitr. Amet lorem est
                            amet labore</p>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="service-item bg-white text-center mb-2 py-5 px-4">
                        <i class="fa fa-2x fa-ticket-alt mx-auto mb-4"></i>
                        <h5 class="mb-2">Ticket Booking</h5>
                        <p class="m-0">Justo sit justo eos amet tempor amet clita amet ipsum eos elitr. Amet lorem est
                            amet labore</p>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="service-item bg-white text-center mb-2 py-5 px-4">
                        <i class="fa fa-2x fa-hotel mx-auto mb-4"></i>
                        <h5 class="mb-2">Hotel Booking</h5>
                        <p class="m-0">Justo sit justo eos amet tempor amet clita amet ipsum eos elitr. Amet lorem est
                            amet labore</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Service End -->


<!-- Trips Start -->
<div class="container-fluid py-5">
    <div class="container pt-5 pb-3">
        <div class="text-center mb-3 pb-3">
            <h6 class="text-primary text-uppercase" style="letter-spacing: 5px;">Trips</h6>
            <h1>Perfect Tour Packages</h1>
        </div>
        <div class="row">
            <?php while ($trip = mysqli_fetch_assoc($result)): ?>
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="package-item bg-white mb-2">
                        <img class="img-fluid" src="<?php echo $trip['trip_image']; ?>" alt="">
                        <div class="p-4">
                            <div class="d-flex justify-content-between mb-3">
                                <small class="m-0"><i class="fa fa-map-marker-alt text-primary mr-2"></i><?php echo $trip['destination']; ?></small>
                                <small class="m-0"><i class="fa fa-calendar-alt text-primary mr-2"></i><?php echo $trip['duration_days']; ?> days</small>
                                <small class="m-0"><i class="fa fa-user text-primary mr-2"></i><?php echo $trip['persons']; ?> Person</small>
                            </div>
                            <a class="h5 text-decoration-none" href="trip_details.php?id=<?php echo $trip['trip_id']; ?>"><?php echo $trip['trip_name']; ?></a>
                            <div class="border-top mt-4 pt-4">
                                <div class="d-flex justify-content-between">
                                    <h6 class="m-0"><i class="fa fa-star text-primary mr-2"></i><?php echo $trip['stars']; ?> </h6>
                                    <h5 class="m-0">$<?php echo $trip['budget']; ?></h5>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    </div>
</div>
<!-- Trips End -->
    <!-- Registration Start -->
    <div class="container-fluid bg-registration py-5" style="margin: 90px 0; background-attachment: fixed !important;">
        <div class="container py-5">
            <div class="row align-items-center justify-content-center">

                <div class="col-lg-5">
                    <div class="card border-0">
                        <div class="card-header bg-primary text-center p-4">
                            <h1 class="text-white m-0">User Review</h1>
                        </div>
                        <div class="card-body rounded-bottom bg-white p-5">
                            <form method="POST" action="index.php" enctype="multipart/form-data" onsubmit="return validateMessage()">
                                <div class="form-group">
                                    <input type="email" name="useremail" class="form-control p-4" placeholder="Your Email" required="required" />
                                </div>
                                <div class="form-group">
                                    <input type="file" name="profile" class="form-control p-4" required="required" style="height:80px;" />
                                </div>
                                <div class="form-group">
                                    <textarea id="usermessage" name="usermessage" class="form-control pl-4 pt-2" rows="6" placeholder="Enter Review" required></textarea>
                                    <small id="wordCount" class="form-text text-muted">You can enter up to 10 words.</small>
                                </div>
                                <div>
                                    <button class="btn btn-primary btn-block py-3" type="submit" name="sendreview">Send Review</button>
                                </div>
                            </form>

                            <script>
                                function validateMessage() {
                                    const messageInput = document.getElementById('usermessage');
                                    const message = messageInput.value.trim();
                                    const wordCount = message.split(/\s+/).filter(word => word.length > 0).length; // Count words

                                    // Check if the word count exceeds 10
                                    if (wordCount > 10) {
                                        alert('Please enter no more than 10 words.');
                                        return false; // Prevent form submission
                                    }
                                    return true; // Allow form submission
                                }

                                // Update word count dynamically and restrict input
                                document.getElementById('usermessage').addEventListener('input', function() {
                                    const message = this.value.trim();
                                    const wordCount = message.split(/\s+/).filter(word => word.length > 0).length; // Count words
                                    document.getElementById('wordCount').textContent = `Words: ${wordCount}/10`;

                                    // Restrict input to 10 words
                                    if (wordCount > 10) {
                                        const words = message.split(/\s+/).slice(0, 10).join(" "); // Keep only first 10 words
                                        this.value = words; // Set the textarea value to the truncated message
                                    }
                                });
                            </script>





                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Registration End -->

<!-- Team Start -->
<div class="container-fluid py-5">
    <div class="container pt-5 pb-3">
        <div class="text-center mb-3 pb-3">
            <h6 class="text-primary text-uppercase" style="letter-spacing: 5px;">Guides</h6>
            <h1>Our Travel Guides</h1>
        </div>
        <div class="row">
            <?php
            // Include your database connection file
            include 'admin/config.php'; // Adjust the path as necessary
            
            // Fetch agent data from the database
            $query = "SELECT * FROM agent"; // Ensure this is the correct table name
            $result = mysqli_query($con, $query);

            // Check for query execution errors
            if (!$result) {
                die("Query failed: " . mysqli_error($con)); // Output the error message
            }

            // Define the base path for images
            $basePath = 'admin/upload/agents/';

            // Loop through the result set and display each agent
            while ($agent = mysqli_fetch_assoc($result)):
                $imageFilename = htmlspecialchars($agent['a_image']); // Assuming this contains only the filename
                $imagePath = $basePath . $imageFilename; // Concatenate the base path with the filename
                $agentName = htmlspecialchars($agent['a_name']); // Prevent XSS
                $agentProfession = htmlspecialchars($agent['a_profetion']); // Correct column name
            ?>
                <div class="col-lg-3 col-md-4 col-sm-6 pb-4">
                    <div class="team-item bg-white mb-4 shadow-sm rounded">
                        <div class="team-img position-relative overflow-hidden">
                            <img class="img-fluid w-100 h-100" src="<?php echo $imagePath; ?>" alt="<?php echo $agentName; ?>" onerror="this.onerror=null; this.src='img/placeholder.jpg';" style="object-fit: cover;">
                            <div class="team-social">
                                <a class="btn btn-outline-primary btn-square" href=""><i class="fab fa-twitter"></i></a>
                                <a class="btn btn-outline-primary btn-square" href=""><i class="fab fa-facebook-f"></i></a>
                                <a class="btn btn-outline-primary btn-square" href=""><i class="fab fa-instagram"></i></a>
                                <a class="btn btn-outline-primary btn-square" href=""><i class="fab fa-linkedin-in"></i></a>
                            </div>
                        </div>
                        <div class="text-center py-4">
                            <h5 class="text-truncate mb-1"><?php echo $agentName; ?></h5>
                            <p class="m-0 text-muted"><?php echo $agentProfession; ?></p>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    </div>
</div>
<!-- Team End -->
<style>
    .team-item {
    height: 400px; /* Set a fixed height for uniformity */
}

.team-img {
    height: 250px; /* Fixed height for image section */
}

.team-social {
    position: absolute;
    bottom: 10px;
    left: 50%;
    transform: translateX(-50%);
}

</style>
<!-- Team End -->


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


    <!-- Blog Start -->
    <div class="container-fluid py-5">
        <div class="container pt-5 pb-3">
            <div class="text-center mb-3 pb-3">
                <h6 class="text-primary text-uppercase" style="letter-spacing: 5px;">Our Blog</h6>
                <h1>Latest From Our Blog</h1>
            </div>
            <div class="row pb-3">
                <div class="col-lg-4 col-md-6 mb-4 pb-2">
                    <div class="blog-item">
                        <div class="position-relative">
                            <img class="img-fluid w-100" src="img/blog-1.jpg" alt="">
                            <div class="blog-date">
                                <h6 class="font-weight-bold mb-n1">01</h6>
                                <small class="text-white text-uppercase">Jan</small>
                            </div>
                        </div>
                        <div class="bg-white p-4">
                            <div class="d-flex mb-2">
                                <a class="text-primary text-uppercase text-decoration-none" href="">Admin</a>
                                <span class="text-primary px-2">|</span>
                                <a class="text-primary text-uppercase text-decoration-none" href="">Tours & Travel</a>
                            </div>
                            <a class="h5 m-0 text-decoration-none" href="blog.php">Dolor justo sea kasd lorem clita
                                justo diam amet</a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 mb-4 pb-2">
                    <div class="blog-item">
                        <div class="position-relative">
                            <img class="img-fluid w-100" src="img/blog-2.jpg" alt="">
                            <div class="blog-date">
                                <h6 class="font-weight-bold mb-n1">01</h6>
                                <small class="text-white text-uppercase">Jan</small>
                            </div>
                        </div>
                        <div class="bg-white p-4">
                            <div class="d-flex mb-2">
                                <a class="text-primary text-uppercase text-decoration-none" href="">Admin</a>
                                <span class="text-primary px-2">|</span>
                                <a class="text-primary text-uppercase text-decoration-none" href="">Tours & Travel</a>
                            </div>
                            <a class="h5 m-0 text-decoration-none" href="blog.php">Dolor justo sea kasd lorem clita
                                justo diam amet</a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 mb-4 pb-2">
                    <div class="blog-item">
                        <div class="position-relative">
                            <img class="img-fluid w-100" src="img/blog-3.jpg" alt="">
                            <div class="blog-date">
                                <h6 class="font-weight-bold mb-n1">01</h6>
                                <small class="text-white text-uppercase">Jan</small>
                            </div>
                        </div>
                        <div class="bg-white p-4">
                            <div class="d-flex mb-2">
                                <a class="text-primary text-uppercase text-decoration-none" href="">Admin</a>
                                <span class="text-primary px-2">|</span>
                                <a class="text-primary text-uppercase text-decoration-none" href="">Tours & Travel</a>
                            </div>
                            <a class="h5 m-0 text-decoration-none" href="blog.php">Dolor justo sea kasd lorem clita
                                justo diam amet</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Blog End -->


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
    <!-- Optional Bootstrap JS and jQuery (for styling and functionality) -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>