<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Infrastructure: Load core dependencies
require_once("../admin/config.php");
require_once("../csrf.php");

/**
 * Intelligent Session Synchronization & Role Bridge
 */
$raw_id = $_SESSION['userid'] ?? $_SESSION['aid'] ?? null;

if ($raw_id === null) {
    header("Location: login.php");
    exit();
}

// Perform Real-Time Database Validation
// This fixes "stale session" issues where roles were updated but the session didn't reflect it.
$user_lookup = mysqli_query($con, "SELECT id, first_name, last_name, email, role, profile_image FROM users WHERE id = '$raw_id' LIMIT 1");
$user_data = mysqli_fetch_assoc($user_lookup);

if (!$user_data) {
    // Orphaned session: User exists in cookie but not in current DB
    session_destroy();
    header("Location: login.php?error=session_invalid");
    exit();
}

// Normalize Session Data
$_SESSION['userid'] = $user_data['id'];
$_SESSION['role'] = $user_data['role'];
$_SESSION['name'] = $user_data['first_name'] . ' ' . $user_data['last_name'];
$_SESSION['email'] = $user_data['email'];

$agent_id = $user_data['id'];
$agent_role = $user_data['role'];
$agent_name = $_SESSION['name'];

// Authorization Step
if (!in_array($agent_role, ['agent', 'admin'])) {
    // Access Denied: We log this for diagnostics then redirect
    $log_data = date('Y-m-d H:i:s') . " - Unauthorized Dashboard Access: email={$user_data['email']}, role={$agent_role}\n";
    file_put_contents('../auth_debug.log', $log_data, FILE_APPEND);
    
    header("Location: login.php?error=unauthorized_access");
    exit();
}

// Asset Resolution
$agent_img = !empty($user_data['profile_image'] ?? '') ? '../upload/agents/'.$user_data['profile_image'] : '../img/default-avatar.png';
?>
