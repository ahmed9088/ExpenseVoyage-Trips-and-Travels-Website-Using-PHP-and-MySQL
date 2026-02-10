<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include 'admin/config.php';

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

$pageTitle = htmlspecialchars($trip['trip_name']) . " | Trip Details";
$currentPage = "package";
include 'header.php';
?>

    <!-- Trip Header -->
    <header class="hero-editorial">
        <div class="hero-editorial-bg ken-burns" style="background-image: url('<?php echo htmlspecialchars($trip['trip_image']); ?>');"></div>
        <div class="container hero-editorial-content reveal-up">
            <span class="text-gold text-uppercase tracking-widest fw-bold mb-4 d-block">Excellent Trip</span>
            <h1 class="display-1 serif-font text-white mb-0"><?php echo htmlspecialchars($trip['trip_name']); ?></h1>
            <div class="price-tag-reveal" style="bottom: auto; top: -50px; left: auto; right: 0; background: var(--primary); color: #000; font-weight: bold;">$<?php echo number_format($trip['budget']); ?></div>
        </div>
    </header>

    <!-- Trip Info -->
    <section class="section-padding bg-deep glow-aura">
        <div class="container mt-n5 position-relative z-2">
            <div class="row g-5">
                <div class="col-lg-8">
                    <div class="glass-card p-5 border-0 shadow-extreme reveal-up" style="background: rgba(10, 10, 11, 0.95);">
                        <h2 class="serif-font mb-5 text-white display-6">About this <span class="text-gold">Trip</span></h2>
                        <div class="text-main lead mb-5" style="line-height: 2;">
                            <?php echo nl2br(htmlspecialchars($trip['description'])); ?>
                        </div>
                        
                        <div class="row g-4 mb-5">
                            <div class="col-6 col-md-3">
                                <div class="text-center p-4 border border-ghost rounded-4 transition-all hover-border-gold">
                                    <div class="fs-2 text-gold mb-3"><i class="fas fa-hotel"></i></div>
                                    <p class="small text-muted text-uppercase tracking-widest fw-bold mb-0">Luxury Hotel</p>
                                </div>
                            </div>
                            <div class="col-6 col-md-3">
                                <div class="text-center p-4 border border-ghost rounded-4 transition-all hover-border-gold">
                                    <div class="fs-2 text-gold mb-3"><i class="fas fa-utensils"></i></div>
                                    <p class="small text-muted text-uppercase tracking-widest fw-bold mb-0">Free Food</p>
                                </div>
                            </div>
                            <div class="col-6 col-md-3">
                                <div class="text-center p-4 border border-ghost rounded-4 transition-all hover-border-gold">
                                    <div class="fs-2 text-gold mb-3"><i class="fas fa-car"></i></div>
                                    <p class="small text-muted text-uppercase tracking-widest fw-bold mb-0">Transport</p>
                                </div>
                            </div>
                            <div class="col-6 col-md-3">
                                <div class="text-center p-4 border border-ghost rounded-4 transition-all hover-border-gold">
                                    <div class="fs-2 text-gold mb-3"><i class="fas fa-headset"></i></div>
                                    <p class="small text-muted text-uppercase tracking-widest fw-bold mb-0">24/7 Guide</p>
                                </div>
                            </div>
                        </div>

                        <?php if (isset($trip['vehicle_type'])): ?>
                            <div class="p-4 glass-card border-ghost d-flex flex-column flex-md-row align-items-center mb-0 gap-4 rough-edges">
                                <img src="img/about.jpg" class="rounded-3 shadow-gold" style="width: 150px; height: 100px; object-fit: cover;" alt="Car">
                                <div>
                                    <h5 class="mb-2 text-gold serif-font">Travel Method: <?php echo htmlspecialchars($trip['vehicle_type']); ?></h5>
                                    <p class="small text-muted mb-0">Enjoy comfortable travel throughout your journey with our private vehicles.</p>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Booking Widget -->
                <div class="col-lg-4">
                    <div class="glass-card p-5 sticky-top border-0 shadow-gold reveal-up" style="top: 120px; background: rgba(10, 10, 11, 0.95);">
                        <h4 class="serif-font mb-4 text-white">Book Your <span class="text-gold">Trip</span></h4>
                        <form action="booking.php" method="GET" class="booking-widget">
                            <input type="hidden" name="trip_id" value="<?php echo $trip_id; ?>">
                            
                            <div class="mb-4">
                                <label class="small text-gold text-uppercase tracking-widest fw-bold mb-2 d-block">Number of Persons</label>
                                <select name="seats" id="guestCount" class="form-select bg-transparent border-0 border-bottom border-ghost text-white py-3 shadow-none custom-select-luxe" onchange="updateTotalPrice()">
                                    <?php for($i=1; $i<=min(10, $trip['persons']); $i++): ?>
                                        <option value="<?php echo $i; ?>" class="bg-deep"><?php echo $i; ?> <?php echo $i==1 ? 'Person' : 'Persons'; ?></option>
                                    <?php endfor; ?>
                                </select>
                            </div>

                            <div class="d-flex justify-content-between mb-4 px-1">
                                <span class="text-muted small text-uppercase tracking-widest">Price per person</span>
                                <span class="text-white fw-bold">$<?php echo number_format($trip['budget']); ?></span>
                            </div>
                            
                            <div class="p-4 border border-gold rounded-4 mb-5 text-center bg-glass-light">
                                <h6 class="small text-gold text-uppercase tracking-widest fw-bold mb-2">Total Price</h6>
                                <h2 class="text-white display-6 fw-bold mb-0">$<span id="totalPriceDisplay"><?php echo number_format($trip['budget']); ?></span></h2>
                            </div>

                            <button type="submit" class="btn-luxe btn-luxe-gold w-100 py-3">Book This Trip</button>
                        </form>
                        
                        <div class="mt-4 pt-4 border-top border-ghost text-center">
                            <p class="small text-ghost mb-0"><i class="fas fa-lock me-2 text-gold"></i> Secure Online Payment</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Itinerary List -->
    <section class="section-padding bg-deep border-top border-subtle glow-aura">
        <div class="container">
            <div class="text-center mb-5 reveal-up">
                <h6 class="text-gold text-uppercase tracking-widest fw-bold mb-3">Daily Plan</h6>
                <h2 class="display-4 serif-font text-white">Trip <span class="text-gold">Timeline</span></h2>
            </div>
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="position-relative">
                        <div class="position-absolute h-100 border-start border-ghost opacity-20" style="left: 20px; top: 0;"></div>
                        
                        <?php
                        $itStmt = $con->prepare("SELECT * FROM itinerary WHERE trip_id = ? ORDER BY day_number ASC");
                        $itStmt->bind_param("i", $trip_id);
                        $itStmt->execute();
                        $itRes = $itStmt->get_result();
                        if ($itRes->num_rows > 0):
                            while ($day = $itRes->fetch_assoc()):
                        ?>
                            <div class="mb-5 d-flex gap-5 reveal-up position-relative">
                                <div class="z-3">
                                    <div class="bg-gold text-secondary rounded-pill d-flex align-items-center justify-content-center fw-bold" style="width: 42px; height: 42px; font-size: 0.8rem;">
                                        <?php echo $day['day_number']; ?>
                                    </div>
                                </div>
                                <div class="glass-card p-5 flex-grow-1 border-ghost transition-all hover-border-gold shadow-soft">
                                    <h4 class="serif-font mb-3 text-white d-flex align-items-center">
                                        <div class="text-gold me-3 fs-3"><i class="fas <?php echo $day['activity_icon']; ?>"></i></div>
                                        <?php echo htmlspecialchars($day['activity_title']); ?>
                                    </h4>
                                    <p class="text-muted mb-0 lead small" style="line-height: 1.8;"><?php echo htmlspecialchars($day['activity_desc']); ?></p>
                                </div>
                            </div>
                        <?php 
                            endwhile;
                        else:
                        ?>
                            <div class="p-5 text-center reveal-up">
                                <div class="mb-4 text-gold opacity-10"><i class="fas fa-compass fa-10x"></i></div>
                                <h4 class="serif-font text-white">Details coming soon...</h4>
                                p class="text-muted mb-0 mx-auto" style="max-width: 500px;">We are currently finalizing the details for this trip. Check back soon!</p>
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

<?php include 'footer.php'; ?>