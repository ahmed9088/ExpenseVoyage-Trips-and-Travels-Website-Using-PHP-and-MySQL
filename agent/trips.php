<?php
require_once("auth.php");

// Using $agent_id from header.php

// Fetch assigned trips
$sql = "SELECT t.*, c.city_name 
        FROM trips t 
        LEFT JOIN city c ON t.destination = c.city_name 
        WHERE t.user_id = '$agent_id' 
        ORDER BY t.trip_id DESC";
$res = mysqli_query($con, $sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Trips - Agent Dashboard</title>
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
                    <h1 class="mb-1">My <span class="text-indigo">Trips</span></h1>
                    <p class="text-muted mb-0">List of travel routes assigned to your management.</p>
                </div>
                <div class="date-node text-end">
                    <span class="badge bg-indigo-light text-indigo">Total Trips: <?php echo mysqli_num_rows($res); ?></span>
                </div>
            </div>

            <div class="intelligence-card animate__animated animate__fadeInUp">
                <div class="table-responsive">
                    <table class="table modern-table align-middle">
                        <thead class="bg-indigo-light">
                            <tr>
                                <th>Trip Details</th>
                                <th>Destination</th>
                                <th>Schedule</th>
                                <th>Capacity</th>
                                <th>Budget</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(mysqli_num_rows($res) > 0): ?>
                                <?php while($row = mysqli_fetch_assoc($res)): ?>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <img src="../upload/trips/<?php echo !empty($row['trip_image']) ? htmlspecialchars($row['trip_image']) : 'default.jpg'; ?>" 
                                                 class="rounded shadow-sm me-3" width="60" height="40" style="object-fit: cover;">
                                            <div>
                                                <div class="fw-bold text-slate-800"><?php echo htmlspecialchars($row['trip_name']); ?></div>
                                                <div class="fs-xs text-muted">ID: #TRP-<?php echo $row['trip_id']; ?></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="fw-medium text-indigo"><?php echo htmlspecialchars($row['destination']); ?></div>
                                        <div class="fs-xs text-muted text-capitalize"><?php echo $row['travel_type']; ?></div>
                                    </td>
                                    <td>
                                        <div class="small fw-bold text-slate-700"><?php echo date('M d, Y', strtotime($row['starts_date'])); ?></div>
                                        <div class="fs-xs text-muted"><?php echo $row['duration_days']; ?> Days</div>
                                    </td>
                                    <td>
                                        <div class="progress mb-1" style="height: 6px; width: 80px;">
                                            <?php 
                                            $percent = ($row['booked_seats'] / $row['seats_available']) * 100;
                                            $color = $percent > 80 ? 'bg-danger' : ($percent > 50 ? 'bg-warning' : 'bg-success');
                                            ?>
                                            <div class="progress-bar <?php echo $color; ?>" style="width: <?php echo $percent; ?>%"></div>
                                        </div>
                                        <div class="fs-xs text-muted"><?php echo $row['booked_seats']; ?> / <?php echo $row['seats_available']; ?> Booked</div>
                                    </td>
                                    <td>
                                        <div class="fw-bold text-indigo">$<?php echo number_format($row['budget'], 2); ?></div>
                                    </td>
                                    <td class="text-end">
                                        <a href="bookings.php?trip_id=<?php echo $row['trip_id']; ?>" class="btn btn-sm btn-outline-indigo rounded-pill px-3">
                                            <i class="fa-solid fa-users me-1"></i> View Bookings
                                        </a>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" class="text-center py-5 text-muted">No trips assigned to you yet.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>

        <?php include("footer.php"); ?>
