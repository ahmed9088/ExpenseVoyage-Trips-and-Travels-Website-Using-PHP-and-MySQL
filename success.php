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
        :root {
            --primary: #4f46e5;
            --slate: #0f172a;
            --indigo-light: #e0e7ff;
        }

        body {
            font-family: 'Outfit', sans-serif;
            background-color: #f8fafc;
            color: var(--slate);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .serif-font {
            font-family: 'Playfair Display', serif;
        }

        .success-card {
            background: white;
            border-radius: 30px;
            box-shadow: 0 20px 50px rgba(0,0,0,0.05);
            max-width: 650px;
            width: 95%;
            overflow: hidden;
            border: 1px solid #e2e8f0;
        }

        .check-header {
            background: linear-gradient(135deg, #4f46e5, #818cf8);
            padding: 60px 20px;
            text-align: center;
            color: white;
        }

        .check-icon {
            font-size: 70px;
            margin-bottom: 20px;
        }

        .ticket-body {
            padding: 50px;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            padding: 15px 0;
            border-bottom: 1px solid #f1f5f9;
        }

        .info-row:last-child {
            border-bottom: none;
        }

        .label {
            color: #64748b;
            text-transform: uppercase;
            font-size: 11px;
            letter-spacing: 1.5px;
            font-weight: 700;
        }

        .value {
            font-weight: 600;
            color: var(--slate);
        }

        .hash-box {
            background: var(--indigo-light);
            padding: 15px;
            border-radius: 12px;
            font-family: monospace;
            font-size: 10px;
            word-break: break-all;
            color: var(--primary);
            margin-top: 20px;
        }

        .btn-voyage {
            padding: 15px 30px;
            border-radius: 12px;
            font-weight: 600;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
        }

        .btn-primary-voyage {
            background: var(--primary);
            color: white;
        }

        .btn-outline-voyage {
            border: 1px solid #e2e8f0;
            color: #64748b;
        }

        .btn-voyage:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.05);
        }
    </style>
</head>
<body>
    <div class="success-card animate__animated animate__zoomIn">
        <div class="check-header">
            <div class="check-icon"><i class="fas fa-check-circle"></i></div>
            <h1 class="serif-font mb-0">Voyage Confirmed</h1>
            <p class="opacity-75 mb-0">Your journey to <?php echo $destination; ?> is secured.</p>
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
                <a href="index.php" class="btn-voyage btn-primary-voyage shadow-sm">Home</a>
                <a href="user-profile.php" class="btn-voyage btn-outline-voyage">My Expeditions</a>
            </div>
        </div>
    </div>
</body>
</html>
