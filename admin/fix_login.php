<?php
include("config.php");

$email = 'ubaidsoomro505@gmail.com';
$firstName = 'Ubaid';
$lastName = 'Soomro';
$pass = '123';
$phone = '1234567890';

// Check if user exists
$check = $con->query("SELECT id FROM users WHERE email = '$email'");

if ($check->num_rows > 0) {
    // User exists, update password and ensure role is admin
    $hashed = password_hash($pass, PASSWORD_DEFAULT);
    $sql = "UPDATE users SET password_hash = '$hashed', role = 'admin', first_name = '$firstName', last_name = '$lastName' WHERE email = '$email'";
    if ($con->query($sql)) {
         echo "<h1>✅ Success!</h1>";
         echo "<p>Admin user <b>$email</b> updated.</p>";
         echo "<p>Login with Password: <b>$pass</b></p>";
    } else {
         echo "<h1>❌ Error</h1>";
         echo "<p>Failed to update user: " . $con->error . "</p>";
    }
} else {
    // Create new user
    $hashed = password_hash($pass, PASSWORD_DEFAULT);
    $sql = "INSERT INTO users (first_name, last_name, email, phone, password_hash, role, created_at) VALUES ('$firstName', '$lastName', '$email', '$phone', '$hashed', 'admin', NOW())";
    if ($con->query($sql)) {
         echo "<h1>✅ Success!</h1>";
         echo "<p>Admin user <b>$email</b> created.</p>";
         echo "<p>Login with Password: <b>$pass</b></p>";
    } else {
         echo "<h1>❌ Error</h1>";
         echo "<p>Failed to create user: " . $con->error . "</p>";
    }
}

echo '<br><a href="index.php">Go to Login</a>';
?>
