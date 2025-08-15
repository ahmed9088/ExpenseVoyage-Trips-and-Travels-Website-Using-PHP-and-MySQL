<?php
session_start();
require 'admin/config.php'; // Ensure your config file connects to the DB

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
    // Fallback to trip_id if session data is not available
    $trip_id = isset($_GET['trip_id']) ? intval($_GET['trip_id']) : null;
    $trip_name = ""; // Initialize trip name
    
    if ($trip_id) {
        // Fetch trip details from database
        $query = "SELECT trip_name, destination FROM trips WHERE trip_id = ?";
        $stmt = $con->prepare($query);
        $stmt->bind_param("i", $trip_id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $trip = $result->fetch_assoc();
            $trip_name = htmlspecialchars($trip['trip_name']);
            $destination = htmlspecialchars($trip['destination']);
        } else {
            $trip_name = "the selected trip";
            $destination = "your destination";
        }
    } else {
        $trip_name = "the selected trip";
        $destination = "your destination";
    }
    
    // Set default values if not available
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
    <title>Payment Successful - ExpenseVoyage</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta content="Your payment was successful. Thank you for booking with ExpenseVoyage." name="description">
    
    <!-- Favicon -->
    <link href="img/favicon.ico" rel="icon">
    <link rel="apple-touch-icon" sizes="180x180" href="img/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="img/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="img/favicon-16x16.png">
    <link rel="manifest" href="img/site.webmanifest">
    
    <!-- Google Web Fonts -->
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Animate.css -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    
    <!-- Particles.js for animated background -->
    <script src="https://cdn.jsdelivr.net/particles.js/2.0.0/particles.min.js"></script>
    
    <style>
        :root {
            --primary: #4361ee;
            --secondary: #3f37c9;
            --accent: #4cc9f0;
            --light: #f8f9fa;
            --dark: #212529;
            --success: #06ffa5;
            --gradient: linear-gradient(135deg, var(--primary), var(--secondary));
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            color: var(--dark);
            background-color: #f5f7ff;
            overflow-x: hidden;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        /* Animated Background */
        #particles-js {
            position: fixed;
            width: 100%;
            height: 100%;
            top: 0;
            left: 0;
            z-index: -1;
            background: linear-gradient(135deg, #1a1c20, #2d3436);
        }
        
        /* Scrollbar Styling */
        ::-webkit-scrollbar {
            width: 12px;
        }
        
        ::-webkit-scrollbar-track {
            background: #232E33;
            border-radius: 1px;
        }
        
        ::-webkit-scrollbar-thumb {
            background: #7AB730;
            border-radius: 3px;
        }
        
        @supports not selector(::-webkit-scrollbar) {
            body {
                scrollbar-color: #7AB730 #232E33;
            }
        }
        
        /* Success Container */
        .success-container {
            max-width: 800px;
            margin: 50px auto;
            background: white;
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
            padding: 40px;
            position: relative;
            z-index: 1;
            text-align: center;
        }
        
        .success-icon {
            width: 100px;
            height: 100px;
            background: var(--gradient);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 30px;
            color: white;
            font-size: 40px;
        }
        
        h2 {
            color: var(--primary);
            font-size: 2.5rem;
            margin-bottom: 20px;
        }
        
        .success-message {
            font-size: 1.2rem;
            margin-bottom: 30px;
            color: #555;
        }
        
        .booking-details {
            background: #f8f9fa;
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 30px;
            text-align: left;
        }
        
        .booking-details h3 {
            color: var(--primary);
            margin-bottom: 20px;
            font-size: 1.5rem;
            text-align: center;
        }
        
        .detail-row {
            display: flex;
            justify-content: space-between;
            padding: 12px 0;
            border-bottom: 1px solid #e9ecef;
        }
        
        .detail-row:last-child {
            border-bottom: none;
            font-weight: 700;
            font-size: 1.1rem;
            color: var(--primary);
        }
        
        .detail-label {
            font-weight: 600;
        }
        
        .btn-home {
            background: var(--gradient);
            color: white;
            padding: 15px 30px;
            border: none;
            border-radius: 50px;
            cursor: pointer;
            font-size: 1.1rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(67, 97, 238, 0.3);
            display: inline-block;
            text-decoration: none;
        }
        
        .btn-home:hover {
            transform: translateY(-3px);
            box-shadow: 0 7px 20px rgba(67, 97, 238, 0.4);
            color: white;
        }
        
        .email-notice {
            background-color: #e7f3ff;
            border-left: 4px solid var(--primary);
            padding: 15px;
            margin-bottom: 30px;
            border-radius: 0 10px 10px 0;
        }
        
        .email-notice i {
            color: var(--primary);
            margin-right: 10px;
        }
        
        /* Responsive */
        @media (max-width: 991px) {
            .success-container {
                margin: 30px 20px;
                padding: 30px 20px;
            }
        }
        
        @media (max-width: 767px) {
            h2 {
                font-size: 2rem;
            }
            
            .detail-row {
                flex-direction: column;
                gap: 5px;
            }
        }
    </style>
</head>
<body>
    <!-- Animated Background -->
    <div id="particles-js"></div>
    
    <!-- Success Container -->
    <div class="success-container animate__animated animate__fadeInUp">
        <div class="success-icon">
            <i class="fas fa-check"></i>
        </div>
        
        <h2>Payment Successful!</h2>
        <p class="success-message">Thank you for your payment. Your booking is confirmed!</p>
        
        <div class="email-notice">
            <i class="fas fa-envelope"></i>
            <span>A confirmation email has been sent to <strong><?php echo $user_email; ?></strong>. Please check your inbox.</span>
        </div>
        
        <div class="booking-details">
            <h3><i class="fas fa-ticket-alt me-2"></i>Booking Details</h3>
            
            <div class="detail-row">
                <span class="detail-label">Trip Name:</span>
                <span><?php echo $trip_name; ?></span>
            </div>
            
            <div class="detail-row">
                <span class="detail-label">Destination:</span>
                <span><?php echo $destination; ?></span>
            </div>
            
            <div class="detail-row">
                <span class="detail-label">Ticket Number:</span>
                <span><?php echo $ticket_number; ?></span>
            </div>
            
            <div class="detail-row">
                <span class="detail-label">Number of Travelers:</span>
                <span><?php echo $seats; ?></span>
            </div>
            
            <div class="detail-row">
                <span class="detail-label">Total Amount Paid:</span>
                <span>$<?php echo $total_price; ?></span>
            </div>
        </div>
        
        <a href="index.php" class="btn-home">
            <i class="fas fa-home me-2"></i>Return to Home
        </a>
    </div>
    
    <!-- JavaScript Libraries -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Template Javascript -->
    <script>
        // Initialize particles.js for animated background
        particlesJS('particles-js', {
            particles: {
                number: {
                    value: 80,
                    density: {
                        enable: true,
                        value_area: 800
                    }
                },
                color: {
                    value: '#4cc9f0'
                },
                shape: {
                    type: 'circle'
                },
                opacity: {
                    value: 0.5,
                    random: true
                },
                size: {
                    value: 3,
                    random: true
                },
                line_linked: {
                    enable: true,
                    distance: 150,
                    color: '#4361ee',
                    opacity: 0.4,
                    width: 1
                },
                move: {
                    enable: true,
                    speed: 2,
                    direction: 'none',
                    random: true,
                    straight: false,
                    out_mode: 'out',
                    bounce: false
                }
            },
            interactivity: {
                detect_on: 'canvas',
                events: {
                    onhover: {
                        enable: true,
                        mode: 'grab'
                    },
                    onclick: {
                        enable: true,
                        mode: 'push'
                    },
                    resize: true
                },
                modes: {
                    grab: {
                        distance: 140,
                        line_linked: {
                            opacity: 1
                        }
                    },
                    push: {
                        particles_nb: 4
                    }
                }
            },
            retina_detect: true
        });
    </script>
</body>
</html>