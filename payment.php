<?php
// Start session
session_start();
require 'vendor/autoload.php';
require 'admin/config.php'; // Ensure your config file connects to the DB
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Check if user is logged in
if (!isset($_SESSION['email'])) {
    $_SESSION['redirect_after_login'] = 'payment.php?' . $_SERVER['QUERY_STRING'];
    header("Location: login/account.php");
    exit();
}

$trip_id = null;
$trip = null;

// Check if trip_id is provided via GET
if (isset($_GET['trip_id']) && !empty($_GET['trip_id'])) {
    $trip_id = intval($_GET['trip_id']);
    
    // Fetch trip data from the database
    $query = "SELECT * FROM trips WHERE trip_id = ?";
    $stmt = $con->prepare($query);
    if ($stmt === false) {
        die("Failed to prepare statement: " . $con->error);
    }
    
    // Bind trip_id and execute query
    $stmt->bind_param("i", $trip_id);
    if (!$stmt->execute()) {
        die("Error executing query: " . $stmt->error);
    }
    
    // Get the result
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $trip = $result->fetch_assoc();
        
        // Initialize seats_available if not set
        if (!isset($trip['seats_available']) || $trip['seats_available'] === null) {
            // Set initial available seats to the persons value (max capacity)
            $initialSeats = isset($trip['persons']) ? (int)$trip['persons'] : 20;
            $updateQuery = "UPDATE trips SET seats_available = ? WHERE trip_id = ?";
            $updateStmt = $con->prepare($updateQuery);
            $updateStmt->bind_param("ii", $initialSeats, $trip_id);
            $updateStmt->execute();
            
            // Refresh trip data
            $trip['seats_available'] = $initialSeats;
        }
        
        // Initialize booked_seats if not set
        if (!isset($trip['booked_seats']) || $trip['booked_seats'] === null) {
            $updateQuery = "UPDATE trips SET booked_seats = 0 WHERE trip_id = ?";
            $updateStmt = $con->prepare($updateQuery);
            $updateStmt->bind_param("i", $trip_id);
            $updateStmt->execute();
            
            // Refresh trip data
            $trip['booked_seats'] = 0;
        }
        
        // Ensure required fields exist with default values
        $trip['trip_name'] = isset($trip['trip_name']) ? $trip['trip_name'] : 'Amazing Trip';
        $trip['starts_date'] = isset($trip['starts_date']) ? $trip['starts_date'] : date('Y-m-d');
        $trip['end_date'] = isset($trip['end_date']) ? $trip['end_date'] : date('Y-m-d', strtotime('+7 days'));
        $trip['budget'] = isset($trip['budget']) ? $trip['budget'] : 100;
        $trip['destination'] = isset($trip['destination']) ? $trip['destination'] : 'Beautiful Destination';
        $trip['duration_days'] = isset($trip['duration_days']) ? $trip['duration_days'] : 7;
        $trip['stars'] = isset($trip['stars']) ? $trip['stars'] : 5;
    } else {
        die("Trip not found!");
    }
} else {
    die("Trip ID not provided!");
}

// Stripe API configuration
\Stripe\Stripe::setApiKey('sk_test_51Q99twRwGHFYJicpNoQhaSmjJ9jqCmVuA6EqVhCSzxBRmylX9mbR0ENJMGR5909nO7m6NsuKrDT8T3UQ65ZnPoup00ljzIxa9c');

// Generalized function to send email
function sendEmail($toEmail, $subject, $bodyContent) {
    $mail = new PHPMailer(true);
    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com'; // Your SMTP host
        $mail->SMTPAuth = true;
        $mail->Username = 'memon1ahmed@gmail.com'; // Your email address
        $mail->Password = 'hvol hgpf fdra wrkb'; // Use an App Password for Gmail if 2FA is enabled
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;
        
        // Recipients
        $mail->setFrom('memon1ahmed@gmail.com', 'Expense Voyage');
        $mail->addAddress($toEmail);
        $mail->isHTML(true);
        
        // Email Subject
        $mail->Subject = $subject;
        
        // Email Body (HTML)
        $mail->Body = $bodyContent;
        
        // Alternative plain-text body for email clients that don't support HTML
        $mail->AltBody = strip_tags($bodyContent);
        
        // Send email
        $mail->send();
        return true;
    } catch (Exception $e) {
        // Log the error for debugging
        error_log("Email sending failed: " . $e->getMessage());
        return false;
    }
}

function sendBookingEmail($trip, $userEmail, $seats, $pricePerSeat, $ticketNumber) {
    // Calculate the total price based on the number of seats and price per seat
    $totalPrice = $pricePerSeat * $seats; 
    
    // Subject of the email
    $subject = "Booking Confirmation for " . htmlspecialchars($trip['trip_name']);
    
    // Body content of the email with safe access to array keys
    $bodyContent = '
        <div style="max-width: 600px; margin: auto; font-family: Arial, sans-serif; padding: 20px; background-color: #f4f4f4; border-radius: 10px; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);">
            <div style="background-color: #007bff; padding: 20px; text-align: center; border-radius: 10px 10px 0 0;">
                <h2 style="color: #fff; margin: 0;">Booking Confirmation</h2>
            </div>
            <div style="padding: 20px; background-color: #fff; border-radius: 0 0 10px 10px;">
                <p style="font-size: 18px; color: #333;">Dear Customer,</p>
                <p style="font-size: 16px; color: #333;">Your booking for the trip "<strong>' . htmlspecialchars($trip['trip_name']) . '</strong>" to ' . htmlspecialchars($trip['destination']) . ' has been confirmed.</p>
                
                <p style="font-size: 16px; color: #333;">Trip Details:</p>
                <ul>
                    <li><strong>Ticket Number:</strong> ' . htmlspecialchars($ticketNumber) . '</li>
                    <li><strong>Destination:</strong> ' . htmlspecialchars($trip['destination']) . '</li>
                    <li><strong>Price Per Seat:</strong> $' . number_format($pricePerSeat, 2) . '</li>
                    <li><strong>Number of Seats:</strong> ' . intval($seats) . '</li>
                    <li><strong>Total Price:</strong> $' . number_format($totalPrice, 2) . '</li>
                    <li><strong>Start Date:</strong> ' . date('F d, Y', strtotime($trip['starts_date'])) . '</li>
                    <li><strong>End Date:</strong> ' . date('F d, Y', strtotime($trip['end_date'])) . '</li>
                    <li><strong>Duration:</strong> ' . intval($trip['duration_days']) . ' days</li>
                    <li><strong>Departure Time:</strong> 8:00 A.M.</li>
                </ul>
                
                <p style="font-size: 16px; color: #333;">Note: Please arrive at the departure location by 7:45 A.M. sharp to ensure a timely departure.</p>
                <p style="color: #666; font-size: 14px;">If you wish to cancel your trip, please contact us at your earliest convenience using the information provided below.</p>
                
                <p style="color: #666; font-size: 14px;">Thank you for booking with us. We hope you have an enjoyable and memorable trip!</p>
            </div>
            <div style="text-align: center; padding: 10px; color: #999; font-size: 12px;">
                <p>&copy; ' . date('Y') . ' ExpenseVoyage. All rights reserved.</p>
            </div>
        </div>
    ';
    
    // Function to send the email
    return sendEmail($userEmail, $subject, $bodyContent);
}

// Handle the payment form submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($trip)) {
    $userEmail = filter_var($_POST['user_email'], FILTER_VALIDATE_EMAIL); // Validate user email
    $seats = isset($_POST['seats']) ? intval($_POST['seats']) : 0; // Get the number of seats booked
    $ticketNumber = uniqid("TT-"); // Generate a unique ticket number
    
    if ($userEmail && $seats > 0) { // Ensure the number of seats is valid
        // Check if there are enough available seats
        if ($trip['seats_available'] >= $seats) {
            try {
                // Start transaction
                $con->begin_transaction();
            
                // Create a payment intent
                $paymentIntent = \Stripe\PaymentIntent::create([
                    'amount' => ($trip['budget'] * $seats) * 100, // Amount in cents, based on number of seats
                    'currency' => 'usd',
                    'description' => 'Payment for ' . $trip['trip_name'],
                    'payment_method' => $_POST['payment_method_id'],
                    'confirm' => true,
                    'return_url' => 'https://' . $_SERVER['HTTP_HOST'] . '/success.php',
                    'automatic_payment_methods' => [
                        'enabled' => true,
                    ],
                ]);
                
                // If payment is successful, send email
                if (sendBookingEmail($trip, $userEmail, $seats, $trip['budget'], $ticketNumber)) {
                    // Update the seats available and booked seats in the database
                    $newAvailableSeats = $trip['seats_available'] - $seats;
                    $newBookedSeats = $trip['booked_seats'] + $seats; // Update booked seats
                    $updateQuery = "UPDATE trips SET seats_available = ?, booked_seats = ? WHERE trip_id = ?";
                    $updateStmt = $con->prepare($updateQuery);
                    if ($updateStmt === false) {
                        throw new Exception("Failed to prepare update statement: " . $con->error);
                    }
                    $updateStmt->bind_param("iii", $newAvailableSeats, $newBookedSeats, $trip_id);
                    if (!$updateStmt->execute()) {
                        throw new Exception("Error updating seats: " . $updateStmt->error);
                    }
                
                    // Commit transaction
                    $con->commit();
                    
                    // Store booking information in session
                    $_SESSION['booking_success'] = [
                        'trip_name' => $trip['trip_name'],
                        'destination' => $trip['destination'],
                        'ticket_number' => $ticketNumber,
                        'seats' => $seats,
                        'total_price' => ($trip['budget'] * $seats),
                        'user_email' => $userEmail
                    ];
                    
                    // Success response
                    header('Location: success.php');
                    exit;
                } else {
                    throw new Exception("Failed to send booking confirmation email.");
                }
            
            } catch (Exception $e) {
                // Rollback transaction on error
                $con->rollback();
                // Display error message to the user
                die("Payment failed: " . $e->getMessage());
            }
        } else {
            die("Not enough seats available for booking.");
        }
    } else {
        die("Invalid email or number of seats.");
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Payment - ExpenseVoyage</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta content="Secure payment processing for your travel bookings with ExpenseVoyage" name="description">
    
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
    
    <!-- Customized Stylesheet -->
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
        
        /* Typography */
        h1, h2, h3, h4, h5, h6 {
            font-weight: 700;
            margin-bottom: 1rem;
        }
        
        .section-title {
    position: relative;
    display: inline-block;
    margin-bottom: 2.5rem;
    color: white;
}           position: relative;
            display: inline-block;
            margin-bottom: 2.5rem;
        }
        
        .section-title::after {
            content: '';
            position: absolute;
            left: 0;
            bottom: -10px;
            width: 50px;
            height: 4px;
            background: var(--gradient);
            border-radius: 2px;
        }
        
        .text-primary {
            color: var(--primary) !important;
        }
        
        .text-accent {
            color: var(--accent) !important;
        }
        
        /* Payment Container */
        .payment-container {
            max-width: 800px;
            margin: 50px auto;
            background: white;
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
            padding: 40px;
            position: relative;
            z-index: 1;
        }
        
        .payment-header {
            text-align: center;
            margin-bottom: 40px;
        }
        
        .payment-header h2 {
            color: var(--primary);
            font-size: 2.5rem;
            margin-bottom: 10px;
        }
        
        .payment-header p {
            color: #6c757d;
            font-size: 1.1rem;
        }
        
        .security-badges {
            display: flex;
            justify-content: center;
            margin-bottom: 30px;
            gap: 20px;
        }
        
        .security-badge {
            display: flex;
            align-items: center;
            background: #f8f9fa;
            padding: 10px 15px;
            border-radius: 50px;
            font-size: 0.9rem;
            color: #6c757d;
        }
        
        .security-badge i {
            color: var(--primary);
            margin-right: 8px;
        }
        
        /* Trip Summary */
        .trip-summary {
            background: #f8f9fa;
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 30px;
            border-left: 5px solid var(--primary);
        }
        
        .trip-summary h3 {
            color: var(--primary);
            margin-bottom: 20px;
            font-size: 1.3rem;
        }
        
        .trip-summary-item {
            display: flex;
            align-items: center;
            margin-bottom: 12px;
        }
        
        .trip-summary-item i {
            color: var(--primary);
            margin-right: 10px;
            width: 20px;
            text-align: center;
        }
        
        /* Seats Available Indicator */
        .seats-indicator {
            display: flex;
            align-items: center;
            margin-top: 10px;
            font-size: 0.9rem;
        }
        
        .seats-indicator.high {
            color: #28a745;
        }
        
        .seats-indicator.medium {
            color: #ffc107;
        }
        
        .seats-indicator.low {
            color: #dc3545;
        }
        
        .seats-indicator i {
            margin-right: 5px;
        }
        
        /* Form Styling */
        .form-section {
            margin-bottom: 30px;
        }
        
        .form-section h3 {
            color: var(--dark);
            margin-bottom: 20px;
            font-size: 1.3rem;
            display: flex;
            align-items: center;
        }
        
        .form-section h3 i {
            background: var(--gradient);
            color: white;
            width: 36px;
            height: 36px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 12px;
            font-size: 0.9rem;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            font-weight: 600;
            color: var(--dark);
            margin-bottom: 8px;
            display: block;
        }
        
        .form-control {
            border: 1px solid #e9ecef;
            border-radius: 10px;
            padding: 15px;
            font-size: 1rem;
            transition: all 0.3s ease;
        }
        
        .form-control:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 0.2rem rgba(67, 97, 238, 0.25);
        }
        
        .form-control[readonly] {
            background-color: #f8f9fa;
        }
        
        /* Price Calculator */
        .price-calculator {
            background: #f8f9fa;
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 30px;
        }
        
        .price-display {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
            font-size: 1.1rem;
        }
        
        .price-display:last-child {
            margin-bottom: 0;
            font-weight: 700;
            font-size: 1.3rem;
            color: var(--primary);
            padding-top: 15px;
            border-top: 1px solid #e9ecef;
        }
        
        /* Payment Method */
        .payment-method {
            background: #f8f9fa;
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 30px;
        }
        
        .StripeElement {
            padding: 15px;
            border: 1px solid #e9ecef;
            border-radius: 10px;
            margin-bottom: 15px;
            font-size: 1rem;
            transition: all 0.3s ease;
        }
        
        .StripeElement:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 0.2rem rgba(67, 97, 238, 0.25);
        }
        
        .error {
            color: #dc3545;
            font-size: 0.9rem;
            margin-top: -10px;
            margin-bottom: 15px;
        }
        
        /* Submit Button */
        .btn-submit {
            background: var(--gradient);
            color: white;
            padding: 15px 30px;
            border: none;
            border-radius: 50px;
            width: 100%;
            cursor: pointer;
            font-size: 1.1rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(67, 97, 238, 0.3);
        }
        
        .btn-submit:hover {
            transform: translateY(-3px);
            box-shadow: 0 7px 20px rgba(67, 97, 238, 0.4);
            color: white;
        }
        
        .btn-submit:disabled {
            opacity: 0.7;
            cursor: not-allowed;
            transform: none;
        }
        
        /* Success Message */
        .success-message {
            background-color: #d4edda;
            color: #155724;
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 20px;
            text-align: center;
            display: none;
        }
        
        /* Loading Spinner */
        .spinner {
            display: none;
            width: 40px;
            height: 40px;
            margin: 0 auto 15px;
            border: 4px solid rgba(0, 0, 0, 0.1);
            border-radius: 50%;
            border-top-color: var(--primary);
            animation: spin 1s ease-in-out infinite;
        }
        
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
        
        /* Responsive */
        @media (max-width: 991px) {
            .payment-container {
                margin: 30px 20px;
                padding: 30px 20px;
            }
        }
        
        @media (max-width: 767px) {
            .payment-header h2 {
                font-size: 2rem;
            }
            
            .security-badges {
                flex-direction: column;
                align-items: center;
                gap: 10px;
            }
            
            .price-display {
                flex-direction: column;
                gap: 5px;
            }
        }
    </style>
</head>
<body>
    <!-- Animated Background -->
    <div id="particles-js"></div>
    
    <!-- Payment Container -->
    <div class="payment-container animate__animated animate__fadeInUp">
        <div class="payment-header">
            <h2>Secure Payment</h2>
            <p>Complete your booking with our secure payment system</p>
        </div>
        
        <div class="security-badges">
            <div class="security-badge">
                <i class="fas fa-lock"></i>
                <span>Secure Payment</span>
            </div>
            <div class="security-badge">
                <i class="fas fa-shield-alt"></i>
                <span>Data Protection</span>
            </div>
            <div class="security-badge">
                <i class="fas fa-credit-card"></i>
                <span>Multiple Payment Options</span>
            </div>
        </div>
        
        <div class="success-message" id="successMessage">
            <i class="fas fa-check-circle me-2"></i>
            Processing your payment...
        </div>
        
        <?php if ($trip): ?>
            <div class="trip-summary">
                <h3><i class="fas fa-info-circle me-2"></i>Trip Details</h3>
                <div class="trip-summary-item">
                    <i class="fas fa-map-marker-alt"></i>
                    <span><strong>Destination:</strong> <?php echo htmlspecialchars($trip['destination']); ?></span>
                </div>
                <div class="trip-summary-item">
                    <i class="fas fa-calendar-alt"></i>
                    <span><strong>Duration:</strong> <?php echo htmlspecialchars($trip['duration_days']); ?> days</span>
                </div>
                <div class="trip-summary-item">
                    <i class="fas fa-users"></i>
                    <span><strong>Max Capacity:</strong> <?php echo htmlspecialchars($trip['persons']); ?> people</span>
                </div>
                <div class="trip-summary-item">
                    <i class="fas fa-star"></i>
                    <span><strong>Rating:</strong> <?php echo htmlspecialchars($trip['stars']); ?>/5</span>
                </div>
                <div class="trip-summary-item">
                    <i class="fas fa-dollar-sign"></i>
                    <span><strong>Price per Person:</strong> $<?php echo htmlspecialchars($trip['budget']); ?></span>
                </div>
                <div class="trip-summary-item">
                    <i class="fas fa-chair"></i>
                    <span><strong>Available Seats:</strong> <?php echo htmlspecialchars($trip['seats_available']); ?></span>
                </div>
                
                <?php 
                // Add seats availability indicator
                $seatsAvailable = isset($trip['seats_available']) ? (int)$trip['seats_available'] : 0;
                $seatsClass = 'high';
                $seatsIcon = 'fa-check-circle';
                $seatsText = 'Seats Available';
                
                if ($seatsAvailable <= 5) {
                    $seatsClass = 'low';
                    $seatsIcon = 'fa-exclamation-triangle';
                    $seatsText = 'Limited Seats';
                } elseif ($seatsAvailable <= 10) {
                    $seatsClass = 'medium';
                    $seatsIcon = 'fa-info-circle';
                    $seatsText = 'Few Seats Left';
                }
                ?>
                
                <div class="seats-indicator <?php echo $seatsClass; ?>">
                    <i class="fas <?php echo $seatsIcon; ?>"></i>
                    <span><?php echo $seatsText; ?></span>
                </div>
            </div>
            
            <form id="payment-form" method="POST">
                <!-- User Details Section -->
                <div class="form-section">
                    <h3><i class="fas fa-user"></i>Personal Information</h3>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="user_name">Full Name</label>
                                <input type="text" name="user_name" id="user_name" value="<?php echo isset($_GET['name']) ? htmlspecialchars($_GET['name']) : ''; ?>" readonly class="form-control">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="user_email">Email Address</label>
                                <input type="email" name="user_email" id="user_email" value="<?php echo isset($_GET['email']) ? htmlspecialchars($_GET['email']) : ''; ?>" readonly class="form-control">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="user_phone">Phone Number</label>
                                <input type="tel" name="user_phone" id="user_phone" value="<?php echo isset($_GET['phone']) ? htmlspecialchars($_GET['phone']) : ''; ?>" readonly class="form-control">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="start_date">Travel Date</label>
                                <input type="date" name="start_date" id="start_date" value="<?php echo isset($_GET['start_date']) ? htmlspecialchars($_GET['start_date']) : ''; ?>" readonly class="form-control">
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Seats and Price Details Section -->
                <div class="form-section">
                    <h3><i class="fas fa-chair"></i>Booking Details</h3>
                    <div class="form-group">
                        <label for="seats">Number of Travelers</label>
                        <input type="number" name="seats" id="seats" value="<?php echo isset($_GET['seats']) ? (int)$_GET['seats'] : 1; ?>" min="1" max="<?php echo isset($trip['seats_available']) ? (int)$trip['seats_available'] : 1; ?>" class="form-control" oninput="updatePrice()">
                        <small class="form-text text-muted">Maximum <?php echo isset($trip['seats_available']) ? (int)$trip['seats_available'] : 0; ?> seats available</small>
                    </div>
                    
                    <div class="price-calculator">
                        <div class="price-display">
                            <span>Price per Person:</span>
                            <span id="price-per-person">$<?php echo htmlspecialchars($trip['budget']); ?></span>
                        </div>
                        <div class="price-display">
                            <span>Number of Travelers:</span>
                            <span id="travelers-count"><?php echo isset($_GET['seats']) ? (int)$_GET['seats'] : 1; ?></span>
                        </div>
                        <div class="price-display">
                            <span>Total Amount:</span>
                            <span id="total-price">$<?php echo htmlspecialchars($trip['budget']); ?></span>
                        </div>
                    </div>
                </div>
                
                <!-- Special Requests -->
                <div class="form-section">
                    <h3><i class="fas fa-comment-alt"></i>Special Requests</h3>
                    <div class="form-group">
                        <textarea name="user_message" id="user_message" readonly class="form-control" rows="3"><?php echo isset($_GET['message']) ? htmlspecialchars($_GET['message']) : ''; ?></textarea>
                    </div>
                </div>
                
                <!-- Payment Details Section -->
                <div class="form-section">
                    <h3><i class="fas fa-credit-card"></i>Payment Information</h3>
                    <div class="payment-method">
                        <div id="card-element" class="StripeElement"></div>
                        <div class="error" id="card-errors"></div>
                    </div>
                </div>
                
                <div class="spinner" id="payment-spinner"></div>
                
                <button type="submit" class="btn-submit" id="submit-button">
                    <i class="fas fa-lock me-2"></i>Pay Securely
                </button>
            </form>
        <?php else: ?>
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-triangle me-2"></i>
                No trip data available. Please go back and select a trip.
            </div>
        <?php endif; ?>
    </div>
    
    <!-- JavaScript Libraries -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://js.stripe.com/v3/"></script>
    
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
        
        // Initialize Stripe
        const stripe = Stripe('pk_test_51Q99twRwGHFYJicpFXqjS5QC4toslBSiKZMwtSfMml5N6gONgaBJ4IJ74YDAgS3QQxU9WRqQCYOtwA22jr3erNwz00AoOChaPY');
        const elements = stripe.elements();
        
        // Create an instance of the card Element
        const cardElement = elements.create('card', {
            style: {
                base: {
                    color: '#32325d',
                    fontFamily: '"Poppins", sans-serif',
                    fontSmoothing: 'antialiased',
                    fontSize: '16px',
                    '::placeholder': {
                        color: '#aab7c4'
                    },
                },
            },
        });
        
        cardElement.mount('#card-element');
        
        // Function to update the price display
        function updatePrice() {
            const pricePerPerson = <?php echo json_encode(isset($trip['budget']) ? (float)$trip['budget'] : 0); ?>;
            const seats = parseInt(document.getElementById('seats').value) || 1;
            const totalPrice = pricePerPerson * seats;
            
            document.getElementById('travelers-count').textContent = seats;
            document.getElementById('total-price').textContent = '$' + totalPrice.toFixed(2);
        }
        
        // Initialize the price on page load
        updatePrice();
        
        // Handle form submission
        const form = document.getElementById('payment-form');
        const submitButton = document.getElementById('submit-button');
        const spinner = document.getElementById('payment-spinner');
        const successMessage = document.getElementById('successMessage');
        
        form.addEventListener('submit', async (event) => {
            event.preventDefault();
            
            // Disable submit button and show spinner
            submitButton.disabled = true;
            spinner.style.display = 'block';
            successMessage.style.display = 'block';
            
            try {
                const { paymentMethod, error } = await stripe.createPaymentMethod({
                    type: 'card',
                    card: cardElement,
                    billing_details: {
                        name: document.getElementById('user_name').value,
                        email: document.getElementById('user_email').value,
                        phone: document.getElementById('user_phone').value,
                    },
                });
                
                if (error) {
                    // Display error to the user
                    document.getElementById('card-errors').textContent = error.message;
                    // Re-enable submit button and hide spinner
                    submitButton.disabled = false;
                    spinner.style.display = 'none';
                    successMessage.style.display = 'none';
                } else {
                    // Send paymentMethod.id to the server
                    const hiddenInput = document.createElement('input');
                    hiddenInput.setAttribute('type', 'hidden');
                    hiddenInput.setAttribute('name', 'payment_method_id');
                    hiddenInput.setAttribute('value', paymentMethod.id);
                    form.appendChild(hiddenInput);
                    
                    // Submit the form
                    form.submit();
                }
            } catch (error) {
                console.error('Error:', error);
                document.getElementById('card-errors').textContent = 'An unexpected error occurred. Please try again.';
                // Re-enable submit button and hide spinner
                submitButton.disabled = false;
                spinner.style.display = 'none';
                successMessage.style.display = 'none';
            }
        });
    </script>
</body>
</html>