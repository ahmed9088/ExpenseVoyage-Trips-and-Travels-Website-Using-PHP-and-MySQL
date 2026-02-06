<?php
include 'admin/config.php';

echo "--- Account Generation System ---\n";

function createAccount($con, $fn, $ln, $email, $pass, $role) {
    $hash = password_hash($pass, PASSWORD_DEFAULT);
    $sql = "INSERT INTO users (first_name, last_name, email, password_hash, role, is_verified) VALUES (?, ?, ?, ?, ?, 1)";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, 'sssss', $fn, $ln, $email, $hash, $role);
    if (mysqli_stmt_execute($stmt)) {
        echo "Successfully created $role account: $email\n";
    } else {
        echo "Error creating $role account: " . mysqli_error($con) . "\n";
    }
}

// Admin Account
createAccount($con, "Ahmed", "Admin", "ahmed_admin@expensevoyage.com", "Ahmed@Admin2026", "admin");

// Agent Account
createAccount($con, "Ahmed", "Agent", "ahmed_agent@expensevoyage.com", "Ahmed@Agent2026", "agent");

echo "--- Generation Complete ---\n";
?>
