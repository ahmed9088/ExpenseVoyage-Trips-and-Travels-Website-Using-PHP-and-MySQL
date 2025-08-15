<?php
// Start session and include the database connection file
session_start();
include 'db.php';

$message = '';

// Handle manual registration (bypassing OTP verification)
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['manual_register'])) {
    // Check if registration data exists
    if (isset($_SESSION['reg_data']) && isset($_SESSION['reg_data']['email'])) {
        $name = $_SESSION['reg_data']['name'];
        $email = $_SESSION['reg_data']['email'];
        $password = password_hash($_SESSION['reg_data']['password'], PASSWORD_DEFAULT);
        $otp = $_SESSION['reg_data']['otp'];
        $otpExpiry = $_SESSION['reg_data']['otp_expiry'];
        
        // Check if email already exists
        $checkEmail = $con->prepare("SELECT * FROM user WHERE email = ?");
        $checkEmail->execute([$email]);
        
        if ($checkEmail->rowCount() > 0) {
            $message = "Email already exists!";
        } else {
            try {
                // Insert user into the database with all required fields
                $sql = "INSERT INTO user (name, email, password, is_verified, otp, otp_expiry, created_at) VALUES (?, ?, ?, 1, ?, ?, NOW())";
                $stmt = $con->prepare($sql);
                
                if ($stmt->execute([$name, $email, $password, $otp, $otpExpiry])) {
                    // Get the user ID
                    $userId = $con->lastInsertId();
                    
                    // Set session variables
                    $_SESSION['name'] = $name;
                    $_SESSION['email'] = $email;
                    $_SESSION['userid'] = $userId;
                    
                    // Clear registration data
                    unset($_SESSION['reg_data']);
                    
                    // Redirect to index page
                    header('Location: ../index.php');
                    exit;
                } else {
                    $message = "Error registering user!";
                    $errorInfo = $stmt->errorInfo();
                    error_log("Database error: " . print_r($errorInfo, true));
                }
            } catch (PDOException $e) {
                $message = "Database error: " . $e->getMessage();
                error_log("PDO Exception: " . $e->getMessage());
            }
        }
    } else {
        $message = "Session expired. Please try again.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manual Registration - ExpenseVoyage</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f5f7ff;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }
        
        .container {
            width: 100%;
            max-width: 500px;
            background: white;
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
            padding: 40px;
        }
        
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .header h2 {
            font-size: 28px;
            color: #212529;
            margin-bottom: 10px;
        }
        
        .message {
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            text-align: center;
        }
        
        .message.error {
            background-color: #fee2e2;
            color: #991b1b;
        }
        
        .message.success {
            background-color: #d1fae5;
            color: #065f46;
        }
        
        .btn {
            display: block;
            width: 100%;
            padding: 14px;
            font-size: 16px;
            font-weight: 600;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
            border: none;
            outline: none;
            text-align: center;
            background: linear-gradient(135deg, #4361ee, #3f37c9);
            color: white;
            margin-bottom: 15px;
        }
        
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(67, 97, 238, 0.2);
        }
        
        .back-btn {
            display: inline-flex;
            align-items: center;
            color: #4361ee;
            text-decoration: none;
            font-weight: 500;
        }
        
        .back-btn i {
            margin-right: 8px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>Manual Registration</h2>
        </div>
        
        <?php if (!empty($message)): ?>
            <div class="message <?php echo strpos($message, 'successfully') !== false ? 'success' : 'error'; ?>">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>
        
        <?php if (!isset($_SESSION['reg_data']) || !isset($_SESSION['reg_data']['email'])): ?>
            <div class="message error">
                Session expired. Please try again.
            </div>
            <a href="account.php" class="back-btn"><i class="fas fa-arrow-left"></i> Back to Login</a>
        <?php endif; ?>
    </div>
</body>
</html>