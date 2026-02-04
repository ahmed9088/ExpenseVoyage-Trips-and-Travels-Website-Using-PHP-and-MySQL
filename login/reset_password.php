<?php
session_start();
include '../admin/config.php';
include '../csrf.php';

$notification = '';
$notification_type = '';
$show_form = true;

$token = $_GET['token'] ?? $_POST['token'] ?? '';

if (empty($token)) {
    $notification = "No reset token provided.";
    $notification_type = 'error';
    $show_form = false;
} else {
    $sql = "SELECT id FROM users WHERE reset_token = ? AND token_expiry > NOW()";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "s", $token);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $user = mysqli_fetch_assoc($result);
    
    if (!$user) {
        $notification = "This password reset link is invalid or has expired.";
        $notification_type = 'error';
        $show_form = false;
    }
}

if ($show_form && $_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['reset_password'])) {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        die("Security validation failed.");
    }

    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    
    if ($password !== $confirm_password) {
        $notification = "Passwords do not match!";
        $notification_type = 'error';
    } elseif (strlen($password) < 6) {
        $notification = "Password must be at least 6 characters!";
        $notification_type = 'error';
    } else {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $update = mysqli_prepare($con, "UPDATE users SET password_hash = ?, reset_token = NULL, token_expiry = NULL WHERE reset_token = ?");
        mysqli_stmt_bind_param($update, "ss", $hashed_password, $token);
        
        if (mysqli_stmt_execute($update)) {
            $notification = "Password updated successfully.";
            $notification_type = 'success';
            $show_form = false;
        } else {
            $notification = "Database error. Please try again.";
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
    <title>Reset Password - Midnight Luxe</title>
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
            margin-bottom: 30px;
            text-align: center;
            background: linear-gradient(to right, #fff, var(--gold));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .form-group {
            margin-bottom: 25px;
        }

        label {
            display: block;
            margin-bottom: 10px;
            font-size: 14px;
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
        }

        .input-wrapper i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--gold);
        }

        .btn {
            width: 100%;
            background: linear-gradient(135deg, var(--gold), var(--gold-dark));
            padding: 15px;
            border: none;
            border-radius: 12px;
            font-weight: 700;
            color: #000;
            cursor: pointer;
            text-transform: uppercase;
            letter-spacing: 2px;
            transition: 0.3s;
        }

        .btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(212, 175, 55, 0.2);
        }

        .notification {
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 25px;
            text-align: center;
            font-size: 14px;
        }

        .notification.success { background: rgba(16, 185, 129, 0.1); color: #10b981; border: 1px solid rgba(16, 185, 129, 0.2); }
        .notification.error { background: rgba(239, 68, 68, 0.1); color: #ef4444; border: 1px solid rgba(239, 68, 68, 0.2); }

        .success-flow { text-align: center; }
        .success-flow i { font-size: 60px; color: var(--gold); margin-bottom: 20px; }
    </style>
</head>
<body>
    <div class="bg-glow"></div>

    <div class="container animate__animated animate__fadeIn">
        <h1>New Credentials</h1>

        <?php if ($notification): ?>
            <div class="notification <?= $notification_type ?>">
                <?= $notification ?>
            </div>
        <?php endif; ?>

        <?php if ($notification_type == 'success'): ?>
            <div class="success-flow">
                <i class="fas fa-check-circle"></i>
                <p style="margin-bottom: 30px; color: #888;">Your entry key has been updated.</p>
                <a href="account.php" style="text-decoration: none;" class="btn">Proceed to Login</a>
            </div>
        <?php elseif ($show_form): ?>
            <form method="POST" action="">
                <?php echo csrf_input(); ?>
                <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">
                
                <div class="form-group">
                    <label>New Secret</label>
                    <div class="input-wrapper">
                        <i class="fas fa-lock"></i>
                        <input type="password" name="password" required minlength="6" placeholder="At least 6 characters">
                    </div>
                </div>

                <div class="form-group">
                    <label>Confirm Secret</label>
                    <div class="input-wrapper">
                        <i class="fas fa-lock"></i>
                        <input type="password" name="confirm_password" required minlength="6" placeholder="Repeat secret">
                    </div>
                </div>

                <button type="submit" name="reset_password" class="btn">Confirm Change</button>
            </form>
        <?php else: ?>
            <div style="text-align: center;">
                <i class="fas fa-exclamation-triangle" style="font-size: 50px; color: #ef4444; margin-bottom: 20px;"></i>
                <p style="color: #888; margin-bottom: 30px;">Access link expired or invalid.</p>
                <a href="forget_password.php" style="text-decoration: none;" class="btn">Request New Link</a>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
