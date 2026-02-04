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
    $seats = htmlspecialchars($booking['seats']);
    $total_price = htmlspecialchars($booking['total_price']);
    $user_email = htmlspecialchars($booking['user_email']);
    
    // Clear the booking session after displaying
    unset($_SESSION['booking_success']);
} else {
    $trip_id = isset($_GET['trip_id']) ? intval($_GET['trip_id']) : null;
    $trip_name = "the selected trip";
    $destination = "your destination";
    
    if ($trip_id) {
        $query = "SELECT trip_name, destination FROM trips WHERE trip_id = ?";
        $stmt = $con->prepare($query);
        $stmt->bind_param("i", $trip_id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $trip = $result->fetch_assoc();
            $trip_name = htmlspecialchars($trip['trip_name']);
            $destination = htmlspecialchars($trip['destination']);
        }
    }
    
    $ticket_number = "N/A";
    $seats = "N/A";
    $total_price = "N/A";
    $user_email = isset($_SESSION['email']) ? htmlspecialchars($_SESSION['email']) : "your email";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Voyage Confirmed | ExpenseVoyage</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;800&family=Playfair+Display:ital,wght@0,400..900;1,400..900&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <script src="https://cdn.jsdelivr.net/particles.js/2.0.0/particles.min.js"></script>
    
    <link href="css/custom.css" rel="stylesheet">
    
    <style>
        .success-hero {
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
        }

        .success-card {
            max-width: 600px;
            width: 90%;
            border-top: 4px solid var(--gold);
        }

        .check-icon {
            font-size: 80px;
            background: var(--gold-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 30px;
        }

        .ticket-detail {
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            padding: 12px 0;
        }

        .ticket-detail:last-child {
            border-bottom: none;
        }
    </style>
</head>
<body>
    <div id="particles-js"></div>

    <section class="success-hero">
        <div class="glass-panel success-card p-5 animate__animated animate__zoomIn">
            <div class="check-icon animate__animated animate__bounceIn mt-2">
                <i class="fas fa-circle-check"></i>
            </div>
            
            <h1 class="serif-font display-4 mb-3">Voyage Confirmed</h1>
            <p class="text-white-50 mb-5">Your dream journey to <strong><?php echo $destination; ?></strong> is now secured in our archives.</p>
            
            <div class="booking-details text-start mb-5">
                <div class="ticket-detail d-flex justify-content-between">
                    <span class="text-white-50 small tracking-widest text-uppercase">Reference No.</span>
                    <span class="text-gold fw-bold"><?php echo $ticket_number; ?></span>
                </div>
                <div class="ticket-detail d-flex justify-content-between">
                    <span class="text-white-50 small tracking-widest text-uppercase">Voyage Name</span>
                    <span><?php echo $trip_name; ?></span>
                </div>
                <div class="ticket-detail d-flex justify-content-between">
                    <span class="text-white-50 small tracking-widest text-uppercase">Voyagers</span>
                    <span><?php echo $seats; ?> Guest(s)</span>
                </div>
                <div class="ticket-detail d-flex justify-content-between">
                    <span class="text-white-50 small tracking-widest text-uppercase">Financial Total</span>
                    <span class="text-white">$<?php echo number_format((float)$total_price); ?></span>
                </div>
            </div>

            <div class="p-3 bg-white-5 border border-secondary mb-5">
                <p class="small text-white-50 mb-0">
                    <i class="fas fa-envelope text-gold me-2"></i>
                    A digital concierge package has been dispatched to <strong><?php echo $user_email; ?></strong>.
                </p>
            </div>
            
            <div class="d-grid gap-3 d-md-flex justify-content-center">
                <a href="index.php" class="btn btn-primary px-5 py-3 rounded-0 tracking-widest">RETURN TO ESTATE</a>
                <a href="user-profile.php" class="btn btn-outline-light px-5 py-3 rounded-0 tracking-widest">MY ARCHIVE</a>
            </div>
        </div>
    </section>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="js/custom.js"></script>
</body>
</html>