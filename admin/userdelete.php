<?php
include("config.php");
$uid = $_GET['id'];

// Check and delete user profile image
$sql = "SELECT profile_image FROM users WHERE id = ?";
if($stmt = $con->prepare($sql)) {
    $stmt->bind_param("i", $uid);
    $stmt->execute();
    $res = $stmt->get_result();
    if($row = $res->fetch_assoc()) {
        $img = $row['profile_image'];
        // Assuming images are stored relative to the project root or a specific folder. 
        // Based on migration, profile_image might be a path.
        // Checking commonly used paths. `profile_image` often contains relative path.
        if(!empty($img) && file_exists("../" . $img)) {
            @unlink("../" . $img);
        }
    }
    $stmt->close();
}
$msg="";
$sql = "DELETE FROM users WHERE id = ? AND role = 'traveler'";
if($stmt = mysqli_prepare($con, $sql)) {
    mysqli_stmt_bind_param($stmt, "i", $uid);
    if(mysqli_stmt_execute($stmt))
    {
        $msg="User Deleted successfully";
        header("Location:userlist.php?msg=$msg&type=success");
    }
    else
    {
        $msg="User not Deleted";
        header("Location:userlist.php?msg=$msg&type=danger");
    }
}

mysqli_close($con);
?>
