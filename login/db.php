<?php
// Database credentials
$host = 'localhost';
$db   = 'trip_travel';
$user = 'root';
$pass = '';

try {
    // Create a new PDO instance
    $con = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass);
    // Set the PDO error mode to exception
    $con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("ERROR: Could not connect. " . $e->getMessage());
}

// Set the global variable $con for use in other scripts
$con = $con;
?>
