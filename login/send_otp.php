<?php
session_start();
include 'db.php'; // Database connection
require '../vendor/autoload.php'; // PHPMailer autoload if using Composer

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Function to send OTP via email
function sendOTP($email, $otp) {
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

        // Content
        $mail->isHTML(true);
        $mail->Subject = 'Your OTP Code';
        $mail->Body = "Your OTP code is: <b>$otp</b>";
        $mail->AltBody = "Your OTP code is: $otp";

        $mail->send();
        return true;
    } catch (Exception $e) {
        return false;
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['send_otp'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Generate OTP
    $otp = rand(100000, 999999);
    
    // Store OTP in session for later verification
    $_SESSION['otp'] = $otp;

    // Send OTP to user's email
    if (sendOTP($email, $otp)) {
        echo "OTP has been sent to your email!";
    } else {
        echo "Failed to send OTP!";
    }
}
