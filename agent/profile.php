<?php
require_once("auth.php");

// Using $agent_id from header.php
$success = "";
$error = "";

// Fetch current user details (supports both agent and admin)
$sql = "SELECT id, first_name, email, phone, profile_image FROM users WHERE id = '$agent_id'";
$result = mysqli_query($con, $sql);
$agent = mysqli_fetch_assoc($result);

if (isset($_POST['update_profile'])) {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
         die("Security Violation");
    }

    $name = mysqli_real_escape_string($con, $_POST['name']);
    $email = mysqli_real_escape_string($con, $_POST['email']);
    $phone = mysqli_real_escape_string($con, $_POST['phone']);
    $password = $_POST['new_password'];

    // Handle Image Upload
    $imageName = $agent['profile_image'];
    if (isset($_FILES['profile_img']) && $_FILES['profile_img']['error'] === 0) {
        $fileExt = strtolower(pathinfo($_FILES['profile_img']['name'], PATHINFO_EXTENSION));
        $newName = uniqid() . '.' . $fileExt;
        $target = '../upload/agents/' . $newName;
        
        if (move_uploaded_file($_FILES['profile_img']['tmp_name'], $target)) {
            // Clean up old image
            if (!empty($agent['profile_image']) && file_exists('../upload/agents/' . $agent['profile_image'])) {
                @unlink('../upload/agents/' . $agent['profile_image']);
            }
            $imageName = $newName;
        }
    }

    if (!empty($password)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $update_sql = "UPDATE users SET first_name=?, email=?, phone=?, password_hash=?, profile_image=? WHERE id=?";
        $stmt = $con->prepare($update_sql);
        $stmt->bind_param('sssssi', $name, $email, $phone, $hashed_password, $imageName, $agent_id);
    } else {
        $update_sql = "UPDATE users SET first_name=?, email=?, phone=?, profile_image=? WHERE id=?";
        $stmt = $con->prepare($update_sql);
        $stmt->bind_param('ssssi', $name, $email, $phone, $imageName, $agent_id);
    }

    if ($stmt->execute()) {
        $success = "Profile updated successfully.";
        // Refresh local data
        $_SESSION['name'] = $name;
        header("Refresh: 2; url=profile.php");
    } else {
        $error = "Failed to update profile: " . $stmt->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile - Agent Dashboard</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../admin/assets/css/admin_modern.css">
</head>
<body>

<div class="admin-wrapper">
    <?php include("sidebar.php"); ?>

    <div class="modern-content-wrapper">
        <?php include("header.php"); ?>

        <main class="modern-main">
            <div class="page-header d-flex justify-content-between align-items-center mb-5">
                <div class="animate__animated animate__fadeIn">
                    <h1 class="mb-1">Agent <span class="text-indigo">Identity</span></h1>
                    <p class="text-muted mb-0">Manage your personal credentials and operational profile.</p>
                </div>
            </div>

            <div class="row justify-content-center">
                <div class="col-xl-8">
                    <div class="intelligence-card animate__animated animate__fadeInUp">
                        <h5 class="section-title mb-4">Edit Profile</h5>

                        <?php if($success): ?>
                            <div class="alert alert-success fs-sm mb-4">
                                <i class="fa-solid fa-circle-check me-2"></i> <?php echo $success; ?>
                            </div>
                        <?php endif; ?>

                        <?php if($error): ?>
                            <div class="alert alert-danger fs-sm mb-4">
                                <i class="fa-solid fa-circle-exclamation me-2"></i> <?php echo $error; ?>
                            </div>
                        <?php endif; ?>

                        <form method="post" enctype="multipart/form-data">
                            <?php echo csrf_input(); ?>
                            <div class="row g-4">
                                <div class="col-md-4 text-center">
                                    <div class="profile-avatar-update position-relative d-inline-block">
                                        <img src="<?php echo !empty($agent['profile_image']) ? '../upload/agents/'.$agent['profile_image'] : '../img/default-avatar.png'; ?>" 
                                             class="rounded-circle shadow border border-4 border-indigo-light mb-3" 
                                             width="150" height="150" style="object-fit: cover;" id="avatarPreview">
                                        <label for="profile_img" class="btn btn-primary btn-sm rounded-circle position-absolute bottom-0 end-0 mb-3 me-2 shadow">
                                            <i class="fa-solid fa-camera"></i>
                                        </label>
                                        <input type="file" name="profile_img" id="profile_img" class="d-none" accept="image/*" onchange="previewImage(this)">
                                    </div>
                                    <p class="text-muted small mt-2">Operational Avatar</p>
                                </div>

                                <div class="col-md-8">
                                    <div class="mb-3">
                                        <label class="form-label fs-xs text-uppercase fw-bold">Full Name</label>
                                        <input type="text" name="name" class="form-control" value="<?php echo htmlspecialchars($agent['first_name']); ?>" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label fs-xs text-uppercase fw-bold">Email Address</label>
                                        <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($agent['email']); ?>" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label fs-xs text-uppercase fw-bold">Phone Number</label>
                                        <input type="text" name="phone" class="form-control" value="<?php echo htmlspecialchars($agent['phone']); ?>" placeholder="+X XXX XXX XXXX">
                                    </div>
                                    <div class="mb-4">
                                        <label class="form-label fs-xs text-uppercase fw-bold">New Password</label>
                                        <input type="password" name="new_password" class="form-control" placeholder="••••••••">
                                        <div class="form-text text-muted fs-xxs">Leave blank to keep your current access key.</div>
                                    </div>
                                    
                                    <hr class="my-4 opacity-10">
                                    
                                    <div class="d-grid">
                                        <button type="submit" name="update_profile" class="btn btn-primary rounded-pill py-3">
                                            <i class="fa-solid fa-id-card-clip me-2"></i> Save Profile Changes
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </main>

        <?php include("footer.php"); ?>
    </div>
</div>

<script>
function previewImage(input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('avatarPreview').src = e.target.result;
        }
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
</body>
</html>
