<?php
require_once("auth.php");
require_once("../audit_helper.php");

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $booking_id = intval($_POST['booking_id'] ?? 0);
    $status = $_POST['status'] ?? ''; // 'verified' or 'rejected'

    if (!$booking_id || !in_array($status, ['verified', 'rejected'])) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid parameters']);
        exit();
    }

    // 1. Fetch booking and trip details for context/email
    $sql = "SELECT b.*, u.email, u.first_name, t.trip_name 
            FROM bookings b 
            JOIN users u ON b.user_id = u.id 
            JOIN trips t ON b.trip_id = t.trip_id
            WHERE b.booking_id = $booking_id";
    $res = mysqli_query($con, $sql);
    $booking = mysqli_fetch_assoc($res);

    if (!$booking) {
        echo json_encode(['status' => 'error', 'message' => 'Booking not found']);
        exit();
    }

    // 2. Update Booking Status
    $payment_status = ($status === 'verified') ? 'paid' : 'unpaid';
    $update_sql = "UPDATE bookings SET 
                    verification_status = '$status', 
                    payment_status = '$payment_status' 
                  WHERE booking_id = $booking_id";
    
    if (mysqli_query($con, $update_sql)) {
        // 3. Audit Logging
        log_audit($con, $_SESSION['userid'], 'PAYMENT_VERIFIED', "Booking #$booking_id set to $status");

        // 4. Trigger Notification (Simulation)
        $msg = ($status === 'verified') 
            ? "Your payment for {$booking['trip_name']} has been verified. Welcome aboard!" 
            : "Your payment verification for {$booking['trip_name']} failed. Please contact support.";
        
        // In a real scenario, use mail() or PHPMailer here
        log_audit($con, 0, 'EMAIL_NOTIFICATION', "To: {$booking['email']}, Subject: Payment $status, Body: $msg");

        echo json_encode(['status' => 'success', 'message' => 'Payment status updated and traveler notified.']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Database update failed: ' . mysqli_error($con)]);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
}
?>
