<?php
session_start();
include '../admin/config.php';
include '../csrf.php';
require '../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$notification = '';
$notification_type = '';
$email = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['send_reset_link'])) {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        die("Security validation failed.");
    }

    $email = mysqli_real_escape_string($con, trim($_POST['email']));

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $notification = "Please enter a valid email address.";
        $notification_type = 'error';
    } else {
        $sql = "SELECT id, first_name FROM users WHERE email = ?";
        $stmt = mysqli_prepare($con, $sql);
        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $user = mysqli_fetch_assoc($result);

        if ($user) {
            $token = bin2hex(random_bytes(32));
            $update = mysqli_prepare($con, "UPDATE users SET reset_token = ?, token_expiry = DATE_ADD(NOW(), INTERVAL 1 HOUR) WHERE email = ?");
            mysqli_stmt_bind_param($update, "ss", $token, $email);

            if (mysqli_stmt_execute($update)) {
                $reset_link = "http://localhost/ExpenseVoyage/login/reset_password.php?token=$token";
                $mail = new PHPMailer(true);
                try {
                    $mail->isSMTP();
                    $mail->Host       = 'smtp.gmail.com';
                    $mail->SMTPAuth   = true;
                    $mail->Username   = 'alizamemonnn@gmail.com'; 
                    $mail->Password   = 'hawt qyeu voqm guek';    
                    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                    $mail->Port       = 587;

                    $mail->setFrom('alizamemonnn@gmail.com', 'ExpenseVoyage Support');
                    $mail->addAddress($email);

                    $mail->isHTML(true);
                    $mail->Subject = 'Reset Your Password - ExpenseVoyage';
                    $mail->Body    = "
                        <div style='background-color: #1a1c20; color: #fff; padding: 40px; font-family: sans-serif;'>
                            <h2 style='color: #d4af37;'>Password Reset Request</h2>
                            <p>Hi " . htmlspecialchars($user['first_name']) . ",</p>
                            <p>We received a request to reset your password for your ExpenseVoyage account.</p>
                            <div style='margin: 30px 0;'>
                                <a href='$reset_link' style='background: linear-gradient(135deg, #d4af37, #aa8822); color: #000; padding: 12px 25px; text-decoration: none; border-radius: 5px; font-weight: bold;'>Reset Password</a>
                            </div>
                            <p>This link will expire in 1 hour.</p>
                            <p style='color: #888; font-size: 12px;'>If you did not request this, please ignore this email.</p>
                        </div>
                    ";

                    $mail->send();
                    $notification = "A password reset link has been dispatched to <b>$email</b>.";
                    $notification_type = 'success';
                } catch (Exception $e) {
                    $notification = "Dispatch failed. Error: {$mail->ErrorInfo}";
                    $notification_type = 'error';
                }
            }
        } else {
            $notification = "No matching account found under this email.";
            $notification_type = 'error';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forget Password - Midnight Luxe</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=Inter:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --gold: #d4af37;
            --gold-dark: #aa8822;
            --dark-bg: #0f1012;
            --glass: rgba(255, 255, 255, 0.05);
            --glass-border: rgba(255, 255, 255, 0.1);
        }

        body {
            font-family: 'Inter', sans-serif;
            background: var(--dark-bg);
            color: #fff;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
            overflow: hidden;
        }

        .bg-glow {
            position: fixed;
            width: 100vw;
            height: 100vh;
            background: radial-gradient(circle at 50% 50%, #1a1c20 0%, #0f1012 100%);
            z-index: -1;
        }

        .glow-orb {
            position: absolute;
            width: 400px;
            height: 400px;
            background: radial-gradient(circle, rgba(212, 175, 55, 0.05) 0%, transparent 70%);
            border-radius: 50%;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            z-index: -1;
        }

        .container {
            width: 90%;
            max-width: 450px;
            background: var(--glass);
            backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border);
            border-radius: 20px;
            padding: 50px 40px;
            box-shadow: 0 25px 50px rgba(0,0,0,0.5);
        }

        h1 {
            font-family: 'Playfair Display', serif;
            font-size: 32px;
            margin-bottom: 10px;
            text-align: center;
            background: linear-gradient(to right, #fff, var(--gold));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        p.subtitle {
            text-align: center;
            color: #888;
            font-size: 14px;
            margin-bottom: 40px;
        }

        .form-group {
            margin-bottom: 25px;
        }

        label {
            display: block;
            margin-bottom: 10px;
            font-size: 14px;
            letter-spacing: 1px;
            text-transform: uppercase;
            color: var(--gold);
        }

        .input-wrapper {
            position: relative;
        }

        input {
            width: 100%;
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid var(--glass-border);
            border-radius: 12px;
            padding: 15px 15px 15px 45px;
            color: #fff;
            box-sizing: border-box;
            transition: 0.3s;
        }

        input:focus {
            outline: none;
            border-color: var(--gold);
            background: rgba(255, 255, 255, 0.08);
        }

        .input-wrapper i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--gold);
            opacity: 0.6;
        }

        .btn {
            width: 100%;
            background: linear-gradient(135deg, var(--gold), var(--gold-dark));
            padding: 15px;
            border: none;
            border-radius: 12px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 2px;
            cursor: pointer;
            transition: 0.3s;
            margin-top: 10px;
        }

        .btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(212, 175, 55, 0.2);
        }

        .notification {
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 25px;
            font-size: 14px;
            text-align: center;
        }

        .notification.success {
            background: rgba(16, 185, 129, 0.1);
            color: #10b981;
            border: 1px solid rgba(16, 185, 129, 0.2);
        }

        .notification.error {
            background: rgba(239, 68, 68, 0.1);
            color: #ef4444;
            border: 1px solid rgba(239, 68, 68, 0.2);
        }

        .back-link {
            display: block;
            text-align: center;
            margin-top: 30px;
            color: #888;
            text-decoration: none;
            font-size: 13px;
            transition: 0.3s;
        }

        .back-link:hover {
            color: var(--gold);
        }
    </style>
</head>
<body>
    <div class="bg-glow"></div>
    <div class="glow-orb"></div>

    <div class="container animate__animated animate__fadeIn">
        <h1>Restore Access</h1>
        <p class="subtitle">Elegance meets recovery. Enter your email below.</p>

        <?php if ($notification): ?>
            <div class="notification <?= $notification_type ?>">
                <?= $notification ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="">
            <?php echo csrf_input(); ?>
            <div class="form-group">
                <label>Email Portfolio</label>
                <div class="input-wrapper">
                    <i class="fas fa-envelope"></i>
                    <input type="email" name="email" required placeholder="Ex: monsieur@voyage.com" value="<?= htmlspecialchars($email) ?>">
                </div>
            </div>

            <button type="submit" name="send_reset_link" class="btn">
                Request Link
            </button>
        </form>

        <a href="account.php" class="back-link">
            <i class="fas fa-arrow-left"></i> Return to Registry
        </a>
    </div>
</body>
</html>