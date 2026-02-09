<?php
include 'admin/config.php';
$res = mysqli_query($con, 'DESCRIBE agent');
echo "Agent Table Columns:\n";
while($row = mysqli_fetch_row($res)) {
    echo "- " . $row[0] . "\n";
}
?>
