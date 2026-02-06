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

$error = "";
$success = "";

if(isset($_POST['update_city'])){
    $city_id = intval($_POST['city_id']);
    $country = mysqli_real_escape_string($con, $_POST['country']);
    $city = mysqli_real_escape_string($con, $_POST['city']);
    
    // Handle Image Update
    $cover_image_sql = "";
    if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
        $img_name = time() . '_' . $_FILES['image']['name'];
        $target = '../img/cityimages/' . $img_name;
        if (move_uploaded_file($_FILES['image']['tmp_name'], $target)) {
            $cover_image_sql = ", cover_image = 'img/cityimages/$img_name'";
            // Delete old image if needed (optional)
        }
    }

    $sql = "UPDATE city SET country_name = ?, city_name = ? $cover_image_sql WHERE city_id = ?";
    $stmt = $con->prepare($sql);
    $stmt->bind_param('ssi', $country, $city, $city_id);
    if($stmt->execute()){
        $success = "City details updated successfully!";
    } else {
        $error = "Failed to update city: " . $stmt->error;
    }
}

// Fetch City Data
if(!isset($_GET['id'])) { header("Location: cityadd.php"); exit(); }
$id = intval($_GET['id']);
$res = $con->query("SELECT * FROM city WHERE city_id = $id");
$city_data = $res->fetch_assoc();
if(!$city_data) { header("Location: cityadd.php"); exit(); }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit City - ExpenseVoyage Dashboard</title>
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
                <h1 class="mb-1">Edit <span class="text-indigo">City</span></h1>
                <p class="text-muted mb-0">Update information and images for this location.</p>
            </div>
            <div class="date-node text-end">
                <a href="cityadd.php" class="btn btn-outline-indigo rounded-pill px-4">
                    <i class="fa-solid fa-chevron-left me-2"></i>City List
                </a>
            </div>
        </div>

        <div class="row justify-content-center">
            <div class="col-xl-6 col-lg-8">
                <div class="intelligence-card animate__animated animate__fadeInUp">
                    <h5 class="section-title mb-4">City Details</h5>
                    
                    <?php if($success): ?>
                        <div class="alert alert-success mb-4">
                            <i class="fa-solid fa-circle-check me-2"></i> <?php echo $success; ?>
                        </div>
                    <?php endif; ?>
                    
                    <?php if($error): ?>
                        <div class="alert alert-danger mb-4">
                            <i class="fa-solid fa-circle-exclamation me-2"></i> <?php echo $error; ?>
                        </div>
                    <?php endif; ?>

                    <form method="post" enctype="multipart/form-data">
                        <input type="hidden" name="city_id" value="<?php echo $city_data['city_id']; ?>">
                        
                        <div class="mb-3">
                            <label class="form-label fs-xs text-uppercase fw-bold">Country</label>
                            <input type="text" name="country" class="form-control" value="<?php echo htmlspecialchars($city_data['country_name']); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fs-xs text-uppercase fw-bold">City Name</label>
                            <input type="text" name="city" class="form-control" value="<?php echo htmlspecialchars($city_data['city_name']); ?>" required>
                        </div>
                        <div class="mb-4">
                            <label class="form-label fs-xs text-uppercase fw-bold">Update Photo (Optional)</label>
                            <input type="file" name="image" class="form-control" accept="image/*">
                            <div class="mt-3">
                                <label class="fs-xs text-muted d-block mb-2">Current Photo:</label>
                                <img src="../<?php echo $city_data['cover_image']; ?>" width="100%" height="150" class="rounded object-fit-cover shadow-sm">
                            </div>
                        </div>
                        <div class="d-grid pt-2">
                            <button type="submit" name="update_city" class="btn btn-primary rounded-pill py-3 shadow-sm">
                                <i class="fa-solid fa-floppy-disk me-2"></i>Save Changes
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </main>
    <?php include("footer.php"); ?>
