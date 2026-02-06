<?php
include 'admin/config.php';
$res = mysqli_query($con, "SELECT id, email, role, first_name FROM users WHERE role = 'agent'");
echo "--- AGENTS FOUND ---\n";
while($row = mysqli_fetch_assoc($res)) {
    echo "ID: [{$row['id']}] | Email: {$row['email']} | Name: {$row['first_name']}\n";
}
echo "--- END ---\n";
?>
