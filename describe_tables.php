<?php
include 'admin/config.php';

function describe($con, $table) {
    echo "Columns for $table:\n";
    $res = mysqli_query($con, "DESCRIBE $table");
    if ($res) {
        while($row = mysqli_fetch_row($res)) {
            echo "- " . $row[0] . "\n";
        }
    } else {
        echo "Error: Table $table not found.\n";
    }
    echo "\n";
}

describe($con, 'trips');
describe($con, 'review');
describe($con, 'agent');
?>
