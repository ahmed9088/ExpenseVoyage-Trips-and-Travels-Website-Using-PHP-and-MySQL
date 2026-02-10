<?php
include 'chatbot-loader.php'; 
session_start();
include 'admin/config.php';
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Check if the user is logged in
if (!isset($_SESSION['email'])) {
    $_SESSION['redirect_after_login'] = 'guide.php';
    header("Location: login/account.php");
    exit();
}

// Fetch agent data
$agentQuery = "SELECT id, a_name, a_profetion, a_image, date_time FROM agent ORDER BY date_time DESC";
$agentResult = mysqli_query($con, $agentQuery);

$pageTitle = "Our Guides | ExpenseVoyage";
$currentPage = "guide";
include 'header.php';
?>

    <!-- Page Header -->
    <header class="hero-editorial" style="height: 50vh;">
        <div class="hero-editorial-bg ken-burns" style="background-image: url('img/guides-header.jpg');"></div>
        <div class="container hero-editorial-content reveal-up">
            <span class="text-gold text-uppercase tracking-widest fw-bold mb-4 d-block">World-Class Companions</span>
            <h1 class="display-1 serif-font text-white mb-0">Our Travel <span class="text-gold">Guides</span></h1>
        </div>
    </header>

    <!-- Guides Section -->
    <section class="section-padding bg-deep glow-aura">
        <div class="container text-center mb-5 reveal-up">
            <span class="text-gold text-uppercase tracking-widest small fw-bold mb-3 d-block">The Experts</span>
            <h2 class="display-3 serif-font text-white">Masterful <span class="text-gold">Curation</span></h2>
            <p class="text-muted lead mx-auto" style="max-width: 700px;">Meet the architects of your journey. Each of our guides brings a lifetime of local wisdom and a passion for storytelling.</p>
        </div>

        <div class="container">
            <div class="row g-4">
                <?php if ($agentResult && mysqli_num_rows($agentResult) > 0): ?>
                    <?php while ($agent = mysqli_fetch_assoc($agentResult)): ?>
                        <div class="col-lg-3 col-md-6 reveal-up">
                            <div class="glass-card p-5 h-100 text-center group">
                                <div class="position-relative mb-4 mx-auto rough-edges" style="width: 150px; height: 150px;">
                                    <img src="img/<?php echo htmlspecialchars($agent['a_image']); ?>" 
                                         class="w-100 h-100 rounded-circle border border-gold p-1" 
                                         style="object-fit: cover;" alt="Guide">
                                </div>
                                <h3 class="h4 serif-font text-white mb-2"><?php echo htmlspecialchars($agent['a_name']); ?></h3>
                                <span class="text-gold x-small text-uppercase tracking-widest d-block mb-4"><?php echo htmlspecialchars($agent['a_profetion']); ?></span>
                                <div class="d-flex justify-content-center gap-3">
                                    <a href="#" class="text-muted hover-gold transition-all"><i class="fab fa-instagram"></i></a>
                                    <a href="#" class="text-muted hover-gold transition-all"><i class="fab fa-twitter"></i></a>
                                    <a href="#" class="text-muted hover-gold transition-all"><i class="fab fa-linkedin-in"></i></a>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div class="col-12 py-5 text-center opacity-20">No experts found at this time.</div>
                <?php endif; ?>
            </div>

            <!-- How it works -->
            <div class="mt-5 pt-5">
                <div class="row g-5 align-items-center">
                    <div class="col-lg-6 reveal-up">
                        <div class="rough-edges">
                            <img src="img/guide-working.jpg" class="w-100" style="height: 400px; object-fit: cover;" alt="Guides at work">
                        </div>
                    </div>
                    <div class="col-lg-6 reveal-up">
                        <span class="text-gold text-uppercase tracking-widest small fw-bold mb-3 d-block">Synergistic Exploration</span>
                        <h2 class="display-5 serif-font text-white mb-4">A Human <span class="text-gold">Touch</span> in a Digital World</h2>
                        <ul class="list-unstyled">
                            <li class="mb-4 d-flex align-items-start">
                                <i class="fas fa-check-circle text-gold me-3 mt-1"></i>
                                <div>
                                    <h5 class="text-white serif-font mb-2">Deep Local Intelligence</h5>
                                    <p class="text-muted small">Our guides don't just know the map; they know the heartbeat of the city.</p>
                                </div>
                            </li>
                            <li class="mb-4 d-flex align-items-start">
                                <i class="fas fa-check-circle text-gold me-3 mt-1"></i>
                                <div>
                                    <h5 class="text-white serif-font mb-2">Safety & Security</h5>
                                    <p class="text-muted small">24/7 protection and guidance throughout your expedition.</p>
                                </div>
                            </li>
                            <li class="d-flex align-items-start">
                                <i class="fas fa-check-circle text-gold me-3 mt-1"></i>
                                <div>
                                    <h5 class="text-white serif-font mb-2">Bespoke Storytelling</h5>
                                    <p class="text-muted small">Insights tailored to your personal interests and curiosity.</p>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>

<?php include 'footer.php'; ?>