<?php
// Start session and include the database connection file
session_start();
include 'db.php';
require '../vendor/autoload.php'; // PHPMailer autoload if using Composer

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$message = '';

// Function to send OTP via email
function sendOTP($email, $otp)
{
    $mail = new PHPMailer(true);
    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com'; // Your SMTP host
        $mail->SMTPAuth = true;
        $mail->Username = 'ubaidsoomro505@gmail.com'; // Your email address
        $mail->Password = 'rgja elkh bfag uarz'; // Your email password (use App Password for Gmail if 2FA is enabled)
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        // Recipients
        $mail->setFrom('ubaidsoomro505@gmail.com', 'Trip_travel');
        $mail->addAddress($email);

        $mail->isHTML(true);

        // Email Subject
        $mail->Subject = 'Your OTP Code';

        // Design the email body as an HTML card
        $mail->Body = '
            <div style="max-width: 600px; margin: auto; font-family: Arial, sans-serif; padding: 20px; background-color: #f4f4f4; border-radius: 10px; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);">
                <div style="background-color: #007bff; padding: 20px; text-align: center; border-radius: 10px 10px 0 0;">
                    <h2 style="color: #fff; margin: 0;">Your OTP Code</h2>
                </div>
                <div style="padding: 20px; background-color: #fff; border-radius: 0 0 10px 10px; text-align: center;">
                    <p style="font-size: 18px; color: #333;">Please use the following OTP code to complete your action:</p>
                    <div style="margin: 20px 0;">
                        <span style="display: inline-block; padding: 15px 30px; background-color: #007bff; color: #fff; font-size: 22px; font-weight: bold; border-radius: 5px;">' . $otp . '</span>
                    </div>
                    <p style="color: #666; font-size: 14px;">If you did not request this, please ignore this email.</p>
                </div>
                <div style="text-align: center; padding: 10px; color: #999; font-size: 12px;">
                    <p>&copy; ' . date('Y') . ' Your Company. All rights reserved.</p>
                </div>
            </div>
        ';

        // Alternative plain-text body for email clients that don't support HTML
        $mail->AltBody = "Your OTP code is: $otp";

        // Send email
        $mail->send();
        return true;
    } catch (Exception $e) {
        return false;
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    // Registration process
    if (isset($_POST['register'])) {
        $name = $_POST['name'];
        $email = $_POST['email'];
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Hash the password for security
        $otp = $_POST['otp']; // OTP entered by the user

        // Check if the OTP matches the one stored in the session
        if ($otp == $_SESSION['otp']) {
            // Check if email already exists
            $checkEmail = $con->prepare("SELECT * FROM user WHERE email = ?");
            $checkEmail->execute([$email]);

            if ($checkEmail->rowCount() > 0) {
                $message = "Email already exists!";
            } else {
                // Insert user into the database
                $stmt = $con->prepare("INSERT INTO user (name, email, password) VALUES (?, ?, ?)");
                if ($stmt->execute([$name, $email, $password])) {
                    // Set session variables
                    $_SESSION['name'] = $name;
                    $_SESSION['email'] = $email;
                    $message = "User registered successfully!";

                    // Clear OTP session after successful registration
                    unset($_SESSION['otp']);


                    exit;
                } else {
                    $message = "Error registering user!";
                }
            }
        } else {
            $message = "Invalid OTP!";
        }
    }

    // Login process
    elseif (isset($_POST['login'])) {
        $email = $_POST['email'];
        $password = $_POST['password'];

        // Get the user by email
        $stmt = $con->prepare("SELECT * FROM user WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            // Password is correct, login successful

            // Set session variables
            $_SESSION['name'] = $user['name'];
            $_SESSION['userid'] = $user['id'];
            $_SESSION['email'] = $user['email'];

            // Redirect to welcome or index page
            header('Location: ../index.php');
            exit;
        } else {
            $message = "Invalid email or password!";
        }
    }

    // Generate and send OTP when user clicks register but before they enter OTP
    if (isset($_POST['send_otp'])) {
        $name = $_POST['name'];
        $email = $_POST['email'];
        $password = $_POST['password'];

        // Generate OTP
        $otp = rand(100000, 999999);

        // Store OTP in session for later verification
        $_SESSION['otp'] = $otp;

        // Send OTP to user's email
        if (sendOTP($email, $otp)) {
            $message = "OTP has been sent to your email!";
        } else {
            $message = "Failed to send OTP!";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Register Form</title>
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="account.css">
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

   <div class="wrapper"> 
    <span class="rotate-bg"></span>
    <span class="rotate-bg2"></span>

    <!-- Display message (success or error) -->
    <?php if (!empty($message)): ?>
        <p><?php echo $message; ?></p>
    <?php endif; ?>

    <!-- Login Form -->
    <div class="form-box login">
        <h2 class="title animation" style="--i:0; --j:21">Login</h2>
        <form action="account.php" method="POST">
            <div class="input-box animation" style="--i:1; --j:22">
                <input type="email" name="email" required>
                <label for="">Email</label>
                <i class='bx bxs-user'></i>
            </div>

            <div class="input-box animation" style="--i:2; --j:23">
                <input type="password" name="password" required>
                <label for="">Password</label>
                <i class='bx bxs-lock-alt'></i>
            </div>
            <div class="linkTxt animation">
                <p><a href="forget_password.php" class="forget-link">Forget Password</a></p>
            </div>

            <button type="submit" name="login" class="btn animation">Login</button>

          
            <div class="linkTxt animation">
                <p>Don't have an account? <a href="#" class="register-link">Sign Up</a></p>
            </div>
        </form>
    </div>
    <div class="info-text login">
    <h2 class="animation" style="--i:0; --j:20">Welcome Back!</h2>
    <p class="animation" style="--i:1; --j:21">We're glad to see you again! Please enter your credentials to access your account.</p>
</div>


    <!-- Registration Form -->
    <div class="form-box register">
        <h2 class="title animation">Sign Up</h2>
        <form id="registerForm" method="POST">
            <div class="register_form">
                <div class="input-box animation">
                    <input type="text" name="name" id="name" required>
                    <label for="">Username</label>
                    <i class='bx bxs-user'></i>
                </div>

                <div class="input-box animation">
                    <input type="email" name="email" id="email" required>
                    <label for="">Email</label>
                    <i class='bx bxs-envelope'></i>
                </div>

                <div class="input-box animation">
                    <input type="password" name="password" id="password" required>
                    <label for="">Password</label>
                    <i class='bx bxs-lock-alt'></i>
                </div>
                
                <button type="button" id="sendOtpBtn" class="btn animation">Send OTP</button>
            </div>

            <div class="register_otp" style="display: none;">
                <div class="input-box animation" id="otpBox" style="display: none;">
                    <input type="number" name="otp" id="otp" required>
                    <label for="">OTP</label>
                    <i class='bx bxs-key'></i>
                </div>
            </div>

            <button type="submit" name="register" class="btn animation" style="display: none;" id="registerBtn">Sign
                Up</button>

            <div class="linkTxt animation">
                <p>Already have an account? <a href="#" class="login-link">Login</a></p>
            </div>
        </form>
    </div> 
    <div class="info-text register" style="padding:5%;">
    <h2 class="animation" style="--i:0; --j:20">Join Us!</h2>
    <p class="animation" style="--i:1; --j:21">Create an account to enjoy all the features we offer. Sign up now and get started!</p>
</div>

    </div>
</div>



    <script src="account.js"></script>
</body>

</html>