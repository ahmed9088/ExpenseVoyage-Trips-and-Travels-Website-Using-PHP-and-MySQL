<?php
session_start();
include 'db.php';

$message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['reset_password'])) {
    $token = $_POST['token'];
    $new_password = $_POST['password'];

    // Check if the token is valid and not expired
    $stmt = $con->prepare("SELECT * FROM user WHERE reset_token = ? AND token_expiry > NOW()");
    $stmt->execute([$token]);
    $user = $stmt->fetch();

    if ($user) {
        // Hash the new password
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

        // Update the password and clear the reset token
        $stmt = $con->prepare("UPDATE user SET password = ?, reset_token = NULL, token_expiry = NULL WHERE reset_token = ?");
        if ($stmt->execute([$hashed_password, $token])) {
            $message = "Password has been reset successfully.";
            
            // Redirect to account.php
            header('Location: account.php');
            exit;
        } else {
            $message = "Failed to reset password.";
        }
        
    } else {
        $message = "Invalid or expired token.";
    }
}
?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
    <link rel="stylesheet" href="account.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f9f9f9;
            margin: 0;
            padding: 0;
        }

        .wrapper {
            max-width: 400px;
            margin: 100px auto;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            padding: 30px;
        }

        h2 {
            margin-bottom: 20px;
            color: var(--highlighted);
            text-align: center;
        }

        .input-box {
            margin: 20px 0;
            position: relative;
        }

        .input-box input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            transition: border-color 0.3s;
            font-size: 16px;
        }

        .input-box input:focus {
            border-color: var(--highlighted);
            outline: none;
        }

        .input-box label {
            position: absolute;
            left: 10px;
            top: -8px;
            font-size: 14px;
            font-weight: bold;
            color: var(--highlighted);
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
            margin-top: 20px;
        }

        .btn:hover {
            background-color: var(--primary);
        }

        p {
            text-align: center;
            color: red;
        }

        .back-to-account {
            margin-top: 30px;
            text-align: center;
            border-top: 1px solid #ddd;
            padding-top: 20px;
        }

        .backbtn {
            display: inline-block;
            width: 45%;
            padding: 10px;
            margin: 5px;
            background-color: var(--highlighted);
            color: white;
            text-decoration: none;
            text-align: center;
            border-radius: 5px;
            transition: background-color 0.3s;
        }

        .backbtn:hover {
            background-color: var(--primary);
        }

        .backtext {
            display: block;
            margin-bottom: 10px;
            font-weight: 600;
            font-size: 18px;
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <h2>Reset Password</h2>
        <form action="reset_password.php" method="POST">
            <input type="hidden" name="token" value="<?php echo isset($_GET['token']) ? htmlspecialchars($_GET['token']) : ''; ?>">
            
            <div class="input-box">
                <input type="password" name="password" required>
                <label for="password">New Password</label>
            </div>
            <?php if (!empty($message)): ?>
                <p><?php echo htmlspecialchars($message); ?></p>
            <?php endif; ?>
            
            <button type="submit" name="reset_password" class="btn">Reset Password</button>
            
           
        </form>
        <div class="back-to-account">
            <span class="backtext">Go Back</span>
            <a href="login.php" class="backbtn">Sign In</a>
            <a href="register.php" class="backbtn">Sign Up</a>
        </div>
    </div>
</body>
</html>
