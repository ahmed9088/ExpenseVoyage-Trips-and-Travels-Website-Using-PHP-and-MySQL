<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once("config.php");

if(!isset($_SESSION['aid'])) {
    header("location:index.php");
    exit();
}

$msg = "";
$error = "";

if(isset($_POST['update'])) {
    $fid = intval($_GET['id']);
    $status = mysqli_real_escape_string($con, $_POST['status']);
    
    $sql = "UPDATE review SET status = '$status' WHERE id = $fid";
    $result = mysqli_query($con, $sql);
    if($result) {
        $msg = "Feedback Status Updated Successfully";
        header("Location:feedbackview.php?msg=$msg");
        exit();
    } else {
        $error = "Update Failed";
    }
}

// Fetch Feedback Data
if(!isset($_GET['id'])) { header("Location: feedbackview.php"); exit(); }
$fid = intval($_GET['id']);
$res = $con->query("SELECT * FROM review WHERE id = $fid");
$row = $res->fetch_assoc();
if(!$row) { header("Location: feedbackview.php"); exit(); }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Feedback - ExpenseVoyage Intelligence</title>
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
                <h1 class="mb-1">Feedback <span class="text-indigo">Calibration</span></h1>
                <p class="text-muted mb-0">Adjust visibility and status of voyager feedback.</p>
            </div>
            <div class="date-node text-end">
                <a href="feedbackview.php" class="btn btn-outline-indigo rounded-pill px-4">
                    <i class="fa-solid fa-chevron-left me-2"></i>Experience Feed
                </a>
            </div>
        </div>

        <div class="row justify-content-center">
            <div class="col-xl-6 col-lg-8">
                <div class="intelligence-card animate__animated animate__fadeInUp">
                    <h5 class="section-title mb-4">Status Configuration</h5>
                    
                    <?php if($error): ?>
                        <div class="alert alert-danger mb-4">
                            <i class="fa-solid fa-circle-exclamation me-2"></i> <?php echo $error; ?>
                        </div>
                    <?php endif; ?>

                    <form method="post">
                        <div class="mb-3">
                            <label class="form-label fs-xs text-uppercase fw-bold">Feedback ID</label>
                            <input type="text" class="form-control bg-light" value="#RE-<?php echo $row['id']; ?>" disabled>
                        </div>
                        
                        <div class="mb-4">
                            <label class="form-label fs-xs text-uppercase fw-bold">Voyager Feedback Content</label>
                            <div class="p-3 bg-slate-50 rounded border text-muted small fst-italic">
                                "<?php echo htmlspecialchars($row['feedback']); ?>"
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fs-xs text-uppercase fw-bold">Publication Status</label>
                            <select name="status" class="form-select">
                                <option value="1" <?php if($row['status'] == '1') echo 'selected'; ?>>Published (Testimonial)</option>
                                <option value="0" <?php if($row['status'] == '0') echo 'selected'; ?>>Hidden / Archived</option>
                            </select>
                            <div class="form-text text-muted mt-2">
                                <i class="fa-solid fa-circle-info me-1"></i> Select 'Published' to feature this feedback on the public testimonial reel.
                            </div>
                        </div>

                        <div class="d-grid pt-2">
                            <button type="submit" name="update" class="btn btn-primary rounded-pill py-3 shadow-sm">
                                <i class="fa-solid fa-rotate me-2"></i>Update Status
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </main>
    <?php include("footer.php"); ?>