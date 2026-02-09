<?php
session_start();
include 'admin/config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = mysqli_real_escape_string($con, $_POST['name']);
    $email = mysqli_real_escape_string($con, $_POST['email']);
    $message = mysqli_real_escape_string($con, $_POST['review']);
    $userid = $_SESSION['userid'] ?? 0;
    
    // Default avatar for reviews
    $image = "Ali.jpg";
    
    $query = "INSERT INTO review (userid, username, email, usermessage, image, date_time) 
              VALUES ($userid, '$name', '$email', '$message', '$image', NOW())";
    
    if (mysqli_query($con, $query)) {
        header("Location: index.php?review_success=1");
    } else {
        header("Location: about.php?error=1");
    }
    exit();
}
?>
