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

$sql = "SELECT l.*, u.email as user_email 
        FROM audit_logs l 
        LEFT JOIN users u ON l.user_id = u.id 
        ORDER BY l.created_at DESC 
        LIMIT 100";
$res = mysqli_query($con, $sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Enterprise Security: Audit Trail - ExpenseVoyage</title>
    <link rel="shortcut icon" type="image/x-icon" href="../img/favicon-32x32.png">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/admin_modern.css">
</head>
<body>

<div class="admin-wrapper">
    <?php include("header.php"); ?>

    <main class="modern-main">
        <div class="page-header d-flex justify-content-between align-items-center mb-5">
            <div class="animate__animated animate__fadeIn">
                <h1 class="mb-1">Security <span class="text-indigo">Audit Trail</span></h1>
                <p class="text-muted mb-0">Tamper-evident record of all critical enterprise maneuvers.</p>
            </div>
            <div class="date-node text-end">
                <span class="badge bg-indigo-light text-indigo">System Sentinel Active</span>
            </div>
        </div>

        <div class="intelligence-card animate__animated animate__fadeInUp">
            <div class="table-responsive">
                <table class="table modern-table align-middle">
                    <thead class="bg-indigo-light">
                        <tr>
                            <th>Temporal Marker</th>
                            <th>Intelligence Node (User)</th>
                            <th>Maneuver (Action)</th>
                            <th>Critical Payload (Details)</th>
                            <th class="text-end">Origin Vector (IP)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($row = mysqli_fetch_assoc($res)): ?>
                        <tr>
                            <td class="font-monospace fs-xs fw-bold text-slate-600">
                                <?php echo date('Y-m-d H:i:s', strtotime($row['created_at'])); ?>
                            </td>
                            <td>
                                <div class="fw-medium text-indigo small">
                                    <i class="fa-solid fa-user-gear me-2 opacity-50"></i>
                                    <?php echo htmlspecialchars($row['user_email'] ?? 'SYSTEM_CORE'); ?>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-slate-100 text-slate-700 px-3 py-2 rounded-pill fs-xs text-uppercase fw-bold ls-1">
                                    <?php echo htmlspecialchars($row['action']); ?>
                                </span>
                            </td>
                            <td>
                                <div class="text-muted small max-w-400">
                                    <?php echo htmlspecialchars($row['details']); ?>
                                </div>
                            </td>
                            <td class="text-end font-monospace text-slate-400 fs-xs">
                                <?php echo htmlspecialchars($row['ip_address']); ?>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-4 text-center">
            <p class="text-muted small">Only displaying the last 100 tactical events. Access complete archives for deep forensic analysis.</p>
        </div>
    </main>
    <?php include("footer.php"); ?>
