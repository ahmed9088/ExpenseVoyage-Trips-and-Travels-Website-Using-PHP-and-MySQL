<?php
include 'chatbot-loader.php'; 
session_start();
include 'admin/config.php';
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Check if the user is logged in
if (!isset($_SESSION['email'])) {
    $_SESSION['redirect_after_login'] = 'blog.php';
    header("Location: login/account.php");
    exit();
}

// Set up pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$perPage = 6;
$offset = ($page - 1) * $perPage;

// Get total number of blogs for pagination
$countQuery = "SELECT COUNT(*) as total FROM blog";
$countResult = $con->query($countQuery);
$totalBlogs = $countResult->fetch_assoc()['total'];
$totalPages = ceil($totalBlogs / $perPage);

// Fetch blogs for current page
$blogQuery = "SELECT * FROM blog ORDER BY date_time DESC LIMIT $perPage OFFSET $offset";
$blogResult = mysqli_query($con, $blogQuery);

$pageTitle = "Insights & Stories | ExpenseVoyage";
$currentPage = "blog";
include 'header.php';
?>

    <!-- Page Header -->
    <header class="hero-editorial" style="height: 50vh;">
        <div class="hero-editorial-bg ken-burns" style="background-image: url('img/blog-header.jpg');"></div>
        <div class="container hero-editorial-content reveal-up">
            <span class="text-gold text-uppercase tracking-widest fw-bold mb-4 d-block">The Voyage Journal</span>
            <h1 class="display-1 serif-font text-white mb-0">Insights & <span class="text-gold">Stories</span></h1>
        </div>
    </header>

    <!-- Blog Section -->
    <section class="section-padding bg-deep glow-aura">
        <div class="container">
            <div class="row g-5">
                <!-- Main Content -->
                <div class="col-lg-8">
                    <div class="row g-4">
                        <?php if ($blogResult && mysqli_num_rows($blogResult) > 0): ?>
                            <?php while ($blog = mysqli_fetch_assoc($blogResult)): ?>
                                <div class="col-md-6 reveal-up">
                                    <div class="glass-card h-100 group">
                                        <div class="position-relative overflow-hidden rough-edges" style="height: 250px;">
                                            <img src="img/<?php echo htmlspecialchars($blog['b_image']); ?>" 
                                                 class="w-100 h-100 transition-all group-hover:scale-110" 
                                                 style="object-fit: cover;" alt="Blog">
                                            <div class="position-absolute top-0 start-0 m-3 p-2 bg-glass border border-gold rounded text-center" style="min-width: 60px;">
                                                <div class="text-gold fw-bold h4 mb-0"><?php echo date('d', strtotime($blog['date_time'])); ?></div>
                                                <div class="text-white x-small text-uppercase tracking-widest"><?php echo date('M', strtotime($blog['date_time'])); ?></div>
                                            </div>
                                        </div>
                                        <div class="p-5">
                                            <span class="text-gold x-small text-uppercase tracking-widest mb-2 d-block">Travel Chronicles</span>
                                            <h3 class="h4 serif-font text-white mb-4"><?php echo htmlspecialchars($blog['b_name']); ?></h3>
                                            <p class="text-muted small mb-4"><?php echo substr(strip_tags($blog['b_description']), 0, 120); ?>...</p>
                                            <a href="blog-details.php?id=<?php echo $blog['blog_id']; ?>" class="btn-luxe btn-luxe-outline x-small">Read Narrative</a>
                                        </div>
                                    </div>
                                </div>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <div class="col-12 py-5 text-center opacity-20">No stories discovered yet.</div>
                        <?php endif; ?>
                    </div>

                    <!-- Pagination -->
                    <?php if ($totalPages > 1): ?>
                    <nav class="mt-5 pt-5 border-top border-ghost reveal-up">
                        <ul class="pagination justify-content-center gap-2">
                            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                <li class="page-item <?php echo $i == $page ? 'active' : ''; ?>">
                                    <a class="page-link bg-glass border-ghost text-gold rounded-pill px-4 py-2" href="?page=<?php echo $i; ?>">
                                        <?php echo $i; ?>
                                    </a>
                                </li>
                            <?php endfor; ?>
                        </ul>
                    </nav>
                    <?php endif; ?>
                </div>

                <!-- Sidebar -->
                <div class="col-lg-4">
                    <div class="sticky-top" style="top: 120px;">
                        <!-- Search -->
                        <div class="glass-card p-4 mb-4 reveal-up">
                            <h4 class="serif-font text-white h5 mb-4 px-2 border-start border-gold">Search Journal</h4>
                            <div class="position-relative">
                                <input type="text" class="form-control bg-transparent border-0 border-bottom border-ghost text-white shadow-none py-3" 
                                       placeholder="Keywords...">
                                <button class="btn p-0 position-absolute top-50 end-0 translate-middle-y text-gold"><i class="fas fa-search"></i></button>
                            </div>
                        </div>

                        <!-- Newsletter -->
                        <div class="glass-card p-5 reveal-up" style="transition-delay: 0.1s;">
                            <h4 class="serif-font text-white h5 mb-3 px-2 border-start border-gold">The Insider</h4>
                            <p class="text-muted small mb-4">Receive curated travel narratives directly in your inbox.</p>
                            <form class="email-wrapper">
                                <input type="email" class="form-control bg-black border-ghost text-white rounded-pill mb-3" placeholder="Your Email">
                                <button type="submit" class="btn-luxe btn-luxe-gold w-100 py-3">Subscribe</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

<?php include 'footer.php'; ?>