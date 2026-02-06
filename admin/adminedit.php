<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once("config.php");
require_once("../csrf.php");

if (!isset($_SESSION['aid'])) {
    header("Location: index.php");
    exit();
}

$error = "";
$success = "";

if (isset($_GET['id'])) {
    $admin_id = intval($_GET['id']);
    
    // Fetch admin details
    $query = "SELECT id, first_name, last_name, email FROM users WHERE id = ? AND role = 'admin'";
    $stmt = mysqli_prepare($con, $query);
    mysqli_stmt_bind_param($stmt, 'i', $admin_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if ($result && mysqli_num_rows($result) > 0) {
        $admin = mysqli_fetch_assoc($result);

        if (isset($_POST['update'])) {
            if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
                 die("Security Violation");
            }

            $first_name = trim($_POST['first_name']);
            $last_name = trim($_POST['last_name']);
            $email = trim($_POST['email']);
            $password = $_POST['password'];
            
            // Build update query
            if (!empty($password)) {
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $update_query = "UPDATE users SET first_name=?, last_name=?, email=?, password_hash=? WHERE id=? AND role='admin'";
                $stmt = mysqli_prepare($con, $update_query);
                mysqli_stmt_bind_param($stmt, 'ssssi', $first_name, $last_name, $email, $hashed_password, $admin_id);
            } else {
                $update_query = "UPDATE users SET first_name=?, last_name=?, email=? WHERE id=? AND role='admin'";
                $stmt = mysqli_prepare($con, $update_query);
                mysqli_stmt_bind_param($stmt, 'sssi', $first_name, $last_name, $email, $admin_id);
            }

            if (mysqli_stmt_execute($stmt)) {
                header("Location: adminlist.php?msg=Admin profile updated successfully&type=success");
                exit();
            } else {
                error_log("Admin Edit Error: " . mysqli_error($con));
                $error = "Failed to update admin profile. Please try again.";
            }
        }
    } else {
        header("Location: adminlist.php");
        exit();
    }
} else {
    header("Location: adminlist.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Admin Profile - ExpenseVoyage Dashboard</title>
    
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
                <h1 class="mb-1">Edit <span class="text-indigo">Admin Profile</span></h1>
                <p class="text-muted mb-0">Update the login details and profile information for this administrator.</p>
            </div>
            <div class="date-node text-end">
                <span class="badge bg-indigo-light text-indigo">Admin Settings</span>
            </div>
        </div>

        <div class="row justify-content-center">
            <div class="col-xl-6">
                <div class="intelligence-card animate__animated animate__zoomIn">
                    <h5 class="section-title mb-4">Admin Details</h5>
                    
                    <?php if($error): ?>
                    <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
                    <?php endif; ?>

                    <form method="post">
                        <?php echo csrf_input(); ?>
                        <div class="row g-3">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">First Name</label>
                                <input type="text" name="first_name" class="form-control" value="<?php echo htmlspecialchars($admin['first_name']); ?>" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Last Name</label>
                                <input type="text" name="last_name" class="form-control" value="<?php echo htmlspecialchars($admin['last_name']); ?>" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Email Address</label>
                            <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($admin['email']); ?>" required>
                        </div>

                        <div class="mb-4">
                            <label class="form-label">New Password</label>
                            <input type="password" name="password" class="form-control" placeholder="••••••••">
                            <div class="form-text text-muted fs-xs mt-2">
                                <i class="fa-solid fa-circle-info me-1"></i> Leave blank to keep the current password.
                            </div>
                        </div>

                        <hr class="my-4 opacity-10">

                        <div class="d-flex justify-content-between align-items-center">
                            <a href="adminlist.php" class="text-indigo text-decoration-none fw-medium fs-sm">
                                <i class="fa-solid fa-arrow-left-long me-2"></i> Back to Admin List
                            </a>
                            <button type="submit" name="update" class="btn btn-primary rounded-pill px-4">
                                <i class="fa-solid fa-shield-halved me-2"></i> Save Changes
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </main>
    <?php include("footer.php"); ?>
