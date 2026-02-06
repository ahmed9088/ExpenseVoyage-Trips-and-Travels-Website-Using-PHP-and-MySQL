<?php
$current_page = basename($_SERVER['PHP_SELF']);
?>
<aside class="modern-sidebar">
    <div class="sidebar-brand p-4">
        <h3 class="serif-font text-white mb-0">Agent<span class="text-indigo">Voyage</span></h3>
        <p class="text-muted small mb-0">Control Module</p>
    </div>

    <div class="sidebar-nav px-3">
        <div class="nav-section mb-4">
            <h6 class="nav-label text-muted text-uppercase fw-bold fs-xxs mb-3 px-3">Main Console</h6>
            <a href="dashboard.php" class="nav-link <?php echo $current_page == 'dashboard.php' ? 'active' : ''; ?>">
                <i class="fa-solid fa-house-chimney me-3"></i>Dashboard
            </a>
            <a href="trips.php" class="nav-link <?php echo $current_page == 'trips.php' ? 'active' : ''; ?>">
                <i class="fa-solid fa-map-location-dot me-3"></i>My Trips
            </a>
            <a href="bookings.php" class="nav-link <?php echo $current_page == 'bookings.php' ? 'active' : ''; ?>">
                <i class="fa-solid fa-calendar-check me-3"></i>My Bookings
            </a>
        </div>

        <div class="nav-section mb-4">
            <h6 class="nav-label text-muted text-uppercase fw-bold fs-xxs mb-3 px-3">Support & Settings</h6>
            <a href="profile.php" class="nav-link <?php echo $current_page == 'profile.php' ? 'active' : ''; ?>">
                <i class="fa-solid fa-user-gear me-3"></i>My Account
            </a>
            <a href="../contact.php" class="nav-link">
                <i class="fa-solid fa-headset me-3"></i>Support
            </a>
        </div>
    </div>

    <div class="sidebar-footer p-4 mt-auto">
        <a href="../logout.php" class="btn btn-outline-danger w-100 rounded-pill fs-xs">
            <i class="fa-solid fa-arrow-right-from-bracket me-2"></i>Terminate Session
        </a>
    </div>
</aside>
