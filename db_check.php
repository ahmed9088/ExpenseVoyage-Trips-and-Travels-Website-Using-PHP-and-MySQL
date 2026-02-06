<?php
include 'admin/config.php';
$res = mysqli_query($con, "DESCRIBE bookings");
echo "--- BOOKINGS SCHEMA ---\n";
while($row = mysqli_fetch_assoc($res)) {
    print_r($row);
}
?>
