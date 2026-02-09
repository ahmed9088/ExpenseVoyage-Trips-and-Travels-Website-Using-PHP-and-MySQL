<!-- Footer -->
    <footer class="section-padding bg-deep border-top border-subtle">
        <div class="container">
            <div class="row g-5">
                <div class="col-lg-5">
                    <h2 class="serif-font text-white mb-4">Expense<span class="text-gold">Voyage</span></h2>
                    <p class="text-muted lead mb-5" style="max-width: 400px;">We help you find and book the best luxury trips around the world easily and safely.</p>
                    
                    <div class="d-flex gap-4">
                        <a href="#" class="text-white opacity-50 hover-opacity-100 transition-all fs-4"><i class="fab fa-instagram"></i></a>
                        <a href="#" class="text-white opacity-50 hover-opacity-100 transition-all fs-4"><i class="fab fa-facebook"></i></a>
                        <a href="#" class="text-white opacity-50 hover-opacity-100 transition-all fs-4"><i class="fab fa-twitter"></i></a>
                    </div>
                </div>
                
                <div class="col-lg-2 ms-auto">
                    <h6 class="text-gold text-uppercase tracking-widest fw-bold mb-4 small">Links</h6>
                    <ul class="list-unstyled">
                        <li class="mb-3"><a href="index.php" class="text-muted text-decoration-none hover-white transition-all">Home</a></li>
                        <li class="mb-3"><a href="package.php" class="text-muted text-decoration-none hover-white transition-all">Packages</a></li>
                        <li class="mb-3"><a href="about.php" class="text-muted text-decoration-none hover-white transition-all">About Us</a></li>
                        <li class="mb-3"><a href="contact.php" class="text-muted text-decoration-none hover-white transition-all">Contact</a></li>
                    </ul>
                </div>
                
                <div class="col-lg-4">
                    <h6 class="text-gold text-uppercase tracking-widest fw-bold mb-4 small">Stay Updated</h6>
                    <p class="text-muted small mb-4">Subscribe to get the latest travel deals in your inbox.</p>
                    <form class="d-flex gap-0 border-bottom border-ghost pb-2 mt-4">
                        <input type="email" class="form-control bg-transparent border-0 text-white shadow-none ps-0" placeholder="Your Email">
                        <button type="submit" class="btn btn-link text-gold p-0 text-decoration-none fw-bold">Join</button>
                    </form>
                </div>
            </div>
            
            <div class="mt-5 pt-5 d-flex flex-column flex-md-row justify-content-between align-items-center gap-4 text-ghost small border-top border-subtle">
                <p class="mb-0">&copy; <?php echo date('Y'); ?> ExpenseVoyage. All rights reserved.</p>
                <div class="d-flex gap-4">
                    <a href="#" class="text-ghost text-decoration-none hover-white">Privacy Policy</a>
                    <a href="#" class="text-ghost text-decoration-none hover-white">Terms of Use</a>
                </div>
            </div>
        </div>
    </footer>

    <!-- Scroll to Top Progress -->
    <div class="scroll-to-top">
        <svg class="progress-ring" width="50" height="50">
            <circle class="progress-ring__circle" stroke-width="2" fill="transparent" r="23" cx="25" cy="25"/>
        </svg>
        <i class="fas fa-arrow-up text-gold"></i>
    </div>

    <!-- Chatbot & Scripts -->
    <?php include 'chatbot-loader.php'; ?>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/custom.js"></script>
</body>
</html>
