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

// Handle Feedback Deletion
if(isset($_GET['delete_id'])) {
    $did = intval($_GET['delete_id']);
    $con->query("DELETE FROM review WHERE id = $did");
    header("Location: feedbackview.php?msg=Experience feed purged from active registry&type=success");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Voyager Experience Feed - ExpenseVoyage Intelligence</title>
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
                <h1 class="mb-1">Voyager <span class="text-indigo">Experience Feed</span></h1>
                <p class="text-muted mb-0">Deep analysis of sentiment dynamics and expedition feedback.</p>
            </div>
            <div class="date-node text-end">
                <span class="badge bg-indigo-light text-indigo">Strategic Sentiment Analysis</span>
            </div>
        </div>

        <?php if(isset($_GET['msg'])): ?>
            <div class="alert alert-success fs-xs mb-4 animate__animated animate__headShake">
                <i class="fa-solid fa-star-half-stroke me-2"></i> <?php echo htmlspecialchars($_GET['msg']); ?>
            </div>
        <?php endif; ?>

        <div class="intelligence-card animate__animated animate__fadeInUp">
            <div class="table-responsive">
                <table id="feedback-table" class="table modern-table align-middle">
                    <thead class="bg-indigo-light">
                        <tr>
                            <th>Voyager Node</th>
                            <th>Expedition Context</th>
                            <th>Sentiment Score</th>
                            <th>Feedback Dynamics</th>
                            <th class="text-end">Operations</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Assuming review table: id, user_name (or similar), trip_name, stars, feedback
                        $query = $con->query("SELECT * FROM review ORDER BY id DESC");
                        while($row = $query->fetch_assoc()):
                        ?>
                        <tr>
                            <td>
                                <div class="fw-bold text-slate-800"><?php echo htmlspecialchars($row['user_name'] ?? 'Voyager Identity Restricted'); ?></div>
                                <div class="fs-xs text-muted">ID: #RE-<?php echo $row['id']; ?></div>
                            </td>
                            <td>
                                <div class="fs-sm text-indigo fw-medium"><?php echo htmlspecialchars($row['trip_name'] ?? 'General Experience'); ?></div>
                            </td>
                            <td>
                                <div class="text-warning fs-xs">
                                    <?php 
                                    $stars = intval($row['stars'] ?? 0);
                                    for($i=0; $i<$stars; $i++) echo '<i class="fa-solid fa-star"></i>';
                                    for($i=$stars; $i<5; $i++) echo '<i class="fa-regular fa-star opacity-50"></i>';
                                    ?>
                                </div>
                            </td>
                            <td>
                                <div class="text-muted small text-truncate-custom" style="max-width: 400px;"><?php echo htmlspecialchars($row['feedback']); ?></div>
                            </td>
                            <td class="text-end">
                                <a href="?delete_id=<?php echo $row['id']; ?>" class="btn btn-sm btn-outline-danger border-0" onclick="return confirm('Purge experience feed record?')">
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