<?php
include 'chatbot-loader.php'; 
session_start();
include 'admin/config.php';
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Fetch destination values from the 'trips' table
$query = "SELECT DISTINCT destination FROM trips";
$destinationResult = $con->query($query);

// Fetch review data for testimonials
$reviewQuery = "SELECT * FROM review ORDER BY id DESC LIMIT 6";
$reviewResult = mysqli_query($con, $reviewQuery);

$pageTitle = "Our Services | ExpenseVoyage";
$currentPage = "service";
include 'header.php';
?>

    <!-- Page Header -->
    <header class="hero-editorial" style="height: 50vh;">
        <div class="hero-editorial-bg ken-burns" style="background-image: url('img/service-bg.jpg');"></div>
        <div class="container hero-editorial-content reveal-up">
            <span class="text-gold text-uppercase tracking-widest fw-bold mb-4 d-block">Premium Travel Services</span>
            <h1 class="display-1 serif-font text-white mb-0">Our <span class="text-gold">Expertise</span></h1>
        </div>
    </header>

    <!-- Services Section -->
    <section class="section-padding bg-deep glow-aura">
        <div class="container">
            <div class="row g-5">
                <div class="col-lg-4">
                    <div class="sticky-top" style="top: 120px;">
                        <span class="text-gold text-uppercase tracking-widest small fw-bold mb-3 d-block">What We Do</span>
                        <h2 class="display-4 serif-font text-white mb-4">Elite <span class="text-gold">Solutions</span> For Every Explorer</h2>
                        <p class="text-muted lead mb-5">We curate more than just trips; we design life-defining experiences with a focus on luxury, comfort, and seamless execution.</p>
                        <a href="contact.php" class="btn-luxe btn-luxe-gold">Request Consultation</a>
                    </div>
                </div>
                
                <div class="col-lg-8">
                    <div class="row g-4">
                        <!-- Service Item -->
                        <div class="col-md-6 reveal-up">
                            <div class="glass-card p-5 h-100 transition-all hover-border-gold">
                                <div class="mb-4 d-inline-block p-3 bg-glass border border-gold rounded-3">
                                    <i class="fas fa-passport fa-2x text-gold"></i>
                                </div>
                                <h3 class="h4 serif-font text-white mb-3">Visa Assistance</h3>
                                <p class="text-muted small">Navigating global borders with ease. Our specialists handle all documentation, ensuring a stress-free entry to your dream destinations.</p>
                            </div>
                        </div>

                        <!-- Service Item -->
                        <div class="col-md-6 reveal-up" style="transition-delay: 0.1s;">
                            <div class="glass-card p-5 h-100 transition-all hover-border-gold">
                                <div class="mb-4 d-inline-block p-3 bg-glass border border-gold rounded-3">
                                    <i class="fas fa-plane-departure fa-2x text-gold"></i>
                                </div>
                                <h3 class="h4 serif-font text-white mb-3">Flight Services</h3>
                                <p class="text-muted small">Concierge flight booking with priority seating and competitive rates across major global airlines.</p>
                            </div>
                        </div>

                        <!-- Service Item -->
                        <div class="col-md-6 reveal-up" style="transition-delay: 0.2s;">
                            <div class="glass-card p-5 h-100 transition-all hover-border-gold">
                                <div class="mb-4 d-inline-block p-3 bg-glass border border-gold rounded-3">
                                    <i class="fas fa-map-marked-alt fa-2x text-gold"></i>
                                </div>
                                <h3 class="h4 serif-font text-white mb-3">Guided Tours</h3>
                                <p class="text-muted small">Deep dives into local culture with certified guides who reveal the hidden stories of the world's most iconic landmarks.</p>
                            </div>
                        </div>

                        <!-- Service Item -->
                        <div class="col-md-6 reveal-up" style="transition-delay: 0.3s;">
                            <div class="glass-card p-5 h-100 transition-all hover-border-gold">
                                <div class="mb-4 d-inline-block p-3 bg-glass border border-gold rounded-3">
                                    <i class="fas fa-hotel fa-2x text-gold"></i>
                                </div>
                                <h3 class="h4 serif-font text-white mb-3">Hotel Selection</h3>
                                <p class="text-muted small">Access to a handpicked collection of the world’s most luxurious hotels, boutique stays, and secluded retreats.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Process Section -->
    <section class="section-padding bg-surface border-top border-ghost">
        <div class="container text-center mb-5 reveal-up">
            <span class="text-gold text-uppercase tracking-widest small fw-bold mb-3 d-block">The Methodology</span>
            <h2 class="display-3 serif-font text-white">Our Masterful <span class="text-gold">Process</span></h2>
        </div>
        <div class="container">
            <div class="row g-5">
                <div class="col-md-3 reveal-up">
                    <div class="text-center">
                        <div class="display-1 serif-font text-gold opacity-10 mb-n4">01</div>
                        <h4 class="text-white serif-font mb-3">Curate</h4>
                        <p class="text-muted small">We analyze your travel DNA to find the perfect match from our global catalog.</p>
                    </div>
                </div>
                <div class="col-md-3 reveal-up" style="transition-delay: 0.1s;">
                    <div class="text-center">
                        <div class="display-1 serif-font text-gold opacity-10 mb-n4">02</div>
                        <h4 class="text-white serif-font mb-3">Personalize</h4>
                        <p class="text-muted small">Every detail is calibrated to your specific tastes and requirements.</p>
                    </div>
                </div>
                <div class="col-md-3 reveal-up" style="transition-delay: 0.2s;">
                    <div class="text-center">
                        <div class="display-1 serif-font text-gold opacity-10 mb-n4">03</div>
                        <h4 class="text-white serif-font mb-3">Execute</h4>
                        <p class="text-muted small">Frictionless booking and logistics managed by our boutique team.</p>
                    </div>
                </div>
                <div class="col-md-3 reveal-up" style="transition-delay: 0.3s;">
                    <div class="text-center">
                        <div class="display-1 serif-font text-gold opacity-10 mb-n4">04</div>
                        <h4 class="text-white serif-font mb-3">Experience</h4>
                        <p class="text-muted small">You travel. We handle the rest. 24/7 support throughout your journey.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Testimonials -->
    <section class="section-padding bg-deep glow-aura">
        <div class="container text-center mb-5 reveal-up">
            <span class="text-gold text-uppercase tracking-widest small fw-bold mb-3 d-block">Testimonials</span>
            <h2 class="display-3 serif-font text-white">The Voyage <span class="text-gold">Stories</span></h2>
        </div>
        <div class="container">
            <div class="row g-4">
                <?php
                if ($reviewResult && mysqli_num_rows($reviewResult) > 0):
                    while ($review = mysqli_fetch_assoc($reviewResult)):
                ?>
                    <div class="col-md-4 reveal-up">
                        <div class="glass-card p-5 h-100">
                            <div class="mb-4 text-gold">
                                <i class="fas fa-quote-left fa-2x opacity-20"></i>
                            </div>
                            <p class="text-white italic mb-5" style="font-size: 1.1rem; line-height: 1.8;">
                                "<?php echo htmlspecialchars($review['usermessage']); ?>"
                            </p>
                            <div class="d-flex align-items-center">
                                <img src="img/reviewerimages/<?php echo htmlspecialchars($review['image']); ?>" 
                                     class="rounded-pill border border-gold p-1 me-3" 
                                     style="width: 60px; height: 60px; object-fit: cover;" alt="Reviewer">
                                <div>
                                    <h5 class="serif-font text-white mb-0"><?php echo htmlspecialchars($review['username']); ?></h5>
                                    <span class="text-gold x-small text-uppercase tracking-widest">Global Traveler</span>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endwhile; else: ?>
                    <div class="col-12 text-center py-5 opacity-20">No stories found yet.</div>
                <?php endif; ?>
            </div>
        </div>
    </section>

<?php include 'footer.php'; ?>