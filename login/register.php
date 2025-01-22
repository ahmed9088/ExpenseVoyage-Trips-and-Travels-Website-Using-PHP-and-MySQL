<?php
// Start session and include the database connection file
session_start();
include 'db.php'; 
require '../vendor/autoload.php'; // PHPMailer autoload if using Composer

$message = '';

// Login process
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['login'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $stmt = $con->prepare("SELECT * FROM user WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();
    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['userid'] = $user['id'];
        $_SESSION['name'] = $user['name'];
        $_SESSION['email'] = $user['email'];
        header('Location: ../index.php');
        exit;
    } else {
        $message = "Invalid email or password!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Form</title>
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
            <h2 class="title animation">Login</h2>
            <form action="account.php" method="POST">
                <div class="input-box animation">
                    <input type="email" name="email" required>
                    <label for="">Email</label>
                    <i class='bx bxs-user'></i>
                </div>

                <div class="input-box animation">
                    <input type="password" name="password" required>
                    <label for="">Password</label>
                    <i class='bx bxs-lock-alt'></i>
                </div>

                <div class="linkTxt animation">
                    <p><a href="forget_password.php" class="forget-link">Forget Password</a></p>
                </div>

                <button type="submit" name="login" class="btn animation">Login</button>

                <div class="linkTxt animation">
                    <p>Don't have an account? <a href="register.php" class="register-link">Sign Up</a></p>
                </div>
            </form>
        </div>

        <div class="info-text login">
            <h2 class="animation">Welcome Back!</h2>
            <p class="animation">Lorem ipsum dolor sit amet consectetur adipisicing elit. Deleniti,rem?</p>
        </div>
    </div>

    <script src="account.js"></script>
</body>
</html>
