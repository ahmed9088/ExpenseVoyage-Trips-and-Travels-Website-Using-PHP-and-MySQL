<?php
$con = new mysqli('localhost', 'root', '', 'trip_travel');

if ($con->connect_error) {
    die("Connection failed: " . $con->connect_error);
}

// Check if the ID is set
if (isset($_GET['id'])) {
    $id = intval($_GET['id']); // Get the ID and ensure it's an integer

    // Delete the record
    $query = "DELETE FROM city WHERE city_id = $id";

    if ($con->query($query) === TRUE) {
        // Redirect after deletion
        header('Location: cityadd.php'); // Redirect to your desired page
        exit(); // Stop further script execution
    } else {
        echo "Error deleting record: " . $con->error; // Display any errors
    }
} else {
    echo "No ID specified.";
}

$con->close();
?>
