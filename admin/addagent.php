<?php
// Include database connection
include 'config.php';
require_once("../csrf.php");

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if admin is logged in
if(!isset($_SESSION['aid'])) {
    header("location:index.php");
    exit();
}

if (isset($_POST['submit'])) {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
         die("Security Violation");
    }

    $first_name = trim($_POST['a_name']);
    $email = trim($_POST['a_email']);
    $password = $_POST['a_pass'];
    $phone = trim($_POST['a_phone']);
    
    // Hash password for security
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    
    // Handle file upload
    $imageName = "";
    if (isset($_FILES['a_image']) && $_FILES['a_image']['error'] === 0) {
        $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
        $fileType = $_FILES['a_image']['type'];
        $fileExt = strtolower(pathinfo($_FILES['a_image']['name'], PATHINFO_EXTENSION));
        
        if (in_array($fileType, $allowedTypes) && $_FILES['a_image']['size'] < 5000000) {
            $imageName = uniqid() . '.' . $fileExt;
            $imagePath2 = '../upload/agents/' . $imageName;
            move_uploaded_file($_FILES['a_image']['tmp_name'], $imagePath2);
        }
    }

    // Insert into the unified users table as 'agent'
    $sql = "INSERT INTO users (first_name, email, password_hash, phone, role, profile_image, created_at) VALUES (?, ?, ?, ?, 'agent', ?, NOW())";
    $stmt = $con->prepare($sql);
    $stmt->bind_param('sssss', $first_name, $email, $hashed_password, $phone, $imageName);
    
    if ($stmt->execute()) {
        echo "<script>alert('Agent Added Successfully!'); window.location.href='addagent.php';</script>";
    } else {
        error_log("Agent Add Error: " . $stmt->error);
        echo "<script>alert('Error adding agent.');</script>";
    }
    $stmt->close();
}

// Handle Agent Deletion
if (isset($_GET['delete_id'])) {
    if (!verify_csrf_token($_GET['csrf_token'] ?? '')) {
         die("Security Violation");
    }

    $delete_id = intval($_GET['delete_id']);
    
    // Get image to clean up
    $res = $con->query("SELECT profile_image FROM users WHERE id = $delete_id AND role = 'agent'");
    if($row = $res->fetch_assoc()) {
        if(!empty($row['profile_image']) && file_exists('../upload/agents/' . $row['profile_image'])) {
            @unlink('../upload/agents/' . $row['profile_image']);
        }
    }
    
    $delStmt = $con->prepare("DELETE FROM users WHERE id = ? AND role = 'agent'");
    $delStmt->bind_param("i", $delete_id);
    $delStmt->execute();
    
    echo "<script>alert('Agent Removed.'); window.location.href='addagent.php';</script>";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agent Management - ExpenseVoyage Dashboard</title>
    <link rel="shortcut icon" type="image/x-icon" href="../img/favicon-32x32.png">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/admin_modern.css">
</head>
<body>

<div class="admin-wrapper">
    <?php include("header.php"); ?>

    <main class="modern-main">
        <div class="page-header d-flex justify-content-between align-items-center mb-5">
            <div class="animate__animated animate__fadeIn">
                <h1 class="mb-1">Agent <span class="text-indigo">Management</span></h1>
                <p class="text-muted mb-0">Add and manage travel agents for the website.</p>
            </div>
            <div class="date-node text-end">
                <span class="badge bg-indigo-light text-indigo">Agent List</span>
            </div>
        </div>

        <div class="row g-4">
            <!-- Provisioning Form -->
            <div class="col-xl-4">
                <div class="intelligence-card animate__animated animate__fadeInLeft">
                    <h5 class="section-title mb-4">Add New Agent</h5>
                    <form method="post" enctype="multipart/form-data">
                        <?php echo csrf_input(); ?>
                        <div class="mb-3">
                            <label class="form-label">Full Name</label>
                            <input type="text" name="a_name" class="form-control" required placeholder="Agent full name">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Email Address</label>
                            <input type="email" name="a_email" class="form-control" required placeholder="agent@expensevoyage.com">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Password</label>
                            <input type="password" name="a_pass" class="form-control" required placeholder="Generate secure key">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Phone Number</label>
                            <input type="text" name="a_phone" class="form-control" placeholder="+X XXX XXX XXXX">
                        </div>
                        <div class="mb-4">
                            <label class="form-label">Agent Photo</label>
                            <input type="file" name="a_image" class="form-control" accept="image/*">
                        </div>
                        <div class="d-grid">
                            <button type="submit" name="submit" class="btn btn-primary rounded-pill py-2 shadow-sm">
                                <i class="fa-solid fa-user-plus me-2"></i>Add Agent
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Agent Fleet List -->
            <div class="col-xl-8">
                <div class="intelligence-card animate__animated animate__fadeInRight">
                    <h5 class="section-title mb-4">Agent List</h5>
                    <div class="table-responsive">
                        <table class="table modern-table align-middle">
                            <thead class="bg-indigo-light">
                                <tr>
                                    <th>Agent Name</th>
                                    <th>Contact Information</th>
                                    <th>Status</th>
                                    <th class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $agents = $con->query("SELECT * FROM users WHERE role = 'agent' ORDER BY id DESC");
                                while($agent = $agents->fetch_assoc()):
                                    $img = $agent['profile_image'] ?? $agent['image'] ?? ''; 
                                ?>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-node me-3">
                                                <img src="<?php echo !empty($img) ? '../upload/agents/'.htmlspecialchars($img) : 'assets/img/profiles/avatar-01.png'; ?>" 
                                                     class="rounded-circle shadow-sm" width="45" height="45" alt="Agent">
                                            </div>
                                            <div>
                                                <div class="fw-bold text-slate-800"><?php echo htmlspecialchars($agent['first_name']); ?></div>
                                                <div class="fs-xs text-muted">ID: #AV-<?php echo $agent['id']; ?></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="fs-sm fw-medium text-indigo"><?php echo htmlspecialchars($agent['email']); ?></div>
                                        <div class="fs-xs text-muted"><?php echo htmlspecialchars($agent['phone']); ?></div>
                                    </td>
                                    <td>
                                        <span class="badge bg-success-light text-success rounded-pill">Active</span>
                                    </td>
                                    <td class="text-end">
                                        <a href="?delete_id=<?php echo $agent['id']; ?>&csrf_token=<?php echo generate_csrf_token(); ?>" class="btn btn-sm btn-outline-danger border-0" 
                                           onclick="return confirm('Remove this agent?')">
                                            <i class="fa-solid fa-trash-can"></i>
                                        </a>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </main>
    <?php include("footer.php"); ?>