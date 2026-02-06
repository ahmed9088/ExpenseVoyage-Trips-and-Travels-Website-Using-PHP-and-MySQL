<?php
include 'admin/config.php';
$id = 48;
$res = mysqli_query($con, "SELECT id, email, role FROM users WHERE id=$id");
$row = mysqli_fetch_assoc($res);
echo "Final Check - ID: {$row['id']} | Email: {$row['email']} | Role: {$row['role']}\n";
?>
