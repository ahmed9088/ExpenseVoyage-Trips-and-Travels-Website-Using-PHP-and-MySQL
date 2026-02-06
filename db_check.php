<?php
include 'admin/config.php';
echo "--- TABLES ---\n";
$res = mysqli_query($con, "SHOW TABLES");
while($row = mysqli_fetch_array($res)) {
    echo $row[0] . "\n";
}
echo "\n--- USERS SCHEMA ---\n";
$res = mysqli_query($con, "DESCRIBE users");
while($row = mysqli_fetch_assoc($res)) {
    print_r($row);
}
if ($result = mysqli_query($con, "SHOW TABLES LIKE 'admin'")) {
    if (mysqli_num_rows($result) > 0) {
        echo "\n--- ADMIN SCHEMA ---\n";
        $res = mysqli_query($con, "DESCRIBE admin");
        while($row = mysqli_fetch_assoc($res)) {
            print_r($row);
        }
    }
}
?>
