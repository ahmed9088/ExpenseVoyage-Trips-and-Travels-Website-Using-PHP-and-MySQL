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
        body {
            background: #0f172a;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            margin: 0;
            overflow: hidden;
        }

        #particles-js {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 0;
            background: radial-gradient(circle at center, #1e293b 0%, #0f172a 100%);
        }

        .container {
            z-index: 1;
            width: 90%;
            max-width: 450px;
            background: rgba(255, 255, 255, 0.03);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: var(--radius-xl);
            padding: 50px 40px;
            box-shadow: var(--shadow-premium);
        }

        h1 {
            font-family: 'Playfair Display', serif;
            font-size: 32px;
            margin-bottom: 10px;
            text-align: center;
            background: linear-gradient(to right, #fff, var(--primary-light));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        p.subtitle {
            text-align: center;
            color: rgba(255, 255, 255, 0.6);
            font-size: 14px;
            margin-bottom: 40px;
        }

        .form-group label {
            display: block;
            margin-bottom: 10px;
            font-size: 12px;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            color: var(--primary-light);
            font-weight: 600;
        }

        input {
            width: 100%;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: var(--radius-md);
            padding: 15px 15px 15px 45px;
            color: #fff;
            transition: all 0.3s ease;
        }

        input:focus {
            outline: none;
            border-color: var(--primary-light);
            background: rgba(255, 255, 255, 0.1);
        }

        .input-wrapper i {
            position: absolute;
            left: 15px;
            top: 18px;
            color: var(--primary-light);
            opacity: 0.8;
        }

        .btn {
            width: 100%;
            background: var(--primary);
            color: white;
            padding: 15px;
            border: none;
            border-radius: var(--radius-md);
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 2px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(67, 56, 202, 0.3);
        }

        .back-link {
            display: block;
            text-align: center;
            margin-top: 30px;
            color: rgba(255, 255, 255, 0.5);
            text-decoration: none;
            font-size: 13px;
            transition: 0.3s;
        }

        .back-link:hover {
            color: white;
        }
    </style>
</head>
<body>
    <!-- Animated Background -->
    <script src="https://cdn.jsdelivr.net/particles.js/2.0.0/particles.min.js"></script>
    <div id="particles-js"></div>
    <script>
        particlesJS("particles-js", {
            "particles": {
                "number": { "value": 80 },
                "color": { "value": "#ffffff" },
                "opacity": { "value": 0.1 },
                "size": { "value": 2 },
                "line_linked": { "enable": true, "distance": 150, "color": "#ffffff", "opacity": 0.05, "width": 1 }
            }
        });
    </script>

    <div class="container animate__animated animate__fadeIn">
        <h1 class="serif-font">Restore Access</h1>
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

            <button type="submit" name="send_reset_link" class="btn btn-premium-glow">
                Request Link
            </button>
        </form>

        <a href="account.php" class="back-link">
            <i class="fas fa-arrow-left"></i> Return to Registry
        </a>
    </div>
</body>
</html>