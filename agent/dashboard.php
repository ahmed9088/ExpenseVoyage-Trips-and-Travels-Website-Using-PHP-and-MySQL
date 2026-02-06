<?php
require_once("auth.php");

// Data Fetching: Using $agent_id from header.php

// 1. Total Trips Assigned
$trips_count_res = mysqli_query($con, "SELECT COUNT(*) as total FROM trips WHERE user_id = '$agent_id'");
$trips_count = mysqli_fetch_assoc($trips_count_res)['total'];

// 2. Total Bookings
$bookings_count_res = mysqli_query($con, "SELECT COUNT(*) as total FROM bookings b JOIN trips t ON b.trip_id = t.trip_id WHERE t.user_id = '$agent_id'");
$bookings_total = mysqli_fetch_assoc($bookings_count_res)['total'];

// 3. Active Missions (Scheduled or Departed)
$active_missions_res = mysqli_query($con, "SELECT COUNT(*) as total FROM bookings b JOIN trips t ON b.trip_id = t.trip_id WHERE t.user_id = '$agent_id' AND b.expedition_status IN ('scheduled', 'departed')");
$active_missions = mysqli_fetch_assoc($active_missions_res)['total'];

// 4. Recent Bookings
$recent_bookings = mysqli_query($con, "SELECT b.*, t.trip_name, u.first_name, u.last_name 
    FROM bookings b 
    JOIN trips t ON b.trip_id = t.trip_id 
    JOIN users u ON b.user_id = u.id 
    WHERE t.user_id = '$agent_id' 
    ORDER BY b.booking_date DESC LIMIT 5");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agent Dashboard - ExpenseVoyage</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../admin/assets/css/admin_modern.css">
</head>
<body>

<div class="admin-wrapper">
    <?php include("sidebar.php"); ?>

    <div class="modern-content-wrapper">
        <?php include("header.php"); ?>

        <main class="modern-main">
            <div class="page-header d-flex justify-content-between align-items-center mb-5">
                <div class="animate__animated animate__fadeIn">
                    <h1 class="mb-1">Agent <span class="text-indigo">Workspace</span></h1>
                    <p class="text-muted mb-0">Operational overview of your assigned travel missions.</p>
                </div>
                <div class="date-node text-end">
                    <span class="badge bg-indigo-light text-indigo px-3 py-2 rounded-pill">
                        <i class="fa-solid fa-signal me-2"></i>Live Operations
                    </span>
                </div>
            </div>

            <!-- Stats Rows -->
            <div class="row g-4 mb-5">
                <div class="col-xl-4 col-md-6">
                    <div class="intelligence-card animate__animated animate__fadeInUp">
                        <div class="stat-icon bg-indigo-light">
                            <i class="fa-solid fa-map-location-dot"></i>
                        </div>
                        <h6 class="text-muted small text-uppercase fw-bold mb-1">Assigned Trips</h6>
                        <h2 class="fw-bold mb-0"><?php echo $trips_count; ?></h2>
                        <div class="mt-2 small">
                            <span class="text-success fw-medium"><i class="fa-solid fa-circle-check"></i> Globally Distributed</span>
                        </div>
                    </div>
                </div>

                <div class="col-xl-4 col-md-6">
                    <div class="intelligence-card animate__animated animate__fadeInUp" style="animation-delay: 0.1s">
                        <div class="stat-icon bg-success-light text-success">
                            <i class="fa-solid fa-users"></i>
                        </div>
                        <h6 class="text-muted small text-uppercase fw-bold mb-1">Total Bookings</h6>
                        <h2 class="fw-bold mb-0"><?php echo $bookings_total; ?></h2>
                        <div class="mt-2 small">
                            <span class="text-indigo fw-medium"><i class="fa-solid fa-chart-line"></i> Managing Travelers</span>
                        </div>
                    </div>
                </div>

                <div class="col-xl-4 col-md-6">
                    <div class="intelligence-card animate__animated animate__fadeInUp" style="animation-delay: 0.2s">
                        <div class="stat-icon bg-warning-light text-warning">
                            <i class="fa-solid fa-person-walking-luggage"></i>
                        </div>
                        <h6 class="text-muted small text-uppercase fw-bold mb-1">Active Missions</h6>
                        <h2 class="fw-bold mb-0"><?php echo $active_missions; ?></h2>
                        <div class="mt-2 small">
                            <span class="text-warning fw-medium"><i class="fa-solid fa-clock"></i> In Progress</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row g-4 mb-5">
                <!-- Recent Bookings Table -->
                <div class="col-xl-8">
                    <div class="intelligence-card animate__animated animate__fadeInLeft h-100">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h5 class="section-title mb-0">Recent Assignments</h5>
                            <a href="bookings.php" class="btn btn-link text-indigo text-decoration-none small fw-bold">View Ledger</a>
                        </div>
                        <div class="table-responsive">
                            <table class="table modern-table align-middle">
                                <thead class="bg-indigo-light">
                                    <tr>
                                        <th>Trip Name</th>
                                        <th>Traveler</th>
                                        <th>Status</th>
                                        <th class="text-end">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if(mysqli_num_rows($recent_bookings) > 0): ?>
                                        <?php while($row = mysqli_fetch_assoc($recent_bookings)): ?>
                                        <tr>
                                            <td class="fw-bold"><?php echo htmlspecialchars($row['trip_name']); ?></td>
                                            <td>
                                                <div class="fw-medium"><?php echo htmlspecialchars($row['first_name'].' '.$row['last_name']); ?></div>
                                                <div class="fs-xs text-muted">Booked: <?php echo date('M d, Y', strtotime($row['booking_date'])); ?></div>
                                            </td>
                                            <td>
                                                <span class="badge bg-indigo-light text-indigo rounded-pill text-capitalize">
                                                    <?php echo $row['expedition_status']; ?>
                                                </span>
                                            </td>
                                            <td class="text-end">
                                                <a href="bookings.php" class="btn btn-sm btn-outline-indigo border-0">
                                                    <i class="fa-solid fa-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                        <?php endwhile; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="4" class="text-center py-5 text-muted small">No recent bookings found.</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Fast Actions -->
                <div class="col-xl-4">
                    <div class="intelligence-card animate__animated animate__fadeInRight h-100">
                        <h5 class="section-title mb-4">Operational Shortcuts</h5>
                        <div class="d-grid gap-3">
                            <a href="trips.php" class="btn btn-outline-indigo text-start p-3 rounded-4 d-flex align-items-center">
                                <div class="icon-box me-3"><i class="fa-solid fa-map-pin fs-4"></i></div>
                                <div>
                                    <div class="fw-bold">My Trips</div>
                                    <div class="fs-xs opacity-75">View all assigned routes</div>
                                </div>
                            </a>
                            <a href="bookings.php" class="btn btn-outline-success text-start p-3 rounded-4 d-flex align-items-center">
                                <div class="icon-box me-3"><i class="fa-solid fa-id-card-clip fs-4"></i></div>
                                <div>
                                    <div class="fw-bold">Bookings</div>
                                    <div class="fs-xs opacity-75">Update mission statuses</div>
                                </div>
                            </a>
                            <a href="profile.php" class="btn btn-outline-slate text-start p-3 rounded-4 d-flex align-items-center">
                                <div class="icon-box me-3"><i class="fa-solid fa-user-gear fs-4"></i></div>
                                <div>
                                    <div class="fw-bold">Account</div>
                                    <div class="fs-xs opacity-75">Manage your profile</div>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </main>

        <?php include("footer.php"); ?>
