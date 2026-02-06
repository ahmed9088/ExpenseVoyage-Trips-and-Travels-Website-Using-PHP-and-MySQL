<?php
include("config.php");

$email = 'ubaidsoomro505@gmail.com';
$pass = '123';

echo "<h2>Admin User Check</h2>";
echo "Checking for email: <strong>$email</strong><br>";

// 1. Check in 'users' table
$sql = "SELECT * FROM users WHERE email = '$email'";
$result = $con->query($sql);

if ($result->num_rows > 0) {
    echo "<h3 style='color:green'>Found in 'users' table!</h3>";
    $row = $result->fetch_assoc();
    echo "ID: " . $row['id'] . "<br>";
    echo "Role: " . $row['role'] . "<br>";
    echo "First Name: " . $row['first_name'] . "<br>";
    echo "Password Hash (truncated): " . substr($row['password_hash'], 0, 10) . "...<br>";
    
    // Verify password
    if (password_verify($pass, $row['password_hash'])) {
        echo "<h3 style='color:green'>Password Verify: SUCCESS</h3>";
    } else {
        echo "<h3 style='color:red'>Password Verify: FAILED</h3>";
        echo "Hash stored: " . $row['password_hash'] . "<br>";
        echo "Trying plain match... ";
        if ($row['password_hash'] === $pass) {
             echo "MATCH (Plaintext)<br>";
        } else {
             echo "NO MATCH<br>";
        }
    }
} else {
    echo "<h3 style='color:red'>NOT FOUND in 'users' table.</h3>";
}

// 2. Check in legacy 'admin' table just for info
echo "<hr>Checking legacy 'admin' table...<br>";
$sql_admin = "SELECT * FROM admin WHERE aemail = '$email'";
$result_admin = $con->query($sql_admin);

if ($result_admin && $result_admin->num_rows > 0) {
    echo "Found in legacy 'admin' table (This is expected but we don't use it anymore).<br>";
} else {
    echo "Not found in legacy 'admin' table.<br>";
}
?>
