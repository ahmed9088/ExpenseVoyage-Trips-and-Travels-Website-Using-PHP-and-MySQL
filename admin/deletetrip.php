<?php

    include 'config.php';

    $id = $_GET['deleteid'];

    $sql = "DELETE FROM trips WHERE trip_id = $id";
    $result = mysqli_query($con,$sql);

    if($result)
    {
        header("Location:tripview.php");

    }
        header("Location: tripview.php");


?>