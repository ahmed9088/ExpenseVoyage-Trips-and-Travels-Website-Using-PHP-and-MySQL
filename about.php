<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include 'admin/config.php';

$pageTitle = "About Us | Our Story and Team";
$currentPage = "about";
include 'header.php';
?>

    <!-- Simple Hero -->
    <header class="hero-editorial">
        <div class="hero-editorial-bg ken-burns" style="background-image: url('img/about.jpg');"></div>
        <div class="container hero-editorial-content reveal-up">
            <span class="text-gold text-uppercase tracking-widest fw-bold mb-4 d-block">Company Story</span>
            <h1 class="display-1 serif-font text-white mb-0">Our <span class="text-gold">Heritage</span></h1>
        </div>
    </header>

    <!-- Main Content -->
    <section class="section-padding bg-deep">
        <div class="container">
            <div class="row align-items-center mb-5 g-5">
                <div class="col-lg-6 reveal-up">
                    <div class="position-relative">
                        <img src="img/about-1.jpg" class="img-fluid shadow-extreme rounded-0" alt="About Side">
                        <div class="position-absolute border border-gold" style="inset: -20px; z-index: -1;"></div>
                    </div>
                </div>
                <div class="col-lg-6 ps-lg-5 reveal-up">
                    <h6 class="text-gold text-uppercase tracking-widest fw-bold mb-3">Who We Are</h6>
                    <h2 class="display-4 serif-font text-white mb-4">Making Travel <span class="text-gold">Easy</span></h2>
                    <p class="text-main lead mb-4" style="line-height: 2;">ExpenseVoyage was started to help people find and book luxury trips without any stress. We pick only the best destinations so you can enjoy your travel.</p>
                    <p class="text-muted" style="line-height: 1.8;">From beautiful beaches to calm mountains, we have something for everyone. Our team works hard to make sure your trip is safe and comfortable.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Our Team (Agents) -->
    <section class="section-padding bg-deep border-top border-subtle">
        <div class="container">
            <div class="text-center mb-5 reveal-up">
                <h6 class="text-gold text-uppercase tracking-widest fw-bold mb-3">Meet the Team</h6>
                <h2 class="display-4 serif-font text-white">Our Expert <span class="text-gold">Guides</span></h2>
            </div>
            
            <div class="row g-5">
                $agent_query = "SELECT * FROM agent ORDER BY id DESC LIMIT 3";
                $agent_result = mysqli_query($con, $agent_query);
                <?php
                if ($agent_result && mysqli_num_rows($agent_result) > 0):
                    while ($agent = mysqli_fetch_assoc($agent_result)):
                ?>
                    <div class="col-lg-4 reveal-up">
                        <div class="glass-card p-4 h-100 text-center border-ghost transition-all hover-border-gold">
                            <img src="img/<?php echo htmlspecialchars($agent['a_image']); ?>" class="rounded-pill mb-4 shadow-gold border border-gold p-2" style="width: 150px; height: 150px; object-fit: cover;" alt="Agent">
                            <h4 class="serif-font text-white mb-2"><?php echo htmlspecialchars($agent['a_name']); ?></h4>
                            <p class="text-gold text-uppercase tracking-widest small fw-bold mb-3"><?php echo htmlspecialchars($agent['a_profetion'] ?? 'Expert Guide'); ?></p>
                            <p class="text-muted small"><?php echo htmlspecialchars($agent['description'] ?? 'Ready to help you plan your next big adventure.'); ?></p>
                            <div class="d-flex justify-content-center gap-3 mt-4 text-ghost">
                                <a href="mailto:<?php echo $agent['email'] ?? ''; ?>" class="hover-gold"><i class="fas fa-envelope"></i></a>
                                <a href="tel:<?php echo $agent['phone'] ?? ''; ?>" class="hover-gold"><i class="fas fa-phone"></i></a>
                            </div>
                        </div>
                    </div>
                <?php 
                    endwhile; 
                endif; 
                ?>
            </div>
        </div>
    </section>

    <!-- Review Form -->
    <section class="section-padding bg-deep border-top border-subtle">
        <div class="container">
            <div class="row g-5 justify-content-center">
                <div class="col-lg-8">
                    <div class="glass-card p-5 border-0 shadow-extreme reveal-up" style="background: rgba(10, 10, 11, 0.95);">
                        <div class="text-center mb-5">
                            <h6 class="text-gold text-uppercase tracking-widest fw-bold mb-3 small">Share Your Story</h6>
                            <h2 class="display-5 serif-font text-white mb-4">Send Us a <span class="text-gold">Review</span></h2>
                        </div>
                        
                        <form action="review-handler.php" method="POST">
                            <div class="row g-4">
                                <div class="col-md-6">
                                    <label class="small text-gold text-uppercase tracking-widest fw-bold mb-2 d-block">Your Name</label>
                                    <input type="text" name="name" class="form-control bg-transparent border-0 border-bottom border-ghost py-3 text-white shadow-none" placeholder="Enter your name" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="small text-gold text-uppercase tracking-widest fw-bold mb-2 d-block">Your Email</label>
                                    <input type="email" name="email" class="form-control bg-transparent border-0 border-bottom border-ghost py-3 text-white shadow-none" placeholder="Enter your email" required>
                                </div>
                                <div class="col-12">
                                    <label class="small text-gold text-uppercase tracking-widest fw-bold mb-2 d-block">Your Message</label>
                                    <textarea name="review" rows="4" class="form-control bg-transparent border-0 border-bottom border-ghost py-3 text-white shadow-none" placeholder="Tell us about your trip..." required></textarea>
                                </div>
                                <div class="col-12 text-center mt-5">
                                    <button type="submit" class="btn-luxe btn-luxe-gold px-5 py-3">Submit Review</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>

<?php include 'footer.php'; ?>