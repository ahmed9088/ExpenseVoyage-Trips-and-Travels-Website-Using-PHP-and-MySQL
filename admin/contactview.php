<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once("config.php");

// Session Check
if(!isset($_SESSION['aid'])) {
    header("location:index.php");
    exit();
}

// Handle Inquiry Deletion
if(isset($_GET['delete_id'])) {
    $did = intval($_GET['delete_id']);
    $con->query("DELETE FROM contactus WHERE contactus_id = $did");
    header("Location: contactview.php?msg=Inquiry purged from active logs&type=success");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Engagement Intelligence: Inquiries - ExpenseVoyage</title>
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
                <h1 class="mb-1">Engagement <span class="text-indigo">Inquiries</span></h1>
                <p class="text-muted mb-0">Monitor global traveler intersections and support trajectories.</p>
            </div>
            <div class="date-node text-end">
                <span class="badge bg-indigo-light text-indigo">Real-time Intersection Log</span>
            </div>
        </div>

        <?php if(isset($_GET['msg'])): ?>
            <div class="alert alert-success fs-xs mb-4 animate__animated animate__headShake">
                <i class="fa-solid fa-envelope-circle-check me-2"></i> <?php echo htmlspecialchars($_GET['msg']); ?>
            </div>
        <?php endif; ?>

        <div class="intelligence-card animate__animated animate__fadeInUp">
            <div class="table-responsive">
                <table id="contact-table" class="table modern-table align-middle">
                    <thead class="bg-indigo-light">
                        <tr>
                            <th>Voyager Identity</th>
                            <th>Contact Vector</th>
                            <th>Critical Payload (Inquiry)</th>
                            <th class="text-end">Operations</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Assuming contactus table has: contactus_id, name, email, phone, subject, message
                        $query = $con->query("SELECT * FROM contactus ORDER BY contactus_id DESC");
                        while($row = $query->fetch_assoc()):
                        ?>
                        <tr>
                            <td>
                                <div class="fw-bold text-slate-800"><?php echo htmlspecialchars($row['name']); ?></div>
                                <div class="fs-xs text-muted">Ticket: #INQ-<?php echo $row['contactus_id']; ?></div>
                            </td>
                            <td>
                                <div class="fs-sm text-indigo fw-medium"><?php echo htmlspecialchars($row['email']); ?></div>
                                <div class="fs-xs text-muted"><?php echo htmlspecialchars($row['phone'] ?? 'Secure Line'); ?></div>
                            </td>
                            <td>
                                <div class="fw-bold fs-xs text-uppercase ls-1 text-slate-600 mb-1"><?php echo htmlspecialchars($row['subject']); ?></div>
                                <div class="text-muted small text-truncate-custom" style="max-width: 400px;"><?php echo htmlspecialchars($row['message']); ?></div>
                            </td>
                            <td class="text-end">
                                <a href="?delete_id=<?php echo $row['contactus_id']; ?>" class="btn btn-sm btn-outline-danger border-0" onclick="return confirm('Full purge of inquiry payload?')">
                                    <i class="fa-solid fa-trash-can"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
    <?php include("footer.php"); ?>