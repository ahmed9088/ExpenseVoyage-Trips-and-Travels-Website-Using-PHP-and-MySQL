<?php
session_start();
include("config.php");
require_once("../csrf.php");

if (!isset($_SESSION['aid'])) {
    header("Location: index.php");
    exit();
}

$msg = "";
$type = "danger";

if (isset($_GET['id'])) {
    if (!verify_csrf_token($_GET['csrf_token'] ?? '')) {
         die("Security Violation");
    }

    $aid = intval($_GET['id']);
    
    // Prevent self-deletion if needed, or just ensure it's an admin
    $check = $con->query("SELECT role FROM users WHERE id = $aid");
    if ($row = $check->fetch_assoc()) {
        if ($row['role'] === 'admin') {
            // Check if this is the last admin
            $count_res = $con->query("SELECT COUNT(*) as total FROM users WHERE role = 'admin'");
            $count_row = $count_res->fetch_assoc();
            
            if ($count_row['total'] > 1) {
                $sql = "DELETE FROM users WHERE id = ? AND role = 'admin'";
                $stmt = mysqli_prepare($con, $sql);
                mysqli_stmt_bind_param($stmt, "i", $aid);
                
                if (mysqli_stmt_execute($stmt)) {
                    $msg = "Administrative clearance revoked successfully.";
                    $type = "success";
                } else {
                    error_log("Admin Deletion Error: " . mysqli_error($con));
                    $msg = "Critical error during decommissioning.";
                }
            } else {
                $msg = "Operation aborted: Global Enterprise elevation requires at least one active controller.";
            }
        } else {
            $msg = "Invalid target: Entity is not an administrative controller.";
        }
    } else {
        $msg = "Target not found in enterprise registry.";
    }
} else {
    $msg = "No target specified for decommissioning.";
}

header("Location: adminlist.php?msg=" . urlencode($msg) . "&type=" . $type);
exit();
?>
