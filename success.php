<?php
include 'chatbot-loader.php'; 
session_start();
require 'admin/config.php';

// Check if booking information is stored in session
if (isset($_SESSION['booking_success'])) {
    $booking = $_SESSION['booking_success'];
    $trip_name = htmlspecialchars($booking['trip_name']);
    $destination = htmlspecialchars($booking['destination']);
    $ticket_number = htmlspecialchars($booking['ticket_number']);
    $ticket_hash = htmlspecialchars($booking['ticket_hash'] ?? 'N/A');
    $seats = htmlspecialchars($booking['seats']);
    $total_price = htmlspecialchars($booking['total_price']);
    $user_email = htmlspecialchars($booking['user_email']);
    
    // Clear the booking session after displaying
    unset($_SESSION['booking_success']);
} else {
    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Voyage Confirmed | ExpenseVoyage</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    
    <!-- Google Web Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;800&family=Playfair+Display:ital,wght@0,400..900;1,400..900&display=swap" rel="stylesheet">
    
    <!-- Libraries -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    
    <style>
        .success-card {
            border-radius: var(--radius-xl);
            box-shadow: var(--shadow-premium);
            max-width: 650px;
            width: 95%;
            overflow: hidden;
            border: none;
            background: white;
        }

        .check-header {
            background: linear-gradient(135deg, var(--primary), var(--primary-light));
            padding: 60px 20px;
            text-align: center;
            color: white;
        }

        .ticket-body {
            padding: 40px;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            padding: 15px 0;
            border-bottom: 1px solid rgba(0,0,0,0.05);
        }

        .hash-box {
            background: var(--bg-light);
            padding: 15px;
            border-radius: var(--radius-md);
            font-family: monospace;
            font-size: 10px;
            word-break: break-all;
            color: var(--primary);
            margin-top: 20px;
            border: 1px solid rgba(67, 56, 202, 0.1);
        }
    </style>
</head>
<body>
    <div class="success-card animate__animated animate__zoomIn">
        <div class="check-header <?php echo ($booking['verification_pending'] ?? false) ? 'bg-warning' : ''; ?>">
            <div class="check-icon">
                <i class="fas <?php echo ($booking['verification_pending'] ?? false) ? 'fa-clock animate__animated animate__pulse animate__infinite' : 'fa-check-circle'; ?>"></i>
            </div>
            <h1 class="serif-font mb-0"><?php echo ($booking['verification_pending'] ?? false) ? 'Request Processing' : 'Voyage Confirmed'; ?></h1>
            <p class="opacity-75 mb-0">
                <?php echo ($booking['verification_pending'] ?? false) 
                    ? 'Your payment is under verification. We will notify you via email.' 
                    : 'Your journey to ' . $destination . ' is secured.'; ?>
            </p>
        </div>
        
        <div class="ticket-body">
            <div class="info-row">
                <span class="label">Reference</span>
                <span class="value text-primary"><?php echo $ticket_number; ?></span>
            </div>
            <div class="info-row">
                <span class="label">Expedition</span>
                <span class="value"><?php echo $trip_name; ?></span>
            </div>
            <div class="info-row">
                <span class="label">Voyagers</span>
                <span class="value"><?php echo $seats; ?> Guest(s)</span>
            </div>
            <div class="info-row">
                <span class="label">Total Paid</span>
                <span class="value">$<?php echo number_format((float)$total_price); ?></span>
            </div>

            <div class="hash-box">
                <div class="label text-muted mb-2">Secure Verification Code</div>
                <?php echo $ticket_hash; ?>
            </div>

            <div class="d-flex gap-3 mt-5">
                <a href="index.php" class="btn btn-primary px-5 py-3 btn-premium-glow shadow-sm">Home</a>
                <a href="user-profile.php" class="btn btn-outline-primary px-5 py-3 rounded-pill">My Expeditions</a>
            </div>
        </div>
    </div>
</body>
</html>
