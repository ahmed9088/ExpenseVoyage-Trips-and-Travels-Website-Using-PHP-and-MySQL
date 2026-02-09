<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once("config.php");

// Check if admin is logged in
if(!isset($_SESSION['aid'])) {
    header("location:index.php");
    exit();
}

$error = "";
$success = "";

if (isset($_POST['add_trip'])) {
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
    $user_id = intval($_POST['user_id']); // Assigned Agent
    $vehicle_name = mysqli_real_escape_string($con, $_POST['vehicle_name']);
    $is_ac = isset($_POST['is_ac']) ? 1 : 0;
    $departure_time = $_POST['departure_time'];

    // Handle Image
    $trip_image = "";
    if (isset($_FILES['trip_image']) && $_FILES['trip_image']['error'] === 0) {
        $img_name = time() . '_' . $_FILES['trip_image']['name'];
        $target = '../upload/trips/' . $img_name;
        if (move_uploaded_file($_FILES['trip_image']['tmp_name'], $target)) {
            $trip_image = $img_name;
        }
    }

    $sql = "INSERT INTO trips (trip_name, destination, travel_type, budget, starts_date, end_date, duration_days, seats_available, stars, description, trip_image, user_id, vehicle_name, is_ac, departure_time, booked_seats) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 0)";
    
    $stmt = $con->prepare($sql);
    $stmt->bind_param('sssissiiissis s', $trip_name, $destination, $travel_type, $budget, $starts_date, $end_date, $duration_days, $seats_available, $stars, $description, $trip_image, $user_id, $vehicle_name, $is_ac, $departure_time);
    
    if ($stmt->execute()) {
        $success = "Trip Added Successfully!";
    } else {
        $error = "Failed to add trip: " . $stmt->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Trip - ExpenseVoyage Dashboard</title>
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
                <h1 class="mb-1">Add New <span class="text-indigo">Trip</span></h1>
                <p class="text-muted mb-0">Fill in the details below to create a new trip.</p>
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
                        <div class="row g-4">
                            <!-- Left Column: Core Data -->
                            <div class="col-lg-7">
                                <h5 class="section-title mb-4">Trip Information</h5>
                                <div class="mb-3">
                                    <label class="form-label">Trip Name</label>
                                    <input type="text" name="trip_name" class="form-control" required placeholder="e.g. Northern Lights Discovery">
                                </div>
                                <div class="row g-3">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Destination (City)</label>
                                        <input type="text" name="destination" class="form-control" required placeholder="City or Region">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Trip Type</label>
                                        <select name="travel_type" class="form-select">
                                            <option value="local">Local Trip</option>
                                            <option value="international">International Trip</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="mb-4">
                                    <label class="form-label">Description (Itinerary)</label>
                                    <textarea name="description" class="form-control" rows="5" placeholder="Full itinerary and details..."></textarea>
                                </div>

                                <h5 class="section-title mb-4">Chatbot Intelligence (Vehicle Metadata)</h5>
                                <div class="row g-3">
                                    <div class="col-md-7 mb-3">
                                        <label class="form-label">Vehicle Name</label>
                                        <input type="text" name="vehicle_name" class="form-control" placeholder="e.g. Toyota Hiace Grand" value="Standard Bus">
                                    </div>
                                    <div class="col-md-5 mb-3">
                                        <label class="form-label">Departure Time</label>
                                        <input type="time" name="departure_time" class="form-control" value="08:00">
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <div class="form-check form-switch p-3 bg-light rounded-3 border">
                                        <input class="form-check-input ms-0 me-2" type="checkbox" name="is_ac" id="isAc" checked>
                                        <label class="form-check-label fw-bold" for="isAc">Air Conditioned (AC) Available</label>
                                    </div>
                                </div>
                            </div>

                            <!-- Right Column: Dynamics -->
                            <div class="col-lg-5">
                                <h5 class="section-title mb-4">Pricing & Capacity</h5>
                                <div class="row g-3">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Price ($)</label>
                                        <input type="number" name="budget" class="form-control" step="0.01" required placeholder="0.00">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Total Seats</label>
                                        <input type="number" name="seats_available" class="form-control" required placeholder="Total slots">
                                    </div>
                                </div>
                                <div class="row g-3">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Start Date</label>
                                        <input type="date" name="starts_date" class="form-control" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">End Date</label>
                                        <input type="date" name="end_date" class="form-control" required>
                                    </div>
                                </div>
                                <div class="row g-3">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Duration (Days)</label>
                                        <input type="number" name="duration_days" class="form-control" required placeholder="e.g. 7">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Trip Rating</label>
                                        <select name="stars" class="form-select">
                                            <option value="5">5 Star - Luxury</option>
                                            <option value="4">4 Star - Premium</option>
                                            <option value="3">3 Star - Good</option>
                                            <option value="2">2 Star - Budget</option>
                                            <option value="1">1 Star - Basic</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Assigned Agent</label>
                                    <select name="user_id" class="form-select" required>
                                        <option value="">Select an Agent</option>
                                        <?php
                                        $agents = $con->query("SELECT id, first_name, last_name FROM users WHERE role = 'agent' ORDER BY first_name ASC");
                                        while($agent = $agents->fetch_assoc()):
                                        ?>
                                            <option value="<?php echo $agent['id']; ?>"><?php echo htmlspecialchars($agent['first_name'] . ' ' . $agent['last_name']); ?></option>
                                        <?php endwhile; ?>
                                    </select>
                                </div>
                                <div class="mb-4">
                                    <label class="form-label">Trip Image</label>
                                    <input type="file" name="trip_image" class="form-control" accept="image/*">
                                </div>
                                <div class="d-grid pt-2">
                                    <button type="submit" name="add_trip" class="btn btn-primary rounded-pill py-3 shadow-sm">
                                        <i class="fa-solid fa-plus me-2"></i>Add Trip Now
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </main>
    <?php include("footer.php"); ?>