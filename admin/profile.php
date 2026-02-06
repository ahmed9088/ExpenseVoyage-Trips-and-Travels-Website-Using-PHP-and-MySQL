<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once("config.php");

if(!isset($_SESSION['aid'])) {
    header("location:index.php");
    exit();
}

$admin_id = $_SESSION['aid'];
$query = "SELECT first_name, last_name, email, phone, role, created_at FROM users WHERE id = ? AND role = 'admin' LIMIT 1";
$stmt = mysqli_prepare($con, $query);
mysqli_stmt_bind_param($stmt, 'i', $admin_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$admin_data = mysqli_fetch_assoc($result);

if (!$admin_data) {
    session_destroy();
    header("location:index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Profile - ExpenseVoyage Dashboard</title>
    <link rel="shortcut icon" type="image/x-icon" href="../img/favicon-32x32.png">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/admin_modern.css">
    <style>
        .profile-container {
            max-width: 900px;
            margin: 0 auto;
        }
        .profile-avatar-large {
            width: 120px;
            height: 120px;
            border-radius: 32px;
            object-fit: cover;
            border: 4px solid var(--slate-100);
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>

<div class="admin-wrapper">
    <?php include("header.php"); ?>

    <main class="modern-main">
        <div class="page-header d-flex justify-content-between align-items-center mb-5">
            <div class="animate__animated animate__fadeIn">
                <h1 class="mb-1">Admin <span class="text-indigo">Profile</span></h1>
                <p class="text-muted mb-0">Manage your personal information and account details.</p>
            </div>
            <div class="date-node text-end">
                <span class="badge bg-indigo-light text-indigo">Administrator Account</span>
            </div>
        </div>

        <div class="profile-container">
            <div class="intelligence-card p-0 overflow-hidden mb-5 animate__animated animate__fadeInUp">
                <div class="profile-masthead p-5 bg-indigo-light d-flex align-items-center">
                    <img src="../img/Aliza.jpg" class="profile-avatar-large me-4" alt="Admin Avatar">
                    <div>
                        <h2 class="mb-1 fw-bold"><?php echo htmlspecialchars($admin_data['first_name'] . ' ' . ($admin_data['last_name'] ?? '')); ?></h2>
                        <div class="d-flex align-items-center text-indigo">
                            <i class="fa-solid fa-user-shield me-2"></i>
                            <span class="text-uppercase fw-bold ls-1" style="font-size: 0.8rem;">Admin User</span>
                        </div>
                    </div>
                </div>

                <div class="p-5">
                    <div class="row g-5">
                        <div class="col-md-7">
                            <h5 class="section-title mb-4">Personal Information</h5>
                            
                            <div class="mb-4">
                                <label class="text-muted fs-small text-uppercase fw-bold mb-1">Full Name</label>
                                <p class="mb-0 fs-5"><?php echo htmlspecialchars($admin_data['first_name'] . ' ' . ($admin_data['last_name'] ?? '')); ?></p>
                            </div>

                            <div class="mb-4">
                                <label class="text-muted fs-small text-uppercase fw-bold mb-1">Email Address</label>
                                <p class="mb-0 fs-5 text-indigo fw-medium"><?php echo htmlspecialchars($admin_data['email']); ?></p>
                            </div>

                            <div>
                                <label class="text-muted fs-small text-uppercase fw-bold mb-1">Phone Number</label>
                                <p class="mb-0 fs-5"><?php echo htmlspecialchars($admin_data['phone'] ?? 'No phone added'); ?></p>
                            </div>
                        </div>

                        <div class="col-md-5">
                            <h5 class="section-title mb-4">Account Details</h5>
                            
                            <div class="intelligence-card bg-slate-50 border-0 p-4 mb-4">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="stat-icon bg-white shadow-sm me-3" style="width: 40px; height: 40px; font-size: 1rem;">
                                        <i class="fa-solid fa-clock-rotate-left"></i>
                                    </div>
                                    <div>
                                        <label class="text-muted d-block fs-xs">Member Since</label>
                                        <span class="fw-bold"><?php echo date('M d, Y', strtotime($admin_data['created_at'])); ?></span>
                                    </div>
                                </div>
                                <div class="d-grid">
                                    <button class="btn btn-outline-indigo btn-sm rounded-pill">View Audit Logs</button>
                                </div>
                            </div>

                            <div class="d-grid gap-3">
                                <button class="btn btn-primary rounded-pill py-3">
                                    <i class="fa-solid fa-key me-2"></i>Change Password
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
    <?php include("footer.php"); ?>