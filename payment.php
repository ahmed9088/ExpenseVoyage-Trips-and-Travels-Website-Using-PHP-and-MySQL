<?php
include 'chatbot-loader.php'; 
session_start();
require 'admin/config.php';
include 'csrf.php';

// Check if user is logged in
if (!isset($_SESSION['email'])) {
    $_SESSION['redirect_after_login'] = 'payment.php?' . $_SERVER['QUERY_STRING'];
    header("Location: login/account.php");
    exit();
}

$trip_id = intval($_GET['trip_id'] ?? 0);
if (!$trip_id) die("Invalid Trip Reference.");

$stmt = $con->prepare("SELECT * FROM trips WHERE trip_id = ?");
$stmt->bind_param("i", $trip_id);
$stmt->execute();
$trip = $stmt->get_result()->fetch_assoc();

if (!$trip) die("Voyage not found.");

// Handle the booking confirmation (simplified as requested, focusing on working well)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Verify CSRF
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        die("Security validation failed.");
    }

    $seats = intval($_POST['seats'] ?? 1);
    $userEmail = $_SESSION['email'];
    $ticketNumber = uniqid("EV-");
    $totalPrice = $trip['budget'] * $seats;

    // Direct success simulation (as requested for working well/faster)
    $_SESSION['booking_success'] = [
        'trip_name' => $trip['trip_name'],
        'destination' => $trip['destination'],
        'ticket_number' => $ticketNumber,
        'seats' => $seats,
        'total_price' => $totalPrice,
        'user_email' => $userEmail,
        'start_date' => $trip['starts_date']
    ];
    
    header('Location: success.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Secure Checkout | ExpenseVoyage</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;800&family=Playfair+Display:ital,wght@0,400..900;1,400..900&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <script src="https://cdn.particles.js/2.0.0/particles.min.js"></script>
    
    <link href="css/custom.css" rel="stylesheet">
    
    <style>
        .checkout-container {
            max-width: 900px;
            margin: 100px auto;
        }

        .payment-method-card {
            border: 1px solid rgba(255, 255, 255, 0.1);
            background: rgba(255, 255, 255, 0.03);
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .payment-method-card:hover, .payment-method-card.active {
            border-color: var(--gold);
            background: rgba(212, 175, 55, 0.05);
        }

        .price-summary {
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            padding-top: 20px;
        }
    </style>
</head>
<body>
    <div id="particles-js"></div>

    <nav class="navbar navbar-expand-lg sticky-top glass-panel mx-4 mt-3 py-3">
        <div class="container">
            <a href="index.php" class="navbar-brand">
                <span class="text-gold">Expense</span><span class="text-white">Voyage</span>
            </a>
        </div>
    </nav>

    <div class="container checkout-container">
        <div class="row g-5">
            <div class="col-lg-7">
                <div class="glass-panel p-5 h-100">
                    <h2 class="serif-font mb-4">Secure Checkout</h2>
                    <p class="text-white-50 mb-5">Confirm your selection and finalize your luxury journey.</p>

                    <form action="payment.php?trip_id=<?php echo $trip_id; ?>" method="POST">
                        <?php echo csrf_input(); ?>
                        <input type="hidden" name="seats" value="<?php echo intval($_GET['seats'] ?? 1); ?>">

                        <h5 class="serif-font mb-4">Payment Method</h5>
                        <div class="row g-3 mb-5">
                            <div class="col-6">
                                <div class="payment-method-card p-4 text-center active">
                                    <i class="fas fa-credit-card fa-2x mb-2 text-gold"></i>
                                    <p class="small mb-0">Credit Card</p>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="payment-method-card p-4 text-center">
                                    <i class="fab fa-paypal fa-2x mb-2 text-white-50"></i>
                                    <p class="small mb-0">PayPal</p>
                                </div>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="small text-white-50 mb-2">Card Details</label>
                            <input type="text" class="form-control bg-white-5 border-secondary text-white py-3" placeholder="XXXX XXXX XXXX XXXX">
                        </div>
                        <div class="row g-3 mb-5">
                            <div class="col-6">
                                <input type="text" class="form-control bg-white-5 border-secondary text-white py-3" placeholder="MM/YY">
                            </div>
                            <div class="col-6">
                                <input type="text" class="form-control bg-white-5 border-secondary text-white py-3" placeholder="CVC">
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary w-100 py-4 tracking-widest">CONFIRM & EXECUTE BOOKING</button>
                    </form>
                </div>
            </div>

            <div class="col-lg-5">
                <div class="glass-panel p-5">
                    <h3 class="serif-font mb-4">Invoice Summary</h3>
                    
                    <div class="d-flex justify-content-between mb-3">
                        <span class="text-white-50">Voyage</span>
                        <span class="text-end"><?php echo htmlspecialchars($trip['trip_name']); ?></span>
                    </div>
                    <div class="d-flex justify-content-between mb-3">
                        <span class="text-white-50">Seats</span>
                        <span><?php echo intval($_GET['seats'] ?? 1); ?></span>
                    </div>
                    <div class="d-flex justify-content-between mb-3">
                        <span class="text-white-50">Unit Price</span>
                        <span>$<?php echo number_format($trip['budget']); ?></span>
                    </div>
                    
                    <div class="price-summary mt-4 d-flex justify-content-between align-items-center">
                        <h4 class="serif-font mb-0">Total Amount</h4>
                        <h3 class="text-gold mb-0">$<?php echo number_format($trip['budget'] * intval($_GET['seats'] ?? 1)); ?></h3>
                    </div>

                    <div class="mt-5 p-3 bg-white-10 text-center small">
                        <i class="fas fa-undo me-2 text-gold"></i>
                        Complimentary cancellation within 24h
                    </div>
                </div>
            </div>
        </div>
    </div>

    <footer class="py-5 border-top border-secondary mt-5">
        <div class="container text-center">
            <h4 class="text-gold mb-3">ExpenseVoyage</h4>
            <p class="text-white-50 small mb-0">&copy; 2026 ExpenseVoyage. Excellence Delivered.</p>
        </div>
    </footer>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="js/custom.js"></script>
</body>
</html>