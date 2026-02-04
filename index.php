<?php
include 'chatbot-loader.php';
session_start();
include 'admin/config.php';

// Check if user is logged in
if (!isset($_SESSION['email'])) {
    $_SESSION['redirect_after_login'] = 'index.php';
    header("Location: login/account.php");
    exit();
}

// Fetch data for the page
$query = "SELECT * FROM trips LIMIT 6";
$trips_result = mysqli_query($con, $query);

$agentQuery = "SELECT * FROM agent LIMIT 4";
$agents_result = mysqli_query($con, $agentQuery);

$reviewQuery = "SELECT * FROM review ORDER BY date_time DESC LIMIT 3";
$reviews_result = mysqli_query($con, $reviewQuery);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>ExpenseVoyage | Premium Bespoke Travel</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    
    <!-- Google Web Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;800&family=Playfair+Display:ital,wght@0,400..900;1,400..900&display=swap" rel="stylesheet">
    
    <!-- External Libraries -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <script src="https://cdn.jsdelivr.net/particles.js/2.0.0/particles.min.js"></script>
    
    <!-- Custom Midnight Luxe CSS -->
    <link href="css/custom.css" rel="stylesheet">
    
    <style>
        :root {
            --font-serif: 'Playfair Display', serif;
            --font-sans: 'Outfit', sans-serif;
        }

        body {
            font-family: var(--font-sans);
        }

        .hero-title {
            font-family: var(--font-serif);
            font-style: italic;
        }

        .navbar {
            padding: 20px 0;
            transition: all 0.4s ease;
        }

        .hero-section {
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(rgba(10, 12, 16, 0.7), rgba(10, 12, 16, 0.7)), 
                        url('img/carousel-1.jpg') center/cover no-repeat;
            text-align: center;
        }

        .section-padding {
            padding: 120px 0;
        }

        .trip-card {
            transition: transform 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            border: none;
            background: var(--bg-card);
            overflow: hidden;
        }

        .trip-card:hover {
            transform: translateY(-15px);
        }

        .trip-image {
            height: 300px;
            object-fit: cover;
            transition: transform 0.8s ease;
        }

        .trip-card:hover .trip-image {
            transform: scale(1.1);
        }
    </style>
</head>
<body>
    <div id="particles-js"></div>

    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg sticky-top">
        <div class="container">
            <a href="index.php" class="navbar-brand fs-2 fw-bold">
                <span class="text-gold">Expense</span><span class="text-white">Voyage</span>
            </a>
            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#nav">
                <i class="fas fa-bars text-white"></i>
            </button>
            <div class="collapse navbar-collapse" id="nav">
                <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                    <li class="nav-item"><a class="nav-link px-3" href="index.php">Home</a></li>
                    <li class="nav-item"><a class="nav-link px-3" href="package.php">Packages</a></li>
                    <li class="nav-item"><a class="nav-link px-3" href="about.php">Our Story</a></li>
                    <li class="nav-item"><a class="nav-link px-3" href="contact.php">Contact</a></li>
                    <?php if (isset($_SESSION['email'])): ?>
                        <li class="nav-item dropdown ms-lg-3">
                            <a class="nav-link dropdown-toggle text-gold fw-bold" href="#" data-bs-toggle="dropdown">
                                <?php echo htmlspecialchars($_SESSION['name'] ?? 'Voyager'); ?>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end glass-panel border-0 mt-3 p-3">
                                <li><a class="dropdown-item text-white py-2" href="user-profile.php"><i class="fas fa-user-circle me-2"></i> Profile</a></li>
                                <li><a class="dropdown-item text-white py-2" href="logout.php"><i class="fas fa-sign-out-alt me-2"></i> Logout</a></li>
                            </ul>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Cinematic Hero -->
    <section class="hero-section">
        <div class="container">
            <h1 class="display-1 hero-title text-gold animate__animated animate__fadeInUp">Journeys Beyond <br> Imagination</h1>
            <p class="lead text-white-50 mt-4 fs-4 animate__animated animate__fadeInUp animate__delay-1s">Hand-crafted travel experiences for the discerning soul.</p>
            <div class="mt-5 animate__animated animate__fadeInUp animate__delay-2s">
                <a href="#packages" class="btn btn-outline-light px-5 py-3 rounded-0 tracking-widest me-3">EXPLORE</a>
                <a href="contact.php" class="btn btn-primary px-5 py-3 rounded-0 tracking-widest">INQUIRE</a>
            </div>
        </div>
    </section>

    <!-- Booking Overlay -->
    <div class="container" style="margin-top: -80px; position: relative; z-index: 10;">
        <div class="glass-panel p-5">
            <form action="booking.php" method="GET" class="row g-4 align-items-end">
                <div class="col-lg-4">
                    <label class="text-gold small text-uppercase fw-bold mb-2">Destination</label>
                    <input type="text" class="form-control bg-transparent border-secondary text-white py-3 rounded-0" placeholder="Where do you crave to go?">
                </div>
                <div class="col-lg-3">
                    <label class="text-gold small text-uppercase fw-bold mb-2">Voyage Date</label>
                    <input type="date" class="form-control bg-transparent border-secondary text-white py-3 rounded-0">
                </div>
                <div class="col-lg-3">
                    <label class="text-gold small text-uppercase fw-bold mb-2">Voyage Style</label>
                    <select class="form-select bg-transparent border-secondary text-white py-3 rounded-0">
                        <option>Bespoke Luxury</option>
                        <option>Cultural Immersion</option>
                        <option>Private Yacht</option>
                    </select>
                </div>
                <div class="col-lg-2">
                    <button class="btn btn-primary w-100 py-3 rounded-0">SEARCH</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Featured Packages -->
    <section id="packages" class="section-padding">
        <div class="container">
            <div class="text-center mb-5">
                <h6 class="text-gold text-uppercase tracking-widest mb-3">The Extraordinary</h6>
                <h2 class="display-4 hero-title">Featured Voyages</h2>
            </div>
            <div class="row g-4">
                <?php while($trip = mysqli_fetch_assoc($trips_result)): ?>
                    <div class="col-lg-4 col-md-6">
                        <div class="trip-card glass-panel h-100">
                            <div class="overflow-hidden">
                                <img src="<?php echo htmlspecialchars($trip['trip_image']); ?>" class="trip-image w-100" alt="Trip">
                            </div>
                            <div class="p-4">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <span class="text-gold fw-bold">$<?php echo number_format($trip['budget']); ?></span>
                                    <small class="text-white-50"><i class="far fa-clock me-1"></i> <?php echo $trip['duration_days']; ?> Days</small>
                                </div>
                                <h4 class="hero-title mb-3"><?php echo htmlspecialchars($trip['trip_name']); ?></h4>
                                <p class="text-white-50 small mb-4"><?php echo substr(htmlspecialchars($trip['description']), 0, 100); ?>...</p>
                                <a href="trip_details.php?id=<?php echo $trip['trip_id']; ?>" class="text-gold text-decoration-none small text-uppercase fw-bold">View Detail <i class="fas fa-arrow-right ms-1"></i></a>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="section-padding border-top border-secondary">
        <div class="container">
            <div class="row g-5">
                <div class="col-lg-4">
                    <h3 class="text-gold mb-4">ExpenseVoyage</h3>
                    <p class="text-white-50">Curating bespoke travel experiences for those who seek the extraordinary. Our mission is to transform your dreams into cinematic realities.</p>
                </div>
                <div class="col-lg-2 ms-auto">
                    <h5 class="text-white mb-4">Quick Links</h5>
                    <ul class="list-unstyled">
                        <li class="mb-2"><a href="package.php" class="text-white-50 text-decoration-none">Packages</a></li>
                        <li class="mb-2"><a href="about.php" class="text-white-50 text-decoration-none">About Us</a></li>
                        <li class="mb-2"><a href="contact.php" class="text-white-50 text-decoration-none">Contact</a></li>
                    </ul>
                </div>
                <div class="col-lg-4">
                    <h5 class="text-white mb-4">Follow Our Journey</h5>
                    <div class="d-flex gap-3">
                        <a href="#" class="btn btn-outline-secondary rounded-circle"><i class="fab fa-instagram"></i></a>
                        <a href="#" class="btn btn-outline-secondary rounded-circle"><i class="fab fa-facebook-p"></i></a>
                        <a href="#" class="btn btn-outline-secondary rounded-circle"><i class="fab fa-twitter"></i></a>
                    </div>
                </div>
            </div>
            <div class="text-center mt-5 pt-5 text-white-50 small">
                &copy; 2026 ExpenseVoyage. Bespoke Travel Excellence.
            </div>
        </div>
    </footer>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/custom.js"></script>
</body>
</html>