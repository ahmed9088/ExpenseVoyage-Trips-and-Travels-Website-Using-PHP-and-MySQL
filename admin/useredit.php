<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once("config.php");

if (!isset($_SESSION['aid'])) {
    header("location:index.php");
    exit();
}

$error = "";
$success = "";

if (isset($_POST['update_user'])) {
    $uid = intval($_POST['user_id']);
    $first_name = mysqli_real_escape_string($con, $_POST['first_name']);
    $last_name = mysqli_real_escape_string($con, $_POST['last_name']);
    $email = mysqli_real_escape_string($con, $_POST['email']);
    $is_verified = isset($_POST['is_verified']) ? 1 : 0;

    // Handle Image Update
    $image_sql = "";
    if (isset($_FILES['user_image']) && $_FILES['user_image']['error'] === 0) {
        $img_name = time() . '_' . $_FILES['user_image']['name'];
        $target = '../img/userimages/' . $img_name;
        if (move_uploaded_file($_FILES['user_image']['tmp_name'], $target)) {
            $image_sql = ", user_image = 'img/userimages/$img_name'";
        }
    }

    $sql = "UPDATE users SET first_name = ?, last_name = ?, email = ?, is_verified = ? $image_sql WHERE id = ?";
    $stmt = $con->prepare($sql);
    $stmt->bind_param('sssii', $first_name, $last_name, $email, $is_verified, $uid);
    
    if ($stmt->execute()) {
        $success = "User profile updated successfully!";
    } else {
        $error = "Failed to update profile: " . $stmt->error;
    }
}

// Fetch User Data
if(!isset($_GET['id'])) { header("Location: userlist.php"); exit(); }
$id = intval($_GET['id']);
$res = $con->query("SELECT * FROM users WHERE id = $id AND role = 'traveler'");
$user = $res->fetch_assoc();
if(!$user) { header("Location: userlist.php"); exit(); }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User Profile - ExpenseVoyage Dashboard</title>
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
                <h1 class="mb-1">Edit User <span class="text-indigo">Profile</span></h1>
                <p class="text-muted mb-0">Change user details and verification status for #USR-<?php echo $id; ?>.</p>
            </div>
            <div class="date-node text-end">
                <a href="userlist.php" class="btn btn-outline-indigo rounded-pill px-4">
                    <i class="fa-solid fa-users-gear me-2"></i>User List
                </a>
            </div>
        </div>

        <div class="row justify-content-center">
            <div class="col-xl-6 col-lg-8">
                <div class="intelligence-card animate__animated animate__fadeInUp">
                    <?php if($success): ?>
                        <div class="alert alert-success fs-xs mb-4">
                            <i class="fa-solid fa-circle-check me-2"></i> <?php echo $success; ?>
                        </div>
                    <?php endif; ?>
                    
                    <?php if($error): ?>
                        <div class="alert alert-danger fs-xs mb-4">
                            <i class="fa-solid fa-circle-exclamation me-2"></i> <?php echo $error; ?>
                        </div>
                    <?php endif; ?>

                    <form method="post" enctype="multipart/form-data">
                        <input type="hidden" name="user_id" value="<?php echo $id; ?>">
                        
                        <div class="row g-3">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fs-xs text-uppercase fw-bold">First Name</label>
                                <input type="text" name="first_name" class="form-control" value="<?php echo htmlspecialchars($user['first_name']); ?>" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fs-xs text-uppercase fw-bold">Last Name</label>
                                <input type="text" name="last_name" class="form-control" value="<?php echo htmlspecialchars($user['last_name']); ?>" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fs-xs text-uppercase fw-bold">Email Address</label>
                            <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                        </div>

                        <div class="mb-4 d-flex align-items-center gap-3 bg-slate-50 p-3 rounded-3 border">
                            <i class="fa-solid fa-user-shield text-indigo fs-4"></i>
                            <div class="form-check form-switch m-0">
                                <input class="form-check-input" type="checkbox" name="is_verified" id="verifySwitch" <?php echo $user['is_verified'] ? 'checked' : ''; ?>>
                                <label class="form-check-label fw-bold text-slate-700" for="verifySwitch">Verified Account</label>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fs-xs text-uppercase fw-bold">Profile Photo</label>
                            <input type="file" name="user_image" class="form-control" accept="image/*">
                            <div class="mt-3 text-center">
                                <?php if($user['user_image']): ?>
                                    <img src="../<?php echo $user['user_image']; ?>" width="100" height="100" class="rounded-circle object-fit-cover shadow-sm border border-3 border-white">
                                    <div class="fs-xs text-muted mt-2">Current Photo</div>
                                <?php else: ?>
                                    <div class="bg-slate-100 p-4 rounded text-muted fs-xs">No photo uploaded</div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="d-grid pt-2">
                            <button type="submit" name="update_user" class="btn btn-primary rounded-pill py-3 shadow-sm">
                                <i class="fa-solid fa-id-card me-2"></i>Save Changes
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </main>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
