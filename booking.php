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

$pageTitle = "Secure Booking | ExpenseVoyage";
$currentPage = "package";
include 'header.php';
?>

    <header class="sub-hero" style="height: 35vh;">
        <div class="sub-hero-bg" style="background-image: url('<?php echo htmlspecialchars($trip['trip_image']); ?>');"></div>
        <div class="container sub-hero-content">
            <span class="text-gold text-uppercase tracking-widest fw-bold mb-3 d-inline-block">Booking Details</span>
            <h1 class="display-3 serif-font text-white">Book Your Trip</h1>
        </div>
    </header>

    <section class="py-5 bg-deep">
        <div class="container">
            <div class="row g-5">
                <!-- Trip Summary Sidebar -->
                <div class="col-lg-4 order-lg-2">
                    <div class="glass-card p-4 sticky-top border-ghost shadow-soft" style="top: 120px;">
                        <h3 class="serif-font text-white mb-4">Trip Summary</h3>
                        <div class="mb-4">
                            <img src="<?php echo htmlspecialchars($trip['trip_image']); ?>" class="img-fluid mb-3 rounded-2" alt="Trip">
                            <h4 class="h5 text-white"><?php echo htmlspecialchars($trip['trip_name']); ?></h4>
                            <p class="text-muted small mb-0"><i class="fas fa-map-marker-alt text-gold me-2"></i><?php echo htmlspecialchars($trip['destination']); ?></p>
                        </div>
                        
                        <hr class="border-ghost">
                        
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted small">Days</span>
                            <span class="text-white"><?php echo htmlspecialchars($trip['duration_days']); ?></span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted small">Price</span>
                            <span class="text-gold fw-bold">$<?php echo number_format($trip['budget']); ?></span>
                        </div>
                        
                        <div class="bg-glass-light mt-4 p-3 rounded-3">
                            <p class="small text-muted mb-0">Book now and start your adventure today.</p>
                        </div>
                    </div>
                </div>

                <!-- Booking Form -->
                <div class="col-lg-8 order-lg-1">
                    <div class="glass-card p-5 border-0 shadow-extreme reveal-up" style="background: rgba(10, 10, 11, 0.95);">
                        <h2 class="serif-font text-white mb-5">Your Information</h2>
                        
                        <form action="payment.php" method="GET" class="booking-form">
                            <input type="hidden" name="trip_id" value="<?php echo $trip_id; ?>">
                            
                            <div class="row g-4">
                                <div class="col-12">
                                    <label class="small text-gold text-uppercase tracking-widest fw-bold mb-2 d-block">Full Name</label>
                                    <input type="text" name="name" class="form-control bg-transparent border-0 border-bottom border-ghost py-3 text-white shadow-none" value="<?php echo htmlspecialchars($_SESSION['name'] ?? ''); ?>" required>
                                </div>
                                
                                <div class="col-md-6">
                                    <label class="small text-gold text-uppercase tracking-widest fw-bold mb-2 d-block">Email</label>
                                    <input type="email" name="email" class="form-control bg-transparent border-0 border-bottom border-ghost py-3 text-white shadow-none" value="<?php echo htmlspecialchars($_SESSION['email'] ?? ''); ?>" required>
                                </div>
                                
                                <div class="col-md-6">
                                    <label class="small text-gold text-uppercase tracking-widest fw-bold mb-2 d-block">Phone Number</label>
                                    <input type="tel" name="phone" class="form-control bg-transparent border-0 border-bottom border-ghost py-3 text-white shadow-none" placeholder="+1 234 567 890" required>
                                </div>

                                <div class="col-md-6">
                                    <label class="small text-gold text-uppercase tracking-widest fw-bold mb-2 d-block">Start Date</label>
                                    <input type="date" name="start_date" class="form-control bg-transparent border-0 border-bottom border-ghost py-3 text-white shadow-none" value="<?php echo $trip['starts_date']; ?>" readonly>
                                </div>

                                <div class="col-md-6">
                                    <label class="small text-gold text-uppercase tracking-widest fw-bold mb-2 d-block">Number of Guests</label>
                                    <select name="seats" class="form-select bg-transparent border-0 border-bottom border-ghost text-white py-3 shadow-none custom-select-luxe">
                                        <?php 
                                        $selected_seats = intval($_GET['seats'] ?? 1);
                                        for($i=1; $i<=min(10, $trip['persons']); $i++): 
                                        ?>
                                            <option value="<?php echo $i; ?>" <?php echo $i == $selected_seats ? 'selected' : ''; ?> class="bg-deep">
                                                <?php echo $i; ?> <?php echo $i==1 ? 'Person' : 'Persons'; ?>
                                            </option>
                                        <?php endfor; ?>
                                    </select>
                                </div>

                                <div class="col-12">
                                    <label class="small text-gold text-uppercase tracking-widest fw-bold mb-2 d-block">Special Requests (Optional)</label>
                                    <textarea name="notes" rows="4" class="form-control bg-transparent border-0 border-bottom border-ghost py-3 text-white shadow-none" placeholder="Any special requests?"></textarea>
                                </div>

                                <div class="col-12 mt-5">
                                    <div class="d-flex align-items-center mb-4 p-3 border border-gold rounded-3 bg-glass-light">
                                        <i class="fas fa-shield-alt text-gold fa-2x me-3"></i>
                                        <div>
                                            <h6 class="text-white mb-1">Safe and Secure</h6>
                                            <p class="small text-muted mb-0">Your information is protected and safe with us.</p>
                                        </div>
                                    </div>
                                    <button type="submit" class="btn-luxe btn-luxe-gold w-100 py-3">Proceed to Payment</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>

<?php include 'footer.php'; ?>