<?php
session_start();
include 'admin/config.php';
include 'audit_helper.php';

header('Content-Type: application/json');

if (!isset($_SESSION['role']) || ($_SESSION['role'] !== 'agent' && $_SESSION['role'] !== 'admin')) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $booking_id = intval($_POST['booking_id']);
    $new_status = mysqli_real_escape_string($con, $_POST['status']);
    
    // Update booking status
    $sql = "UPDATE bookings SET expedition_status = ? WHERE booking_id = ?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "si", $new_status, $booking_id);
    
    if (mysqli_stmt_execute($stmt)) {
        // Fetch user_id for notification
        $uSql = "SELECT user_id, trip_id FROM bookings WHERE booking_id = ?";
        $uStmt = mysqli_prepare($con, $uSql);
        mysqli_stmt_bind_param($uStmt, "i", $booking_id);
        mysqli_stmt_execute($uStmt);
        $res = mysqli_stmt_get_result($uStmt);
        $booking = mysqli_fetch_assoc($res);
        $user_id = $booking['user_id'];
        
        // Fetch trip name
        $tSql = "SELECT trip_name FROM trips WHERE trip_id = ?";
        $tStmt = mysqli_prepare($con, $tSql);
        mysqli_stmt_bind_param($tStmt, "i", $booking['trip_id']);
        mysqli_stmt_execute($tStmt);
        $tRes = mysqli_stmt_get_result($tStmt);
        $trip = mysqli_fetch_assoc($tRes);
        $trip_name = $trip['trip_name'];

        // Create notification
        $title = "Expedition Update: $new_status";
        $message = "Your voyage '$trip_name' has been updated to: " . strtoupper($new_status);
        
        $nSql = "INSERT INTO notifications (user_id, title, message) VALUES (?, ?, ?)";
        $nStmt = mysqli_prepare($con, $nSql);
        mysqli_stmt_bind_param($nStmt, "iss", $user_id, $title, $message);
        mysqli_stmt_execute($nStmt);

        log_audit($con, $_SESSION['userid'], 'EXPEDITION_STATUS_UPDATE', "Booking ID: $booking_id, Status: $new_status");

        echo json_encode(['status' => 'success', 'message' => 'Status updated and traveler notified.']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Database error']);
    }
}
?>
