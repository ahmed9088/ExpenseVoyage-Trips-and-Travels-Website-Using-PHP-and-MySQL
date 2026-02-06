<?php  
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once("config.php");
$error = "";

// Redirect to dashboard if already logged in
if(isset($_SESSION['auser'])) {
    header("Location: dashboard.php");
    exit();
}

if (isset($_POST['login'])) {
    $identifier = $_POST['user']; // Can be email or username
    $pass = $_POST['pass'];
    
    if (!empty($identifier) && !empty($pass)) {
        // Query the unified users table for administrators
        $query = "SELECT id, first_name, email, password_hash FROM users WHERE (email = ? OR first_name = ?) AND role = 'admin' LIMIT 1";
        $stmt = mysqli_prepare($con, $query);
        mysqli_stmt_bind_param($stmt, 'ss', $identifier, $identifier);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        if ($row = mysqli_fetch_assoc($result)) {
            $stored_hash = $row['password_hash'];
            
            // Validate credentials with priority on hashed passwords
            // Also checking plain text for legacy support during migration
            if (password_verify($pass, $stored_hash) || $pass === $stored_hash) {
                $_SESSION['auser'] = $row['first_name'];
                $_SESSION['aemail'] = $row['email'];
                $_SESSION['aid'] = $row['id'];
                $_SESSION['role'] = 'admin';
                
                header("Location: dashboard.php");
                exit();
            } else {
                $error = 'Authorization failed. Invalid credentials.';
            }
        } else {
            $error = 'Authorization failed. Invalid credentials.';
        }
    } else {
        $error = "Please provide all required credentials.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - ExpenseVoyage Dashboard</title>
    
    <!-- Design Foundation -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Outfit:wght@600;800&display=swap');

        :root {
            --primary: #4f46e5;
            --slate-900: #0f172a;
            --slate-800: #1e293b;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--slate-900);
            color: #fff;
            overflow: hidden;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* Cinematic Background */
        .portal-bg {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: radial-gradient(circle at center, #1e293b 0%, #0f172a 100%);
            z-index: -1;
        }

        .portal-bg::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: url('https://www.transparenttextures.com/patterns/carbon-fibre.png');
            opacity: 0.05;
        }

        /* Authorization Card */
        .auth-card {
            width: 100%;
            max-width: 420px;
            background: rgba(30, 41, 59, 0.7);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 24px;
            padding: 48px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
        }

        .brand-node {
            text-align: center;
            margin-bottom: 40px;
        }

        .brand-node i {
            font-size: 3rem;
            color: var(--primary);
            margin-bottom: 16px;
            filter: drop-shadow(0 0 10px rgba(79, 70, 229, 0.4));
        }

        .brand-node h2 {
            font-family: 'Outfit', sans-serif;
            font-weight: 800;
            letter-spacing: -0.02em;
            margin-bottom: 8px;
        }

        .brand-node p {
            color: #94a3b8;
            font-size: 0.9rem;
        }

        /* Form Styling */
        .form-floating > .form-control {
            background: rgba(15, 23, 42, 0.5);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: #fff;
            border-radius: 12px;
        }

        .form-floating > .form-control:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.1);
        }

        .form-floating > label {
            color: #94a3b8;
        }

        .btn-authorize {
            background: var(--primary);
            color: white;
            border: none;
            padding: 14px;
            border-radius: 12px;
            font-weight: 600;
            width: 100%;
            margin-top: 24px;
            transition: all 0.3s ease;
        }

        .btn-authorize:hover {
            background: #4338ca;
            transform: translateY(-2px);
            box-shadow: 0 10px 15px -3px rgba(79, 70, 229, 0.4);
        }

        .error-node {
            background: rgba(239, 68, 68, 0.1);
            border-left: 4px solid #ef4444;
            padding: 12px;
            border-radius: 8px;
            font-size: 0.85rem;
            color: #f87171;
            margin-bottom: 24px;
        }

        .system-trace {
            text-align: center;
            margin-top: 32px;
            font-size: 0.8rem;
            color: #64748b;
        }

        .system-trace a {
            color: #94a3b8;
            text-decoration: none;
            transition: color 0.3s;
        }

        .system-trace a:hover {
            color: var(--primary);
        }
    </style>
</head>
<body>
    <div class="portal-bg"></div>

    <div class="auth-card animate__animated animate__zoomIn">
        <div class="brand-node">
            <i class="fa-solid fa-compass-drafting"></i>
            <h2>Expense <span style="color:var(--primary)">Voyage</span></h2>
            <p>Please login to access the admin dashboard</p>
        </div>

        <?php if($error): ?>
            <div class="error-node">
                <i class="fa-solid fa-triangle-exclamation me-2"></i> <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <form method="post">
            <div class="form-floating mb-3">
                <input type="text" class="form-control" name="user" id="userInput" placeholder="Email or Username" required>
                <label for="userInput">Email or Username</label>
            </div>
            
            <div class="form-floating mb-4">
                <input type="password" class="form-control" name="pass" id="passInput" placeholder="Password" required>
                <label for="passInput">Password</label>
            </div>

            <button type="submit" name="login" class="btn-authorize">
                Log In
            </button>
        </form>

        <div class="system-trace">
            <a href="../index.php"><i class="fa-solid fa-arrow-left me-1"></i> Back to Home</a>
            <p class="mt-4">© 2024 Admin Dashboard</p>
        </div>
    </div>
</body>
</html>