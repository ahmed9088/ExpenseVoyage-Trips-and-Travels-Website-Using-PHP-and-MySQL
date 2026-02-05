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
                        <form action="booking.php" method="GET" class="booking-widget">
                            <input type="hidden" name="trip_id" value="<?php echo $trip_id; ?>">
                            
                            <div class="mb-3">
                                <label class="small text-muted mb-2">Number of Voyagers</label>
                                <select name="seats" id="guestCount" class="form-select bg-light border-0 py-3" onchange="updateTotalPrice()">
                                    <?php for($i=1; $i<=min(10, $trip['persons']); $i++): ?>
                                        <option value="<?php echo $i; ?>"><?php echo $i; ?> <?php echo $i==1 ? 'Voyager' : 'Voyagers'; ?></option>
                                    <?php endfor; ?>
                                </select>
                            </div>

                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Unit Price</span>
                                <span>$<span id="unitPrice"><?php echo number_format($trip['budget']); ?></span></span>
                            </div>
                            
                            <div class="price-display p-3 bg-primary bg-opacity-10 rounded-3 mb-4 text-center">
                                <h6 class="small text-primary text-uppercase tracking-widest mb-1">Total Expedition Cost</h6>
                                <h3 class="text-primary fw-bold mb-0">$<span id="totalPriceDisplay"><?php echo number_format($trip['budget']); ?></span></h3>
                            </div>

                            <button type="submit" class="btn btn-primary w-100 py-4 shadow-sm">COMMENCE BOOKING</button>
                        </form>
                        
                        <div class="mt-4 text-center">
                            <p class="small text-muted mb-0"><i class="fas fa-shield-alt text-primary me-2"></i>Secure Estate Transaction</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Detailed Itinerary Section -->
    <section class="section-padding bg-light">
        <div class="container">
            <h2 class="serif-font text-center mb-5">Expedition Itinerary</h2>
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="itinerary-timeline">
                        <?php
                        $itStmt = $con->prepare("SELECT * FROM itinerary WHERE trip_id = ? ORDER BY day_number ASC");
                        $itStmt->bind_param("i", $trip_id);
                        $itStmt->execute();
                        $itRes = $itStmt->get_result();
                        if ($itRes->num_rows > 0):
                            while ($day = $itRes->fetch_assoc()):
                        ?>
                            <div class="itinerary-item mb-4 d-flex">
                                <div class="itinerary-day text-primary fw-bold me-4" style="min-width: 60px;">Day <?php echo $day['day_number']; ?></div>
                                <div class="glass-panel p-4 bg-white shadow-sm flex-grow-1">
                                    <h5 class="serif-font mb-2"><i class="fas <?php echo $day['activity_icon']; ?> text-primary me-2"></i><?php echo htmlspecialchars($day['activity_title']); ?></h5>
                                    <p class="text-muted small mb-0"><?php echo htmlspecialchars($day['activity_desc']); ?></p>
                                </div>
                            </div>
                        <?php 
                            endwhile;
                        else:
                        ?>
                            <div class="p-5 text-center bg-white rounded-4 shadow-sm">
                                <i class="fas fa-calendar-alt text-muted fa-3x mb-3"></i>
                                <p class="text-muted mb-0">Our curators are currently drafting the detailed day-by-day plan for this voyage.</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <script>
        function updateTotalPrice() {
            const guestCount = document.getElementById('guestCount').value;
            const unitPrice = <?php echo $trip['budget']; ?>;
            const total = guestCount * unitPrice;
            document.getElementById('totalPriceDisplay').innerText = new Intl.NumberFormat().format(total);
        }
    </script>

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