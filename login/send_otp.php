<?php
// Start session and include the database connection file
session_start();
include 'db.php';
require '../vendor/autoload.php'; // PHPMailer autoload if using Composer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$message = '';

// Function to send OTP via email with improved error handling
function sendOTP($email, $otp)
{
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com'; // Your SMTP host
        $mail->SMTPAuth = true;
        $mail->Username = 'memon1ahmed@gmail.com'; // Your email address
        $mail->Password = 'hvol hgpf fdra wrkb'; // Use an App Password for Gmail if 2FA is enabled
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;
        
        // Enable debugging for development (remove in production)
        $mail->SMTPDebug = 0; // Set to 0 for production, 2 for debugging
        $mail->Debugoutput = 'html';
        
        // Recipients
        $mail->setFrom('memon1ahmed@gmail.com', 'ExpenseVoyage');
        $mail->addAddress($email);
        $mail->isHTML(true);
        
        // Email Subject
        $mail->Subject = 'Your OTP Code';
        
        // Design the email body as an HTML card
        $mail->Body = '
            <div style="max-width: 600px; margin: auto; font-family: Arial, sans-serif; padding: 20px; background-color: #f4f4f4; border-radius: 10px; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);">
                <div style="background-color: #4361ee; padding: 20px; text-align: center; border-radius: 10px 10px 0 0;">
                    <h2 style="color: #fff; margin: 0;">Your OTP Code</h2>
                </div>
                <div style="padding: 20px; background-color: #fff; border-radius: 0 0 10px 10px; text-align: center;">
                    <p style="font-size: 18px; color: #333;">Please use the following OTP code to complete your action:</p>
                    <div style="margin: 20px 0;">
                        <span style="display: inline-block; padding: 15px 30px; background-color: #4361ee; color: #fff; font-size: 22px; font-weight: bold; border-radius: 5px;">' . $otp . '</span>
                    </div>
                    <p style="color: #666; font-size: 14px;">If you did not request this, please ignore this email.</p>
                </div>
                <div style="text-align: center; padding: 10px; color: #999; font-size: 12px;">
                    <p>&copy; ' . date('Y') . ' ExpenseVoyage. All rights reserved.</p>
                </div>
            </div>
        ';
        
        // Alternative plain-text body for email clients that don't support HTML
        $mail->AltBody = "Your OTP code is: $otp";
        
        // Send email
        if($mail->send()) {
            return true;
        }
        return false;
    } catch (Exception $e) {
        // Log error instead of just returning false
        error_log("Mailer Error: " . $mail->ErrorInfo);
        return false;
    }
}

// Handle OTP verification
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['verify_otp'])) {
        // Collect OTP from form inputs
        $otp = '';
        for ($i = 1; $i <= 6; $i++) {
            if (isset($_POST['otp' . $i])) {
                $otp .= $_POST['otp' . $i];
            }
        }
        
        // Debugging - log the submitted OTP
        error_log("Submitted OTP: " . $otp);
        
        // Check if registration data exists
        if (isset($_SESSION['reg_data']) && isset($_SESSION['reg_data']['email'])) {
            // Debugging - log session data
            error_log("Session data: " . print_r($_SESSION['reg_data'], true));
            
            $email = $_SESSION['reg_data']['email'];
            // Ensure both OTPs are strings for consistent comparison
            $storedOtp = (string)$_SESSION['reg_data']['otp'];
            $otpExpiry = $_SESSION['reg_data']['otp_expiry'];
            
            // Debugging - log the stored OTP and expiry
            error_log("Stored OTP: " . $storedOtp);
            error_log("OTP Expiry: " . $otpExpiry);
            error_log("Current Time: " . date('Y-m-d H:i:s'));
            
            // Verify OTP and check if it's expired
            if ($otp === $storedOtp && strtotime($otpExpiry) > time()) {
                // OTP is correct and not expired, register the user
                $name = $_SESSION['reg_data']['name'];
                $password = password_hash($_SESSION['reg_data']['password'], PASSWORD_DEFAULT);
                
                // Check if email already exists (again, for security)
                $checkEmail = $con->prepare("SELECT * FROM user WHERE email = ?");
                $checkEmail->execute([$email]);
                if ($checkEmail->rowCount() > 0) {
                    $message = "Email already exists!";
                    error_log("Email already exists: " . $email);
                } else {
                    try {
                        // Insert user into the database with all required fields
                        $sql = "INSERT INTO user (name, email, password, is_verified, otp, otp_expiry, created_at) VALUES (?, ?, ?, 1, ?, ?, NOW())";
                        $stmt = $con->prepare($sql);
                        
                        // Log the parameters before execution
                        error_log("Inserting user with parameters: name=$name, email=$email, password=HASHED, otp=$storedOtp, otp_expiry=$otpExpiry");
                        
                        if ($stmt->execute([$name, $email, $password, $storedOtp, $otpExpiry])) {
                            // Get the user ID
                            $userId = $con->lastInsertId();
                            error_log("User inserted successfully with ID: " . $userId);
                            
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
                            
                            // Try to get more detailed error information
                            $errorCode = $con->errorCode();
                            $errorInfo = $con->errorInfo();
                            error_log("PDO Error Code: " . $errorCode);
                            error_log("PDO Error Info: " . print_r($errorInfo, true));
                        }
                    } catch (PDOException $e) {
                        $message = "Database error: " . $e->getMessage();
                        error_log("PDO Exception: " . $e->getMessage());
                        error_log("Exception Trace: " . $e->getTraceAsString());
                    }
                }
            } else {
                $message = "Invalid or expired OTP!";
                error_log("OTP verification failed. Submitted: " . $otp . ", Stored: " . $storedOtp . ", Expired: " . (strtotime($otpExpiry) <= time() ? 'Yes' : 'No'));
            }
        } else {
            $message = "Session expired. Please try again.";
            error_log("Session data not found");
        }
    } elseif (isset($_POST['resend_otp'])) {
        // Check if enough time has passed since last OTP (60 seconds cooldown)
        if (isset($_SESSION['otp_generated_time']) && (time() - $_SESSION['otp_generated_time']) < 60) {
            $message = "Please wait " . (60 - (time() - $_SESSION['otp_generated_time'])) . " seconds before requesting another OTP.";
        } else if (isset($_SESSION['reg_data']) && isset($_SESSION['reg_data']['email'])) {
            // Generate new OTP
            $otp = rand(100000, 999999);
            $otpExpiry = date('Y-m-d H:i:s', time() + 600); // 10 minutes expiry
            $_SESSION['otp_generated_time'] = time();
            
            // Update registration data with new OTP - ensure it's stored as string
            $_SESSION['reg_data']['otp'] = (string)$otp;
            $_SESSION['reg_data']['otp_expiry'] = $otpExpiry;
            
            // Debugging - log the new OTP
            error_log("Resent OTP: " . $otp . " for email: " . $_SESSION['reg_data']['email']);
            
            // Send OTP to user's email
            if (sendOTP($_SESSION['reg_data']['email'], $otp)) {
                $message = "OTP has been resent to your email!";
            } else {
                $message = "Failed to resend OTP!";
            }
        } else {
            $message = "Session expired. Please try again.";
        }
    }
}

// Check if user came from registration page with OTP sent
if (!isset($_SESSION['reg_data']) || !isset($_SESSION['reg_data']['email'])) {
    // If no registration data, redirect to account.php
    header('Location: account.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify OTP - ExpenseVoyage</title>
    <meta name="description" content="Verify your email address with OTP">
    
    <!-- Favicon -->
    <link href="../img/favicon.ico" rel="icon">
    <link rel="apple-touch-icon" sizes="180x180" href="../img/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="../img/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="../img/favicon-16x16.png">
    <link rel="manifest" href="../img/site.webmanifest">
    
    <!-- Google Web Fonts -->
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
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
            --gradient: linear-gradient(135deg, var(--primary), var(--secondary));
            --gradient-light: linear-gradient(120deg, rgba(67, 97, 238, 0.1), rgba(76, 201, 240, 0.1));
            --success: #10b981;
            --error: #ef4444;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f5f7ff;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
            overflow-x: hidden;
            position: relative;
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
        
        /* Container */
        .container {
            width: 100%;
            max-width: 500px;
            background: white;
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
            padding: 40px;
            position: relative;
            z-index: 1;
            animation: fadeInUp 0.6s ease forwards;
        }
        
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .header h2 {
            font-size: 28px;
            color: var(--dark);
            margin-bottom: 10px;
        }
        
        .header p {
            color: #6b7280;
            font-size: 16px;
        }
        
        .email-display {
            text-align: center;
            margin-bottom: 20px;
            padding: 12px;
            background-color: rgba(67, 97, 238, 0.1);
            border-radius: 8px;
            font-weight: 500;
        }
        
        .otp-section {
            margin: 30px 0;
        }
        
        .otp-label {
            display: block;
            margin-bottom: 15px;
            font-size: 16px;
            color: #374151;
            text-align: center;
        }
        
        .otp-inputs {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }
        
        .otp-input {
            width: 50px;
            height: 50px;
            text-align: center;
            font-size: 20px;
            font-weight: 600;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            transition: all 0.3s ease;
            outline: none;
        }
        
        .otp-input:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(67, 97, 238, 0.1);
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
            background: var(--gradient);
            color: white;
            margin-bottom: 15px;
        }
        
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(67, 97, 238, 0.2);
        }
        
        .btn:disabled {
            opacity: 0.7;
            cursor: not-allowed;
            transform: none;
        }
        
        .resend-otp {
            text-align: center;
            margin-top: 20px;
        }
        
        .resend-otp a {
            color: var(--primary);
            text-decoration: none;
            font-weight: 500;
        }
        
        .resend-otp a:hover {
            color: var(--secondary);
            text-decoration: underline;
        }
        
        .countdown {
            display: block;
            margin-top: 5px;
            font-size: 14px;
            color: #6b7280;
        }
        
        .back-to-login {
            text-align: center;
            margin-top: 30px;
        }
        
        .back-to-login a {
            color: var(--primary);
            text-decoration: none;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }
        
        .back-to-login a:hover {
            color: var(--secondary);
            text-decoration: underline;
        }
        
        .back-to-login i {
            margin-right: 8px;
        }
        
        .message-notification {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 16px 24px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            z-index: 1000;
            max-width: 350px;
            animation: slideIn 0.3s ease forwards;
        }
        
        .message-notification.success {
            border-left: 4px solid var(--success);
            color: var(--success);
        }
        
        .message-notification.error {
            border-left: 4px solid var(--error);
            color: var(--error);
        }
        
        @keyframes slideIn {
            from {
                transform: translateX(100%);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }
        
        @keyframes slideOut {
            from {
                transform: translateX(0);
                opacity: 1;
            }
            to {
                transform: translateX(100%);
                opacity: 0;
            }
        }
        
        /* Loading Spinner */
        .spinner {
            display: inline-block;
            width: 16px;
            height: 16px;
            border: 2px solid rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            border-top-color: white;
            animation: spin 1s ease-in-out infinite;
        }
        
        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }
        
        /* Responsive */
        @media (max-width: 480px) {
            .container {
                padding: 30px 20px;
            }
            
            .otp-input {
                width: 40px;
                height: 40px;
                font-size: 18px;
            }
        }
        
        /* Debugging info */
        .debug-info {
            background-color: #f8f9fa;
            border: 1px solid #e9ecef;
            border-radius: 5px;
            padding: 10px;
            margin-bottom: 20px;
            font-family: monospace;
            font-size: 12px;
            color: #495057;
        }
        
        /* Manual Registration Button */
        .manual-reg-btn {
            background-color: #3f37c9;
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            margin-top: 10px;
            margin-bottom: 20px;
            width: 100%;
        }
        
        .manual-reg-btn:hover {
            background-color: #332fa1;
        }
        
        .db-result {
            margin-top: 10px;
            padding: 10px;
            border-radius: 4px;
            font-size: 14px;
            display: none;
        }
        
        .db-result.success {
            background-color: #d1fae5;
            color: #065f46;
            display: block;
        }
        
        .db-result.error {
            background-color: #fee2e2;
            color: #991b1b;
            display: block;
        }
    </style>
</head>
<body>
    <!-- Animated Background -->
    <div id="particles-js"></div>
    
    <div class="container">
        <div class="header">
            <h2>Verify Your Email</h2>
            <p>We've sent a 6-digit code to your email</p>
        </div>
        
        <!-- Manual Registration Button -->
        <button class="manual-reg-btn" id="manualRegBtn">Create Account Manually (Debug)</button>
        <div id="manualRegResult" class="db-result"></div>
        
        <!-- Debugging Information -->
        <div class="debug-info">
            <strong>Debug Info:</strong><br>
            Email: <?php echo isset($_SESSION['reg_data']['email']) ? htmlspecialchars($_SESSION['reg_data']['email']) : 'Not set'; ?><br>
            Stored OTP: <?php echo isset($_SESSION['reg_data']['otp']) ? $_SESSION['reg_data']['otp'] : 'Not set'; ?><br>
            OTP Expiry: <?php echo isset($_SESSION['reg_data']['otp_expiry']) ? $_SESSION['reg_data']['otp_expiry'] : 'Not set'; ?><br>
            Current Time: <?php echo date('Y-m-d H:i:s'); ?><br>
            Is Expired: <?php echo isset($_SESSION['reg_data']['otp_expiry']) && strtotime($_SESSION['reg_data']['otp_expiry']) <= time() ? 'Yes' : 'No'; ?>
        </div>
        
        <div class="email-display">
            <i class="fas fa-envelope"></i> <?php echo htmlspecialchars($_SESSION['reg_data']['email']); ?>
        </div>
        
        <?php if (!empty($message)): ?>
            <div class="message-notification <?php echo strpos($message, 'successfully') !== false || strpos($message, 'sent') !== false ? 'success' : 'error'; ?>">
                <?php echo $message; ?>
            </div>
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const message = document.querySelector('.message-notification');
                    
                    setTimeout(() => {
                        message.style.animation = 'slideOut 0.3s ease forwards';
                        setTimeout(() => {
                            message.style.display = 'none';
                        }, 300);
                    }, 5000);
                });
            </script>
        <?php endif; ?>
        
        <form method="POST" action="send_otp.php" id="otpForm">
            <div class="otp-section">
                <label class="otp-label">Enter the verification code</label>
                <div class="otp-inputs">
                    <input type="text" class="otp-input" name="otp1" maxlength="1" pattern="[0-9]" required>
                    <input type="text" class="otp-input" name="otp2" maxlength="1" pattern="[0-9]" required>
                    <input type="text" class="otp-input" name="otp3" maxlength="1" pattern="[0-9]" required>
                    <input type="text" class="otp-input" name="otp4" maxlength="1" pattern="[0-9]" required>
                    <input type="text" class="otp-input" name="otp5" maxlength="1" pattern="[0-9]" required>
                    <input type="text" class="otp-input" name="otp6" maxlength="1" pattern="[0-9]" required>
                </div>
            </div>
            
            <button type="submit" name="verify_otp" class="btn" id="verifyBtn">Verify & Complete Registration</button>
        </form>
        
        <div class="resend-otp">
            <p>Didn't receive the code? <a href="#" id="resendLink">Resend OTP</a></p>
            <span class="countdown" id="countdown"></span>
        </div>
        
        <div class="back-to-login">
            <a href="account.php"><i class="fas fa-arrow-left"></i> Back to Login</a>
        </div>
    </div>
    
    <!-- JavaScript Libraries -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
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
        
        document.addEventListener('DOMContentLoaded', function() {
            // Manual Registration Button
            document.getElementById('manualRegBtn').addEventListener('click', function() {
                const manualRegResult = document.getElementById('manualRegResult');
                
                // Create a form to submit the manual registration request
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = 'manual_register.php';
                
                const submitField = document.createElement('input');
                submitField.type = 'hidden';
                submitField.name = 'manual_register';
                submitField.value = '1';
                form.appendChild(submitField);
                
                // Submit the form
                document.body.appendChild(form);
                form.submit();
            });
            
            // OTP input handling
            const otpInputs = document.querySelectorAll('.otp-input');
            otpInputs.forEach((input, index) => {
                input.addEventListener('input', function() {
                    if (input.value.length === 1 && index < otpInputs.length - 1) {
                        otpInputs[index + 1].focus();
                    }
                });
                
                input.addEventListener('keydown', function(e) {
                    if (e.key === 'Backspace' && input.value === '' && index > 0) {
                        otpInputs[index - 1].focus();
                    }
                });
                
                // Only allow numbers
                input.addEventListener('input', function() {
                    this.value = this.value.replace(/[^0-9]/g, '');
                });
            });
            
            // Form submission handling
            document.getElementById('otpForm').addEventListener('submit', function(e) {
                e.preventDefault();
                
                // Show loading state
                const verifyBtn = document.getElementById('verifyBtn');
                const originalText = verifyBtn.innerHTML;
                verifyBtn.innerHTML = '<span class="spinner"></span> Verifying...';
                verifyBtn.disabled = true;
                
                // Submit the form
                this.submit();
            });
            
            // Resend OTP handling
            document.getElementById('resendLink').addEventListener('click', function(e) {
                e.preventDefault();
                
                // Create a form to submit the resend request
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = 'send_otp.php';
                
                const submitField = document.createElement('input');
                submitField.type = 'hidden';
                submitField.name = 'resend_otp';
                submitField.value = '1';
                form.appendChild(submitField);
                
                // Submit the form
                document.body.appendChild(form);
                form.submit();
            });
            
            // Start countdown for resend button (60 seconds)
            let timeLeft = 60;
            const resendLink = document.getElementById('resendLink');
            const countdown = document.getElementById('countdown');
            resendLink.style.pointerEvents = 'none';
            resendLink.style.opacity = '0.5';
            const timerInterval = setInterval(() => {
                timeLeft--;
                
                if (timeLeft <= 0) {
                    clearInterval(timerInterval);
                    countdown.textContent = '';
                    resendLink.style.pointerEvents = 'auto';
                    resendLink.style.opacity = '1';
                } else {
                    countdown.textContent = `Resend available in ${timeLeft} seconds`;
                }
            }, 1000);
        });
    </script>
</body>
</html>