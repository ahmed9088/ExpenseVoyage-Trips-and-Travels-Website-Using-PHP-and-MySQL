<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once("config.php");
require_once("../csrf.php"); // Include CSRF protection

// Session Check
if(!isset($_SESSION['aid'])) {
    header("location:index.php");
    exit();
}

$error = "";
$success = "";

if(isset($_POST['update'])) {
    // CSRF Check
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
         die("Security Error: Invalid Token");
    }

    $aid = intval($_GET['id']);
    $title = trim($_POST['utitle']);
    $content = trim($_POST['ucontent']);
    
    // Handle Image
    $img_name = "";
    $upload_ok = true;

    if (isset($_FILES['aimage']) && $_FILES['aimage']['error'] === 0) {
        $file_tmp = $_FILES['aimage']['tmp_name'];
        $file_type = $_FILES['aimage']['type'];
        $file_size = $_FILES['aimage']['size'];
        $file_ext = strtolower(pathinfo($_FILES['aimage']['name'], PATHINFO_EXTENSION));

        $allowed_exts = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];

        if (!in_array($file_ext, $allowed_exts) || !in_array($file_type, $allowed_types)) {
            $error = "Invalid file type. Only JPG, PNG, GIF, and WEBP allowed.";
            $upload_ok = false;
        } elseif ($file_size > 5 * 1024 * 1024) { // 5MB limit
            $error = "File too large. Max 5MB.";
            $upload_ok = false;
        } else {
            // Generate clean unique filename
            $img_name = uniqid('about_') . '.' . $file_ext;
            $target = '../upload/' . $img_name;
            
            if (!move_uploaded_file($file_tmp, $target)) {
                 $error = "Failed to upload image.";
                 $upload_ok = false;
            }
        }
    }

    if ($upload_ok) {
        if ($img_name) {
            $sql = "UPDATE about SET title = ?, content = ?, image = ? WHERE id = ?";
            $stmt = $con->prepare($sql);
            $stmt->bind_param('sssi', $title, $content, $img_name, $aid);
        } else {
            $sql = "UPDATE about SET title = ?, content = ? WHERE id = ?";
            $stmt = $con->prepare($sql);
            $stmt->bind_param('ssi', $title, $content, $aid);
        }

        if($stmt->execute()){
            $success = "About page content updated successfully!";
        } else {
             // Generic error for user, log specific error
             error_log("Database Error: " . $stmt->error);
             $error = "Failed to update about page. Please try again.";
        }
        $stmt->close();
    }
}

// Fetch About Data
if(!isset($_GET['id'])) { header("Location: dashboard.php"); exit(); }
$id = intval($_GET['id']);
$stmt = $con->prepare("SELECT * FROM about WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$res = $stmt->get_result();
$about_data = $res->fetch_assoc();

if(!$about_data) { header("Location: dashboard.php"); exit(); }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit About Page - ExpenseVoyage Dashboard</title>
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
                <h1 class="mb-1">Edit <span class="text-indigo">About Page</span></h1>
                <p class="text-muted mb-0">Update the content and images for the 'About' page.</p>
            </div>
            <div class="date-node text-end">
                <span class="badge bg-indigo-light text-indigo">Active Content Management</span>
            </div>
        </div>

        <div class="row justify-content-center">
            <div class="col-xl-8 col-lg-10">
                <div class="intelligence-card animate__animated animate__fadeInUp">
                    <h5 class="section-title mb-4">Edit About Content</h5>
                    
                    <?php if($success): ?>
                        <div class="alert alert-success mb-4">
                            <i class="fa-solid fa-fingerprint me-2"></i> <?php echo htmlspecialchars($success); ?>
                        </div>
                    <?php endif; ?>
                    
                    <?php if($error): ?>
                        <div class="alert alert-danger mb-4">
                            <i class="fa-solid fa-triangle-exclamation me-2"></i> <?php echo htmlspecialchars($error); ?>
                        </div>
                    <?php endif; ?>

                    <form method="post" enctype="multipart/form-data">
                        <?php echo csrf_input(); ?>
                        <div class="mb-3">
                            <label class="form-label fs-xs text-uppercase fw-bold">Main Title</label>
                            <input type="text" name="utitle" class="form-control" value="<?php echo htmlspecialchars($about_data['title']); ?>" required>
                        </div>
                        <div class="mb-4">
                            <label class="form-label fs-xs text-uppercase fw-bold text-indigo">About Content</label>
                            <textarea name="ucontent" class="form-control" rows="12" required><?php echo htmlspecialchars($about_data['content']); ?></textarea>
                        </div>
                        <div class="mb-4">
                            <label class="form-label fs-xs text-uppercase fw-bold">About Image</label>
                            <input type="file" name="aimage" class="form-control" accept="image/*">
                            <div class="mt-4 p-3 bg-slate-50 rounded border border-dashed text-center">
                                <label class="fs-xs text-muted d-block mb-3 text-uppercase fw-bold">Current Image</label>
                                <img src="../img/about/<?php echo htmlspecialchars($about_data['image']); ?>" class="rounded shadow-lg img-fluid border border-indigo" style="max-height: 300px;">
                            </div>
                        </div>
                        <div class="d-grid pt-2">
                            <button type="submit" name="update" class="btn btn-primary rounded-pill py-3 shadow-sm">
                                <i class="fa-solid fa-save me-2"></i>Save Changes
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </main>
    <?php include("footer.php"); ?>