<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Intelligent hand-off
if (isset($_SESSION['role']) && in_array($_SESSION['role'], ['agent', 'admin'])) {
    header("Location: dashboard.php");
} else {
    header("Location: login.php");
}
exit();
?>
