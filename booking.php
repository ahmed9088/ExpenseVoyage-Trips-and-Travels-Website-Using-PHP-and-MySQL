<?php
include 'chatbot-loader.php'; 
session_start();
include 'admin/config.php';
include 'csrf.php';

// Check if user is logged in
if (!isset($_SESSION['email'])) {
    $_SESSION['redirect_after_login'] = 'booking.php?trip_id=' . ($_GET['trip_id'] ?? '');
    header("Location: login/account.php");
    exit();
}

// Fetch the trip data
if (isset($_GET['trip_id']) && !empty($_GET['trip_id'])) {
    $trip_id = intval($_GET['trip_id']);
    $stmt = $con->prepare("SELECT * FROM trips WHERE trip_id = ?");
    $stmt->bind_param('i', $trip_id);
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
    <title>Secure Booking | ExpenseVoyage</title>
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
        .booking-hero {
            height: 40vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(rgba(10, 12, 16, 0.8), rgba(10, 12, 16, 0.8)), 
                        url('<?php echo htmlspecialchars($trip['trip_image']); ?>') center/cover no-repeat;
        }

        .summary-card {
            border-left: 4px solid var(--gold);
        }

        .booking-form .form-control {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: white;
            padding: 15px;
            border-radius: 0;
            transition: all 0.3s ease;
        }

        .booking-form .form-control:focus {
            background: rgba(255, 255, 255, 0.1);
            border-color: var(--gold);
            box-shadow: none;
        }
    </style>
</head>
<body>
    <div id="particles-js"></div>

    <nav class="navbar navbar-expand-lg sticky-top glass-panel mx-4 mt-3 py-3">
        <div class="container">
            <a href="index.php" class="navbar-brand">
                <span class="text-gold">Expense</span><span class="text-white">Voyage</span>
            </a>
            <div class="navbar-nav ms-auto">
                <a class="nav-link px-3" href="package.php">Back to Packages</a>
            </div>
        </div>
    </nav>

    <header class="booking-hero">
        <div class="container text-center">
            <h1 class="display-3 serif-font text-white animate__animated animate__fadeInDown">Begin Your Voyage</h1>
            <p class="text-gold tracking-widest text-uppercase animate__animated animate__fadeInUp">Secure Booking Concierge</p>
        </div>
    </header>

    <section class="py-5">
        <div class="container">
            <div class="row g-5">
                <!-- Trip Summary Sidebar -->
                <div class="col-lg-4 order-lg-2">
                    <div class="glass-panel p-4 sticky-top" style="top: 120px;">
                        <h3 class="serif-font mb-4">Voyage Summary</h3>
                        <div class="mb-4">
                            <img src="<?php echo htmlspecialchars($trip['trip_image']); ?>" class="img-fluid mb-3" alt="Trip">
                            <h4 class="h5"><?php echo htmlspecialchars($trip['trip_name']); ?></h4>
                            <p class="text-white-50 small mb-0"><i class="fas fa-map-marker-alt text-gold me-2"></i><?php echo htmlspecialchars($trip['destination']); ?></p>
                        </div>
                        
                        <hr class="border-secondary">
                        
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-white-50">Duration</span>
                            <span><?php echo htmlspecialchars($trip['duration_days']); ?> Days</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-white-50">Base Price</span>
                            <span class="text-gold fw-bold">$<?php echo number_format($trip['budget']); ?></span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-white-50">Rating</span>
                            <span>
                                <?php for($i=0; $i<$trip['stars']; $i++) echo '<i class="fas fa-star text-gold small"></i>'; ?>
                            </span>
                        </div>
                        
                        <div class="summary-card bg-white-5 mt-4 p-3">
                            <p class="small text-white-50 mb-0">"The world is a book and those who do not travel read only one page."</p>
                        </div>
                    </div>
                </div>

                <!-- Booking Form -->
                <div class="col-lg-8 order-lg-1">
                    <div class="glass-panel p-5">
                        <h2 class="serif-font mb-5">Voyager Credentials</h2>
                        
                        <form action="payment.php" method="GET" class="booking-form">
                            <input type="hidden" name="trip_id" value="<?php echo $trip_id; ?>">
                            
                            <div class="row g-4">
                                <div class="col-12">
                                    <label class="small text-white-50 mb-2">Full Name</label>
                                    <input type="text" name="name" class="form-control" value="<?php echo htmlspecialchars($_SESSION['name'] ?? ''); ?>" required>
                                </div>
                                
                                <div class="col-md-6">
                                    <label class="small text-white-50 mb-2">Email Identity</label>
                                    <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($_SESSION['email'] ?? ''); ?>" required>
                                </div>
                                
                                <div class="col-md-6">
                                    <label class="small text-white-50 mb-2">Contact Telephony</label>
                                    <input type="tel" name="phone" class="form-control" placeholder="+X XXX XXX XXXX" required>
                                </div>

                                <div class="col-md-6">
                                    <label class="small text-white-50 mb-2">Commencement Date</label>
                                    <input type="date" name="start_date" class="form-control" value="<?php echo $trip['starts_date']; ?>" readonly>
                                </div>

                                <div class="col-md-6">
                                    <label class="small text-white-50 mb-2">Number of Voyagers</label>
                                    <select name="seats" class="form-select bg-dark border-secondary text-white py-3 rounded-0">
                                        <?php for($i=1; $i<=min(10, $trip['persons']); $i++): ?>
                                            <option value="<?php echo $i; ?>"><?php echo $i; ?> <?php echo $i==1 ? 'Voyager' : 'Voyagers'; ?></option>
                                        <?php endfor; ?>
                                    </select>
                                </div>

                                <div class="col-12">
                                    <label class="small text-white-50 mb-2">Special Requests (Optional)</label>
                                    <textarea name="notes" rows="4" class="form-control" placeholder="Any dietary requirements or special occasions?"></textarea>
                                </div>

                                <div class="col-12 mt-5">
                                    <div class="d-flex align-items-center mb-4 p-3 bg-white-5 border border-secondary">
                                        <i class="fas fa-shield-halved text-gold fa-2x me-3"></i>
                                        <div>
                                            <h6 class="mb-1">Secure Encryption Active</h6>
                                            <p class="small text-white-50 mb-0">Your data is protected by industry-leading 256-bit SSL encryption.</p>
                                        </div>
                                    </div>
                                    <button type="submit" class="btn btn-primary w-100 py-4 tracking-widest">PROCEED TO SECURE PAYMENT</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <footer class="py-5 border-top border-secondary mt-5">
        <div class="container text-center">
            <h4 class="text-gold mb-3">ExpenseVoyage</h4>
            <p class="text-white-50 small mb-0">&copy; 2026 ExpenseVoyage. Luxury Reimagined.</p>
        </div>
    </footer>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/custom.js"></script>
</body>
</html>