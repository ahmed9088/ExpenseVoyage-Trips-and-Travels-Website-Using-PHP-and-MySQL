<?php
include 'admin/config.php';
$res = mysqli_query($con, 'SELECT trip_image FROM trips LIMIT 1');
if ($res) {
    $row = mysqli_fetch_assoc($res);
    echo "Trip Image: " . $row['trip_image'] . "\n";
}
$res = mysqli_query($con, 'SELECT image FROM agent LIMIT 1');
if ($res) {
    $row = mysqli_fetch_assoc($res);
    echo "Agent Image: " . $row['image'] . "\n";
}
?>
