<?php
session_start();
include 'admin/config.php'; // Database connection

// Fetch the trip data using the trip_id from the URL
if (isset($_GET['trip_id']) && !empty($_GET['trip_id'])) {
    $trip_id = $_GET['trip_id'];

    // Prepare the SQL query to fetch trip details
    $sql = "SELECT * FROM trips WHERE trip_id = ?";
    $stmt = $con->prepare($sql);
    $stmt->bind_param('i', $trip_id);
    $stmt->execute();
    $result = $stmt->get_result();

    // If trip found, fetch the data
    if ($result->num_rows > 0) {
        $trip = $result->fetch_assoc();
    } else {
        echo "Trip not found!";
        exit;
    }
} else {
    echo "Trip ID is missing!";
    exit;
}
?>  

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Traveler.com | Booking</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta content="Travel website booking page" name="description">

    <!-- Favicon -->
    <link href="img/favicon.ico" rel="icon">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">

    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f4f4f4;
            color: #333;
        }

        .navbar {
            background-color: #ffffff;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

/* General Styles */
body {
    font-family: 'Poppins', sans-serif;
    background-color: #f4f4f4; /* Soft background */
    color: #333;
}

/* Booking Form Container */
.booking-travel {
    max-width: 423%;
    margin: 0px auto;
    background: #ffffff;
    border-radius: 22px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
    padding: 31px;
}

/* Heading Style */
.booking-travel h2 {
    text-align: center; /* Centered title */
    color: #427c00; /* Primary color */
    margin-bottom: 30px; /* Space below title */
}

/* Label Styling */
.booking-travel label {
    font-weight: bold; /* Bold labels */
    margin-bottom: 5px; /* Space below label */
    display: block; /* Block display for labels */
}

/* Input and Textarea Styles */
.booking-travel input.form-control,
.booking-travel textarea.form-control {
    width: 100%; /* Full width of the form */
    padding: 15px; /* Padding inside input fields */
    border: 1px solid #ddd; /* Light border */
    border-radius: 5px; /* Rounded corners */
    box-shadow: inset 0 1px 3px rgba(0, 0, 0, 0.1); /* Inset shadow */
    font-size: 16px; /* Font size */
    margin-bottom: 20px; /* Space below input fields */
    transition: border-color 0.3s ease, box-shadow 0.3s ease; /* Transition for focus effects */
}

/* Focus Styles */
.booking-travel input.form-control:focus,
.booking-travel textarea.form-control:focus {
    border-color: #427c00; /* Darker green border on focus */
    outline: none; /* Remove default outline */
    box-shadow: 0 0 5px rgba(66, 124, 0, 0.5); /* Shadow effect on focus */
}

/* Button Styles */
.booking-travel button.btn {
    background-color: #427c00; /* Primary button color */
    color: white; /* Text color */
    padding: 15px; /* Padding */
    border: none; /* No border */
    border-radius: 5px; /* Rounded corners */
    cursor: pointer; /* Pointer cursor */
    font-size: 18px; /* Larger font size for buttons */
    transition: background-color 0.3s ease, transform 0.2s ease; /* Transition for hover effects */
    width: 100%; /* Full width button */
}

/* Button Hover Styles */
.booking-travel button.btn:hover {
    background-color: #7AB730; /* Lighter green on hover */
    transform: translateY(-2px); /* Slight lift effect */
}

/* Responsive Adjustments */
@media screen and (max-width: 768px) {
    .booking-travel {
        padding: 20px; /* Adjust padding for smaller screens */
    }

    .booking-travel button.btn {
        padding: 12px; /* Adjust button padding */
        font-size: 16px; /* Adjust button font size */
    }
}


        .booking12 {
            text-align: center;
            margin-bottom: 20px;
        }

        h2 {
            color: #427c00;
        }

        .row-2 {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }

        .row-2 input,
        .row-2 select,
        .request textarea {
            width: 100%;
            padding: 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
            transition: all 0.3s ease;
        }

        .row-2 input:focus,
        .request textarea:focus,
        .row-2 select:focus {
            border-color: #427c00;
            outline: none;
        }

        .btn-submit {
            background-color: #427c00;
            color: white;
            border: none;
            padding: 15px 20px;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
            width: 100%;
        }

        .btn-submit:hover {
            background-color: #7AB730;
        }

        footer {
            background-color: #333;
            color: white;
            padding: 20px 0;
            text-align: center;
        }

        footer a {
            color: #7AB730;
        }

        @media screen and (max-width: 676px) {
            .row-2 {
                flex-direction: column;
            }
            .row-2 input,
            .row-2 select {
                margin-bottom: 15px;
            }
        }
    </style>
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
    <!-- Topbar Section -->
    <div class="container-fluid bg-light pt-3 d-none d-lg-block">
        <div class="container">
            <div class="row">
                <div class="col-lg-6 text-center text-lg-left">
                    <p><i class="fa fa-envelope mr-2"></i>ubaidsoomro505@gmail.com | <i class="fa fa-phone-alt mr-2"></i>+92 3188 893 863</p>
                </div>
                <div class="col-lg-6 text-center text-lg-right">
                    <div class="d-inline-flex align-items-center">
                        <a class="text-primary px-3" href="#"><i class="fab fa-facebook-f"></i></a>
                        <a class="text-primary px-3" href="#"><i class="fab fa-twitter"></i></a>
                        <a class="text-primary px-3" href="#"><i class="fab fa-linkedin-in"></i></a>
                        <a class="text-primary px-3" href="#"><i class="fab fa-instagram"></i></a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Navbar Section -->
    <nav class="navbar navbar-expand-lg navbar-light shadow-lg py-3">
        <div class="container">
            <a class="navbar-brand" href="#"><h1 class="m-0 text-primary"><span style="color:black;">Expense</span>Voyage</h1></a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarCollapse">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarCollapse">
                <div class="navbar-nav ml-auto">
                    <a href="index.php" class="nav-item nav-link">Home</a>
                    <a href="about.php" class="nav-item nav-link active">About</a>
                    <a href="service.php" class="nav-item nav-link">Services</a>
                    <a href="package.php" class="nav-item nav-link">Tour Packages</a>
                    <a href="contact.php" class="nav-item nav-link">Contact</a>
                    <div class="login-register d-flex align-items-center">
                        <?php if (isset($_SESSION['email'])): ?>
                            <div class="nav-item dropdown">
                                <a href="#" class="nav-link dropdown-toggle active" data-toggle="dropdown">
                                    <span><?= htmlspecialchars($_SESSION['name'] ?? 'User') ?></span>
                                </a>
                                <div class="dropdown-menu border-0 rounded-0 m-0">
                                    <a href="user-profile.php" class="dropdown-item">My Account</a>
                                    <a href="admin/index.php" class="dropdown-item">Only Admin</a>
                                    <a href="booking.php" class="dropdown-item">Booking</a>
                                    <a href="logout.php" class="dropdown-item">Logout</a>
                                </div>
                            </div>
                        <?php else: ?>
                            <div class="nav-item dropdown">
                                <a href="#" class="nav-link dropdown-toggle active" data-toggle="dropdown">
                                    <span>Login/Register</span>
                                </a>
                                <div class="dropdown-menu border-0 rounded-0 m-0">
                                    <a href="login/account.php" class="dropdown-item">Login/Register</a>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </nav>

<!-- Booking Travel Section -->
<div class="container-fluid py-5">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="pb-3">
                    <div class="booking-travel">
                        <h2 class="mb-3">Book Your Trip</h2>
                        <form method="GET" action="payment.php" id="bookingForm">
                            <input type="hidden" name="trip_id" value="<?php echo htmlspecialchars($trip['trip_id']); ?>">

                            <!-- Trip details (Read-only) -->
                            <div class="mb-3">
                                <label for="trip_name">Trip Name</label>
                                <input type="text" name="trip_name" id="trip_name" value="<?php echo htmlspecialchars($trip['trip_name']); ?>" readonly class="form-control">
                            </div>
                            <div class="mb-3">
                                <label for="description">Description</label>
                                <textarea name="description" id="description" readonly class="form-control"><?php echo htmlspecialchars($trip['description']); ?></textarea>
                            </div>
                            <div class="mb-3">
                                <label for="destination">Destination</label>
                                <input type="text" name="destination" id="destination" value="<?php echo htmlspecialchars($trip['destination']); ?>" readonly class="form-control">
                            </div>
                            <div class="mb-3">
                                <label for="start_date">Start Date</label>
                                <input type="text" name="start_date" id="start_date" value="<?php echo htmlspecialchars($trip['starts_date']); ?>" readonly class="form-control">
                            </div>
                            <div class="mb-3">
                                <label for="end_date">End Date</label>
                                <input type="text" name="end_date" id="end_date" value="<?php echo htmlspecialchars($trip['end_date']); ?>" readonly class="form-control">
                            </div>
                            <div class="mb-3">
                                <label for="total_seats">Total Available Seats</label>
                                <input type="text" name="total_seats" id="total_seats" value="<?php echo htmlspecialchars($trip['seats_available']); ?>" readonly class="form-control">
                            </div>

                            <!-- User Details -->
                            <div class="mb-3">
                                <label for="name">Full Name</label>
                                <input type="text" name="name" id="name" placeholder="Enter your full name" required class="form-control">
                            </div>
                            <div class="mb-3">
                                <label for="email">Email</label>
                                <input type="email" name="email" id="email" placeholder="Enter your email" required class="form-control">
                            </div>
                            <div class="mb-3">
                                <label for="phone">Phone Number</label>
                                <input type="tel" name="phone" id="phone" placeholder="Enter your phone number" required class="form-control">
                            </div>
                            <div class="mb-3">
                                <label for="message">Message</label>
                                <textarea name="message" id="message" placeholder="Any special requests or messages?" class="form-control"></textarea>
                            </div>

                            <button class="btn btn-primary btn-block" type="submit" style="height: 35px; margin-top: 10px;">Book Now</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>




<!-- JavaScript to dynamically update the price -->
<script>
    function updatePrice() {
        var destinationSelect = document.getElementById('destination');
        var selectedOption = destinationSelect.options[destinationSelect.selectedIndex];
        var price = selectedOption.getAttribute('data-price');
        document.getElementById('price').value = price;
    }
</script>


    <!-- Footer Section -->
    <!-- Footer Start -->
    <div class="container-fluid bg-dark text-white-50 py-5 px-sm-3 px-lg-5">
        <div class="row pt-5">
            <div class="col-lg-3 col-md-6 mb-5">
                <a href="" class="navbar-brand">
                <h1 class="text-primary" style="font-size:25px;"><span style="color:white;">Expense</span>Voyage</h1>
                </a>
                <p>Sed ipsum clita tempor ipsum ipsum amet sit ipsum lorem amet labore rebum lorem ipsum dolor. No sed vero lorem dolor dolor</p>
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
                    <a class="text-white-50 mb-2" href="about.html"><i class="fa fa-angle-right mr-2"></i>About</a>
                    <a class="text-white-50 mb-2" href="destination.html"><i class="fa fa-angle-right mr-2"></i>Destination</a>
                    <a class="text-white-50 mb-2" href="service.html"><i class="fa fa-angle-right mr-2"></i>Services</a>
                    <a class="text-white-50 mb-2" href="package.html"><i class="fa fa-angle-right mr-2"></i>Packages</a>
                    <a class="text-white-50 mb-2" href="guide.html"><i class="fa fa-angle-right mr-2"></i>Guides</a>

                    <a class="text-white-50" href="blog.html"><i class="fa fa-angle-right mr-2"></i>Blog</a>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-5">
                <h5 class="text-white text-uppercase mb-4" style="letter-spacing: 5px;">Usefull Links</h5>
                <div class="d-flex flex-column justify-content-start">
                    <a class="text-white-50 mb-2" href="#"><i class="fa fa-angle-right mr-2"></i>About</a>
                    <a class="text-white-50 mb-2" href="#"><i class="fa fa-angle-right mr-2"></i>Destination</a>
                    <a class="text-white-50 mb-2" href="#"><i class="fa fa-angle-right mr-2"></i>Services</a>
                    <a class="text-white-50 mb-2" href="#"><i class="fa fa-angle-right mr-2"></i>Packages</a>
                    <a class="text-white-50 mb-2" href="#"><i class="fa fa-angle-right mr-2"></i>Guides</a>

                    <a class="text-white-50" href="#"><i class="fa fa-angle-right mr-2"></i>Blog</a>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-5">
                <h5 class="text-white text-uppercase mb-4" style="letter-spacing: 5px;">Contact Us</h5>
                <p><i class="fa fa-map-marker-alt mr-2"></i>123 Street, New York, USA</p>
                <p><i class="fa fa-phone-alt mr-2"></i>+92 3188 893 8630</p>
                <p><i class="fa fa-envelope mr-2"></i>ubaidsoomro505@gmail.com</p>
                <h6 class="text-white text-uppercase mt-4 mb-3" style="letter-spacing: 5px;">Newsletter</h6>
                <div class="w-100">
                    <div class="input-group">
                        <input type="text" class="form-control border-light" style="padding: 25px;" placeholder="Your Email">
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