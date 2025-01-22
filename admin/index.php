<?php  
session_start();
include("config.php");
$error = "";

if (isset($_POST['login'])) {
    $user = $_POST['user'];
    $pass = $_POST['pass'];
    
    if (!empty($user) && !empty($pass)) {
        // Fetch the user data based on the username
        $query = "SELECT auser, apass FROM admin WHERE auser = ?";
        $stmt = mysqli_prepare($con, $query);
        mysqli_stmt_bind_param($stmt, 's', $user);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if ($row = mysqli_fetch_assoc($result)) {
            $stored_pass = $row['apass'];
            
            // Compare the plain text password
            if ($pass === $stored_pass) {
                $_SESSION['auser'] = $user;
                header("Location: dashboard.php");
                exit();
            } else {
                $error = '* Invalid username or password';
            }
        } else {
            $error = '* Invalid username or password';
        }
    } else {
        $error = "* Please fill in all fields!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
      <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
        <title>Tameer.com</title>
    <title>RE Admin - Login</title>
    
    <!-- Favicon -->
   <link rel="shortcut icon" type="image/x-icon" href="assets/img/favicon-32x32.png">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    
    <!-- Fontawesome CSS -->
    <link rel="stylesheet" href="assets/css/font-awesome.min.css">
    
    <!-- Main CSS -->
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

    <div class="page-wrapper login-body">
        <div class="login-wrapper">
            <div class="container" >
                <div class="loginbox" style="margin-left: 300px !important;">
                    <div class="login-right">
                        <div class="login-right-wrap">
                            <h1>Admin Login Panel</h1>
                            <p class="account-subtitle">Access to our dashboard</p>
                            <p style="color:red;"><?php echo $error; ?></p>
                            <!-- Form -->
                            <form method="post">
                                <div class="form-group">
                                    <input class="form-control" name="user" type="text" placeholder="User Name" required>
                                </div>
                                <div class="form-group">
                                    <input class="form-control" type="password" name="pass" placeholder="Password" required>
                                </div>
                                <div class="form-group">
                                    <button class="btn btn-primary btn-block" name="login" type="submit">Login</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- jQuery -->
    <script src="assets/js/jquery-3.2.1.min.js"></script>
    <!-- Bootstrap Core JS -->
    <script src="assets/js/popper.min.js"></script>
    <script src="assets/js/bootstrap.min.js"></script>
    
    <!-- Custom JS -->
    <script src="assets/js/script.js"></script>
</body>
</html>
