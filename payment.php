<?php
include 'chatbot-loader.php'; 
session_start();
require 'admin/config.php';
require 'audit_helper.php';
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

// Handle the booking confirmation
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Verify CSRF
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        die("Security validation failed.");
    }

    $seats = intval($_POST['seats'] ?? 1);
    $userEmail = $_SESSION['email'];
    $ticketNumber = strtoupper(uniqid("EV"));
    $ticketHash = hash('sha256', $ticketNumber . time() . $_SESSION['userid']);
    $totalPrice = $trip['budget'] * $seats;

    // Get user_id from users table
    $uStmt = $con->prepare("SELECT id FROM users WHERE email = ?");
    $uStmt->bind_param("s", $userEmail);
    $uStmt->execute();
    $uRes = $uStmt->get_result();
    $user = $uRes->fetch_assoc();
    $user_id = $user['id'] ?? 0;

    // PERSIST TO BOOKINGS TABLE with Enterprise tracking
    $bStmt = $con->prepare("INSERT INTO bookings (user_id, trip_id, travel_date, guests, total_price, status, expedition_status, ticket_hash, payment_status) VALUES (?, ?, ?, ?, ?, 'confirmed', 'scheduled', ?, 'paid')");
    $bStmt->bind_param("iisids", $user_id, $trip_id, $trip['starts_date'], $seats, $totalPrice, $ticketHash);
    
    if ($bStmt->execute()) {
        log_audit($con, $user_id, 'BOOKING_CREATED', "Trip ID: $trip_id, Total: $totalPrice");
        $_SESSION['booking_success'] = [
            'trip_name' => $trip['trip_name'],
            'destination' => $trip['destination'],
            'ticket_number' => $ticketNumber,
            'ticket_hash' => $ticketHash,
            'seats' => $seats,
            'total_price' => $totalPrice,
            'user_email' => $userEmail,
            'start_date' => $trip['starts_date']
        ];
        header('Location: success.php');
        exit();
    } else {
        echo "Error recording booking: " . $con->error;
    }
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
    
    <link href="css/custom.css" rel="stylesheet">
    
    <style>
        .checkout-container {
            max-width: 900px;
            margin: 60px auto;
        }

        .payment-method-card {
            border: 1px solid #e2e8f0;
            background: #f8fafc;
            cursor: pointer;
            transition: all 0.3s ease;
            border-radius: 12px;
        }

        .payment-method-card:hover, .payment-method-card.active {
            border-color: var(--primary);
            background: rgba(79, 70, 229, 0.05);
            transform: translateY(-2px);
        }

        .price-summary {
            border-top: 1px dashed #e2e8f0;
            padding-top: 20px;
        }

        .checkout-hero {
            background: linear-gradient(rgba(248, 250, 252, 0.9), rgba(248, 250, 252, 0.9)), 
                        url('<?php echo htmlspecialchars($trip['trip_image']); ?>') center/cover no-repeat;
            padding: 80px 0;
            border-bottom: 1px solid #e2e8f0;
        }
    </style>
</head>
<body class="bg-light">
    <nav class="navbar navbar-expand-lg sticky-top py-3 bg-white shadow-sm">
        <div class="container">
            <a href="index.php" class="navbar-brand">
                <span class="text-primary fw-bold">Expense</span><span class="text-dark">Voyage</span>
            </a>
            <div class="navbar-nav ms-auto">
                <a class="nav-link px-3" href="trip_details.php?id=<?php echo $trip_id; ?>">Return to Voyage Details</a>
            </div>
        </div>
    </nav>

    <div class="checkout-hero text-center">
        <div class="container">
            <h1 class="serif-font display-4 mb-3">Finalize Your Expedition</h1>
            <p class="text-primary text-uppercase tracking-widest fw-bold">Secure Luxury Checkout</p>
        </div>
    </div>

    <div class="container checkout-container">
        <div class="row g-5">
            <div class="col-lg-7">
                <div class="glass-panel p-5 h-100 bg-white shadow-sm border-0">
                    <h2 class="serif-font mb-4">Secure Payment</h2>
                    <p class="text-muted mb-5">Confirm your selection and finalize your luxury journey.</p>

                    <form action="payment.php?trip_id=<?php echo $trip_id; ?>" method="POST">
                        <?php echo csrf_input(); ?>
                        <input type="hidden" name="seats" value="<?php echo intval($_GET['seats'] ?? 1); ?>">

                        <h5 class="serif-font mb-4">Select Method</h5>
                        <div class="row g-3 mb-5">
                            <div class="col-6">
                                <div class="payment-method-card p-4 text-center active">
                                    <i class="fas fa-credit-card fa-2x mb-2 text-primary"></i>
                                    <p class="small mb-0">Credit Card</p>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="payment-method-card p-4 text-center">
                                    <i class="fab fa-apple-pay fa-2x mb-2 text-muted"></i>
                                    <p class="small mb-0">Apple Pay</p>
                                </div>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="small text-muted mb-2">Card Holder</label>
                            <input type="text" class="form-control bg-light border-0 py-3" value="<?php echo htmlspecialchars($_SESSION['name'] ?? ''); ?>">
                        </div>
                        <div class="mb-4">
                            <label class="small text-muted mb-2">Card Details</label>
                            <input type="text" class="form-control bg-light border-0 py-3" placeholder="XXXX XXXX XXXX XXXX">
                        </div>
                        <div class="row g-3 mb-5">
                            <div class="col-6">
                                <input type="text" class="form-control bg-light border-0 py-3" placeholder="MM/YY">
                            </div>
                            <div class="col-6">
                                <input type="text" class="form-control bg-light border-0 py-3" placeholder="CVC">
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary w-100 py-4 shadow">CONFIRM & EXECUTE BOOKING</button>
                    </form>
                </div>
            </div>

            <div class="col-lg-5">
                <div class="glass-panel p-5 bg-white shadow-sm border-0">
                    <h3 class="serif-font mb-4">Expedition Invoice</h3>
                    
                    <div class="d-flex justify-content-between mb-3 border-bottom pb-2">
                        <span class="text-muted">Voyage</span>
                        <span class="text-end fw-bold"><?php echo htmlspecialchars($trip['trip_name']); ?></span>
                    </div>
                    <div class="d-flex justify-content-between mb-3 border-bottom pb-2">
                        <span class="text-muted">Voyagers</span>
                        <span class="fw-bold"><?php echo intval($_GET['seats'] ?? 1); ?></span>
                    </div>
                    <div class="d-flex justify-content-between mb-3 border-bottom pb-2">
                        <span class="text-muted">Base Rate</span>
                        <span class="fw-bold">$<?php echo number_format($trip['budget']); ?></span>
                    </div>
                    
                    <div class="price-summary mt-5 d-flex justify-content-between align-items-center">
                        <h4 class="serif-font mb-0">Total Amount</h4>
                        <h3 class="text-primary fw-bold mb-0 text-gradient">$<?php echo number_format($trip['budget'] * intval($_GET['seats'] ?? 1)); ?></h3>
                    </div>

                    <div class="mt-5 p-4 bg-light text-center small rounded-3">
                        <i class="fas fa-undo me-2 text-primary"></i>
                        Complimentary cancellation within 24h of booking commencement.
                    </div>
                </div>
            </div>
        </div>
    </div>

    <footer class="py-5 border-top bg-white mt-5">
        <div class="container text-center">
            <h4 class="text-primary mb-3">ExpenseVoyage</h4>
            <p class="text-muted small mb-0">&copy; 2026 ExpenseVoyage. Luxury Reimagined.</p>
        </div>
    </footer>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
