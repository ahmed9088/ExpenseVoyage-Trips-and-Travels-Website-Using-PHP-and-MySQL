<?php
session_start();
include 'admin/config.php';

if (!isset($_SESSION['userid'])) {
    echo json_encode(['status' => 'error', 'message' => 'Please login to save voyages.']);
    exit();
}

$userid = $_SESSION['userid'];
$trip_id = intval($_POST['trip_id'] ?? 0);
$action = $_POST['action'] ?? 'toggle';

if ($trip_id <= 0) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid voyage reference.']);
    exit();
}

if ($action == 'toggle') {
    $check = mysqli_query($con, "SELECT * FROM wishlist WHERE user_id = $userid AND trip_id = $trip_id");
    if (mysqli_num_rows($check) > 0) {
        mysqli_query($con, "DELETE FROM wishlist WHERE user_id = $userid AND trip_id = $trip_id");
        echo json_encode(['status' => 'removed', 'message' => 'Voyage removed from your collection.']);
    } else {
        mysqli_query($con, "INSERT INTO wishlist (user_id, trip_id) VALUES ($userid, $trip_id)");
        echo json_encode(['status' => 'added', 'message' => 'Voyage added to your collection!']);
    }
}
?>
