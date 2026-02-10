<?php
session_start();
include 'admin/config.php';
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Check if the user is logged in
if (!isset($_SESSION['email'])) {
    $_SESSION['redirect_after_login'] = 'destination.php';
    header("Location: login/account.php");
    exit();
}

// Fetch destination values for search
$query = "SELECT DISTINCT destination FROM trips";
$destinationResult = $con->query($query);

// Fetch review data for testimonials
$reviewQuery = "SELECT * FROM review ORDER BY id DESC LIMIT 3";
$reviewResult = mysqli_query($con, $reviewQuery);

$pageTitle = "Destinations | ExpenseVoyage";
$currentPage = "destination";
include 'header.php';
?>

    <!-- Page Header -->
    <header class="hero-editorial" style="height: 50vh;">
        <div class="hero-editorial-bg ken-burns" style="background-image: url('img/destination-header.jpg');"></div>
        <div class="container hero-editorial-content reveal-up">
            <span class="text-gold text-uppercase tracking-widest fw-bold mb-4 d-block">Curated Explorations</span>
            <h1 class="display-1 serif-font text-white mb-0">Global <span class="text-gold">Destinations</span></h1>
        </div>
    </header>

    <!-- Search Section -->
    <section class="py-5 bg-surface border-bottom border-ghost">
        <div class="container">
            <div class="glass-card p-4 reveal-up">
                <form action="package.php" method="get">
                    <div class="row g-3 align-items-center">
                        <div class="col-md-9">
                            <div class="position-relative">
                                <i class="fas fa-search position-absolute top-50 start-0 translate-middle-y ms-3 text-gold"></i>
                                <input type="text" class="form-control bg-transparent border-0 border-bottom border-ghost ps-5 py-3 text-white shadow-none" 
                                       placeholder="Where do you wish to go?" name="destination" list="destinationsList">
                                <datalist id="destinationsList">
                                    <?php
                                    if ($destinationResult) {
                                        while ($row = $destinationResult->fetch_assoc()) {
                                            echo '<option value="' . htmlspecialchars($row['destination']) . '">';
                                        }
                                    }
                                    ?>
                                </datalist>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <button class="btn-luxe btn-luxe-gold w-100 py-3" type="submit">Begin Search</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </section>

    <!-- Destinations Grid -->
    <section class="section-padding bg-deep glow-aura">
        <div class="container text-center mb-5 reveal-up">
            <span class="text-gold text-uppercase tracking-widest small fw-bold mb-3 d-block">The Portfolio</span>
            <h2 class="display-3 serif-font text-white">Elite <span class="text-gold">Locations</span></h2>
        </div>
        
        <div class="container">
            <!-- Boutique Filter -->
            <div class="d-flex justify-content-center flex-wrap gap-3 mb-5 reveal-up">
                <button class="btn-luxe btn-luxe-outline active x-small filter-btn" data-filter="all">ALL</button>
                <button class="btn-luxe btn-luxe-outline x-small filter-btn" data-filter="europe">EUROPE</button>
                <button class="btn-luxe btn-luxe-outline x-small filter-btn" data-filter="asia">ASIA</button>
                <button class="btn-luxe btn-luxe-outline x-small filter-btn" data-filter="america">AMERICA</button>
                <button class="btn-luxe btn-luxe-outline x-small filter-btn" data-filter="africa">AFRICA</button>
                <button class="btn-luxe btn-luxe-outline x-small filter-btn" data-filter="oceania">OCEANIA</button>
            </div>

            <div class="row g-4 destination-container">
                <!-- Static Featured Items -->
                <div class="col-lg-4 col-md-6 reveal-up" data-category="europe">
                    <div class="glass-card overflow-hidden h-100 group">
                        <div class="rough-edges">
                            <img src="img/destination-2.jpg" class="w-100 transition-all group-hover:scale-110" style="height: 400px; object-fit: cover;" alt="UK">
                        </div>
                        <div class="p-4 text-center">
                            <h3 class="serif-font text-white mb-1">United Kingdom</h3>
                            <span class="text-gold x-small text-uppercase tracking-widest">Heritage & Elegance</span>
                        </div>
                        <a href="package.php?destination=United Kingdom" class="stretched-link"></a>
                    </div>
                </div>

                <div class="col-lg-4 col-md-6 reveal-up" data-category="oceania" style="transition-delay: 0.1s;">
                    <div class="glass-card overflow-hidden h-100 group">
                        <div class="rough-edges">
                            <img src="img/destination-3.jpg" class="w-100 transition-all group-hover:scale-110" style="height: 400px; object-fit: cover;" alt="Australia">
                        </div>
                        <div class="p-4 text-center">
                            <h3 class="serif-font text-white mb-1">Australia</h3>
                            <span class="text-gold x-small text-uppercase tracking-widest">Modern Wilderness</span>
                        </div>
                        <a href="package.php?destination=Australia" class="stretched-link"></a>
                    </div>
                </div>

                <!-- Dynamic Items -->
                <?php
                $cityQuery = "SELECT country_name, city_name, cover_image FROM city";
                $cityResult = $con->query($cityQuery);
                if ($cityResult->num_rows > 0):
                    while ($city = $cityResult->fetch_assoc()):
                        // Same category logic as before or simplified
                        $category = 'all'; 
                        $countryLower = strtolower($city['country_name']);
                        if (strpos($countryLower, 'europe') !== false || in_array($city['country_name'], ['United Kingdom', 'France', 'Germany', 'Italy', 'Spain'])) $category = 'europe';
                        elseif (strpos($countryLower, 'asia') !== false || in_array($city['country_name'], ['China', 'Japan', 'India', 'Thailand', 'Singapore'])) $category = 'asia';
                        elseif (strpos($countryLower, 'america') !== false || in_array($city['country_name'], ['United States', 'Canada', 'Mexico', 'Brazil', 'Argentina'])) $category = 'america';
                        elseif (strpos($countryLower, 'africa') !== false || in_array($city['country_name'], ['South Africa', 'Egypt', 'Kenya', 'Morocco', 'Tanzania'])) $category = 'africa';
                        elseif (strpos($countryLower, 'australia') !== false || in_array($city['country_name'], ['Australia', 'New Zealand', 'Fiji'])) $category = 'oceania';
                ?>
                    <div class="col-lg-4 col-md-6 reveal-up" data-category="<?php echo $category; ?>">
                        <div class="glass-card overflow-hidden h-100 group">
                            <div class="rough-edges">
                                <img src="<?php echo htmlspecialchars($city['cover_image']); ?>" class="w-100 transition-all group-hover:scale-110" style="height: 400px; object-fit: cover;" alt="City">
                            </div>
                            <div class="p-4 text-center">
                                <h3 class="serif-font text-white mb-1"><?php echo htmlspecialchars($city['country_name']); ?></h3>
                                <span class="text-gold x-small text-uppercase tracking-widest"><?php echo htmlspecialchars($city['city_name']); ?></span>
                            </div>
                            <a href="package.php?destination=<?php echo urlencode($city['country_name']); ?>" class="stretched-link"></a>
                        </div>
                    </div>
                <?php endwhile; endif; ?>
            </div>
        </div>
    </section>

    <!-- Script for Filter -->
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const filterBtns = document.querySelectorAll('.filter-btn');
        const items = document.querySelectorAll('.destination-container > div');

        filterBtns.forEach(btn => {
            btn.addEventListener('click', () => {
                const filter = btn.getAttribute('data-filter');
                
                filterBtns.forEach(b => b.classList.remove('active'));
                btn.classList.add('active');

                items.forEach(item => {
                    if (filter === 'all' || item.getAttribute('data-category') === filter) {
                        item.style.display = 'block';
                    } else {
                        item.style.display = 'none';
                    }
                });
            });
        });
    });
    </script>

<?php include 'footer.php'; ?>