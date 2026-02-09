<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include 'admin/config.php';

if (!$con) {
    die("DB Connection Error: " . mysqli_connect_error());
}

$tables = ['users', 'trips', 'agent', 'review'];
foreach ($tables as $table) {
    $res = mysqli_query($con, "SELECT COUNT(*) as total FROM $table");
    if ($res) {
        $row = mysqli_fetch_assoc($res);
        echo "Table $table: OK (" . $row['total'] . " rows)\n";
    } else {
        echo "Table $table: ERROR (" . mysqli_error($con) . ")\n";
    }
}
?>
