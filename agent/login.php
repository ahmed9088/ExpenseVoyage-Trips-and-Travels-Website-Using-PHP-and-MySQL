<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once("../admin/config.php");
require_once("../csrf.php");

$message = "";

// If already logged in as agent/admin, skip login
if (isset($_SESSION['role']) && in_array($_SESSION['role'], ['agent', 'admin'])) {
    header("Location: dashboard.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['agent_login'])) {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        die("Security validation failed.");
    }

    $email = mysqli_real_escape_string($con, $_POST['email']);
    $password = $_POST['password'];

    $sql = "SELECT id, first_name, last_name, email, password_hash, role FROM users WHERE email = ? AND role IN ('agent', 'admin')";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $user = mysqli_fetch_assoc($result);

    if ($user && password_verify($password, $user['password_hash'])) {
        $_SESSION['userid'] = $user['id'];
        $_SESSION['name'] = $user['first_name'] . ' ' . $user['last_name'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['role'] = $user['role'];
        
        // Audit log for agent portal entry
        if (file_exists('../audit_helper.php')) {
            include_once '../audit_helper.php';
            log_audit($con, $user['id'], 'AGENT_PORTAL_LOGIN', "Agent Portal Access");
        }

        header("Location: dashboard.php");
        exit();
    } else {
        $message = "Invalid agent credentials or access denied.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agent Intelligence Portal - ExpenseVoyage</title>
    <!-- Favicon -->
    <link href="../img/favicon-32x32.png" rel="icon">
    <!-- Google Web Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&family=Playfair+Display:wght@700&display=swap" rel="stylesheet">
    <!-- Bootstrap 5.3.0 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Animate.css -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    
    <style>
        :root {
            --midnight: #0f172a;
            --indigo-custom: #6366f1;
            --glass: rgba(255, 255, 255, 0.03);
            --glass-border: rgba(255, 255, 255, 0.08);
        }

        body {
            font-family: 'Outfit', sans-serif;
            background: radial-gradient(circle at top right, #1e1b4b, #0f172a);
            color: #f8fafc;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            position: relative;
        }

        /* Abstract Background Elements */
        .bg-glow {
            position: absolute;
            width: 500px;
            height: 500px;
            background: radial-gradient(circle, rgba(99, 102, 241, 0.15), transparent 70%);
            z-index: 0;
            pointer-events: none;
        }

        .login-card {
            background: var(--glass);
            backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border);
            border-radius: 24px;
            padding: 3rem;
            width: 100%;
            max-width: 450px;
            position: relative;
            z-index: 10;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
        }

        .brand-title {
            font-family: 'Playfair Display', serif;
            font-size: 2.2rem;
            letter-spacing: -1px;
        }

        .form-control {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid var(--glass-border);
            color: #fff;
            padding: 0.8rem 1rem;
            border-radius: 12px;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            background: rgba(255, 255, 255, 0.08);
            border-color: var(--indigo-custom);
            color: #fff;
            box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.1);
        }

        .btn-agent {
            background: var(--indigo-custom);
            border: none;
            padding: 0.9rem;
            border-radius: 12px;
            font-weight: 600;
            letter-spacing: 0.5px;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .btn-agent:hover {
            background: #4f46e5;
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(99, 102, 241, 0.2);
        }

        .floating-label {
            position: relative;
            margin-bottom: 1.5rem;
        }

        .input-icon {
            position: absolute;
            right: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: #64748b;
        }

        .error-alert {
            background: rgba(239, 68, 68, 0.1);
            border: 1px solid rgba(239, 68, 68, 0.2);
            color: #fca5a5;
            padding: 1rem;
            border-radius: 12px;
            margin-bottom: 2rem;
            font-size: 0.875rem;
        }
    </style>
</head>
<body>
    <div class="bg-glow" style="top: -10%; right: -10%;"></div>
    <div class="bg-glow" style="bottom: -10%; left: -10%;"></div>

    <div class="login-card animate__animated animate__zoomIn">
        <div class="text-center mb-5">
            <h1 class="brand-title mb-1">Agent<span class="text-indigo-custom">Voyage</span></h1>
            <p class="text-secondary small text-uppercase fw-bold tracking-wider">Mission Command Center</p>
        </div>

        <?php if ($message): ?>
            <div class="error-alert animate__animated animate__shakeX">
                <i class="fa-solid fa-triangle-exclamation me-2"></i><?php echo $message; ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="login.php">
            <?php echo csrf_input(); ?>
            <div class="floating-label">
                <input type="email" name="email" class="form-control" placeholder="Agent Identity (Email)" required autocomplete="off">
                <i class="fa-solid fa-fingerprint input-icon"></i>
            </div>

            <div class="floating-label mb-4">
                <input type="password" name="password" class="form-control" placeholder="Access Key" required>
                <i class="fa-solid fa-key input-icon"></i>
            </div>

            <div class="d-flex justify-content-between align-items-center mb-4 px-2">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="remember">
                    <label class="form-check-label text-secondary fs-xs" for="remember">Authorize Device</label>
                </div>
                <a href="../login/forget_password.php" class="text-indigo-custom text-decoration-none fs-xs fw-bold">Reset Key</a>
            </div>

            <button type="submit" name="agent_login" class="btn btn-primary btn-agent w-100 mb-4">
                Establish Connection <i class="fa-solid fa-arrow-right-long ms-2"></i>
            </button>

            <div class="text-center">
                <p class="text-secondary fs-xs mb-0">Unauthorized access is monitored. <br>System ID: <?php echo substr(md5($_SERVER['REMOTE_ADDR']), 0, 8); ?></p>
            </div>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
