<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once("config.php");

if(!isset($_SESSION['auser']))
{
	header("location:index.php");
    exit();
}
?>

<!-- Modern CSS Integration -->
<link rel="stylesheet" href="assets/css/admin_modern.css?v=<?php echo time(); ?>">
<!-- Font Awesome 6 for High-Fidelity Icons -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<!-- Modern Sidebar -->
<aside class="modern-sidebar" id="modernSidebar">
    <div class="sidebar-brand">
        <i class="fa-solid fa-compass-drafting text-indigo" style="font-size: 1.8rem;"></i>
        <span class="brand-text">Expense<span class="text-indigo">Voyage</span></span>
    </div>

    <nav class="sidebar-nav">
        <div class="nav-label">Main Menu</div>
        <a href="dashboard.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : ''; ?>">
            <i class="fa-solid fa-chart-line"></i>
            <span>Dashboard</span>
        </a>
        <a href="analytics.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'analytics.php' ? 'active' : ''; ?>">
            <i class="fa-solid fa-brain"></i>
            <span>Analytics</span>
        </a>
        <a href="bookingview.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'bookingview.php' ? 'active' : ''; ?>">
            <i class="fa-solid fa-receipt"></i>
            <span>Bookings</span>
        </a>

        <div class="nav-label">Trips & Content</div>
        <a href="tripview.php" class="nav-link <?php echo in_array(basename($_SERVER['PHP_SELF']), ['tripview.php', 'tripadd.php', 'tripedit.php']) ? 'active' : ''; ?>">
            <i class="fa-solid fa-map-location-dot"></i>
            <span>Trips</span>
        </a>
        <a href="cityadd.php" class="nav-link <?php echo in_array(basename($_SERVER['PHP_SELF']), ['cityadd.php', 'cityedit.php']) ? 'active' : ''; ?>">
            <i class="fa-solid fa-city"></i>
            <span>Cities</span>
        </a>
        <a href="addblog.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'addblog.php' ? 'active' : ''; ?>">
            <i class="fa-solid fa-pen-nib"></i>
            <span>Voyage Blogs</span>
        </a>

        <div class="nav-label">Users & Agents</div>
        <a href="userlist.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'userlist.php' ? 'active' : ''; ?>">
            <i class="fa-solid fa-users-gear"></i>
            <span>Travelers</span>
        </a>
        <a href="addagent.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'addagent.php' ? 'active' : ''; ?>">
            <i class="fa-solid fa-user-tie"></i>
            <span>Agent Portal</span>
        </a>
        <a href="adminlist.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'adminlist.php' ? 'active' : ''; ?>">
            <i class="fa-solid fa-user-shield"></i>
            <span>Administrators</span>
        </a>

        <div class="nav-label">User Support & Logs</div>
        <a href="contactview.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'contactview.php' ? 'active' : ''; ?>">
            <i class="fa-solid fa-envelope-open-text"></i>
            <span>Inquiries</span>
        </a>
        <a href="feedbackview.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'feedbackview.php' ? 'active' : ''; ?>">
            <i class="fa-solid fa-star-half-stroke"></i>
            <span>Reviews</span>
        </a>
        <a href="audit_logs.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'audit_logs.php' ? 'active' : ''; ?>">
            <i class="fa-solid fa-clipboard-list"></i>
            <span>Activity Logs</span>
        </a>
    </nav>

    <div class="sidebar-footer">
        <p>© 2024 Enterprise v2.0</p>
    </div>
</aside>

<!-- Topbar & Main Integration Wrapper -->
<div class="modern-content-wrapper">
    <header class="modern-header">
        <div class="header-left">
            <button class="sidebar-toggle-btn" onclick="toggleSidebar()">
                <i class="fa-solid fa-bars-staggered"></i>
            </button>
        </div>

        <div class="header-right" style="display: flex; align-items: center; gap: 20px;">
            <div class="user-profile-mini" style="display: flex; align-items: center; gap: 12px; cursor: pointer;">
                <div class="user-info" style="text-align: right;">
                    <h6 style="margin: 0; font-size: 0.9rem; font-weight: 600;"><?php echo $_SESSION['auser'];?></h6>
                    <span style="font-size: 0.75rem; color: var(--slate-700);">Administrator</span>
                </div>
                <img src="assets/img/avatar-01.png" alt="Admin" style="width: 40px; height: 40px; border-radius: 10px; border: 2px solid var(--primary);">
            </div>
            <a href="logout.php" class="nav-link" style="color: var(--danger); font-size: 1.2rem; padding: 0; margin: 0;">
                <i class="fa-solid fa-power-off"></i>
            </a>
        </div>
    </header>

    <script>
    function toggleSidebar() {
        const sidebar = document.getElementById('modernSidebar');
        // Toggle the collapsed class on sidebar
        sidebar.classList.toggle('collapsed');
        
        // Use a more specific targeting for the content wrapper if needed via CSS sibling selector, 
        // but since we use CSS sibling combinator in admin_modern.css, JS just needs to toggle sidebar class.
        // The CSS rule: .modern-sidebar.collapsed + .modern-content-wrapper handles the margin update automatically.
    }
    </script>