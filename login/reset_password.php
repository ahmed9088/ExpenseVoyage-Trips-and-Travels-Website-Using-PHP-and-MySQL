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
            margin-bottom: 30px;
            text-align: center;
            background: linear-gradient(to right, #fff, var(--primary-light));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .form-group label {
            display: block;
            margin-bottom: 10px;
            font-size: 12px;
            text-transform: uppercase;
            color: var(--primary-light);
            letter-spacing: 1.5px;
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

        .success-flow { text-align: center; }
        .success-flow i { font-size: 60px; color: var(--primary-light); margin-bottom: 20px; }
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
        <h1 class="serif-font">New Credentials</h1>

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

                <button type="submit" name="reset_password" class="btn btn-premium-glow">Confirm Change</button>
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
