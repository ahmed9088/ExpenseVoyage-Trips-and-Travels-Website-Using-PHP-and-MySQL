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

if (isset($_POST['update_trip'])) {
    $trip_id = intval($_POST['trip_id']);
    $trip_name = mysqli_real_escape_string($con, $_POST['trip_name']);
    $destination = mysqli_real_escape_string($con, $_POST['destination']);
    $travel_type = mysqli_real_escape_string($con, $_POST['travel_type']);
    $budget = floatval($_POST['budget']);
    $starts_date = $_POST['starts_date'];
    $end_date = $_POST['end_date'];
    $duration_days = intval($_POST['duration_days']);
    $seats_available = intval($_POST['seats_available']);
    $stars = intval($_POST['stars']);
    $description = mysqli_real_escape_string($con, $_POST['description']);
    $agent_id = intval($_POST['user_id']);
    $vehicle_name = mysqli_real_escape_string($con, $_POST['vehicle_name']);
    $is_ac = isset($_POST['is_ac']) ? 1 : 0;
    $departure_time = $_POST['departure_time'];

    // Handle Image Update
    $image_sql = "";
    if (isset($_FILES['trip_image']) && $_FILES['trip_image']['error'] === 0) {
        $img_name = time() . '_' . $_FILES['trip_image']['name'];
        $target = '../upload/trips/' . $img_name;
        if (move_uploaded_file($_FILES['trip_image']['tmp_name'], $target)) {
            $image_sql = ", trip_image = '$img_name'";
        }
    }

    $sql = "UPDATE trips SET 
            trip_name = ?, destination = ?, travel_type = ?, budget = ?, 
            starts_date = ?, end_date = ?, duration_days = ?, 
            seats_available = ?, stars = ?, description = ?, user_id = ?,
            vehicle_name = ?, is_ac = ?, departure_time = ?
            $image_sql WHERE trip_id = ?";
    
    $stmt = $con->prepare($sql);
    $stmt->bind_param('sssissiiissis si', $trip_name, $destination, $travel_type, $budget, $starts_date, $end_date, $duration_days, $seats_available, $stars, $description, $agent_id, $vehicle_name, $is_ac, $departure_time, $trip_id);
    
    if ($stmt->execute()) {
        $success = "Trip details updated successfully!";
    } else {
        $error = "Failed to update trip: " . $stmt->error;
    }
}

// Fetch Trip Data
if(!isset($_GET['id'])) { header("Location: tripview.php"); exit(); }
$id = intval($_GET['id']);
$res = $con->query("SELECT * FROM trips WHERE trip_id = $id");
$trip = $res->fetch_assoc();
if(!$trip) { header("Location: tripview.php"); exit(); }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Trip - ExpenseVoyage Dashboard</title>
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
                <h1 class="mb-1">Edit <span class="text-indigo">Trip</span></h1>
                <p class="text-muted mb-0">Update information for trip #<?php echo $id; ?>.</p>
            </div>
            <div class="date-node text-end">
                <a href="tripview.php" class="btn btn-outline-indigo rounded-pill px-4">
                    <i class="fa-solid fa-list-ul me-2"></i>Trip List
                </a>
            </div>
        </div>

        <div class="row justify-content-center">
            <div class="col-xl-10">
                <div class="intelligence-card animate__animated animate__fadeInUp">
                    <?php if($success): ?>
                        <div class="alert alert-success alert-dismissible fade show mb-4">
                            <i class="fa-solid fa-circle-check me-2"></i> <?php echo $success; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>
                    
                    <?php if($error): ?>
                        <div class="alert alert-danger mb-4">
                            <i class="fa-solid fa-circle-exclamation me-2"></i> <?php echo $error; ?>
                        </div>
                    <?php endif; ?>

                    <form method="post" enctype="multipart/form-data">
                        <input type="hidden" name="trip_id" value="<?php echo $id; ?>">
                        <div class="row g-4">
                            <!-- Left Column -->
                            <div class="col-lg-7">
                                <h5 class="section-title mb-4">Trip Information</h5>
                                <div class="mb-3">
                                    <label class="form-label">Trip Name</label>
                                    <input type="text" name="trip_name" class="form-control" value="<?php echo htmlspecialchars($trip['trip_name']); ?>" required>
                                </div>
                                <div class="row g-3">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Destination (City)</label>
                                        <input type="text" name="destination" class="form-control" value="<?php echo htmlspecialchars($trip['destination']); ?>" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Trip Type</label>
                                        <select name="travel_type" class="form-select">
                                            <option value="local" <?php echo $trip['travel_type'] == 'local' ? 'selected' : ''; ?>>Local Trip</option>
                                            <option value="international" <?php echo $trip['travel_type'] == 'international' ? 'selected' : ''; ?>>International Trip</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="mb-4">
                                    <label class="form-label">Description (Itinerary)</label>
                                    <textarea name="description" class="form-control" rows="5"><?php echo htmlspecialchars($trip['description']); ?></textarea>
                                </div>

                                <h5 class="section-title mb-4">Chatbot Intelligence (Vehicle Metadata)</h5>
                                <div class="row g-3">
                                    <div class="col-md-7 mb-3">
                                        <label class="form-label">Vehicle Name</label>
                                        <input type="text" name="vehicle_name" class="form-control" value="<?php echo htmlspecialchars($trip['vehicle_name']); ?>">
                                    </div>
                                    <div class="col-md-5 mb-3">
                                        <label class="form-label">Departure Time</label>
                                        <input type="time" name="departure_time" class="form-control" value="<?php echo substr($trip['departure_time'], 0, 5); ?>">
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <div class="form-check form-switch p-3 bg-light rounded-3 border">
                                        <input class="form-check-input ms-0 me-2" type="checkbox" name="is_ac" id="isAc" <?php echo $trip['is_ac'] ? 'checked' : ''; ?>>
                                        <label class="form-check-label fw-bold" for="isAc">Air Conditioned (AC) Available</label>
                                    </div>
                                </div>
                            </div>

                            <!-- Right Column -->
                            <div class="col-lg-5">
                                <h5 class="section-title mb-4">Pricing & Capacity</h5>
                                <div class="row g-3">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Price ($)</label>
                                        <input type="number" name="budget" class="form-control" step="0.01" value="<?php echo $trip['budget']; ?>" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Total Seats</label>
                                        <input type="number" name="seats_available" class="form-control" value="<?php echo $trip['seats_available']; ?>" required>
                                    </div>
                                </div>
                                <div class="row g-3">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Start Date</label>
                                        <input type="date" name="starts_date" class="form-control" value="<?php echo $trip['starts_date']; ?>" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">End Date</label>
                                        <input type="date" name="end_date" class="form-control" value="<?php echo $trip['end_date']; ?>" required>
                                    </div>
                                </div>
                                <div class="row g-3">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Duration (Days)</label>
                                        <input type="number" name="duration_days" class="form-control" value="<?php echo $trip['duration_days']; ?>" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Trip Rating</label>
                                        <select name="stars" class="form-select">
                                            <?php for($s=1; $s<=5; $s++): ?>
                                            <option value="<?php echo $s; ?>" <?php echo $trip['stars'] == $s ? 'selected' : ''; ?>><?php echo $s; ?> Star</option>
                                            <?php endfor; ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Assigned Agent</label>
                                    <select name="user_id" class="form-select" required>
                                        <?php
                                        $agents = $con->query("SELECT id, first_name, last_name FROM users WHERE role = 'agent' ORDER BY first_name ASC");
                                        while($agent = $agents->fetch_assoc()):
                                        ?>
                                            <option value="<?php echo $agent['id']; ?>" <?php echo $trip['user_id'] == $agent['id'] ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($agent['first_name'] . ' ' . $agent['last_name']); ?>
                                            </option>
                                        <?php endwhile; ?>
                                    </select>
                                </div>
                                <div class="mb-4">
                                    <label class="form-label">Trip Photo (Optional)</label>
                                    <input type="file" name="trip_image" class="form-control" accept="image/*">
                                    <?php if($trip['trip_image']): ?>
                                        <div class="mt-2 fs-xs text-muted">Current: <?php echo $trip['trip_image']; ?></div>
                                    <?php endif; ?>
                                </div>
                                <div class="d-grid pt-2">
                                    <button type="submit" name="update_trip" class="btn btn-primary rounded-pill py-3 shadow-sm">
                                        <i class="fa-solid fa-floppy-disk me-2"></i>Save Changes
                                    </button>
                                </div>
                            </div>
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
