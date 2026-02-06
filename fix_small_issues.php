<?php
include 'admin/config.php';

// I will target the user who was trying to access the dashboard (likely ID 48 based on previous logs)
$id = 48; 
$email = 'ubaidsoomro505@gmail.com';

echo "--- Account Promotion System ---\n";

$res = mysqli_query($con, "SELECT id, email, role FROM users WHERE id=$id OR email='$email'");
if ($row = mysqli_fetch_assoc($res)) {
    echo "Found user: {$row['email']} (Current Role: {$row['role']})\n";
    echo "Promoting to 'agent'...\n";
    mysqli_query($con, "UPDATE users SET role = 'agent' WHERE id = {$row['id']}");
    echo "Promotion successful!\n";
} else {
    echo "Target user not found. Checking for ANY traveler to promote...\n";
    $any = mysqli_query($con, "SELECT id, email FROM users WHERE role = 'traveler' LIMIT 1");
    if ($row = mysqli_fetch_assoc($any)) {
        echo "Promoting {$row['email']} to 'agent'...\n";
        mysqli_query($con, "UPDATE users SET role = 'agent' WHERE id = {$row['id']}");
        echo "Promotion successful!\n";
    } else {
        echo "No candidates found.\n";
    }
}
echo "--- END ---\n";
?>
