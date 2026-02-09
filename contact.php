<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include 'admin/config.php';
include 'csrf.php';

$pageTitle = "Contact Us | Get in Touch";
$currentPage = "contact";
include 'header.php';
?>

    <!-- Simple Hero -->
    <header class="hero-editorial">
        <div class="hero-editorial-bg ken-burns" style="background-image: url('img/beach.jpg');"></div>
        <div class="container hero-editorial-content reveal-up">
            <span class="text-gold text-uppercase tracking-widest fw-bold mb-4 d-block">Get Help</span>
            <h1 class="display-1 serif-font text-white mb-0">Our <span class="text-gold">Contact</span></h1>
        </div>
    </header>

    <!-- Main Content -->
    <section class="section-padding bg-deep">
        <div class="container mt-n5 position-relative z-2">
            <div class="row g-5">
                <!-- Contact Info -->
                <div class="col-lg-4">
                    <div class="glass-card p-5 mb-4 border-ghost reveal-up transition-all hover-border-gold shadow-soft">
                        <div class="text-gold fs-1 mb-4"><i class="fas fa-map-marker-alt"></i></div>
                        <h4 class="serif-font text-white mb-2">Our Office</h4>
                        <p class="text-muted small mb-0">123 Street Address, <br>Dubai, United Arab Emirates</p>
                    </div>
                    
                    <div class="glass-card p-5 mb-4 border-ghost reveal-up transition-all hover-border-gold shadow-soft" style="transition-delay: 0.1s;">
                        <div class="text-gold fs-1 mb-4"><i class="fas fa-envelope"></i></div>
                        <h4 class="serif-font text-white mb-2">Email Us</h4>
                        <p class="text-muted small mb-0">support@expensevoyage.com<br>info@expensevoyage.com</p>
                    </div>
                    
                    <div class="glass-card p-5 border-ghost reveal-up transition-all hover-border-gold shadow-soft" style="transition-delay: 0.2s;">
                        <div class="text-gold fs-1 mb-4"><i class="fas fa-phone"></i></div>
                        <h4 class="serif-font text-white mb-2">Call Us</h4>
                        <p class="text-muted small mb-0">+971 123 4567<br>+971 765 4321</p>
                    </div>
                </div>

                <!-- Contact Form -->
                <div class="col-lg-8">
                    <div class="glass-card p-5 h-100 border-0 shadow-extreme reveal-up" style="background: rgba(10, 10, 11, 0.95);">
                        <div class="mb-5">
                            <h6 class="text-gold text-uppercase tracking-widest fw-bold mb-3 small">Send a Message</h6>
                            <h2 class="display-5 serif-font text-white mb-4">How can we <span class="text-gold">help?</span></h2>
                            <p class="text-muted">Fill out the form below and our team will get back to you soon.</p>
                        </div>
                        
                        <form action="contact-handler.php" method="POST">
                            <?php echo csrf_input(); ?>
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
                                    <label class="small text-gold text-uppercase tracking-widest fw-bold mb-2 d-block">Subject</label>
                                    <input type="text" name="subject" class="form-control bg-transparent border-0 border-bottom border-ghost py-3 text-white shadow-none" placeholder="What is this about?" required>
                                </div>
                                <div class="col-12">
                                    <label class="small text-gold text-uppercase tracking-widest fw-bold mb-2 d-block">Your Message</label>
                                    <textarea name="message" rows="5" class="form-control bg-transparent border-0 border-bottom border-ghost py-3 text-white shadow-none" placeholder="Write your message here..." required></textarea>
                                </div>
                                <div class="col-12 mt-5">
                                    <button type="submit" class="btn-luxe btn-luxe-gold px-5 py-3">Send Message</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            
            <!-- Simple Map -->
            <div class="mt-5 reveal-up">
                <div class="glass-card p-3 border-0 shadow-extreme">
                    <iframe class="w-100 rounded-3 grayscale" src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3609.4!2d55.27!3d25.2!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x0!2zMjXCsDEyJzAwLjAiTiA1NcKwMTYnMTIuMCJF!5e0!3m2!1sen!2sae!4v1620000000000" height="450" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
                </div>
            </div>
        </div>
    </section>

<?php include 'footer.php'; ?>

<style>
    .grayscale { filter: grayscale(1) invert(0.1); opacity: 0.8; transition: all 0.5s ease; }
    .grayscale:hover { filter: grayscale(0) invert(0); opacity: 1; }
</style>