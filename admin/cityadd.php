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

if(isset($_POST['insertcity'])){
    $country = mysqli_real_escape_string($con, $_POST['country']);
    $city = mysqli_real_escape_string($con, $_POST['city']);
    
    // Handle Image
    $cover_image = "";
    if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
        $img_name = time() . '_' . $_FILES['image']['name'];
        $target = '../img/cityimages/' . $img_name;
        if (move_uploaded_file($_FILES['image']['tmp_name'], $target)) {
            $cover_image = "img/cityimages/" . $img_name;
        }
    }

    if($cover_image){
        $sql = "INSERT INTO city (country_name, city_name, cover_image) VALUES (?, ?, ?)";
        $stmt = $con->prepare($sql);
        $stmt->bind_param('sss', $country, $city, $cover_image);
        if($stmt->execute()){
            $success = "Geography Node added successfully!";
        } else {
            $error = "Strategic insertion failed: " . $stmt->error;
        }
    } else {
        $error = "Intelligence Capture (Image) required for geography node.";
    }
}

// Handle Record Deletion
if(isset($_GET['delete_id'])) {
    $did = intval($_GET['delete_id']);
    // Fetch image path to delete
    $res = $con->query("SELECT cover_image FROM city WHERE city_id = $did");
    if($row = $res->fetch_assoc()) {
        if(!empty($row['cover_image']) && file_exists("../" . $row['cover_image'])) {
            @unlink("../" . $row['cover_image']);
        }
    }
    $con->query("DELETE FROM city WHERE city_id = $did");
    header("Location: cityadd.php?msg=Node purged from registry&type=success");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>City Management - ExpenseVoyage Dashboard</title>
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
                <h1 class="mb-1">City <span class="text-indigo">Management</span></h1>
                <p class="text-muted mb-0">Manage all your travel destinations and pictures.</p>
            </div>
            <div class="date-node text-end">
                <span class="badge bg-indigo-light text-indigo">Unified Grid Analysis</span>
            </div>
        </div>

        <div class="row g-4 mb-5">
            <div class="col-xl-4 col-lg-5">
                <div class="intelligence-card animate__animated animate__fadeInLeft">
                    <h5 class="section-title mb-4">Add New City</h5>
                    
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
                        <div class="mb-3">
                            <label class="form-label fs-xs text-uppercase fw-bold">Country / Sovereignty</label>
                            <input type="text" name="country" class="form-control" required placeholder="e.g. Pakistan">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fs-xs text-uppercase fw-bold">City Name</label>
                            <input type="text" name="city" class="form-control" required placeholder="e.g. Hunza Valley">
                        </div>
                        <div class="mb-4">
                            <label class="form-label fs-xs text-uppercase fw-bold">Main Photo</label>
                            <input type="file" name="image" class="form-control" accept="image/*" required>
                        </div>
                        <div class="d-grid">
                            <button type="submit" name="insertcity" class="btn btn-primary rounded-pill py-3 shadow-sm">
                                <i class="fa-solid fa-earth-asia me-2"></i>Add City
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="col-xl-8 col-lg-7">
                <div class="intelligence-card animate__animated animate__fadeInRight">
                    <h5 class="section-title mb-4">City List</h5>
                    
                    <?php if(isset($_GET['msg'])): ?>
                        <div class="alert alert-success fs-xs mb-4">
                            <i class="fa-solid fa-satellite me-2"></i> <?php echo htmlspecialchars($_GET['msg']); ?>
                        </div>
                    <?php endif; ?>

                    <div class="table-responsive">
                        <table id="city-table" class="table modern-table align-middle">
                            <thead class="bg-indigo-light">
                                <tr>
                                    <th>City Name</th>
                                    <th>Country</th>
                                    <th>Photo</th>
                                    <th class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $query = $con->query("SELECT * FROM city ORDER BY city_id DESC");
                                while($row = $query->fetch_assoc()):
                                ?>
                                <tr>
                                    <td>
                                        <div class="fw-bold text-slate-800"><?php echo htmlspecialchars($row['city_name']); ?></div>
                                        <div class="fs-xs text-muted">Node ID: #GEO-<?php echo $row['city_id']; ?></div>
                                    </td>
                                    <td>
                                        <span class="badge bg-slate-100 text-slate-600 px-3 py-2 rounded-pill fs-xs">
                                            <?php echo htmlspecialchars($row['country_name']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <img src="../<?php echo $row['cover_image']; ?>" width="80" height="40" class="rounded object-fit-cover shadow-sm">
                                    </td>
                                    <td class="text-end">
                                        <div class="d-flex justify-content-end gap-1">
                                            <a href="cityedit.php?id=<?php echo $row['city_id']; ?>" class="btn btn-sm btn-outline-indigo border-0">
                                                <i class="fa-solid fa-pen-to-square"></i>
                                            </a>
                                            <a href="?delete_id=<?php echo $row['city_id']; ?>" class="btn btn-sm btn-outline-danger border-0" onclick="return confirm('Delete this city?')">
                                                <i class="fa-solid fa-trash-can"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </main>
    <?php include("footer.php"); ?>