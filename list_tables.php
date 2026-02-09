<?php
include 'admin/config.php';
$res = mysqli_query($con, 'SHOW TABLES');
echo "Tables:\n";
while($row = mysqli_fetch_row($res)) {
    echo "- " . $row[0] . "\n";
}
?>
