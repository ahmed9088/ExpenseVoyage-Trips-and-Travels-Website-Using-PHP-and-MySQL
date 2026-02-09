<?php
include 'admin/config.php';
$res = mysqli_query($con, "DESCRIBE itinerary");
echo "--- ITINERARY FULL SCHEMA ---\n";
while($row = mysqli_fetch_assoc($res)) {
    echo "Field: " . $row['Field'] . " | Type: " . $row['Type'] . "\n";
}

$res = mysqli_query($con, "SELECT * FROM itinerary LIMIT 1");
echo "\n--- ITINERARY SAMPLE DATA ---\n";
if ($row = mysqli_fetch_assoc($res)) {
    print_r($row);
} else {
    echo "No data in itinerary table.\n";
}
?>
