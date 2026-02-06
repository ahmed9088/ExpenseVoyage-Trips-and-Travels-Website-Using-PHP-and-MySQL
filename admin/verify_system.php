<?php
// Database Schema Verification Script
require_once("config.php");

echo "<h2>Database Schema Verification</h2>";

// Check users table
echo "<h3>Users Table</h3>";
$result = $con->query("DESCRIBE users");
echo "<table border='1'><tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th></tr>";
while($row = $result->fetch_assoc()) {
    echo "<tr><td>{$row['Field']}</td><td>{$row['Type']}</td><td>{$row['Null']}</td><td>{$row['Key']}</td></tr>";
}
echo "</table><br>";

// Check trips table
echo "<h3>Trips Table</h3>";
$result = $con->query("DESCRIBE trips");
echo "<table border='1'><tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th></tr>";
while($row = $result->fetch_assoc()) {
    echo "<tr><td>{$row['Field']}</td><td>{$row['Type']}</td><td>{$row['Null']}</td><td>{$row['Key']}</td></tr>";
}
echo "</table><br>";

// Test critical queries
echo "<h3>Query Tests</h3>";
$tests = [
    "SELECT COUNT(*) as count FROM users WHERE role = 'admin'" => "Admin Count",
    "SELECT COUNT(*) as count FROM users WHERE role = 'traveler'" => "Traveler Count",
    "SELECT COUNT(*) as count FROM users WHERE role = 'agent'" => "Agent Count",
    "SELECT COUNT(*) as count FROM trips" => "Trip Count"
];

foreach($tests as $query => $label) {
    $result = $con->query($query);
    if($result) {
        $row = $result->fetch_assoc();
        echo "<p>✓ {$label}: {$row['count']}</p>";
    } else {
        echo "<p>✗ {$label}: ERROR - " . $con->error . "</p>";
    }
}

echo "<h3>Session Status</h3>";
session_start();
echo "<p>Session Active: " . (session_status() === PHP_SESSION_ACTIVE ? "YES" : "NO") . "</p>";
echo "<p>Admin User Set: " . (isset($_SESSION['auser']) ? "YES (" . $_SESSION['auser'] . ")" : "NO") . "</p>";

$con->close();
?>
