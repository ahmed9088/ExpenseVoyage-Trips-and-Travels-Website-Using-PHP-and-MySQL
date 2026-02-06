<?php
include("config.php");

$table = 'users';
$sql = "SHOW COLUMNS FROM $table";
$result = $con->query($sql);

if ($result) {
    echo "<h2>Columns in '$table' table:</h2>";
    echo "<ul>";
    while ($row = $result->fetch_assoc()) {
        echo "<li>" . $row['Field'] . " (" . $row['Type'] . ")</li>";
    }
    echo "</ul>";
} else {
    echo "Error: " . $con->error;
}
?>
