<?php
include 'chatbot-loader.php';
session_start();
include 'admin/config.php';

// Sanitize inputs
$destination = isset($_GET['destination']) ? mysqli_real_escape_string($con, $_GET['destination']) : null;

// Build Query
$query = "SELECT * FROM trips";
if ($destination) {
    $query .= " WHERE destination LIKE '%$destination%'";
}
$trips_result = mysqli_query($con, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Curated Packages | ExpenseVoyage</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    
    <!-- Google Web Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;800&family=Playfair+Display:ital,wght@0,400..900;1,400..900&display=swap" rel="stylesheet">
    
    <!-- Libraries -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <script src="https://cdn.jsdelivr.net/particles.js/2.0.0/particles.min.js"></script>
    
    <!-- Custom CSS -->
    <link href="css/custom.css" rel="stylesheet">
    
    <style>
        .page-hero {
            height: 60vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(rgba(10, 12, 16, 0.8), rgba(10, 12, 16, 0.8)), 
                        url('img/packages-bg.jpg') center/cover no-repeat;
            text-align: center;
        }

        .trip-card {
            transition: all 0.4s ease;
            border: none;
            background: var(--bg-card);
            overflow: hidden;
        }

        .trip-card:hover {
            transform: translateY(-10px);
            border: 1px solid var(--gold);
        }

        .trip-img-wrap {
            height: 250px;
            overflow: hidden;
            position: relative;
        }

        .trip-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.6s ease;
        }

        .trip-card:hover .trip-img {
            transform: scale(1.1);
        }

        .price-tag {
            position: absolute;
            top: 20px;
            right: 20px;
            background: var(--gold-gradient);
            color: black;
            padding: 8px 15px;
            font-weight: 800;
            border-radius: 0;
            clip-path: polygon(0 0, 100% 0, 100% 100%, 15% 100%);
        }
    </style>
</head>
<body>
    <div id="particles-js"></div>

    <!-- Navigation (Simplified for subpages) -->
    <nav class="navbar navbar-expand-lg sticky-top glass-panel mx-4 mt-3 py-3">
        <div class="container">
            <a href="index.php" class="navbar-brand">
                <span class="text-gold">Expense</span><span class="text-white">Voyage</span>
            </a>
            <div class="navbar-nav ms-auto d-none d-lg-flex">
                <a class="nav-link px-3" href="index.php">Home</a>
                <a class="nav-link px-3 active text-gold" href="package.php">Packages</a>
                <a class="nav-link px-3" href="about.php">About</a>
                <a class="nav-link px-3" href="contact.php">Contact</a>
            </div>
        </div>
    </nav>

    <!-- Page Hero -->
    <header class="page-hero">
        <div class="container">
            <h6 class="text-gold text-uppercase tracking-widest mb-3 animate__animated animate__fadeIn">Curated Collections</h6>
            <h1 class="display-3 text-white serif-font animate__animated animate__fadeInUp">Bespoke Journeys</h1>
        </div>
    </header>

    <!-- Packages Grid -->
    <section class="py-5 mt-5">
        <div class="container">
            <div class="row g-4">
                <?php if (mysqli_num_rows($trips_result) > 0): ?>
                    <?php while($trip = mysqli_fetch_assoc($trips_result)): ?>
                        <div class="col-lg-4 col-md-6 animate-on-scroll">
                            <div class="trip-card glass-panel h-100">
                                <div class="trip-img-wrap">
                                    <img src="<?php echo htmlspecialchars($trip['trip_image']); ?>" class="trip-img" alt="Trip">
                                    <div class="price-tag">$<?php echo number_format($trip['budget']); ?></div>
                                </div>
                                <div class="p-4">
                                    <h3 class="h5 mb-3"><?php echo htmlspecialchars($trip['trip_name']); ?></h3>
                                    <div class="d-flex gap-3 mb-4 small text-white-50">
                                        <span><i class="far fa-calendar me-1 text-gold"></i> <?php echo $trip['duration_days']; ?> Days</span>
                                        <span><i class="far fa-user me-1 text-gold"></i> <?php echo $trip['persons']; ?> Guests</span>
                                    </div>
                                    <a href="trip_details.php?id=<?php echo $trip['trip_id']; ?>" class="btn btn-primary w-100 rounded-0 py-3">VIEW DETAILS</a>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div class="col-12 text-center py-5">
                        <h2 class="text-white-50">No journeys found in this collection.</h2>
                        <a href="package.php" class="text-gold mt-3 d-inline-block">View All Packages</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- Footer (Standardized) -->
    <footer class="py-5 border-top border-secondary mt-5">
        <div class="container text-center">
            <h4 class="text-gold mb-3">ExpenseVoyage</h4>
            <p class="text-white-50 small mb-0">&copy; 2026 ExpenseVoyage. Crafted for the extraordinary.</p>
        </div>
    </footer>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/custom.js"></script>
</body>
</html>