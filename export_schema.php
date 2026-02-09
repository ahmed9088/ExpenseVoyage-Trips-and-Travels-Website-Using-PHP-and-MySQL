<?php
include 'admin/config.php';
$tables = ['trips', 'agent', 'review', 'users', 'bookings', 'itinerary'];
$schema = [];
foreach ($tables as $t) {
    $res = mysqli_query($con, "DESCRIBE $t");
    if ($res) {
        $schema[$t] = mysqli_fetch_all($res, MYSQLI_ASSOC);
    }
}
header('Content-Type: application/json');
echo json_encode($schema);
?>
