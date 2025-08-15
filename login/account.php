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

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Login process
    if (isset($_POST['login'])) {
        $email = $_POST['email'];
        $password = $_POST['password'];
        
        // Get the user by email
        $stmt = $con->prepare("SELECT * FROM user WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        
        if ($user && password_verify($password, $user['password'])) {
            // Check if user is verified
            if ($user['is_verified'] == 1) {
                // Password is correct and user is verified, login successful
                // Set session variables
                $_SESSION['name'] = $user['name'];
                $_SESSION['userid'] = $user['id'];
                $_SESSION['email'] = $user['email'];
                
                // Redirect to welcome or index page
                header('Location: ../index.php');
                exit;
            } else {
                $message = "Please verify your email address before logging in!";
            }
        } else {
            $message = "Invalid email or password!";
        }
    }
    
    // Generate and send OTP when user clicks register but before they enter OTP
    if (isset($_POST['send_otp'])) {
        $name = $_POST['name'];
        $email = $_POST['email'];
        $password = $_POST['password'];
        
        // Basic validation
        if (empty($name) || empty($email) || empty($password)) {
            $message = "Please fill in all fields!";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $message = "Please enter a valid email address!";
        } elseif (strlen($password) < 6) {
            $message = "Password must be at least 6 characters long!";
        } else {
            // Check if email already exists
            $checkEmail = $con->prepare("SELECT * FROM user WHERE email = ?");
            $checkEmail->execute([$email]);
            if ($checkEmail->rowCount() > 0) {
                $message = "Email already exists!";
            } else {
                // Generate OTP
                // Generate OTP
$otp = rand(100000, 999999);
// Convert to string to ensure consistent comparison
$otpStr = (string)$otp;

// Set OTP expiry time (10 minutes from now)
$otpExpiry = date('Y-m-d H:i:s', time() + 600);

// Store registration data and OTP in session
$_SESSION['reg_data'] = [
    'name' => $name,
    'email' => $email,
    'password' => $password,
    'otp' => $otpStr, // Store as string
    'otp_expiry' => $otpExpiry
];

// For debugging - let's log the OTP
error_log("Generated OTP: " . $otpStr . " for email: " . $email);
                
                // Send OTP to user's email
                if (sendOTP($email, $otp)) {
                    // Set flag to show OTP section
                    $_SESSION['otp_sent'] = true;
                    $_SESSION['otp_generated_time'] = time();
                    
                    // Redirect to OTP verification page
                    header('Location: send_otp.php');
                    exit; // Make sure to exit after redirect
                } else {
                    $message = "Failed to send OTP! Please try again later.";
                }
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login / Register - ExpenseVoyage</title>
    <meta name="description" content="Login or register for ExpenseVoyage to access premium travel experiences">
    
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
        
        /* Scrollbar Styling */
        ::-webkit-scrollbar {
            width: 10px;
        }
        
        ::-webkit-scrollbar-track {
            background: #f1f1f1;
        }
        
        ::-webkit-scrollbar-thumb {
            background: var(--primary);
            border-radius: 5px;
        }
        
        ::-webkit-scrollbar-thumb:hover {
            background: var(--secondary);
        }
        
        /* Main Container */
        .container {
            width: 100%;
            max-width: 1000px;
            margin: 0 auto;
            padding: 20px;
            position: relative;
            z-index: 1;
        }
        
        /* Auth Container */
        .auth-container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            display: flex;
            min-height: 600px;
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
        
        /* Auth Image Section */
        .auth-image {
            flex: 1;
            background: var(--gradient);
            position: relative;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 40px;
            color: white;
        }
        
        .auth-image::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: url('../img/login-bg.jpg') no-repeat center center/cover;
            opacity: 0.2;
            mix-blend-mode: overlay;
        }
        
        .auth-image-content {
            position: relative;
            z-index: 1;
            text-align: center;
            max-width: 400px;
        }
        
        .auth-image-content h2 {
            font-size: 36px;
            font-weight: 700;
            margin-bottom: 20px;
            text-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
        }
        
        .auth-image-content p {
            font-size: 16px;
            line-height: 1.6;
            margin-bottom: 30px;
            opacity: 0.9;
        }
        
        /* Auth Form Section */
        .auth-form {
            flex: 1;
            padding: 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        
        .auth-tabs {
            display: flex;
            margin-bottom: 30px;
            border-bottom: 1px solid #e5e7eb;
        }
        
        .auth-tab {
            padding: 12px 24px;
            font-size: 16px;
            font-weight: 600;
            color: #6b7280;
            cursor: pointer;
            border-bottom: 3px solid transparent;
            margin-bottom: -1px;
            transition: all 0.3s ease;
        }
        
        .auth-tab.active {
            color: var(--primary);
            border-bottom-color: var(--primary);
        }
        
        .auth-tab:hover {
            color: var(--primary);
        }
        
        /* Form Content */
        .form-content {
            display: none;
        }
        
        .form-content.active {
            display: block;
            animation: fadeIn 0.5s ease;
        }
        
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .form-header {
            margin-bottom: 30px;
        }
        
        .form-header h2 {
            font-size: 28px;
            font-weight: 700;
            color: var(--dark);
            margin-bottom: 8px;
        }
        
        .form-header p {
            color: #6b7280;
            font-size: 14px;
        }
        
        /* Form Groups */
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-size: 14px;
            font-weight: 500;
            color: #374151;
        }
        
        .form-control {
            width: 100%;
            padding: 12px 16px;
            font-size: 15px;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            transition: all 0.3s ease;
            outline: none;
        }
        
        .form-control:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(67, 97, 238, 0.1);
        }
        
        .input-icon {
            position: relative;
        }
        
        .input-icon .form-control {
            padding-left: 42px;
        }
        
        .input-icon i {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: #9ca3af;
            font-size: 16px;
        }
        
        .input-icon .form-control:focus + i {
            color: var(--primary);
        }
        
        /* Form Options */
        .form-options {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 24px;
        }
        
        .checkbox-group {
            display: flex;
            align-items: center;
        }
        
        .checkbox-group input {
            width: 18px;
            height: 18px;
            margin-right: 8px;
            cursor: pointer;
        }
        
        .checkbox-group label {
            font-size: 14px;
            color: #4b5563;
            cursor: pointer;
        }
        
        .forgot-link {
            font-size: 14px;
            color: var(--primary);
            text-decoration: none;
            transition: all 0.3s ease;
        }
        
        .forgot-link:hover {
            color: var(--secondary);
            text-decoration: underline;
        }
        
        /* Buttons */
        .btn {
            display: inline-block;
            padding: 12px 24px;
            font-size: 15px;
            font-weight: 600;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
            border: none;
            outline: none;
            text-align: center;
        }
        
        .btn-primary {
            background: var(--gradient);
            color: white;
            width: 100%;
            margin-bottom: 16px;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(67, 97, 238, 0.2);
        }
        
        .btn-outline {
            background: transparent;
            border: 1px solid var(--primary);
            color: var(--primary);
        }
        
        .btn-outline:hover {
            background: var(--primary);
            color: white;
        }
        
        /* Divider */
        .divider {
            display: flex;
            align-items: center;
            margin: 24px 0;
        }
        
        .divider::before {
            content: '';
            flex: 1;
            height: 1px;
            background: #e5e7eb;
        }
        
        .divider span {
            padding: 0 12px;
            font-size: 14px;
            color: #6b7280;
        }
        
        .divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: #e5e7eb;
        }
        
        /* Social Login */
        .social-login {
            margin-top: 16px;
        }
        
        .social-buttons {
            display: flex;
            gap: 12px;
        }
        
        .social-btn {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 10px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .social-btn i {
            margin-right: 8px;
        }
        
        .social-btn.facebook {
            background: #1877f2;
            color: white;
        }
        
        .social-btn.facebook:hover {
            background: #166fe5;
        }
        
        .social-btn.google {
            background: #ea4335;
            color: white;
        }
        
        .social-btn.google:hover {
            background: #d33b2c;
        }
        
        /* Message Notification */
        .message-notification {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 16px 24px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            z-index: 1000;
            display: none;
            animation: slideIn 0.3s ease forwards;
            max-width: 350px;
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
        
        /* Password Strength Indicator */
        .password-strength {
            height: 5px;
            background-color: #e5e7eb;
            border-radius: 3px;
            margin-top: 8px;
            overflow: hidden;
        }
        
        .password-strength-meter {
            height: 100%;
            width: 0;
            border-radius: 3px;
            transition: all 0.3s ease;
        }
        
        .password-strength-meter.weak {
            width: 33.33%;
            background-color: #ef4444;
        }
        
        .password-strength-meter.medium {
            width: 66.66%;
            background-color: #f59e0b;
        }
        
        .password-strength-meter.strong {
            width: 100%;
            background-color: #10b981;
        }
        
        .password-strength-text {
            font-size: 12px;
            margin-top: 5px;
            color: #6b7280;
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .auth-container {
                flex-direction: column;
            }
            
            .auth-image {
                min-height: 200px;
                padding: 30px 20px;
            }
            
            .auth-image-content h2 {
                font-size: 28px;
            }
            
            .auth-image-content p {
                font-size: 14px;
            }
            
            .auth-form {
                padding: 30px 20px;
            }
        }
    </style>
</head>
<body>
    <!-- Animated Background -->
    <div id="particles-js"></div>
    
    <!-- Message Notification -->
    <?php if (!empty($message)): ?>
        <div class="message-notification <?php echo strpos($message, 'successfully') !== false || strpos($message, 'sent') !== false ? 'success' : 'error'; ?>">
            <?php echo $message; ?>
        </div>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const message = document.querySelector('.message-notification');
                message.style.display = 'block';
                
                setTimeout(() => {
                    message.style.animation = 'slideOut 0.3s ease forwards';
                    setTimeout(() => {
                        message.style.display = 'none';
                    }, 300);
                }, 5000);
            });
        </script>
    <?php endif; ?>
    
    <div class="container">
        <div class="auth-container">
            <!-- Auth Image Section -->
            <div class="auth-image">
                <div class="auth-image-content">
                    <h2>Welcome to ExpenseVoyage</h2>
                    <p>Discover amazing destinations and create unforgettable memories with our premium travel experiences.</p>
                    <button class="btn btn-outline" id="switchToRegister">Create Account</button>
                </div>
            </div>
            
            <!-- Auth Form Section -->
            <div class="auth-form">
                <!-- Auth Tabs -->
                <div class="auth-tabs">
                    <div class="auth-tab active" id="loginTab">Sign In</div>
                    <div class="auth-tab" id="registerTab">Sign Up</div>
                </div>
                
                <!-- Login Form -->
                <div class="form-content active" id="loginForm">
                    <div class="form-header">
                        <h2>Welcome Back</h2>
                        <p>Sign in to your account to continue</p>
                    </div>
                    
                    <form action="account.php" method="POST">
                        <div class="form-group input-icon">
                            <i class="fas fa-envelope"></i>
                            <input type="email" name="email" class="form-control" placeholder="Email Address" required>
                        </div>
                        
                        <div class="form-group input-icon">
                            <i class="fas fa-lock"></i>
                            <input type="password" name="password" class="form-control" placeholder="Password" required>
                        </div>
                        
                        <div class="form-options">
                            <div class="checkbox-group">
                                <input type="checkbox" id="remember" name="remember">
                                <label for="remember">Remember me</label>
                            </div>
                            <a href="forget_password.php" class="forgot-link">Forgot Password?</a>
                        </div>
                        
                        <button type="submit" name="login" class="btn btn-primary">Sign In</button>
                    </form>
                    
                    <div class="divider">
                        <span>Or continue with</span>
                    </div>
                    
                    <div class="social-login">
                        <div class="social-buttons">
                            <button class="social-btn facebook" type="button">
                                <i class="fab fa-facebook-f"></i> Facebook
                            </button>
                            <button class="social-btn google" type="button">
                                <i class="fab fa-google"></i> Google
                            </button>
                        </div>
                    </div>
                </div>
                
                <!-- Register Form -->
                <div class="form-content" id="registerForm">
                    <div class="form-header">
                        <h2>Create Account</h2>
                        <p>Join us to start your travel journey</p>
                    </div>
                    
                    <form id="registrationForm" method="POST" action="account.php">
                        <div class="form-group input-icon">
                            <i class="fas fa-user"></i>
                            <input type="text" name="name" id="name" class="form-control" placeholder="Full Name" required>
                        </div>
                        
                        <div class="form-group input-icon">
                            <i class="fas fa-envelope"></i>
                            <input type="email" name="email" id="email" class="form-control" placeholder="Email Address" required>
                        </div>
                        
                        <div class="form-group input-icon">
                            <i class="fas fa-lock"></i>
                            <input type="password" name="password" id="password" class="form-control" placeholder="Password" required>
                            <div class="password-strength">
                                <div class="password-strength-meter" id="strength-meter"></div>
                            </div>
                            <div class="password-strength-text" id="strength-text"></div>
                        </div>
                        
                        <button type="submit" name="send_otp" class="btn btn-primary" id="sendOtpBtn">Send OTP</button>
                    </form>
                    
                    <div class="divider">
                        <span>Or sign up with</span>
                    </div>
                    
                    <div class="social-login">
                        <div class="social-buttons">
                            <button class="social-btn facebook" type="button">
                                <i class="fab fa-facebook-f"></i> Facebook
                            </button>
                            <button class="social-btn google" type="button">
                                <i class="fab fa-google"></i> Google
                            </button>
                        </div>
                    </div>
                </div>
            </div>
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
            // Tab switching
            const loginTab = document.getElementById('loginTab');
            const registerTab = document.getElementById('registerTab');
            const loginForm = document.getElementById('loginForm');
            const registerForm = document.getElementById('registerForm');
            const switchToRegister = document.getElementById('switchToRegister');
            
            // Function to switch to login tab
            function switchToLogin() {
                loginTab.classList.add('active');
                registerTab.classList.remove('active');
                loginForm.classList.add('active');
                registerForm.classList.remove('active');
            }
            
            // Function to switch to register tab
            function switchToRegisterTab() {
                registerTab.classList.add('active');
                loginTab.classList.remove('active');
                registerForm.classList.add('active');
                loginForm.classList.remove('active');
            }
            
            // Add event listeners for tab switching
            loginTab.addEventListener('click', switchToLogin);
            registerTab.addEventListener('click', switchToRegisterTab);
            switchToRegister.addEventListener('click', switchToRegisterTab);
            
            // Password strength checker
            const password = document.getElementById('password');
            const strength_meter = document.getElementById('strength-meter');
            const strength_text = document.getElementById('strength-text');
            
            if (password) {
                password.addEventListener('input', function() {
                    const pwd = this.value;
                    let strength = 0;
                    
                    // Check password length
                    if (pwd.length >= 8) {
                        strength += 1;
                    }
                    
                    // Check for lowercase letters
                    if (pwd.match(/[a-z]+/)) {
                        strength += 1;
                    }
                    
                    // Check for uppercase letters
                    if (pwd.match(/[A-Z]+/)) {
                        strength += 1;
                    }
                    
                    // Check for numbers
                    if (pwd.match(/[0-9]+/)) {
                        strength += 1;
                    }
                    
                    // Check for special characters
                    if (pwd.match(/[$@#&!]+/)) {
                        strength += 1;
                    }
                    
                    // Update strength meter
                    strength_meter.className = 'password-strength-meter';
                    
                    if (strength <= 2) {
                        strength_meter.classList.add('weak');
                        strength_text.textContent = 'Weak password';
                        strength_text.style.color = '#ef4444';
                    } else if (strength <= 3) {
                        strength_meter.classList.add('medium');
                        strength_text.textContent = 'Medium password';
                        strength_text.style.color = '#f59e0b';
                    } else {
                        strength_meter.classList.add('strong');
                        strength_text.textContent = 'Strong password';
                        strength_text.style.color = '#10b981';
                    }
                });
            }
            
            // OTP functionality
            const sendOtpBtn = document.getElementById('sendOtpBtn');
            const registrationForm = document.getElementById('registrationForm');
            
            // Handle Send OTP button click
            if (sendOtpBtn) {
                sendOtpBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    
                    const name = document.getElementById('name').value;
                    const email = document.getElementById('email').value;
                    const password = document.getElementById('password').value;
                    
                    // Client-side validation
                    if (!name || !email || !password) {
                        showNotification('Please fill in all fields', 'error');
                        return;
                    }
                    
                    if (!validateEmail(email)) {
                        showNotification('Please enter a valid email address', 'error');
                        return;
                    }
                    
                    if (password.length < 6) {
                        showNotification('Password must be at least 6 characters long', 'error');
                        return;
                    }
                    
                    // Show loading state
                    const originalText = sendOtpBtn.innerHTML;
                    sendOtpBtn.innerHTML = '<span class="spinner"></span> Sending...';
                    sendOtpBtn.disabled = true;
                    
                    // Create form data
                    const formData = new FormData();
                    formData.append('name', name);
                    formData.append('email', email);
                    formData.append('password', password);
                    formData.append('send_otp', '1');
                    
                    // Send AJAX request
                    fetch('account.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => {
                        if (response.redirected) {
                            // If the response is a redirect, follow it
                            window.location.href = response.url;
                        } else {
                            return response.text();
                        }
                    })
                    .then(data => {
                        if (data) {
                            // If there's a response text, it means there was an error
                            showNotification(data, 'error');
                            sendOtpBtn.innerHTML = originalText;
                            sendOtpBtn.disabled = false;
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        showNotification('An error occurred. Please try again.', 'error');
                        sendOtpBtn.innerHTML = originalText;
                        sendOtpBtn.disabled = false;
                    });
                });
            }
            
            // Email validation function
            function validateEmail(email) {
                const re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
                return re.test(String(email).toLowerCase());
            }
            
            // Function to show notifications
            function showNotification(message, type) {
                // Remove any existing notifications
                const existingNotifications = document.querySelectorAll('.message-notification');
                existingNotifications.forEach(notification => notification.remove());
                
                // Create new notification
                const notification = document.createElement('div');
                notification.className = `message-notification ${type}`;
                notification.textContent = message;
                document.body.appendChild(notification);
                notification.style.display = 'block';
                
                // Auto-remove notification after 5 seconds
                setTimeout(() => {
                    notification.style.animation = 'slideOut 0.3s ease forwards';
                    setTimeout(() => {
                        notification.remove();
                    }, 300);
                }, 5000);
            }
        });
    </script>
</body>
</html>