<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include 'admin/config.php';

// Safe Stat Fetching
function getCount($con, $table) {
    $res = mysqli_query($con, "SELECT COUNT(*) as total FROM $table");
    if ($res) {
        $row = mysqli_fetch_assoc($res);
        return $row['total'];
    }
    return 0;
}

$countUsers = getCount($con, 'users') + 1200;
$countTrips = getCount($con, 'trips');
$countAgents = getCount($con, 'agent');

// Fetch data for the page
$trips_result = mysqli_query($con, "SELECT * FROM trips LIMIT 6");
$agents_result = mysqli_query($con, "SELECT * FROM agent ORDER BY id DESC LIMIT 4");
$reviews_result = mysqli_query($con, "SELECT * FROM review ORDER BY date_time DESC LIMIT 6");

$pageTitle = "Home | ExpenseVoyage - Best Travel Trips";
$currentPage = "index";
include 'header.php';
?>

    <!-- Ultra-Luxe 3.0 Trending Hero -->
    <section class="hero-section trending-hero" style="height: 100vh;">
        <div id="hero-bg" class="hero-bg ken-burns" style="background-image: url('img/carousel-1.jpg');"></div>
        <div class="hero-overlay"></div>
        
        <div class="container h-100 d-flex align-items-center">
            <div class="row w-100">
                <div class="col-lg-8">
                    <div class="hero-content-trending reveal-up">
                        <span class="trending-badge mb-4 d-inline-block">Volume 2026 • Expedition Elite</span>
                        <h1 class="hero-title-main mb-4">The Art of <br> <span class="text-gold">Voyage</span></h1>
                        <p class="hero-desc mb-5">Crafting hyper-exclusive travel experiences for the modern explorer. Redefining luxury through trending aesthetics and curated adventures.</p>
                        
                        <div class="hero-actions d-flex gap-4">
                            <a href="package.php" class="btn-luxe btn-luxe-gold">Explore Collection</a>
                            <a href="about.php" class="btn-luxe btn-luxe-outline">Our Story</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Trending Decorative Elements -->
        <div class="hero-decorative-text d-none d-lg-block">EXPENSEVOYAGE ©</div>
    </section>
    <!-- Cinematic Marquee -->
    <div class="cinematic-marquee">
        <div class="marquee-content">
            <span class="marquee-text">Adventure Awaits</span>
            <span class="marquee-text">•</span>
            <span class="marquee-text">Luxury Travel</span>
            <span class="marquee-text">•</span>
            <span class="marquee-text">Exotic Locations</span>
            <span class="marquee-text">•</span>
            <span class="marquee-text">Ultimate Ease</span>
            <span class="marquee-text">•</span>
            <!-- Duplicated for seamless loop -->
            <span class="marquee-text">Adventure Awaits</span>
            <span class="marquee-text">•</span>
            <span class="marquee-text">Luxury Travel</span>
            <span class="marquee-text">•</span>
            <span class="marquee-text">Exotic Locations</span>
            <span class="marquee-text">•</span>
            <span class="marquee-text">Ultimate Ease</span>
            <span class="marquee-text">•</span>
        </div>
    </div>

    <!-- Stats Section -->
    <section class="section-padding bg-deep position-relative">
        <div class="container reveal-up">
            <div class="row g-4 text-center">
                <div class="col-md-3">
                    <h2 class="display-4 serif-font text-gold mb-1"><?php echo number_format($countTrips); ?>+</h2>
                    <p class="text-ghost text-uppercase tracking-widest small">Total Trips</p>
                </div>
                <div class="col-md-3">
                    <h2 class="display-4 serif-font text-gold mb-1"><?php echo number_format($countUsers); ?>+</h2>
                    <p class="text-ghost text-uppercase tracking-widest small">Happy Travelers</p>
                </div>
                <div class="col-md-3">
                    <h2 class="display-4 serif-font text-gold mb-1"><?php echo number_format($countAgents); ?>+</h2>
                    <p class="text-ghost text-uppercase tracking-widest small">Expert Guides</p>
                </div>
                <div class="col-md-3">
                    <h2 class="display-4 serif-font text-gold mb-1">24/7</h2>
                    <p class="text-ghost text-uppercase tracking-widest small">Support Service</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Bento Featured Collections -->
    <section class="section-padding bg-deep">
        <div class="container">
            <div class="row align-items-end mb-5 reveal-up">
                <div class="col-lg-6">
                    <h6 class="text-gold text-uppercase tracking-widest fw-bold mb-3 small">Top Destinations</h6>
                    <h2 class="display-4 serif-font text-white">Luxury <br> <span class="text-gold">Collections</span></h2>
                </div>
                <div class="col-lg-6 text-lg-end">
                    <p class="text-muted mb-4" style="max-width: 450px; margin-left: auto;">Experience the world's most exclusive destinations with our trending bento curation.</p>
                </div>
            </div>

            <div class="bento-grid">
                <!-- Large Feature -->
                <div class="bento-item bento-item-large reveal-up">
                    <div class="category-card h-100">
                        <div class="pulsar-badge">
                            <div class="pulsar-dot"></div>
                            <span class="text-white small fw-bold" style="font-size: 0.6rem;"><?php echo rand(5, 15); ?> People Viewing</span>
                        </div>
                        <img src="img/package-1.jpg" alt="Local">
                        <div class="category-content p-5">
                            <span class="text-gold text-uppercase tracking-widest small mb-3 d-block">Elite Choices</span>
                            <h3 class="display-3 serif-font text-white mb-4">Local <br> Gems</h3>
                            <a href="package.php?type=local" class="btn-luxe btn-luxe-gold px-5">Explore</a>
                        </div>
                    </div>
                </div>
                
                <!-- Tall Feature -->
                <div class="bento-item bento-item-tall reveal-up" style="transition-delay: 0.1s;">
                    <div class="category-card h-100">
                        <img src="img/package-2.jpg" alt="International">
                        <div class="category-content p-4">
                            <span class="text-gold text-uppercase tracking-widest small mb-2 d-block">Global</span>
                            <h3 class="display-6 serif-font text-white mb-3">Global <br> Reach</h3>
                            <a href="package.php?type=international" class="btn-luxe btn-luxe-outline x-small">Details</a>
                        </div>
                    </div>
                </div>

                <!-- Wide Feature -->
                <div class="bento-item bento-item-wide reveal-up" style="transition-delay: 0.2s;">
                    <div class="category-card h-100">
                        <img src="img/about.jpg" alt="Experience">
                        <div class="category-content p-4">
                            <h3 class="display-6 serif-font text-white mb-3">Crafted <br> Experiences</h3>
                            <p class="text-muted small mb-3">Bespoke travel for the refined spirit.</p>
                            <a href="about.php" class="text-gold text-uppercase tracking-widest small text-decoration-none fw-bold">Learn More →</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Why Choose Us -->
    <section class="section-padding bg-deep">
        <div class="container">
            <div class="text-center mb-5 reveal-up">
                <h6 class="text-gold text-uppercase tracking-widest fw-bold mb-3 small">About Us</h6>
                <h2 class="display-4 serif-font text-white">Why Book with <span class="text-gold">Us?</span></h2>
            </div>
            
            <div class="row g-5 mt-4">
                <div class="col-lg-4 reveal-up">
                    <div class="glass-card p-5 h-100">
                        <div class="mb-4 fs-1 text-gold"><i class="fas fa-gem"></i></div>
                        <h4 class="serif-font text-white mb-3">Best Prices</h4>
                        <p class="text-muted">We offer high-quality trips at the best rates in the market.</p>
                    </div>
                </div>
                <div class="col-lg-4 reveal-up" style="transition-delay: 0.1s;">
                    <div class="glass-card p-5 h-100">
                        <div class="mb-4 fs-1 text-gold"><i class="fas fa-check-circle"></i></div>
                        <h4 class="serif-font text-white mb-3">Easy Booking</h4>
                        <p class="text-muted">Our booking process is simple and takes only a few minutes.</p>
                    </div>
                </div>
                <div class="col-lg-4 reveal-up" style="transition-delay: 0.2s;">
                    <div class="glass-card p-5 h-100">
                        <div class="mb-4 fs-1 text-gold"><i class="fas fa-headset"></i></div>
                        <h4 class="serif-font text-white mb-3">24/7 Support</h4>
                        <p class="text-muted">Our friendly support team is always ready to help you.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Modern FAQ Section -->
    <section class="section-padding bg-deep">
        <div class="container">
            <div class="text-center mb-5 reveal-up">
                <h6 class="text-gold text-uppercase tracking-widest fw-bold mb-3 small">Concierge AI</h6>
                <h2 class="display-4 serif-font text-white">Common <span class="text-gold">Questions</span></h2>
            </div>

            <div class="row justify-content-center">
                <div class="col-lg-8 reveal-up">
                    <div class="accordion faq-accordion" id="faqAccordion">
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#q1">
                                    How do I book a private tour?
                                </button>
                            </h2>
                            <div id="q1" class="accordion-collapse collapse show" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    Simply browse our collections, select your desired package, and click 'Book Now'. Our concierge team will handle the rest of the arrangements for you.
                                </div>
                            </div>
                        </div>
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#q2">
                                    Are customized luxury trips available?
                                </button>
                            </h2>
                            <div id="q2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    Absolutely. We specialize in bespoke travel. Contact our 24/7 support or use the chatbot to start crafting your unique itinerary.
                                </div>
                            </div>
                        </div>
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#q3">
                                    What is the cancellation policy?
                                </button>
                            </h2>
                            <div id="q3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    We offer flexible cancellation policies for most elite packages. Generally, cancellations 14 days prior to departure are eligible for a full credit.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Testimonial Section -->
    <section class="section-padding bg-deep position-relative">
        <div class="container reveal-up">
            <div class="glass-card testimonial-card overflow-hidden position-relative">
                <div class="row align-items-center">
                    <div class="col-lg-7">
                        <div class="fs-1 text-primary opacity-20 mb-4"><i class="fas fa-quote-left"></i></div>
                        <?php
                        $rev_res = mysqli_query($con, "SELECT * FROM review ORDER BY date_time DESC LIMIT 1");
                        if ($rev_row = mysqli_fetch_assoc($rev_res)):
                        ?>
                            <h2 class="serif-font text-white display-5 mb-5">"<?php echo htmlspecialchars($rev_row['usermessage']); ?>"</h2>
                            <div class="d-flex align-items-center gap-4">
                                <img src="img/Ali.jpg" class="rounded-pill" style="width: 80px; height: 80px; object-fit: cover;" alt="Client">
                                <div>
                                    <h6 class="text-white mb-1"><?php echo htmlspecialchars($rev_row['username']); ?></h6>
                                    <p class="text-gold text-uppercase tracking-widest small mb-0">Satisfied Traveler</p>
                                </div>
                            </div>
                        <?php else: ?>
                            <h2 class="serif-font text-white display-5 mb-5">"This was the best trip of my life. Everything was perfect from start to finish."</h2>
                            <div class="d-flex align-items-center gap-4">
                                <img src="img/Ali.jpg" class="rounded-pill" style="width: 80px; height: 80px; object-fit: cover;" alt="Client">
                                <div>
                                    <h6 class="text-white mb-1">John Doe</h6>
                                    <p class="text-gold text-uppercase tracking-widest small mb-0">Satisfied Traveler</p>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="col-lg-5 d-none d-lg-block">
                        <img src="img/about.jpg" class="w-100 rounded-4 shadow-extreme" style="transform: rotate(2deg);" alt="Experience">
                    </div>
                </div>
            </div>
        </div>
    </section>

<?php include 'footer.php'; ?>