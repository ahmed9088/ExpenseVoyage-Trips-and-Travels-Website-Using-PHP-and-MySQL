<?php
include 'admin/config.php';

function describe($con, $table) {
    echo "--- Table: $table ---\n";
    $res = mysqli_query($con, "DESCRIBE $table");
    if ($res) {
        while($row = mysqli_fetch_assoc($res)) {
            echo "Field: " . $row['Field'] . "\n";
        }
    } else {
        echo "Error: Table $table not found.\n";
    }
    echo "\n";
}

describe($con, 'trips');
describe($con, 'agent');
describe($con, 'review');
describe($con, 'users');
?>
