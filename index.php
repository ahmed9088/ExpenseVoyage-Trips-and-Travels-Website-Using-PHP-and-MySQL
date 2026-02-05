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

$agentQuery = "SELECT * FROM agent ORDER BY id DESC LIMIT 4";
$agents_result = mysqli_query($con, $agentQuery);

$reviewQuery = "SELECT * FROM review ORDER BY date_time DESC LIMIT 6";
$reviews_result = mysqli_query($con, $reviewQuery);

// Fetch Dynamic Stats
$countUsers = mysqli_fetch_assoc(mysqli_query($con, "SELECT COUNT(*) as total FROM users"))['total'] + 1200; // Offset for aesthetic
$countTrips = mysqli_fetch_assoc(mysqli_query($con, "SELECT COUNT(*) as total FROM trips"))['total'];
$countAgents = mysqli_fetch_assoc(mysqli_query($con, "SELECT COUNT(*) as total FROM agent"))['total'];
$countReviews = mysqli_fetch_assoc(mysqli_query($con, "SELECT COUNT(*) as total FROM review"))['total'] + 450;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>ExpenseVoyage | Premium Travel Experiences</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    
    <!-- Google Web Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;800&family=Playfair+Display:ital,wght@0,400..900;1,400..900&display=swap" rel="stylesheet">
    
    <!-- External Libraries -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    
    <!-- Custom CSS -->
    <link href="css/custom.css" rel="stylesheet">
    
    <style>
        .hero-section {
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(rgba(248, 250, 252, 0.4), rgba(248, 250, 252, 0.6)), 
                        url('img/carousel-1.jpg') center/cover no-repeat;
            text-align: center;
        }

        .section-padding {
            padding: 100px 0;
        }

        .trip-card {
            border: none;
            overflow: hidden;
            background: #fff;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .trip-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(0,0,0,0.08);
        }

        .trip-image {
            height: 300px;
            object-fit: cover;
            transition: transform 1.2s ease;
        }

        .trip-card:hover .trip-image {
            transform: scale(1.1);
        }

        .stat-card {
            background: #fff;
            border-radius: 20px;
            padding: 40px;
            text-align: center;
            box-shadow: 0 10px 30px rgba(0,0,0,0.03);
            border: 1px solid var(--glass-border);
        }

        .category-card {
            height: 400px;
            position: relative;
            overflow: hidden;
            border-radius: 24px;
            display: flex;
            align-items: flex-end;
            padding: 30px;
            color: white;
            text-decoration: none;
            transition: all 0.5s ease;
        }

        .category-card::after {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(to top, rgba(0,0,0,0.7), transparent);
            z-index: 1;
        }

        .category-card img {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.8s ease;
        }

        .category-card:hover img {
            transform: scale(1.1);
        }

        .category-content {
            position: relative;
            z-index: 2;
        }

        .step-icon {
            width: 80px;
            height: 80px;
            background: rgba(79, 70, 229, 0.1);
            color: var(--primary);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            margin: 0 auto 25px;
        }

        .newsletter-section {
            background: linear-gradient(135deg, var(--primary), #4338ca);
            border-radius: 40px;
            padding: 80px;
            color: white;
        }

        .social-img {
            aspect-ratio: 1;
            object-fit: cover;
            border-radius: 15px;
            cursor: pointer;
            transition: opacity 0.3s;
        }

        .social-img:hover {
            opacity: 0.8;
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg sticky-top">
        <div class="container">
            <a href="index.php" class="navbar-brand fs-2 fw-bold">
                <span class="text-primary">Expense</span><span class="text-dark">Voyage</span>
            </a>
            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#nav">
                <i class="fas fa-bars text-dark"></i>
            </button>
            <div class="collapse navbar-collapse" id="nav">
                <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                    <li class="nav-item"><a class="nav-link px-3 active" href="index.php">Home</a></li>
                    <li class="nav-item"><a class="nav-link px-3" href="package.php">Packages</a></li>
                    <li class="nav-item"><a class="nav-link px-3" href="about.php">Our Story</a></li>
                    <li class="nav-item"><a class="nav-link px-3" href="contact.php">Contact</a></li>
                    <?php if (isset($_SESSION['email'])): ?>
                        <li class="nav-item dropdown ms-lg-3">
                            <a class="nav-link dropdown-toggle text-primary fw-bold" href="#" data-bs-toggle="dropdown">
                                <?php echo htmlspecialchars($_SESSION['name'] ?? 'Voyager'); ?>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end border-0 shadow-lg p-3">
                                <li><a class="dropdown-item py-2" href="user-profile.php"><i class="fas fa-user-circle me-2"></i> Profile</a></li>
                                <li><a class="dropdown-item py-2 text-danger" href="logout.php"><i class="fas fa-sign-out-alt me-2"></i> Logout</a></li>
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
            <h1 class="display-1 serif-font text-dark animate__animated animate__fadeInUp">Journeys Beyond <br> Imagination</h1>
            <p class="lead text-muted mt-4 fs-4 animate__animated animate__fadeInUp animate__delay-1s">Hand-crafted travel experiences for the discerning soul.</p>
            <div class="mt-5 animate__animated animate__fadeInUp animate__delay-2s">
                <a href="#packages" class="btn btn-outline-indigo px-5 py-3 me-3">EXPLORE</a>
                <a href="contact.php" class="btn btn-primary px-5 py-3">INQUIRE</a>
            </div>
        </div>
    </section>

    <!-- Booking Overlay -->
    <div class="container" style="margin-top: -80px; position: relative; z-index: 10;">
        <div class="glass-panel p-5 animate-on-scroll shadow-lg" data-animation="animate__fadeInUp">
            <form action="package.php" method="GET" class="row g-4 align-items-end">
                <div class="col-lg-4">
                    <label class="text-primary small text-uppercase fw-bold mb-2">Destination</label>
                    <input type="text" name="destination" class="form-control bg-light border-0 py-3" placeholder="Where do you crave to go?">
                </div>
                <div class="col-lg-3">
                    <label class="text-primary small text-uppercase fw-bold mb-2">Voyage Date</label>
                    <input type="date" class="form-control bg-light border-0 py-3">
                </div>
                <div class="col-lg-3">
                    <label class="text-primary small text-uppercase fw-bold mb-2">Voyage Style</label>
                    <select class="form-select bg-light border-0 py-3">
                        <option>Bespoke Luxury</option>
                        <option>Cultural Immersion</option>
                        <option>Private Yacht</option>
                    </select>
                </div>
                <div class="col-lg-2">
                    <button type="submit" class="btn btn-primary w-100 py-3 mt-lg-0">SEARCH</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Stats Section -->
    <section class="section-padding">
        <div class="container">
            <div class="row g-4 text-center">
                <div class="col-lg-3 col-6 animate-on-scroll">
                    <div class="stat-card">
                        <h2 class="display-5 fw-bold text-primary mb-2"><?php echo number_format($countUsers); ?>+</h2>
                        <p class="text-muted text-uppercase small tracking-widest mb-0">Epic Travelers</p>
                    </div>
                </div>
                <div class="col-lg-3 col-6 animate-on-scroll" data-delay="0.2s">
                    <div class="stat-card">
                        <h2 class="display-5 fw-bold text-primary mb-2"><?php echo $countTrips; ?>+</h2>
                        <p class="text-muted text-uppercase small tracking-widest mb-0">Curated Trips</p>
                    </div>
                </div>
                <div class="col-lg-3 col-6 animate-on-scroll" data-delay="0.4s">
                    <div class="stat-card">
                        <h2 class="display-5 fw-bold text-primary mb-2"><?php echo $countAgents; ?>+</h2>
                        <p class="text-muted text-uppercase small tracking-widest mb-0">Elite Agents</p>
                    </div>
                </div>
                <div class="col-lg-3 col-6 animate-on-scroll" data-delay="0.6s">
                    <div class="stat-card">
                        <h2 class="display-5 fw-bold text-primary mb-2"><?php echo number_format($countReviews); ?>+</h2>
                        <p class="text-muted text-uppercase small tracking-widest mb-0">Luxury Reviews</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Trending Categories -->
    <section class="section-padding pt-0">
        <div class="container">
            <div class="d-flex justify-content-between align-items-end mb-5 animate-on-scroll">
                <div>
                    <h6 class="text-primary text-uppercase tracking-widest mb-3">Styles of Travel</h6>
                    <h2 class="display-4 serif-font">Popular Journeys</h2>
                </div>
                <a href="package.php" class="text-primary fw-bold text-decoration-none mb-2">View All Styles <i class="fas fa-arrow-right ms-1"></i></a>
            </div>
            <div class="row g-4">
                <div class="col-lg-3 col-md-6 animate-on-scroll">
                    <a href="package.php?style=Beach" class="category-card">
                        <img src="img/beach.jpg" alt="Beach">
                        <div class="category-content">
                            <h4 class="h5 mb-1">Pristine Shores</h4>
                            <p class="small mb-0 opacity-75">12+ Destinations</p>
                        </div>
                    </a>
                </div>
                <div class="col-lg-6 col-md-6 animate-on-scroll" data-delay="0.2s">
                    <a href="package.php?style=Mountain" class="category-card">
                        <img src="img/mountain.jpg" alt="Mountain">
                        <div class="category-content">
                            <h4 class="h5 mb-1">Alpine Majesty</h4>
                            <p class="small mb-0 opacity-75">18+ Destinations</p>
                        </div>
                    </a>
                </div>
                <div class="col-lg-3 col-md-6 animate-on-scroll" data-delay="0.4s">
                    <a href="package.php?style=City" class="category-card">
                        <img src="img/city.jpg" alt="City">
                        <div class="category-content">
                            <h4 class="h5 mb-1">Urban Sophistication</h4>
                            <p class="small mb-0 opacity-75">24+ Destinations</p>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Featured Packages -->
    <section id="packages" class="section-padding bg-light">
        <div class="container">
            <div class="text-center mb-5 animate-on-scroll">
                <h6 class="text-primary text-uppercase tracking-widest mb-3">The Extraordinary</h6>
                <h2 class="display-4 serif-font">Featured Voyages</h2>
            </div>
            <div class="row g-4">
                <?php while($trip = mysqli_fetch_assoc($trips_result)): ?>
                    <div class="col-lg-4 col-md-6 animate-on-scroll">
                        <div class="trip-card h-100 shadow-sm border-0 rounded-4">
                            <div class="overflow-hidden">
                                <img src="<?php echo htmlspecialchars($trip['trip_image']); ?>" class="trip-image w-100" alt="Trip">
                            </div>
                            <div class="p-4 bg-white">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <span class="text-primary fw-bold fs-4">$<?php echo number_format($trip['budget']); ?></span>
                                    <small class="text-muted"><i class="far fa-clock me-1"></i> <?php echo $trip['duration_days']; ?> Days</small>
                                </div>
                                <h4 class="serif-font mb-3 h5"><?php echo htmlspecialchars($trip['trip_name']); ?></h4>
                                <p class="text-muted small mb-4"><?php echo substr(htmlspecialchars($trip['description']), 0, 100); ?>...</p>
                                <a href="trip_details.php?id=<?php echo $trip['trip_id']; ?>" class="btn btn-outline-primary w-100 rounded-3">Explore Voyage</a>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        </div>
    </section>

    <!-- How It Works -->
    <section class="section-padding">
        <div class="container text-center">
            <h6 class="text-primary text-uppercase tracking-widest mb-3">The Journey</h6>
            <h2 class="display-4 serif-font mb-5">How It Works</h2>
            <div class="row g-5">
                <div class="col-md-4 animate-on-scroll">
                    <div class="step-icon"><i class="fas fa-search"></i></div>
                    <h4 class="serif-font">Choose</h4>
                    <p class="text-muted">Explore our curated collections of bespoke travel experiences.</p>
                </div>
                <div class="col-md-4 animate-on-scroll" data-delay="0.2s">
                    <div class="step-icon"><i class="fas fa-user-check"></i></div>
                    <h4 class="serif-font">Consult</h4>
                    <p class="text-muted">Connect with our elite agents to customize every detail.</p>
                </div>
                <div class="col-md-4 animate-on-scroll" data-delay="0.4s">
                    <div class="step-icon"><i class="fas fa-plane-takeoff"></i></div>
                    <h4 class="serif-font">Voyage</h4>
                    <p class="text-muted">Depart on your dream journey with 24/7 concierge support.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Elite Agents -->
    <section class="section-padding bg-light">
        <div class="container">
            <div class="text-center mb-5 animate-on-scroll">
                <h6 class="text-primary text-uppercase tracking-widest mb-3">The Experts</h6>
                <h2 class="display-4 serif-font">Masters of Luxury</h2>
            </div>
            <div class="row g-4">
                <?php while($agent = mysqli_fetch_assoc($agents_result)): ?>
                    <div class="col-lg-3 col-md-6 animate-on-scroll">
                        <div class="card border-0 shadow-sm rounded-4 text-center p-4 h-100">
                            <img src="admin/user/<?php echo htmlspecialchars($agent['a_image']); ?>" class="rounded-circle mx-auto mb-3" style="width: 120px; height: 120px; object-fit: cover;" alt="Agent">
                            <h5 class="serif-font mb-1"><?php echo htmlspecialchars($agent['a_name']); ?></h5>
                            <p class="text-primary small text-uppercase mb-3"><?php echo htmlspecialchars($agent['a_profetion'] ?? 'Global Specialist'); ?></p>
                            <div class="d-flex justify-content-center gap-2 mt-auto">
                                <a href="contact.php" class="btn btn-outline-primary btn-sm rounded-pill px-3">Consult Now</a>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        </div>
    </section>

    <!-- Professional Testimonials -->
    <section class="section-padding">
        <div class="container">
            <div class="text-center mb-5 animate-on-scroll">
                <h6 class="text-primary text-uppercase tracking-widest mb-3">Client Whispers</h6>
                <h2 class="display-4 serif-font">Traveler Chronicles</h2>
            </div>
            <div id="testimonialSlider" class="carousel slide" data-bs-ride="carousel">
                <div class="carousel-inner">
                    <?php 
                    $first = true;
                    while($review = mysqli_fetch_assoc($reviews_result)): 
                    ?>
                        <div class="carousel-item <?php echo $first ? 'active' : ''; ?>">
                            <div class="row justify-content-center text-center">
                                <div class="col-lg-8">
                                    <div class="mb-4">
                                        <?php for($i=0; $i<5; $i++) echo '<i class="fas fa-star text-primary small"></i>'; ?>
                                    </div>
                                    <h3 class="serif-font fst-italic mb-4">"<?php echo htmlspecialchars($review['usermessage']); ?>"</h3>
                                    <div class="d-flex align-items-center justify-content-center">
                                        <div class="ms-3 text-start">
                                            <h5 class="mb-0"><?php echo htmlspecialchars($review['username'] ?? $review['email']); ?></h5>
                                            <small class="text-muted">Voyager Since <?php echo date('Y', strtotime($review['date_time'])); ?></small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php 
                    $first = false;
                    endwhile; 
                    ?>
                </div>
                <button class="carousel-control-prev" type="button" data-bs-target="#testimonialSlider" data-bs-slide="prev">
                    <span class="carousel-control-prev-icon bg-primary rounded-circle"></span>
                </button>
                <button class="carousel-control-next" type="button" data-bs-target="#testimonialSlider" data-bs-slide="next">
                    <span class="carousel-control-next-icon bg-primary rounded-circle"></span>
                </button>
            </div>
        </div>
    </section>

    <!-- Newsletter & Social Proof -->
    <section class="section-padding pt-0">
        <div class="container">
            <div class="newsletter-section animate-on-scroll">
                <div class="row align-items-center">
                    <div class="col-lg-6 mb-4 mb-lg-0">
                        <h2 class="serif-font display-5 mb-3">Join the Elite</h2>
                        <p class="mb-0 opacity-75">Subscribe for secret departures and exclusive travel insights.</p>
                    </div>
                    <div class="col-lg-6">
                        <form class="d-flex gap-2">
                            <input type="email" class="form-control bg-white border-0 py-3" placeholder="Elite Email Address">
                            <button class="btn btn-dark px-4">JOIN</button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="mt-5 text-center section-padding">
                <h6 class="text-primary text-uppercase tracking-widest mb-4">Follow the Journey @ExpenseVoyage</h6>
                <div class="row g-3">
                    <div class="col-lg-2 col-4"><img src="img/social-1.jpg" class="social-img w-100" alt="Travel"></div>
                    <div class="col-lg-2 col-4"><img src="img/social-2.jpg" class="social-img w-100" alt="Travel"></div>
                    <div class="col-lg-2 col-4"><img src="img/social-3.jpg" class="social-img w-100" alt="Travel"></div>
                    <div class="col-lg-2 col-4"><img src="img/social-4.jpg" class="social-img w-100" alt="Travel"></div>
                    <div class="col-lg-2 col-4"><img src="img/social-5.jpg" class="social-img w-100" alt="Travel"></div>
                    <div class="col-lg-2 col-4"><img src="img/social-6.jpg" class="social-img w-100" alt="Travel"></div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="section-padding border-top bg-white">
        <div class="container">
            <div class="row g-5">
                <div class="col-lg-4">
                    <h3 class="text-primary mb-4">ExpenseVoyage</h3>
                    <p class="text-muted">Curating bespoke travel experiences for those who seek the extraordinary. Our mission is to transform your dreams into cinematic realities.</p>
                </div>
                <div class="col-lg-2 ms-auto">
                    <h5 class="text-dark mb-4">Quick Links</h5>
                    <ul class="list-unstyled">
                        <li class="mb-2"><a href="package.php" class="text-muted text-decoration-none hover-primary">Packages</a></li>
                        <li class="mb-2"><a href="about.php" class="text-muted text-decoration-none hover-primary">About Us</a></li>
                        <li class="mb-2"><a href="contact.php" class="text-muted text-decoration-none hover-primary">Contact</a></li>
                    </ul>
                </div>
                <div class="col-lg-4">
                    <h5 class="text-dark mb-4">Follow Our Journey</h5>
                    <div class="d-flex gap-3">
                        <a href="#" class="btn btn-outline-indigo rounded-circle p-2"><i class="fab fa-instagram"></i></a>
                        <a href="#" class="btn btn-outline-indigo rounded-circle p-2"><i class="fab fa-facebook-p"></i></a>
                        <a href="#" class="btn btn-outline-indigo rounded-circle p-2"><i class="fab fa-twitter"></i></a>
                    </div>
                </div>
            </div>
            <div class="text-center mt-5 pt-5 text-muted small">
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