<?php
include 'chatbot-loader.php'; 
include 'admin/config.php';
session_start();

$trip_id = intval($_GET['id'] ?? $_GET['viewid'] ?? 0);

if ($trip_id > 0) {
    $stmt = $con->prepare("SELECT * FROM trips WHERE trip_id = ?");
    $stmt->bind_param("i", $trip_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $trip = $result->fetch_assoc();
    } else {
        header("Location: package.php");
        exit();
    }
} else {
    header("Location: package.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title><?php echo htmlspecialchars($trip['trip_name']); ?> | ExpenseVoyage</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;800&family=Playfair+Display:ital,wght@0,400..900;1,400..900&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <script src="https://cdn.jsdelivr.net/particles.js/2.0.0/particles.min.js"></script>
    
    <link href="css/custom.css" rel="stylesheet">
    
    <style>
        .details-hero {
            height: 70vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(rgba(0, 0, 0, 0.4), rgba(0, 0, 0, 0.4)), 
                        url('<?php echo htmlspecialchars($trip['trip_image']); ?>') center/cover no-repeat;
            text-align: center;
        }

        .booking-sidebar {
            background: #fff;
            border: 1px solid var(--glass-border);
            position: sticky;
            top: 100px;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
        }

        .amenity-icon {
            width: 50px;
            height: 50px;
            background: rgba(79, 70, 229, 0.1);
            color: var(--primary);
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg sticky-top py-3">
        <div class="container">
            <a href="index.php" class="navbar-brand">
                <span class="text-primary fw-bold">Expense</span><span class="text-dark">Voyage</span>
            </a>
            <div class="navbar-nav ms-auto">
                <a class="nav-link px-3" href="package.php">Return to Collection</a>
            </div>
        </div>
    </nav>

    <header class="details-hero">
        <div class="container">
            <h6 class="text-white text-uppercase tracking-widest mb-3 animate__animated animate__fadeIn">Luxury Expedition</h6>
            <h1 class="display-2 text-white serif-font animate__animated animate__fadeInUp"><?php echo htmlspecialchars($trip['trip_name']); ?></h1>
        </div>
    </header>

    <section class="py-5">
        <div class="container">
            <div class="row g-5">
                <div class="col-lg-8">
                    <div class="glass-panel p-5 bg-white shadow-sm">
                        <h2 class="serif-font mb-4">Voyage Narrative</h2>
                        <p class="text-muted lead mb-5"><?php echo nl2br(htmlspecialchars($trip['description'])); ?></p>
                        
                        <h4 class="serif-font mb-4">Key Inclusions</h4>
                        <div class="row g-4 mb-5">
                            <div class="col-md-3 text-center">
                                <div class="amenity-icon mx-auto"><i class="fas fa-hotel"></i></div>
                                <p class="small text-muted">Luxury Villa</p>
                            </div>
                            <div class="col-md-3 text-center">
                                <div class="amenity-icon mx-auto"><i class="fas fa-utensils"></i></div>
                                <p class="small text-muted">Private Chef</p>
                            </div>
                            <div class="col-md-3 text-center">
                                <div class="amenity-icon mx-auto"><i class="fas fa-shuttle-van"></i></div>
                                <p class="small text-muted">Private Concierge</p>
                            </div>
                            <div class="col-md-3 text-center">
                                <div class="amenity-icon mx-auto"><i class="fas fa-camera-retro"></i></div>
                                <p class="small text-muted">Memoir Service</p>
                            </div>
                        </div>

                        <?php if (isset($trip['vehicle_type'])): ?>
                            <h4 class="serif-font mb-4">Travel Logistics</h4>
                            <div class="p-4 bg-light border-0 d-flex align-items-center mb-5 rounded-3">
                                <img src="img/vehicle-thumb.jpg" class="me-4 rounded-2" style="width: 150px;" alt="Vehicle">
                                <div>
                                    <h5 class="mb-1 text-primary"><?php echo htmlspecialchars($trip['vehicle_type']); ?></h5>
                                    <p class="small text-muted mb-0"><?php echo htmlspecialchars($trip['vehicle_features'] ?? 'Premium logistics for ultimate comfort.'); ?></p>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="booking-sidebar p-5">
                        <h3 class="serif-font text-primary mb-3">$<?php echo number_format($trip['budget']); ?></h3>
                        <p class="text-muted small mb-4">Inclusive of all curated experiences and logistics.</p>
                        
                        <div class="d-flex justify-content-between mb-3 border-bottom pb-3">
                            <span class="text-muted">Duration</span>
                            <span class="fw-bold"><?php echo $trip['duration_days']; ?> Days</span>
                        </div>
                        <div class="d-flex justify-content-between mb-3 border-bottom pb-3">
                            <span class="text-muted">Group Limit</span>
                            <span class="fw-bold"><?php echo $trip['persons']; ?> Guests</span>
                        </div>
                        <div class="d-flex justify-content-between mb-4 border-bottom pb-3">
                            <span class="text-muted">Heritage</span>
                            <span>
                                <?php for($i=0; $i<$trip['stars']; $i++) echo '<i class="fas fa-star text-primary small"></i>'; ?>
                            </span>
                        </div>

                        <a href="booking.php?trip_id=<?php echo $trip_id; ?>" class="btn btn-primary w-100 py-4">COMMENCE BOOKING</a>
                        
                        <div class="mt-4 text-center">
                            <p class="small text-muted mb-0"><i class="fas fa-shield-alt text-primary me-2"></i>Secure Estate Transaction</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <footer class="py-5 border-top bg-white mt-5">
        <div class="container text-center">
            <h4 class="text-primary mb-3">ExpenseVoyage</h4>
            <p class="text-muted small mb-0">&copy; 2026 ExpenseVoyage. Crafted for Elegance.</p>
        </div>
    </footer>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/custom.js"></script>
</body>
</html>