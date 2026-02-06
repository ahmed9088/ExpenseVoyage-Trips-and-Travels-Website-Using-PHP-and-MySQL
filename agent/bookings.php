<?php
require_once("auth.php");

// Using $agent_id from header.php
$trip_id_filter = isset($_GET['trip_id']) ? intval($_GET['trip_id']) : 0;

// Fetch bookings for trips assigned to this agent
$sql = "SELECT b.*, t.trip_name, u.first_name, u.last_name, u.email 
        FROM bookings b 
        JOIN trips t ON b.trip_id = t.trip_id 
        JOIN users u ON b.user_id = u.id 
        WHERE t.user_id = '$agent_id'";

if ($trip_id_filter > 0) {
    $sql .= " AND t.trip_id = '$trip_id_filter'";
}

$sql .= " ORDER BY b.booking_date DESC";
$res = mysqli_query($con, $sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Bookings - Agent Dashboard</title>
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
                    <h1 class="mb-1">Voyage <span class="text-indigo">Logistics</span></h1>
                    <p class="text-muted mb-0">Manage traveler statuses and expedition updates.</p>
                </div>
                <div class="date-node text-end">
                    <?php if($trip_id_filter > 0): ?>
                        <a href="bookings.php" class="btn btn-outline-indigo btn-sm rounded-pill px-3 me-2">Clear Filter</a>
                    <?php endif; ?>
                    <span class="badge bg-indigo-light text-indigo">Total Assignments: <?php echo mysqli_num_rows($res); ?></span>
                </div>
            </div>

            <div class="intelligence-card animate__animated animate__fadeInUp">
                <div class="table-responsive">
                    <table class="table modern-table align-middle">
                        <thead class="bg-indigo-light">
                            <tr>
                                <th>Trip / Expedition</th>
                                <th>Traveler Details</th>
                                <th>Expedition Status</th>
                                <th>Payment Verify</th>
                                <th>Booking Date</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(mysqli_num_rows($res) > 0): ?>
                                <?php while($row = mysqli_fetch_assoc($res)): ?>
                                <tr>
                                    <td>
                                        <div class="fw-bold text-slate-800"><?php echo htmlspecialchars($row['trip_name']); ?></div>
                                        <div class="fs-xs text-muted">Ref: #BK-<?php echo $row['booking_id']; ?></div>
                                    </td>
                                    <td>
                                        <div class="fw-medium text-slate-800"><?php echo htmlspecialchars($row['first_name'].' '.$row['last_name']); ?></div>
                                        <div class="fs-xs text-indigo"><?php echo htmlspecialchars($row['email']); ?></div>
                                    </td>
                                    <td>
                                        <select class="form-select form-select-sm border-0 bg-light rounded-pill px-3" 
                                                onchange="updateExpeditionStatus(<?php echo $row['booking_id']; ?>, this.value)"
                                                style="width: 140px;">
                                            <option value="scheduled" <?php echo $row['expedition_status']=='scheduled'?'selected':''; ?>>Scheduled</option>
                                            <option value="departed" <?php echo $row['expedition_status']=='departed'?'selected':''; ?>>Departed</option>
                                            <option value="arrived" <?php echo $row['expedition_status']=='arrived'?'selected':''; ?>>Arrived</option>
                                            <option value="completed" <?php echo $row['expedition_status']=='completed'?'selected':''; ?>>Completed</option>
                                            <option value="cancelled" <?php echo $row['expedition_status']=='cancelled'?'selected':''; ?>>Cancelled</option>
                                        </select>
                                    </td>
                                    <td>
                                        <?php if($row['verification_status'] == 'pending'): ?>
                                            <button class="btn btn-sm btn-warning rounded-pill px-3 animate__animated animate__pulse animate__infinite" 
                                                    onclick='showVerificationModal(<?php echo json_encode($row); ?>)'>
                                                <i class="fa-solid fa-clock me-1"></i> Verify Now
                                            </button>
                                        <?php elseif($row['verification_status'] == 'verified'): ?>
                                            <span class="badge bg-success-light text-success rounded-pill px-3">
                                                <i class="fa-solid fa-check-double me-1"></i> Verified
                                            </span>
                                        <?php else: ?>
                                            <span class="badge bg-danger-light text-danger rounded-pill px-3">
                                                <i class="fa-solid fa-xmark me-1"></i> Rejected
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="fs-sm text-slate-600"><?php echo date('M d, Y', strtotime($row['booking_date'])); ?></div>
                                    </td>
                                    <td class="text-end">
                                        <button class="btn btn-sm btn-outline-indigo border-0" title="Contact Traveler">
                                            <i class="fa-solid fa-envelope"></i>
                                        </button>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" class="text-center py-5 text-muted">No bookings found matching your parameters.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>

        <?php include("footer.php"); ?>
    </div>
</div>

<!-- Payment Verification Modal -->
<div class="modal fade" id="verifyModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px; overflow: hidden;">
            <div class="modal-header bg-indigo text-white border-0 py-3">
                <h5 class="modal-title serif-font"><i class="fa-solid fa-shield-check me-2"></i>Payment Verification</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="row g-4">
                    <div class="col-md-7">
                        <label class="small text-muted mb-2 d-block">Transaction Screenshot</label>
                        <div class="screenshot-vault p-2 bg-light rounded-3 border">
                            <img id="modalScreenshot" src="" class="img-fluid rounded-2 shadow-sm" alt="Payment Proof">
                        </div>
                    </div>
                    <div class="col-md-5">
                        <div class="p-3 bg-light rounded-3 mb-4">
                            <h6 class="text-indigo small text-uppercase tracking-wider fw-bold mb-3">Booking Intelligence</h6>
                            <div class="mb-2 fs-sm"><strong>Traveler:</strong> <span id="modalUser"></span></div>
                            <div class="mb-2 fs-sm"><strong>Email:</strong> <span id="modalEmail"></span></div>
                            <div class="mb-2 fs-sm"><strong>Method:</strong> <span id="modalMethod" class="text-uppercase fw-bold"></span></div>
                            <div class="mb-0 fs-sm"><strong>Price:</strong> <span id="modalPrice" class="text-success fw-bold"></span></div>
                        </div>
                        
                        <div class="d-grid gap-2">
                            <button class="btn btn-success py-2 rounded-pill" onclick="processVerification('verified')">
                                <i class="fa-solid fa-check me-2"></i>Approve Payment
                            </button>
                            <button class="btn btn-outline-danger py-2 rounded-pill" onclick="processVerification('rejected')">
                                <i class="fa-solid fa-xmark me-2"></i>Reject Payment
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Toast Container -->
<div class="position-fixed bottom-0 end-0 p-3" style="z-index: 1050">
    <div id="statusToast" class="toast align-items-center text-white bg-indigo border-0" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="d-flex">
            <div class="toast-body" id="toastMessage">
                Status updated successfully.
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
    </div>
</div>

<script>
let currentBookingId = null;

function showVerificationModal(booking) {
    currentBookingId = booking.booking_id;
    document.getElementById('modalUser').innerText = booking.first_name + ' ' + booking.last_name;
    document.getElementById('modalEmail').innerText = booking.email;
    document.getElementById('modalMethod').innerText = booking.payment_method;
    document.getElementById('modalPrice').innerText = '$' + parseFloat(booking.total_price).toLocaleString();
    
    const screenshot = booking.payment_screenshot;
    const imgPath = screenshot ? '../upload/payments/' + screenshot : '../img/no-screenshot.png';
    document.getElementById('modalScreenshot').src = imgPath;
    
    const modal = new bootstrap.Modal(document.getElementById('verifyModal'));
    modal.show();
}

function processVerification(status) {
    const toastElem = document.getElementById('statusToast');
    const toast = new bootstrap.Toast(toastElem);
    
    $.ajax({
        url: 'verify_payment.php',
        type: 'POST',
        data: {
            booking_id: currentBookingId,
            status: status
        },
        success: function(response) {
            let data = response;
            try {
                if(typeof response === 'string') data = JSON.parse(response);
            } catch(e) {}
            
            if(data.status === 'success') {
                location.reload(); // Refresh to show new status
            } else {
                document.getElementById('toastMessage').innerText = 'Error: ' + data.message;
                toastElem.classList.remove('bg-indigo');
                toastElem.classList.add('bg-danger');
                toast.show();
            }
        }
    });
}

function updateExpeditionStatus(bookingId, status) {
    const toastElem = document.getElementById('statusToast');
    const toast = new bootstrap.Toast(toastElem);
    
    $.ajax({
        url: '../update_expedition.php',
        type: 'POST',
        data: {
            booking_id: bookingId,
            status: status
        },
        success: function(response) {
            // response is expected to be JSON from update_expedition.php
            let data = response;
            try {
                if(typeof response === 'string') data = JSON.parse(response);
            } catch(e) {}
            
            if(data.status === 'success') {
                document.getElementById('toastMessage').innerText = data.message;
                toastElem.classList.remove('bg-danger');
                toastElem.classList.add('bg-indigo');
                toast.show();
            } else {
                document.getElementById('toastMessage').innerText = 'Error: ' + data.message;
                toastElem.classList.remove('bg-indigo');
                toastElem.classList.add('bg-danger');
                toast.show();
            }
        },
        error: function() {
            document.getElementById('toastMessage').innerText = 'Network error during status update.';
            toastElem.classList.remove('bg-indigo');
            toastElem.classList.add('bg-danger');
            toast.show();
        }
    });
}
</script>
</body>
</html>
