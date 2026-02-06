<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once("config.php");

if (!isset($_SESSION['aid'])) {
    header("location:index.php");
    exit();
}

// Handle Status Update
if (isset($_POST['update_status'])) {
    $bid = intval($_POST['booking_id']);
    $status = mysqli_real_escape_string($con, $_POST['status']);
    
    $stmt = $con->prepare("UPDATE bookings SET status = ? WHERE booking_id = ?");
    $stmt->bind_param("si", $status, $bid);
    if($stmt->execute()) {
        $msg = "Booking #$bid status updated to $status";
        $type = "success";
    } else {
        $msg = "Failed to update status: " . $stmt->error;
        $type = "danger";
    }
    header("Location: bookingview.php?msg=$msg&type=$type");
    exit();
}

// Handle Deletion
if (isset($_GET['delete_id'])) {
    $did = intval($_GET['delete_id']);
    $con->query("DELETE FROM bookings WHERE booking_id = $did");
    header("Location: bookingview.php?msg=Booking record #$did purged&type=warning");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking List - ExpenseVoyage Dashboard</title>
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
                <h1 class="mb-1">Booking <span class="text-indigo">List</span></h1>
                <p class="text-muted mb-0">View all customer bookings and trip earnings.</p>
            </div>
            <div class="date-node text-end">
                <span class="badge bg-indigo-light text-indigo">Last updated: <?php echo date('H:i'); ?></span>
            </div>
        </div>

        <?php if(isset($_GET['msg'])): ?>
        <div class="alert alert-<?php echo $_GET['type'] ?? 'info'; ?> animate__animated animate__headShake">
            <i class="fa-solid fa-circle-info me-2"></i> <?php echo htmlspecialchars($_GET['msg']); ?>
        </div>
        <?php endif; ?>

        <div class="intelligence-card animate__animated animate__fadeInUp">
            <div class="table-responsive">
                <table id="booking-table" class="table modern-table align-middle">
                    <thead class="bg-indigo-light">
                        <tr>
                            <th>Booking ID</th>
                            <th>User</th>
                            <th>Trip</th>
                            <th>Price</th>
                            <th>Date & Time</th>
                            <th>Status</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $query = $con->query("SELECT b.*, u.first_name, u.last_name, u.email, t.trip_name 
                                            FROM bookings b 
                                            JOIN users u ON b.user_id = u.id 
                                            JOIN trips t ON b.trip_id = t.trip_id 
                                            ORDER BY b.booking_id DESC");
                        while($row = $query->fetch_assoc()):
                            $status_class = 'bg-slate-100 text-slate-700';
                            if($row['status'] == 'confirmed') $status_class = 'bg-success-light text-success';
                            if($row['status'] == 'pending') $status_class = 'bg-warning-light text-warning';
                            if($row['status'] == 'cancelled') $status_class = 'bg-danger-light text-danger';
                        ?>
                        <tr>
                            <td><span class="fw-bold">#BK-<?php echo $row['booking_id']; ?></span></td>
                            <td>
                                <div class="fw-bold text-slate-800"><?php echo htmlspecialchars($row['first_name'] . ' ' . $row['last_name']); ?></div>
                                <div class="fs-xs text-muted"><?php echo htmlspecialchars($row['email']); ?></div>
                            </td>
                            <td>
                                <div class="fw-medium text-indigo"><?php echo htmlspecialchars($row['trip_name']); ?></div>
                                <div class="fs-xs text-muted">ID: #TRP-<?php echo $row['trip_id']; ?></div>
                            </td>
                            <td><span class="fw-bold text-slate-800">$<?php echo number_format($row['total_price'], 2); ?></span></td>
                            <td><span class="fs-xs"><?php echo date('Y-m-d H:i', strtotime($row['booking_date'])); ?></span></td>
                            <td>
                                <span class="badge <?php echo $status_class; ?> rounded-pill px-3 py-2 fs-xs text-uppercase">
                                    <?php echo strtoupper($row['status']); ?>
                                </span>
                            </td>
                            <td class="text-end">
                                <div class="d-flex justify-content-end gap-1">
                                    <button class="btn btn-sm btn-outline-indigo border-0" data-bs-toggle="modal" data-bs-target="#statusModal<?php echo $row['booking_id']; ?>" title="Change Status">
                                        <i class="fa-solid fa-arrows-rotate"></i>
                                    </button>
                                    <a href="?delete_id=<?php echo $row['booking_id']; ?>" class="btn btn-sm btn-outline-danger border-0" onclick="return confirm('Delete this booking?')">
                                        <i class="fa-solid fa-trash-can"></i>
                                    </a>
                                </div>

                                <!-- Status Modal -->
                                <div class="modal fade" id="statusModal<?php echo $row['booking_id']; ?>" tabindex="-1">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content border-0 shadow-lg">
                                            <div class="modal-header bg-indigo text-white">
                                                <h5 class="modal-title">Change Status #<?php echo $row['booking_id']; ?></h5>
                                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                            </div>
                                            <form method="post">
                                                <div class="modal-body p-4">
                                                    <input type="hidden" name="booking_id" value="<?php echo $row['booking_id']; ?>">
                                                    <div class="mb-3">
                                                        <label class="form-label fs-xs text-uppercase fw-bold">Select New Status</label>
                                                        <select name="status" class="form-select">
                                                            <option value="pending" <?php echo $row['status'] == 'pending' ? 'selected' : ''; ?>>Pending</option>
                                                            <option value="confirmed" <?php echo $row['status'] == 'confirmed' ? 'selected' : ''; ?>>Confirmed</option>
                                                            <option value="cancelled" <?php echo $row['status'] == 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                                                            <option value="refunded" <?php echo $row['status'] == 'refunded' ? 'selected' : ''; ?>>Refunded</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="modal-footer border-0">
                                                    <button type="button" class="btn btn-slate-200" data-bs-dismiss="modal">Cancel</button>
                                                    <button type="submit" name="update_status" class="btn btn-primary px-4">Update Status</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

<script>
    $(document).ready(function() {
        $('#booking-table').DataTable({
            responsive: true,
            pageLength: 25,
            language: {
                search: "_INPUT_",
                searchPlaceholder: "Search ledger..."
            }
        });
    });
</script>
</body>
</html>
