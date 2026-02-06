<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once("config.php");

if (!isset($_SESSION['aid'])) {
    header("location:index.php");
    exit();
}

// Handle Trip Deletion
if (isset($_GET['delete_id'])) {
    $delete_id = intval($_GET['delete_id']);
    
    // Fetch image path to delete from storage
    $res = $con->query("SELECT trip_image FROM trips WHERE trip_id = $delete_id");
    if($row = $res->fetch_assoc()) {
        if(!empty($row['trip_image']) && file_exists('../upload/trips/' . $row['trip_image'])) {
            @unlink('../upload/trips/' . $row['trip_image']);
        }
    }
    
    $con->query("DELETE FROM trips WHERE trip_id = $delete_id");
    header("Location: tripview.php?msg=Expedition purged from registry&type=success");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trip List - ExpenseVoyage Dashboard</title>
    
    <link rel="shortcut icon" type="image/x-icon" href="../img/favicon-32x32.png">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="assets/css/admin_modern.css">
</head>
<body>

<div class="admin-wrapper">
    <?php include("header.php"); ?>

    <main class="modern-main">
        <div class="page-header d-flex justify-content-between align-items-center mb-5">
            <div class="animate__animated animate__fadeIn">
                <h1 class="mb-1">Trip <span class="text-indigo">Management</span></h1>
                <p class="text-muted mb-0">View and manage all available travel trips.</p>
            </div>
            <div class="date-node text-end">
                <a href="tripadd.php" class="btn btn-primary rounded-pill px-4 shadow-sm">
                    <i class="fa-solid fa-plus-circle me-2"></i>Add New Trip
                </a>
            </div>
        </div>

        <?php if(isset($_GET['msg'])): ?>
        <div class="alert alert-<?php echo $_GET['type'] ?? 'info'; ?> animate__animated animate__headShake">
            <i class="fa-solid fa-circle-check me-2"></i> <?php echo htmlspecialchars($_GET['msg']); ?>
        </div>
        <?php endif; ?>

        <div class="intelligence-card animate__animated animate__fadeInUp">
            <div class="table-responsive">
                <table id="trip-table" class="table modern-table align-middle">
                    <thead class="bg-indigo-light">
                        <tr>
                            <th>Destination</th>
                            <th>Trip Name & Type</th>
                            <th>Price</th>
                            <th>Dates</th>
                            <th>Seats Booked</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $query = $con->query("SELECT * FROM trips ORDER BY trip_id DESC");
                        while($row = $query->fetch_assoc()):
                        ?>
                        <tr>
                            <td>
                                <div class="fw-bold text-slate-800"><?php echo htmlspecialchars($row['destination']); ?></div>
                                <div class="fs-xs text-muted">Registry ID: #TRP-<?php echo $row['trip_id']; ?></div>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="me-3 position-relative">
                                        <img src="../upload/trips/<?php echo !empty($row['trip_image']) ? $row['trip_image'] : 'default_trip.jpg'; ?>" width="60" height="40" class="rounded object-fit-cover shadow-sm">
                                    </div>
                                    <div>
                                        <div class="fw-bold fs-sm text-indigo"><?php echo htmlspecialchars($row['trip_name']); ?></div>
                                        <div class="badge bg-slate-100 text-slate-600 fs-xs"><?php echo strtoupper($row['travel_type']); ?></div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="fw-bold text-slate-800">$<?php echo number_format($row['budget']); ?></div>
                                <div class="fs-xs text-muted">Base Projection</div>
                            </td>
                            <td>
                                <div class="fs-sm fw-medium"><?php echo $row['duration_days']; ?> Days</div>
                                <div class="fs-xs text-muted"><?php echo date('M d', strtotime($row['starts_date'])); ?> - <?php echo date('M d', strtotime($row['end_date'])); ?></div>
                            </td>
                            <td>
                                <div class="fs-sm fw-medium"><?php echo $row['booked_seats']; ?> / <?php echo $row['seats_available']; ?></div>
                                <div class="fs-xs text-muted">Seats Utilized</div>
                            </td>
                            <td class="text-end">
                                <div class="d-flex justify-content-end gap-1">
                                    <a href="tripedit.php?id=<?php echo $row['trip_id']; ?>" class="btn btn-sm btn-outline-indigo border-0">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </a>
                                    <a href="?delete_id=<?php echo $row['trip_id']; ?>" class="btn btn-sm btn-outline-danger border-0" onclick="return confirm('Delete this trip?')">
                                        <i class="fa-solid fa-trash-can"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
    <?php include("footer.php"); ?>