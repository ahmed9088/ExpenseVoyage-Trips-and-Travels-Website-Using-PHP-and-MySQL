<?php
// Include database connection
include 'admin/config.php'; // Your database connection file
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Fetch form data
    $first_name = mysqli_real_escape_string($con, $_POST['first_name']);
    $email = mysqli_real_escape_string($con, $_POST['email']);
    $user_id = $_SESSION['userid'];  
    $password = isset($_POST['password']) ? $_POST['password'] : '';  // New password (optional)

    // Prepare the base SQL update query
    $sql = "UPDATE user SET name='$first_name', email='$email'";

    // Check if the user provided a new password
    if (!empty($password)) {
        // Hash the new password before saving to the database
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $sql .= ", password='$hashed_password'";
    }

    // Check if an image is uploaded
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $image_name = $_FILES['image']['name'];
        $image_tmp = $_FILES['image']['tmp_name'];
        $upload_dir = 'img/userimages/';  // Define the directory to store uploaded images

        // Move the uploaded file to the directory
        if (move_uploaded_file($image_tmp, $upload_dir . $image_name)) {
            // Append the image update to the query
            $sql .= ", profile_image='$image_name'";
        } else {
            echo "Error uploading image.";
            exit;
        }
    }

    // Complete the query by specifying the user ID
    $sql .= " WHERE id='$user_id'";

    // Execute the query
    if (mysqli_query($con, $sql)) {
        echo "<script>
            alert('Profile updated successfully.');
            window.location.href='user-profile.php';
          </script>";
        exit;
    } else {
        echo "Error: " . mysqli_error($con);
    }
}
?>
<style>
    
    body {
      --sb-track-color: #232E33;
      --sb-thumb-color: #7AB730;
      --sb-size: 14px;
    }
    
    body::-webkit-scrollbar {
      width: 12px;
    }
    
    body::-webkit-scrollbar-track {
      background: var(--sb-track-color);
      border-radius: 1px;
    }
    
    body::-webkit-scrollbar-thumb {
      background: var(--sb-thumb-color);
      border-radius: 3px;
      
    }
    
    @supports not selector(::-webkit-scrollbar) {
      body {
        scrollbar-color: var(--sb-thumb-color)
                         var(--sb-track-color);
      }
    }
    </style>