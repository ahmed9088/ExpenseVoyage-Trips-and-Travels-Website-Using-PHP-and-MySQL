<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include 'admin/config.php';

// Sanitize inputs
$destination = isset($_GET['destination']) ? mysqli_real_escape_string($con, $_GET['destination']) : null;
$type = isset($_GET['type']) ? mysqli_real_escape_string($con, $_GET['type']) : 'all';

// Build Query
$query = "SELECT * FROM trips WHERE 1=1";
if ($destination) {
    $query .= " AND (destination LIKE '%$destination%' OR trip_name LIKE '%$destination%')";
}
if ($type !== 'all') {
    $query .= " AND travel_type = '$type'";
}
$trips_result = mysqli_query($con, $query);

// Fetch active wishlist if logged in
$user_wishlist = [];
if (isset($_SESSION['userid'])) {
    $uid = $_SESSION['userid'];
    $wRes = mysqli_query($con, "SELECT trip_id FROM wishlist WHERE user_id = $uid");
    while($wRow = mysqli_fetch_assoc($wRes)) {
        $user_wishlist[] = $wRow['trip_id'];
    }
}

$pageTitle = "Travel Packages | Book Your Journey";
$currentPage = "package";
include 'header.php';
?>

    <!-- Section: Hero -->
    <header class="hero-editorial glow-aura">
        <div id="hero-bg" class="hero-editorial-bg ken-burns" style="background-image: url('img/city.jpg');"></div>
        <div class="hero-overlay"></div>
        <div class="light-leak"></div>
        <div class="container hero-editorial-content reveal-up">
            <div class="hero-stamp mb-4">Voyage Collection</div>
            <span class="text-gold text-uppercase tracking-widest fw-bold mb-4 d-block hero-subtitle-luxe">Curated Journeys</span>
            <h1 class="display-1 serif-font text-white mb-0 hero-title-luxe">Our <span class="text-gold">Packages</span></h1>
        </div>
    </header>

    <!-- Section: Search Bar -->
    <section class="bg-deep pb-5">
        <div class="container mt-n5 position-relative z-3">
            <div class="glass-card p-4 p-lg-5 shadow-extreme border-0 reveal-up">
                <form action="package.php" method="GET" class="row g-4 align-items-end">
                    <div class="col-lg-5">
                        <label class="small text-gold text-uppercase tracking-widest fw-bold mb-3 d-block">Search Destination</label>
                        <div class="input-group border-bottom border-ghost px-2">
                            <span class="input-group-text bg-transparent border-0 ps-0 text-gold opacity-50"><i class="fas fa-map-pin"></i></span>
                            <input type="text" name="destination" class="form-control bg-transparent border-0 text-white py-3 ps-3 shadow-none luxury-input" placeholder="e.g. Maldives, Paris..." value="<?php echo htmlspecialchars($destination ?? ''); ?>">
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <label class="small text-gold text-uppercase tracking-widest fw-bold mb-3 d-block">Select Category</label>
                        <div class="position-relative">
                            <select name="type" class="form-select bg-transparent border-0 border-bottom border-ghost text-white py-3 shadow-none luxury-input custom-select-luxe">
                                <option value="all" <?php echo $type == 'all' ? 'selected' : ''; ?> class="bg-deep">Global Portfolio</option>
                                <option value="local" <?php echo $type == 'local' ? 'selected' : ''; ?> class="bg-deep">Local Sanctuary</option>
                                <option value="international" <?php echo $type == 'international' ? 'selected' : ''; ?> class="bg-deep">Overseas Expeditions</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <button type="submit" class="btn-luxe btn-luxe-gold w-100 py-3">
                            <span class="me-2">Explore Now</span>
                            <i class="fas fa-arrow-right small"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </section>

    <!-- Packages List -->
    <section class="section-padding bg-deep glow-aura">
        <div class="container">
            <!-- Quick Chips -->
            <div class="filter-chips justify-content-center reveal-up">
                <a href="package.php?type=all" class="chip <?php echo $type == 'all' ? 'active' : ''; ?>">
                    <i class="fas fa-th-large me-2 small"></i>All Collections
                </a>
                <a href="package.php?type=local" class="chip <?php echo $type == 'local' ? 'active' : ''; ?>">
                    <i class="fas fa-map-marked-alt me-2 small"></i>Local Gems
                </a>
                <a href="package.php?type=international" class="chip <?php echo $type == 'international' ? 'active' : ''; ?>">
                    <i class="fas fa-globe-americas me-2 small"></i>Global Reach
                </a>
            </div>

            <div class="row g-5">
                <?php if (mysqli_num_rows($trips_result) > 0): ?>
                    <?php while($trip = mysqli_fetch_assoc($trips_result)): ?>
                        <div class="col-lg-4 col-md-6 reveal-up">
                            <div class="trip-card h-100 border-0 bg-surface rounded-0 overflow-hidden shadow-soft group">
                                <!-- Card Image Header -->
                                <div class="card-img-container">
                                    <div class="pulsar-badge">
                                        <div class="pulsar-dot pulse-gold"></div>
                                        <span class="text-white small fw-bold"><?php echo rand(2, 9); ?> ELITE VIEWERS</span>
                                    </div>
                                    <button class="wishlist-btn <?php echo in_array($trip['trip_id'], $user_wishlist) ? 'active' : ''; ?>" 
                                            onclick="toggleWishlist(this, <?php echo $trip['trip_id']; ?>)">
                                        <i class="fas fa-heart"></i>
                                    </button>
                                    <div class="rough-edges w-100 h-100 group-hover:scale-110 transition-transform duration-1000">
                                        <img src="<?php echo htmlspecialchars($trip['trip_image']); ?>" class="w-100 h-100 object-fit-cover" alt="Trip Image">
                                    </div>
                                    <div class="trip-overlay"></div>
                                    <div class="price-tag-luxe">
                                        <span class="small opacity-50 block mb-n1">Starting from</span>
                                        <span class="h4 serif-font mb-0">$<?php echo number_format($trip['budget']); ?></span>
                                    </div>
                                </div>
                                <!-- Card Body -->
                                <div class="p-4 pt-5 text-center position-relative mt-n5 z-3 card-body-luxe">
                                    <div class="d-flex justify-content-center gap-3 mb-3">
                                        <span class="text-gold text-uppercase tracking-widest small fw-bold"><i class="far fa-calendar-alt me-2"></i> <?php echo $trip['duration_days']; ?> DAYS</span>
                                        <span class="text-gold opacity-30">|</span>
                                        <span class="text-gold text-uppercase tracking-widest small fw-bold"><i class="far fa-compass me-2"></i> <?php echo strtoupper($trip['travel_type']); ?></span>
                                    </div>
                                    <h3 class="serif-font h4 mb-4 text-white trip-card-title"><?php echo htmlspecialchars($trip['trip_name']); ?></h3>
                                    <div class="card-action-reveal">
                                        <a href="trip_details.php?id=<?php echo $trip['trip_id']; ?>" class="btn-luxe btn-luxe-outline w-100">
                                            View Narrative
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div class="col-12 text-center py-5">
                        <div class="mb-5 text-gold opacity-10"><i class="fas fa-map-marked-alt fa-10x"></i></div>
                        <h2 class="serif-font text-white display-5">No trips found.</h2>
                        <p class="text-muted lead mb-5">Try searching for something else or browse all our packages.</p>
                        <a href="package.php" class="btn-luxe btn-luxe-gold px-5">View All Trips</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>


<style>
    /* Package Page Specific styles */
    .hero-subtitle-luxe { letter-spacing: 0.5em; font-size: 0.75rem; }
    .hero-title-luxe { letter-spacing: -0.05em; line-height: 0.95; }

    .luxury-input { transition: all 0.4s var(--ease-boutique); opacity: 0.7; }
    .luxury-input:focus { opacity: 1; border-color: var(--gold-primary) !important; padding-left: 1rem !important; }

    .filter-chips { display: flex; gap: 1.5rem; margin-bottom: 5rem; flex-wrap: wrap; }
    .chip { 
        padding: 0.75rem 2rem; border: 1px solid var(--border-subtle); border-radius: var(--radius-full); 
        color: var(--text-contrast); text-transform: uppercase; font-size: 0.7rem; 
        letter-spacing: 0.15em; font-weight: 600; transition: var(--transition); 
        text-decoration: none; background: var(--bg-surface); 
    }
    .chip:hover, .chip.active { 
        background: var(--gold-primary); color: var(--bg-base); 
        border-color: var(--gold-primary); transform: translateY(-3px); box-shadow: var(--shadow-gold); 
    }

    /* Trip Card Revolution 3.0 */
    .card-img-container { height: 480px; overflow: hidden; position: relative; }
    .pulsar-badge span { font-size: 0.6rem; letter-spacing: 0.15em; text-transform: uppercase; }
    .trip-card-title { letter-spacing: -0.02em; }

    .price-tag-luxe { 
        position: absolute; bottom: 2rem; left: 2rem; background: var(--gold-primary); 
        color: var(--bg-base); padding: 0.75rem 1.5rem; border-radius: var(--radius-sm); 
        z-index: 4; transform: translateY(100%); opacity: 0; 
        transition: all 0.6s var(--ease-boutique); box-shadow: var(--shadow-gold); 
    }
    .trip-card:hover .price-tag-luxe { transform: translateY(0); opacity: 1; }

    .card-body-luxe { background: var(--bg-surface); transition: all 0.5s var(--ease-boutique); }
    .trip-card:hover .card-body-luxe { transform: translateY(-0.5rem); background: var(--bg-surface-subtle); }

    .card-action-reveal { max-height: 0; opacity: 0; overflow: hidden; transition: all 0.6s var(--ease-boutique); }
    .trip-card:hover .card-action-reveal { max-height: 100px; opacity: 1; margin-top: 1.5rem; }

    .wishlist-btn { 
        position: absolute; top: 1.5rem; right: 1.5rem; width: 40px; height: 40px; 
        border-radius: 50%; background: var(--bg-glass); backdrop-filter: blur(10px); 
        border: 1px solid var(--border-glass); color: var(--text-contrast); 
        display: flex; align-items: center; justify-content: center; z-index: 5; transition: var(--transition); 
    }
    .wishlist-btn:hover { background: var(--gold-primary); color: var(--bg-base); transform: scale(1.1); }
    .wishlist-btn.active { color: #ef4444; background: white; }

    .pulsar-dot.pulse-gold { background: var(--gold-primary); box-shadow: 0 0 0 0 rgba(212, 175, 55, 0.7); animation: pulse-gold 2s infinite; }
    @keyframes pulse-gold {
        0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(212, 175, 55, 0.7); }
        70% { transform: scale(1); box-shadow: 0 0 0 10px rgba(212, 175, 55, 0); }
        100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(212, 175, 55, 0); }
    }
    .duration-1000 { transition-duration: 1000ms; }
</style>

<?php include 'footer.php'; ?>


<script>
    function toggleWishlist(btn, tripId) {
        <?php if (!isset($_SESSION['userid'])): ?>
            window.location.href = 'login/account.php';
            return;
        <?php endif; ?>

        $.ajax({
            url: 'wishlist-handler.php',
            method: 'POST',
            data: { trip_id: tripId, action: 'toggle' },
            dataType: 'json',
            success: function(response) {
                if (response.status === 'added') {
                    $(btn).addClass('active');
                    $(btn).css('color', '#ef4444');
                } else if (response.status === 'removed') {
                    $(btn).removeClass('active');
                    $(btn).css('color', 'white');
                }
            }
        });
    }
</script>