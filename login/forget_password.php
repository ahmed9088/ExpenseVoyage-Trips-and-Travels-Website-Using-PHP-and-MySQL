<?php
session_start();
include 'db.php';
require '../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$message = '';

function sendResetLink($email, $token)
{
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'ubaidsoomro505@gmail.com';
        $mail->Password = 'rgja elkh bfag uarz';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        $mail->setFrom('ubaidsoomro505@gmail.com', 'Trip_travel');
        $mail->addAddress($email);

        $mail->isHTML(true);
        $mail->Subject = 'Password Reset Request';
        $mail->Body = "To reset your password, please click the following link: <a href='localhost/expense-voyage/login/reset_password.php?token=$token'>Reset Password</a>";
        $mail->AltBody = "To reset your password, please visit the following link: localhost/travel/login/reset_password.php?token=$token";

        $mail->send();
        return true;
    } catch (Exception $e) {
        return false;
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['reset_request'])) {
    $email = $_POST['email'];

    $stmt = $con->prepare("SELECT * FROM user WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user) {
        $token = bin2hex(random_bytes(50)); // Generate a secure token

        $stmt = $con->prepare("UPDATE user SET reset_token = ?, token_expiry = DATE_ADD(NOW(), INTERVAL 1 HOUR) WHERE email = ?");
        $stmt->execute([$token, $email]);

        if (sendResetLink($email, $token)) {
            $message = "Password reset link has been sent to your email.";
        } else {
            $message = "Failed to send reset link.";
        }
    } else {
        $message = "Email not found.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password</title>
    <link rel="stylesheet" href="account.css">

    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f9f9f9;
            margin: 0;
            padding: 0;
        }

        .wrapper {
            padding: 5%;
            max-width: 500px;
            /* Limit max width for better readability */
            margin: auto;
            /* Center the wrapper */
            background: white;
            /* Background for the form area */
            border-radius: 8px;
            /* Rounded corners */
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            /* Subtle shadow */
        }

        h2 {
            margin-bottom: 20px;
            color: var(--highlighted);
            /* Highlighted color for the title */
        }

        .input-box {
            margin: 20px 0;
            text-align: left;
        }

        .input-box span {
            display: block;
            font-weight: 600;
            margin-bottom: 5px;
        }

        .input-box input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            transition: border-color 0.3s;
        }

        .input-box input:focus {
            border-color: var(--highlighted);
            /* Change border color on focus */
            outline: none;
            /* Remove default outline */
        }

        .btn {
            width: 100%;
            height: 45px;
            background-color: var(--highlighted);
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            font-weight: 600;
            transition: background-color 0.3s;
        }

        .btn:hover {
            background-color: var(--primary);
        }

        .back-to-account {
            margin-top: 30px;
            border-top: 1px solid #ddd;
            padding-top: 20px;
            text-align: center;
        }

        .backtext {
            font-weight: 600;
            font-size: 18px;
            margin-bottom: 10px;
        }

        .backbtn {
            display: inline-block;
            width: 45%;
            height: 45px;
            background-color: var(--highlighted);
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            font-weight: 600;
            transition: background-color 0.3s;
            text-decoration: none;
            /* Remove underline */
            line-height: 45px;
            /* Center text vertically */
        }

        .backbtn:hover {
            background-color: var(--primary);
        }
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
    <div class="wrapper">
        <h2>Forgot Password</h2>
        <form action="forget_password.php" method="POST">
            <div class="input-box">
                <span>Email</span>
                <input type="email" name="email" required placeholder="Enter Email Address">
            </div>
            <?php if (!empty($message)): ?>
                <p style="    color: #fff;
    background-color: #5cb85c;
    border-color: #4cae4c;padding: 10px; margin-bottom: 10px;"><?php echo $message; ?></p>
            <?php endif; ?>
            <button type="submit" name="reset_request" class="btn">Send Reset Link</button>

        </form>
        <div class="back-to-account">
            <span class="backtext">Go Back</span>
            <div>
                <a href="login.php" class="backbtn">Sign In</a>
                <a href="register.php" class="backbtn">Sign Up</a>
            </div>
        </div>
    </div>
</body>

</html>