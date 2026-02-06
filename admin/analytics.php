<?php
session_start();
include("config.php");

// Session Check
if(!isset($_SESSION['aid'])) {
    header("location:index.php");
    exit();
}

$month = isset($_GET['month']) ? intval($_GET['month']) : 0;
// Default to current year if filtering by month, otherwise all time or specific year logic can be added. 
// For now, assuming current year for month filtering.
$year = date('Y');

// Total Revenue
// If month is selected, filter by month AND year
if($month > 0) {
    $revStmt = $con->prepare("SELECT SUM(total_price) as total FROM bookings WHERE MONTH(created_at) = ? AND YEAR(created_at) = ?");
    if ($revStmt) {
        $revStmt->bind_param("ii", $month, $year);
    } else {
        error_log("Analytics Error (Revenue Prepare): " . $con->error);
    }
} else {
    $revStmt = $con->prepare("SELECT SUM(total_price) as total FROM bookings");
    if (!$revStmt) {
        error_log("Analytics Error (Revenue Prepare All Time): " . $con->error);
    }
}
if ($revStmt) {
    $revStmt->execute();
    $revRes = $revStmt->get_result();
    $revData = $revRes->fetch_assoc();
    $revenue = $revData['total'] ?? 0;
    $revStmt->close();
} else {
    $revenue = 0;
}


// Total Bookings
if($month > 0) {
    $bookStmt = $con->prepare("SELECT COUNT(*) as total FROM bookings WHERE MONTH(created_at) = ? AND YEAR(created_at) = ?");
    if ($bookStmt) {
        $bookStmt->bind_param("ii", $month, $year);
    } else {
        error_log("Analytics Error (Bookings Prepare): " . $con->error);
    }
} else {
    $bookStmt = $con->prepare("SELECT COUNT(*) as total FROM bookings");
    if (!$bookStmt) {
        error_log("Analytics Error (Bookings Prepare All Time): " . $con->error);
    }
}
if ($bookStmt) {
    $bookStmt->execute();
    $bookRes = $bookStmt->get_result();
    $bookData = $bookRes->fetch_assoc();
    $totalBookings = $bookData['total'] ?? 0;
    $bookStmt->close();
} else {
    $totalBookings = 0;
}


// Top Expeditions (Most Booked)
// Fix GROUP BY: Only grouping by trip_id, fetching name via join or by trusting trip_id is unique per trip.
// Better to group by both or use ANY_VALUE if supported, but safer to group by both.
$popSql = "SELECT t.trip_name, COUNT(b.booking_id) as bookings 
           FROM bookings b
           JOIN trips t ON b.trip_id = t.trip_id
           GROUP BY t.trip_id, t.trip_name
           ORDER BY bookings DESC LIMIT 5";
$popRes = $con->query($popSql);

if (!$popRes) {
    error_log("Analytics Error (Expeditions): " . $con->error);
}

// Monthly Bookings Trend (Current Year)
$trendSql = "SELECT MONTHNAME(booking_date) as month, COUNT(*) as count 
             FROM bookings 
             WHERE YEAR(booking_date) = ? 
             GROUP BY MONTH(booking_date), MONTHNAME(booking_date) 
             ORDER BY MONTH(booking_date)";

$trendStmt = $con->prepare($trendSql);
if ($trendStmt) {
    $trendStmt->bind_param("i", $year);
    $trendStmt->execute();
    $trendRes = $trendStmt->get_result();
    $trendStmt->close();
} else {
    error_log("Analytics Error (Trend Prepare): " . $con->error);
    $trendRes = false; // Indicate failure
}


// Status Distribution
$statusSql = "SELECT status, COUNT(*) as count FROM bookings GROUP BY status";
$statusRes = $con->query($statusSql);
if (!$statusRes) {
    error_log("Analytics Error (Status): " . $con->error);
}

// Revenue this Month vs Last Month
$currentMonth = date('m');
$currentYear = date('Y');
$lastMonth = $currentMonth - 1;
$lastMonthYear = $currentYear;
if ($lastMonth == 0) {
    $lastMonth = 12;
    $lastMonthYear--;
}

// Current Month Revenue
$revStmt = $con->prepare("SELECT SUM(total_price) as total FROM bookings WHERE MONTH(booking_date) = ? AND YEAR(booking_date) = ?");
if ($revStmt) {
    $revStmt->bind_param("ii", $currentMonth, $currentYear);
    $revStmt->execute();
    $revRes = $revStmt->get_result();
    $currRev = $revRes->fetch_assoc()['total'] ?? 0;
    $revStmt->close();
} else {
    error_log("Analytics Error (Revenue Prepare): " . $con->error);
    $currRev = 0;
}

// Last Month Revenue
$revStmtLast = $con->prepare("SELECT SUM(total_price) as total FROM bookings WHERE MONTH(booking_date) = ? AND YEAR(booking_date) = ?");
if ($revStmtLast) {
    $revStmtLast->bind_param("ii", $lastMonth, $lastMonthYear);
    $revStmtLast->execute();
    $revResLast = $revStmtLast->get_result();
    $lastRev = $revResLast->fetch_assoc()['total'] ?? 0;
    $revStmtLast->close();
} else {
     $lastRev = 0;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Analytics - ExpenseVoyage Dashboard</title>
    <link rel="shortcut icon" type="image/x-icon" href="../img/favicon-32x32.png">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/admin_modern.css">
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>

<div class="admin-wrapper">
    <?php include("header.php"); ?>

    <main class="modern-main">
        <div class="page-header d-flex justify-content-between align-items-center mb-5">
            <div class="animate__animated animate__fadeIn">
                <h1 class="mb-1">Business <span class="text-indigo">Analytics</span></h1>
                <p class="text-muted mb-0">Track your performance and booking trends.</p>
            </div>
            
            <form method="get" class="d-flex align-items-center gap-2 animate__animated animate__fadeInRight">
                <select name="month" class="form-select form-select-sm rounded-pill border-indigo" onchange="this.form.submit()">
                    <option value="0">All Time Data</option>
                    <?php 
                    for($m=1; $m<=12; $m++){
                        $selected = ($m == $month) ? 'selected' : '';
                        echo "<option value='$m' $selected>".date('F', mktime(0,0,0,$m, 1, date('Y')))."</option>";
                    }
                    ?>
                </select>
                <div class="date-node">
                    <span class="badge bg-indigo text-white shadow-sm">Live Feed</span>
                </div>
            </form>
        </div>

        <!-- KPI Cards -->
        <div class="row g-4 mb-5">
            <div class="col-xl-6 col-md-6">
                <div class="intelligence-card h-100 animate__animated animate__zoomIn">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <p class="text-muted fs-xs fw-bold text-uppercase mb-1">Total Earnings</p>
                            <h2 class="mb-0 text-slate-800 fw-bold">$<?php echo number_format($revenue, 2); ?></h2>
                        </div>
                        <div class="icon-box bg-indigo-light text-indigo rounded-circle p-3">
                            <i class="fa-solid fa-sack-dollar fa-lg"></i>
                        </div>
                    </div>
                    <div class="d-flex align-items-center fs-xs">
                        <span class="text-success fw-bold me-2"><i class="fa-solid fa-arrow-trend-up"></i> +12.5%</span>
                        <span class="text-muted">since last month</span>
                    </div>
                </div>
            </div>
            <div class="col-xl-6 col-md-6">
                <div class="intelligence-card h-100 animate__animated animate__zoomIn" style="animation-delay: 0.1s;">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <p class="text-muted fs-xs fw-bold text-uppercase mb-1">Total Bookings</p>
                            <h2 class="mb-0 text-slate-800 fw-bold"><?php echo number_format($totalBookings); ?></h2>
                        </div>
                        <div class="icon-box bg-teal-light text-teal rounded-circle p-3">
                            <i class="fa-solid fa-ticket fa-lg"></i>
                        </div>
                    </div>
                    <div class="d-flex align-items-center fs-xs">
                        <span class="text-success fw-bold me-2"><i class="fa-solid fa-arrow-trend-up"></i> +5.3%</span>
                        <span class="text-muted">since last month</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Row -->
        <div class="row g-4 mb-5">
            <div class="col-xl-8">
                <div class="intelligence-card h-100 animate__animated animate__fadeInUp">
                    <h5 class="section-title mb-4">Most Popular Trips</h5>
                    <canvas id="popularityChart" height="300"></canvas>
                </div>
            </div>
            <div class="col-xl-4">
                <div class="intelligence-card h-100 animate__animated animate__fadeInUp" style="animation-delay: 0.2s;">
                    <h5 class="section-title mb-4">Booking Status</h5>
                    <canvas id="statusChart" height="300"></canvas>
                </div>
            </div>
        </div>
        
        <!-- Trend Chart -->
        <div class="row">
             <div class="col-12">
                <div class="intelligence-card animate__animated animate__fadeInUp">
                     <h5 class="section-title mb-4">Monthly Booking Trend (<?php echo $year; ?>)</h5>
                     <canvas id="trendChart" height="100"></canvas>
                </div>
             </div>
        </div>

    </main>
    <?php include("footer.php"); ?>
</div>
<script>
    // Data from PHP
    const tripLabels = <?php 
        $labels = [];
        if ($popRes) {
            mysqli_data_seek($popRes, 0);
            while($row = mysqli_fetch_assoc($popRes)) $labels[] = $row['trip_name'];
        }
        echo json_encode($labels);
    ?>;
    const tripData = <?php 
        $data = [];
        if ($popRes) {
            mysqli_data_seek($popRes, 0);
            while($row = mysqli_fetch_assoc($popRes)) $data[] = $row['bookings'];
        }
        echo json_encode($data);
    ?>;

    new Chart(document.getElementById('popularityChart'), {
        type: 'bar',
        data: {
            labels: tripLabels,
            datasets: [{
                label: 'Number of Bookings',
                data: tripData,
                backgroundColor: '#4361ee',
                borderRadius: 12,
                barThickness: 40
            }]
        },
        options: {
            plugins: { legend: { display: false } },
            scales: {
                y: { grid: { display: false }, ticks: { font: { family: 'Outfit' } } },
                x: { grid: { display: false }, ticks: { font: { family: 'Outfit', size: 10 } } }
            }
        }
    });

    const statusLabels = <?php 
        $sLabels = [];
        mysqli_data_seek($statusRes, 0);
        while($row = mysqli_fetch_assoc($statusRes)) $sLabels[] = strtoupper($row['status']);
        echo json_encode($sLabels);
    ?>;
    const statusData = <?php 
        $sData = [];
        mysqli_data_seek($statusRes, 0);
        while($row = mysqli_fetch_assoc($statusRes)) $sData[] = $row['count'];
        echo json_encode($sData);
    ?>;

    new Chart(document.getElementById('statusChart'), {
        type: 'doughnut',
        data: {
            labels: statusLabels,
            datasets: [{
                data: statusData,
                backgroundColor: ['#4361ee', '#818cf8', '#10b981', '#f43f5e', '#64748b'],
                borderWidth: 0
            }]
        },
        options: { 
            cutout: '75%',
            plugins: { legend: { position: 'bottom', labels: { font: { family: 'Outfit', size: 10 } } } }
        }
    });

    // Trend Chart
    const trendLabels = <?php 
        $tLabels = [];
        if($trendRes) {
            mysqli_data_seek($trendRes, 0);
            while($row = mysqli_fetch_assoc($trendRes)) $tLabels[] = $row['month'];
        }
        echo json_encode($tLabels);
    ?>;
    const trendData = <?php 
        $tData = [];
        if($trendRes) {
            mysqli_data_seek($trendRes, 0);
            while($row = mysqli_fetch_assoc($trendRes)) $tData[] = $row['count'];
        }
        echo json_encode($tData);
    ?>;

    new Chart(document.getElementById('trendChart'), {
        type: 'line',
        data: {
            labels: trendLabels,
            datasets: [{
                label: 'Total Bookings',
                data: trendData,
                borderColor: '#10b981',
                backgroundColor: 'rgba(16, 185, 129, 0.1)',
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            plugins: { legend: { display: false } },
            scales: {
                y: { beginAtZero: true, ticks: { font: { family: 'Outfit' } } },
                x: { ticks: { font: { family: 'Outfit' } } }
            }
        }
    });
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
