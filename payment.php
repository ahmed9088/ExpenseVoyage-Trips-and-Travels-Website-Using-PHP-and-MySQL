<?php
// Start session
session_start();
require 'vendor/autoload.php';
require 'admin/config.php'; // Ensure your config file connects to the DB
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

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
        $mail->Username = 'ubaidsoomro505@gmail.com'; // Your email address
        $mail->Password = 'rgja elkh bfag uarz'; // Use an App Password for Gmail if 2FA is enabled
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        // Recipients
        $mail->setFrom('ubaidsoomro505@gmail.com', 'Expense Voyage');
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
        return false;
    }
}

function sendBookingEmail($trip, $userEmail, $seats, $pricePerSeat, $ticketNumber) {
    // Calculate the total price based on the number of seats and price per seat
    $totalPrice = $pricePerSeat * $seats; 
    
    // Subject of the email
    $subject = "Booking Confirmation for " . htmlspecialchars($trip['trip_name']);
    
    // Body content of the email
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
                    <li><strong>Departure Time:</strong> 8:00 A.M.</li>
                    <li><strong>Departure Location:</strong> ' . htmlspecialchars($trip['departure']) . '</li>
                </ul>
                
                <p style="font-size: 16px; color: #333;">Note: Please arrive at the departure location by 7:45 A.M. sharp to ensure a timely departure.</p>
                <p style="color: #666; font-size: 14px;">If you wish to cancel your trip, please contact us at your earliest convenience using the information provided below.</p>
                
                <p style="color: #666; font-size: 14px;">Thank you for booking with us. We hope you have an enjoyable and memorable trip!</p>
            </div>
            <div style="text-align: center; padding: 10px; color: #999; font-size: 12px;">
                <p>&copy; ' . date('Y') . ' Trip Travel. All rights reserved.</p>
            </div>
        </div>
    ';

    // Function to send the email
    sendEmail($userEmail, $subject, $bodyContent);
}



// Handle the payment form submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($trip)) {
    $userEmail = filter_var($_POST['user_email'], FILTER_VALIDATE_EMAIL); // Validate user email
    $seats = isset($_POST['seats']) ? intval($_POST['seats']) : 0; // Get the number of seats booked
    $ticketNumber = uniqid("TT-"); // Generate a unique ticket number
    $departureLocation = "Main Street, Karachi"; // Example departure location

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
                    'return_url' => 'http://yourwebsite.com/return_url.php', // Change to your return URL
                    'automatic_payment_methods' => [
                        'enabled' => true,
                    ],
                ]);
                
                // If payment is successful, send email
                sendBookingEmail($trip, $userEmail, $seats, $trip['budget'], $ticketNumber, $departureLocation); // Pass the correct arguments
            
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

                // Success response
                header('Location: success.php');
                exit;
            
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
    <title>Traveler.com | Payment</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <link href="css/style.css" rel="stylesheet">
    <script src="https://js.stripe.com/v3/"></script>
    <style>
        body { font-family: 'Poppins', sans-serif; background-color: #f4f4f4; color: #333; margin: 0; padding: 0; }
        .payment-container { max-width: 600px; margin: 100px auto; background: #fff; padding: 40px; border-radius: 10px; box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1); }
        h2 { color: #427c00; text-align: center; margin-bottom: 20px; }
        .trip-info { margin-bottom: 20px; }
        .trip-info h3 { margin: 0; }
        .StripeElement { padding: 15px; border: 1px solid #ddd; border-radius: 5px; margin-bottom: 10px; font-size: 16px; }
        .btn-submit { background-color: #427c00; color: white; padding: 15px 20px; border: none; border-radius: 5px; width: 100%; cursor: pointer; font-size: 18px; }
        .btn-submit:hover { background-color: #7AB730; }
        .error { color: red; font-size: 14px; margin-top: -5px; margin-bottom: 15px; }
        .email-input { width: 100%; padding: 10px; font-size: 16px; border: 1px solid #ddd; border-radius: 5px; margin-bottom: 15px; }
    </style>
</head>
<body>
<style>
    
body {
  --sb-track-color: #232E33;
  --sb-thumb-color: #7AB730;
  --sb-size: 14px;
}

body::-webkit-scrollbar {
  width: 12px;
}

body::-webkit-scrollbar-track {
  background: var(--sb-track-color);
  border-radius: 1px;
}

body::-webkit-scrollbar-thumb {
  background: var(--sb-thumb-color);
  border-radius: 3px;
  
}

@supports not selector(::-webkit-scrollbar) {
  body {
    scrollbar-color: var(--sb-thumb-color)
                     var(--sb-track-color);
  }
}
</style>
<div class="payment-container">
    <h2>Payment Details</h2>
    <?php if ($trip): ?>
        <div class="trip-info">
            <h3>Trip Name: <?php echo htmlspecialchars($trip['trip_name']); ?></h3>
            <p>Destination: <?php echo htmlspecialchars($trip['destination']); ?></p>
            <p>Budget: $<?php echo htmlspecialchars($trip['budget']); ?></p>
            <p>Starts Date: <?php echo htmlspecialchars($trip['starts_date']); ?></p>
            <p>End Date: <?php echo htmlspecialchars($trip['end_date']); ?></p>
            <p>Seats Available: <?php echo htmlspecialchars($trip['seats_available']); ?></p>
            <p id="price-per-person" style="display: none;">Price per Person: $<?php 
                $totalTripPrice = htmlspecialchars($trip['budget']);
                $totalSeats = htmlspecialchars($trip['seats_available']);
                $pricePerPerson = $totalTripPrice / $totalSeats; 
                echo number_format($pricePerPerson, 2);
            ?></p>
        </div>
    <?php else: ?>
        <p>No trip data available.</p>
    <?php endif; ?>

    <form id="payment-form" method="POST">
        <!-- User Details Section -->
        <div class="user-details">
            <h3>User Details</h3>
            <div class="mb-3">
                <label for="user_name">Full Name</label>
                <input type="text" name="user_name" id="user_name" value="<?php echo isset($_GET['name']) ? htmlspecialchars($_GET['name']) : ''; ?>" readonly class="form-control">
            </div>
            <div class="mb-3">
                <label for="user_email">Email</label>
                <input type="email" name="user_email" id="user_email" value="<?php echo isset($_GET['email']) ? htmlspecialchars($_GET['email']) : ''; ?>" readonly class="form-control">
            </div>
            <div class="mb-3">
                <label for="user_phone">Phone Number</label>
                <input type="tel" name="user_phone" id="user_phone" value="<?php echo isset($_GET['phone']) ? htmlspecialchars($_GET['phone']) : ''; ?>" readonly class="form-control">
            </div>
            <div class="mb-3">
                <label for="user_message">Message</label>
                <textarea name="user_message" id="user_message" readonly class="form-control"><?php echo isset($_GET['message']) ? htmlspecialchars($_GET['message']) : ''; ?></textarea>
            </div>
        </div>

        <!-- Seats and Price Details Section -->
        <h3>Seats and Price Details</h3>
        <div class="mb-3">
            <label for="seats">Number of Seats</label>
            <input type="number" name="seats" id="seats" value="<?php echo isset($_GET['seats']) ? htmlspecialchars($_GET['seats']) : 1; ?>" min="1" class="form-control" oninput="updatePrice()">
        </div>
        <p id="price-per-person-display"></p>
        <p id="total-price-display"></p>

        <!-- Payment Details Section -->
        <h3>Payment Details</h3>
        <div id="card-element" class="StripeElement"></div>
        <div class="error" id="card-errors"><?php echo isset($error) ? htmlspecialchars($error) : ''; ?></div>

        <button type="submit" class="btn-submit">Proceed to Payment</button>
    </form>
</div>

<script>
// Function to update the price display
function updatePrice() {
    const budget = <?php echo json_encode(htmlspecialchars($trip['budget'])); ?>; // Total budget for the trip
    const totalSeats = <?php echo json_encode(htmlspecialchars($trip['seats_available'])); ?>; // Total seats available
    const seatsBooked = parseInt(document.getElementById('seats').value); // Number of seats booked by the user

    // Reset display elements
    const pricePerPersonDisplay = document.getElementById('price-per-person-display');
    const totalPriceDisplay = document.getElementById('total-price-display');
    const pricePerPersonContainer = document.getElementById('price-per-person');

    // Validate the number of seats booked
    if (isNaN(seatsBooked) || seatsBooked <= 0) {
        pricePerPersonDisplay.textContent = 'Please enter a valid number of seats.';
        totalPriceDisplay.textContent = 'Total Price: $0.00';
        pricePerPersonContainer.style.display = 'none';
        return; // Exit the function
    } 

    if (seatsBooked > totalSeats) {
        pricePerPersonDisplay.textContent = 'Not enough seats available. Please choose a lower number.';
        totalPriceDisplay.textContent = 'Total Price: $0.00';
        pricePerPersonContainer.style.display = 'none';
        return; // Exit the function
    }

    // Calculate price per person based on total available seats
    const pricePerPerson = budget / totalSeats; // Price per person based on total available seats
    const totalPrice = pricePerPerson * seatsBooked; // Total price based on booked seats

    // Display the calculated prices
    pricePerPersonDisplay.textContent = 'Price per Person: $' + pricePerPerson.toFixed(2);
    totalPriceDisplay.textContent = 'Total Price: $' + totalPrice.toFixed(2);
    
    // Show the price per person only if valid seats are booked
    if (seatsBooked > 0 && seatsBooked <= totalSeats) {
        pricePerPersonContainer.style.display = 'block';
    } else {
        pricePerPersonContainer.style.display = 'none';
    }
}


// Initialize the price on page load
updatePrice();
</script>

    <script>
        // Initialize Stripe
        const stripe = Stripe('pk_test_51Q99twRwGHFYJicpFXqjS5QC4toslBSiKZMwtSfMml5N6gONgaBJ4IJ74YDAgS3QQxU9WRqQCYOtwA22jr3erNwz00AoOChaPY');
        const elements = stripe.elements();

        // Create an instance of the card Element
        const cardElement = elements.create('card');
        cardElement.mount('#card-element');

        // Handle form submission
        const form = document.getElementById('payment-form');
        form.addEventListener('submit', async (event) => {
            event.preventDefault();

            const { paymentMethod, error } = await stripe.createPaymentMethod({
                type: 'card',
                card: cardElement,
            });

            if (error) {
                // Display error to the user
                document.getElementById('card-errors').textContent = error.message;
            } else {
                // Send paymentMethod.id to the server
                const hiddenInput = document.createElement('input');
                hiddenInput.setAttribute('type', 'hidden');
                hiddenInput.setAttribute('name', 'payment_method_id');
                hiddenInput.setAttribute('value', paymentMethod.id);
                form.appendChild(hiddenInput);

                form.submit();
            }
        });
    </script>

</body>
</html>
