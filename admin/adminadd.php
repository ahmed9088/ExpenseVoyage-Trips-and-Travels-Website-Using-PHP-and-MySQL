<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once("config.php");
require_once("../csrf.php");

// Session Check
if(!isset($_SESSION['aid'])) {
    header("location:index.php");
    exit();
}

$success = "";
$error = "";

if(isset($_POST['add_admin'])){
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        die("Security Error: Invalid Token");
    }

    $fname = trim($_POST['fname']);
    $lname = trim($_POST['lname']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $pass = $_POST['pass'];
    
    // Secure Password Hashing
    $hashed_pass = password_hash($pass, PASSWORD_DEFAULT);

    // Check if email exists (Precise Check)
    $stmt = $con->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();
    
    if($stmt->num_rows > 0) {
        $error = "This enterprise email is already registered.";
    } else {
        $stmt->close();
        // Insert into users table
        // Correcting column name 'password' to 'password_hash' based on schema
        $sql = "INSERT INTO users (first_name, last_name, email, phone, password_hash, role, created_at) VALUES (?, ?, ?, ?, ?, 'admin', NOW())";
        $stmt = $con->prepare($sql);
        $stmt->bind_param('sssss', $fname, $lname, $email, $phone, $hashed_pass);
        
        if($stmt->execute()){
            $success = "Administrator account created successfully.";
        } else {
            error_log("DB Error: " . $stmt->error);
            $error = "Failed to create administrator. Please try again.";
        }
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Admin - ExpenseVoyage Dashboard</title>
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
                <h1 class="mb-1">Add <span class="text-indigo">Administrator</span></h1>
                <p class="text-muted mb-0">Create a new administrator account to manage the dashboard.</p>
            </div>
            <div class="date-node text-end">
                <a href="adminlist.php" class="btn btn-outline-indigo rounded-pill px-4">
                    <i class="fa-solid fa-chevron-left me-2"></i>Admin List
                </a>
            </div>
        </div>

        <div class="row justify-content-center">
            <div class="col-xl-6 col-lg-8">
                <div class="intelligence-card animate__animated animate__fadeInUp">
                    <h5 class="section-title mb-4">New Admin Form</h5>
                    
                    <?php if($success): ?>
                        <div class="alert alert-success fs-xs mb-4">
                            <i class="fa-solid fa-user-shield me-2"></i> <?php echo htmlspecialchars($success); ?>
                        </div>
                    <?php endif; ?>
                    
                    <?php if($error): ?>
                        <div class="alert alert-danger fs-xs mb-4">
                            <i class="fa-solid fa-circle-exclamation me-2"></i> <?php echo htmlspecialchars($error); ?>
                        </div>
                    <?php endif; ?>

                    <form method="post">
                        <?php echo csrf_input(); ?>
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label fs-xs text-uppercase fw-bold">First Name</label>
                                <input type="text" name="fname" class="form-control" required placeholder="e.g. Sarah">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fs-xs text-uppercase fw-bold">Last Name</label>
                                <input type="text" name="lname" class="form-control" required placeholder="e.g. Connor">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fs-xs text-uppercase fw-bold text-indigo">Email Address</label>
                            <input type="email" name="email" class="form-control" required placeholder="admin@example.com">
                        </div>

                        <div class="mb-3">
                            <label class="form-label fs-xs text-uppercase fw-bold">Phone Number</label>
                            <input type="text" name="phone" class="form-control" placeholder="+1 (555) 000-0000">
                        </div>

                        <div class="mb-4">
                            <label class="form-label fs-xs text-uppercase fw-bold">Password</label>
                            <input type="password" name="pass" class="form-control" required placeholder="••••••••">
                            <div class="form-text text-muted fs-xs mt-1">
                                <i class="fa-solid fa-lock me-1"></i> Use a strong password for better security.
                            </div>
                        </div>

                        <div class="d-grid pt-2">
                            <button type="submit" name="add_admin" class="btn btn-primary rounded-pill py-3 shadow-sm">
                                <i class="fa-solid fa-user-plus me-2"></i>Add Administrator
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </main>
    <?php include("footer.php"); ?>
