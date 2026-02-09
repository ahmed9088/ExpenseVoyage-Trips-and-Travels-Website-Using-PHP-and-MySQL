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

    <!-- Simple Hero -->
    <header class="hero-editorial">
        <div class="hero-editorial-bg ken-burns" style="background-image: url('img/city.jpg');"></div>
        <div class="container hero-editorial-content reveal-up">
            <span class="text-gold text-uppercase tracking-widest fw-bold mb-4 d-block">Available Trips</span>
            <h1 class="display-1 serif-font text-white mb-0">Our <span class="text-gold">Packages</span></h1>
        </div>
    </header>

    <!-- Simple Search Bar -->
    <section class="bg-deep py-5">
        <div class="container mt-n5 position-relative z-2">
            <div class="glass-card p-4 p-lg-5 shadow-gold border-0" style="background: rgba(10, 10, 11, 0.85);">
                <form action="package.php" method="GET" class="row g-4 align-items-end">
                    <div class="col-lg-5">
                        <label class="small text-gold text-uppercase tracking-widest fw-bold mb-3 d-block">Where to?</label>
                        <div class="input-group border-bottom border-ghost">
                            <span class="input-group-text bg-transparent border-0 ps-0 text-muted"><i class="fas fa-search"></i></span>
                            <input type="text" name="destination" class="form-control bg-transparent border-0 text-white py-3 ps-0 shadow-none" placeholder="Search for a place" value="<?php echo htmlspecialchars($destination ?? ''); ?>">
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <label class="small text-gold text-uppercase tracking-widest fw-bold mb-3 d-block">Trip Type</label>
                        <select name="type" class="form-select bg-transparent border-0 border-bottom border-ghost text-white py-3 shadow-none custom-select-luxe">
                            <option value="all" <?php echo $type == 'all' ? 'selected' : ''; ?> class="bg-deep">All Types</option>
                            <option value="local" <?php echo $type == 'local' ? 'selected' : ''; ?> class="bg-deep">Local Trips</option>
                            <option value="international" <?php echo $type == 'international' ? 'selected' : ''; ?> class="bg-deep">International Trips</option>
                        </select>
                    </div>
                    <div class="col-lg-3">
                        <button type="submit" class="btn-luxe btn-luxe-gold w-100 py-3">Search Now</button>
                    </div>
                </form>
            </div>
        </div>
    </section>

    <!-- Packages List -->
    <section class="section-padding bg-deep">
        <div class="container">
            <!-- Quick Chips -->
            <div class="filter-chips justify-content-center reveal-up">
                <a href="package.php?type=all" class="chip <?php echo $type == 'all' ? 'active' : ''; ?>">All Collections</a>
                <a href="package.php?type=local" class="chip <?php echo $type == 'local' ? 'active' : ''; ?>">Local Gems</a>
                <a href="package.php?type=international" class="chip <?php echo $type == 'international' ? 'active' : ''; ?>">Global Reach</a>
            </div>

            <div class="row g-5">
                <?php if (mysqli_num_rows($trips_result) > 0): ?>
                    <?php while($trip = mysqli_fetch_assoc($trips_result)): ?>
                        <div class="col-lg-4 col-md-6 reveal-up">
                            <div class="trip-card h-100 border-0 bg-surface rounded-0 overflow-hidden shadow-soft group">
                                <div class="position-relative card-img-container" style="height: 480px;">
                                    <div class="pulsar-badge">
                                        <div class="pulsar-dot"></div>
                                        <span class="text-white small fw-bold" style="font-size: 0.6rem;"><?php echo rand(2, 9); ?> Active Viewers</span>
                                    </div>
                                    <button class="wishlist-btn <?php echo in_array($trip['trip_id'], $user_wishlist) ? 'active' : ''; ?>" 
                                            onclick="toggleWishlist(this, <?php echo $trip['trip_id']; ?>)"
                                            style="background: rgba(0,0,0,0.5); backdrop-filter: blur(10px); color: white;">
                                        <i class="fas fa-heart"></i>
                                    </button>
                                    <img src="<?php echo htmlspecialchars($trip['trip_image']); ?>" class="w-100 h-100 object-fit-cover transition-all" alt="Trip Image">
                                    <div class="trip-overlay"></div>
                                    <div class="price-tag-reveal" style="background: var(--primary); color: #000; font-weight: bold;">$<?php echo number_format($trip['budget']); ?></div>
                                </div>
                                <div class="p-4 pt-5 text-center position-relative mt-n5 z-3">
                                    <div class="d-flex justify-content-center gap-3 mb-3">
                                        <span class="text-gold text-uppercase tracking-widest small fw-bold"><i class="far fa-calendar me-2"></i> <?php echo $trip['duration_days']; ?> Days</span>
                                        <span class="text-gold text-uppercase tracking-widest small fw-bold">|</span>
                                        <span class="text-gold text-uppercase tracking-widest small fw-bold"><i class="far fa-user me-2"></i> <?php echo $trip['persons']; ?> Persons</span>
                                    </div>
                                    <h3 class="serif-font h4 mb-4 text-white"><?php echo htmlspecialchars($trip['trip_name']); ?></h3>
                                    <a href="trip_details.php?id=<?php echo $trip['trip_id']; ?>" class="btn-luxe btn-luxe-outline py-2 px-5 opacity-0 group-hover-opacity-100 transition-all">Details</a>
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

<?php include 'footer.php'; ?>

<style>
    .group:hover .group-hover-opacity-100 { opacity: 1 !important; transform: translateY(-5px); }
</style>

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