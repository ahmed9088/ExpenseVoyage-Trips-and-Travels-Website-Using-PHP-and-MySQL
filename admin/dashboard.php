<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once("config.php");

// Check if admin is logged in
if(!isset($_SESSION['auser'])) {
    header("location:index.php");
    exit();
}

// Fetch Intelligence Data
$totalUsers = $con->query("SELECT COUNT(*) AS total_users FROM users WHERE role = 'traveler'")->fetch_assoc()['total_users'] ?? 0;
$totalAdmins = $con->query("SELECT COUNT(*) AS total_admins FROM users WHERE role = 'admin'")->fetch_assoc()['total_admins'] ?? 0;
$totalAgents = $con->query("SELECT COUNT(*) AS total_agents FROM users WHERE role = 'agent'")->fetch_assoc()['total_agents'] ?? 0;
$totalTrips = $con->query("SELECT COUNT(*) AS total_trips FROM trips")->fetch_assoc()['total_trips'] ?? 0;
$totalContacts = $con->query("SELECT COUNT(*) AS total_contacts FROM contactus")->fetch_assoc()['total_contacts'] ?? 0;
$totalReviews = $con->query("SELECT COUNT(*) AS total_reviews FROM review")->fetch_assoc()['total_reviews'] ?? 0;
$totalBlogs = $con->query("SELECT COUNT(*) AS total_blogs FROM blog")->fetch_assoc()['total_blogs'] ?? 0;

// Fetch Monthly Revenue for Chart
$months = [];
$revenues = [];
for ($i = 5; $i >= 0; $i--) {
    $m = date('m', strtotime("-$i months"));
    $y = date('Y', strtotime("-$i months"));
    $monthName = date('M', strtotime("-$i months"));
    $res = $con->query("SELECT SUM(total_price) as total FROM bookings WHERE MONTH(booking_date) = '$m' AND YEAR(booking_date) = '$y'");
    $row = $res->fetch_assoc();
    $months[] = $monthName;
    $revenues[] = $row['total'] ?? 0;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Enterprise Intelligence Dashboard - ExpenseVoyage</title>
    <link rel="shortcut icon" type="image/x-icon" href="../img/favicon-32x32.png">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/admin_modern.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>

<div class="admin-wrapper">
    <?php include("header.php"); ?>

    <main class="modern-main">
        <div class="page-header d-flex justify-content-between align-items-center mb-4">
            <div class="animate__animated animate__fadeIn">
                <h1 class="mb-1">Dashboard <span class="text-indigo">Overview</span></h1>
                <p class="text-muted mb-0">View all your business data in one place.</p>
            </div>
            <div class="date-node text-end">
                <h6 class="mb-0"><?php echo date('F d, Y'); ?></h6>
                <span class="badge bg-indigo-light text-indigo">Last updated: <?php echo date('H:i'); ?></span>
            </div>
        </div>

        <div class="row g-4 mb-5">
            <div class="col-xl-3 col-md-6">
                <div class="intelligence-card">
                    <div class="stat-icon"><i class="fa-solid fa-users-viewfinder"></i></div>
                    <h3 class="mb-1"><?php echo number_format($totalUsers); ?></h3>
                    <p class="text-muted mb-2">Total Users</p>
                    <div class="progress" style="height: 4px;">
                        <div class="progress-bar bg-primary" style="width: 70%"></div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="intelligence-card" style="border-left: 4px solid var(--success);">
                    <div class="stat-icon text-success"><i class="fa-solid fa-user-tie"></i></div>
                    <h3 class="mb-1"><?php echo number_format($totalAgents); ?></h3>
                    <p class="text-muted mb-2">Active Agents</p>
                    <div class="progress" style="height: 4px;">
                        <div class="progress-bar bg-success" style="width: 55%"></div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="intelligence-card" style="border-left: 4px solid var(--warning);">
                    <div class="stat-icon text-warning"><i class="fa-solid fa-map-location-dot"></i></div>
                    <h3 class="mb-1"><?php echo number_format($totalTrips); ?></h3>
                    <p class="text-muted mb-2">Live Expeditions</p>
                    <div class="progress" style="height: 4px;">
                        <div class="progress-bar bg-warning" style="width: 80%"></div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="intelligence-card" style="border-left: 4px solid var(--info);">
                    <div class="stat-icon text-indigo"><i class="fa-solid fa-star-half-stroke"></i></div>
                    <h3 class="mb-1"><?php echo number_format($totalReviews); ?></h3>
                    <p class="text-muted mb-2">Total Feedback</p>
                    <div class="progress" style="height: 4px;">
                        <div class="progress-bar bg-info" style="width: 90%"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-xl-8">
                <div class="intelligence-card h-100">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h5 class="mb-0">Earnings & Bookings</h5>
                        <div class="badge bg-slate-100 text-slate-700">6-Month Trend</div>
                    </div>
                    <div style="height: 320px; position: relative;">
                        <canvas id="revenueDashboardChart"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-xl-4">
                <div class="intelligence-card h-100">
                    <h5 class="mb-4">Quick Stats</h5>
                    <div class="d-flex justify-content-between mb-3 border-bottom pb-2">
                        <span>Total Questions</span>
                        <span class="badge bg-indigo-light text-indigo"><?php echo $totalContacts; ?></span>
                    </div>
                    <div class="d-flex justify-content-between mb-3 border-bottom pb-2">
                        <span>Published Blogs</span>
                        <span class="badge bg-indigo-light text-indigo"><?php echo $totalBlogs; ?></span>
                    </div>
                    <div class="d-flex justify-content-between mb-3 border-bottom pb-2">
                        <span>System Status</span>
                        <?php
                        $db_check = $con->ping() ? '<span class="text-success"><i class="fa-solid fa-circle-check me-1"></i> 99.9%</span>' : '<span class="text-danger"><i class="fa-solid fa-circle-xmark me-1"></i> Offline</span>';
                        echo $db_check;
                        ?>
                    </div>
                    <div class="mt-auto pt-4">
                        <div class="d-grid">
                            <a href="tripadd.php" class="btn btn-primary rounded-pill py-2 shadow-sm">
                                <i class="fa-solid fa-plus me-2"></i>Add New Trip
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
    <?php include("footer.php"); ?>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('revenueDashboardChart').getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: <?php echo json_encode($months); ?>,
            datasets: [{
                label: 'Revenue ($)',
                data: <?php echo json_encode($revenues); ?>,
                borderColor: '#4f46e5',
                backgroundColor: 'rgba(79, 70, 229, 0.1)',
                fill: true,
                tension: 0.4,
                borderWidth: 3,
                pointBackgroundColor: '#fff',
                pointBorderColor: '#4f46e5',
                pointBorderWidth: 2,
                pointRadius: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: { color: 'rgba(0, 0, 0, 0.05)' },
                    ticks: { font: { family: 'Outfit', size: 11 } }
                },
                x: {
                    grid: { display: false },
                    ticks: { font: { family: 'Outfit', size: 11 } }
                }
            }
        }
    });
});
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
