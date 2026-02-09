<?php
include 'admin/config.php';
$tables = ['trips', 'agent', 'review', 'users', 'bookings', 'itinerary', 'notifications', 'wishlist'];
$schema = [];
foreach ($tables as $t) {
    $res = mysqli_query($con, "DESCRIBE `$t` ");
    if ($res) {
        $schema[$t] = mysqli_fetch_all($res, MYSQLI_ASSOC);
    }
}
file_put_contents('schema.json', json_encode($schema, JSON_PRETTY_PRINT));
echo "Schema saved to schema.json\n";
?>
