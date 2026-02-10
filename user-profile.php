<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include 'admin/config.php';
include 'csrf.php';

if (!isset($_SESSION['userid'])) {
    header("Location: login/account.php");
    exit();
}

$userid = $_SESSION['userid'];

// Fetch user data
$sql_fetch = "SELECT * FROM users WHERE id = ?";
$stmt_fetch = mysqli_prepare($con, $sql_fetch);
mysqli_stmt_bind_param($stmt_fetch, "i", $userid);
mysqli_stmt_execute($stmt_fetch);
$res_fetch = mysqli_stmt_get_result($stmt_fetch);
$user = mysqli_fetch_assoc($res_fetch);

if (!$user) {
    session_destroy();
    header("Location: login/account.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Verify CSRF
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        die("Security validation failed.");
    }
    
    $first_name = $_POST['first_name'] ?? '';
    $last_name = $_POST['last_name'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $old_password = $_POST['old_password'] ?? '';
    
    $profile_image = $user['profile_image'];
    if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] == 0) {
        $filetype = pathinfo($_FILES['profile_image']['name'], PATHINFO_EXTENSION);
        $new_filename = uniqid() . '.' . $filetype;
        $targetDir = 'img/profiles/';
        if (!file_exists($targetDir)) mkdir($targetDir, 0755, true);
        if (move_uploaded_file($_FILES['profile_image']['tmp_name'], $targetDir . $new_filename)) {
            $profile_image = $new_filename;
        }
    }
    
    if (!isset($error)) {
        if (!empty($password)) {
            if (password_verify($old_password, $user['password_hash'])) {
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $sql_update = "UPDATE users SET first_name = ?, last_name = ?, email = ?, password_hash = ?, profile_image = ? WHERE id = ?";
                $stmt = mysqli_prepare($con, $sql_update);
                mysqli_stmt_bind_param($stmt, 'sssssi', $first_name, $last_name, $email, $hashed_password, $profile_image, $userid);
            } else {
                $error = "Incorrect current password.";
            }
        } else {
            $sql_update = "UPDATE users SET first_name = ?, last_name = ?, email = ?, profile_image = ? WHERE id = ?";
            $stmt = mysqli_prepare($con, $sql_update);
            mysqli_stmt_bind_param($stmt, 'ssssi', $first_name, $last_name, $email, $profile_image, $userid);
        }
        
        if (isset($stmt) && mysqli_stmt_execute($stmt)) {
            $_SESSION['name'] = $first_name . ' ' . $last_name;
            header("Location: user-profile.php?success=1");
            exit();
        }
    }
}

$pageTitle = "My Profile | ExpenseVoyage";
$currentPage = "profile";
include 'header.php';
?>

    <!-- Simple Hero -->
    <header class="hero-editorial" style="height: 40vh;">
        <div class="hero-editorial-bg ken-burns" style="background-image: url('img/mountain.jpg');"></div>
        <div class="container hero-editorial-content reveal-up">
            <span class="text-gold text-uppercase tracking-widest fw-bold mb-4 d-block">User Dashboard</span>
            <h1 class="display-1 serif-font text-white mb-0">My <span class="text-gold">Profile</span></h1>
        </div>
    </header>

    <!-- Profile Content -->
    <section class="section-padding bg-deep glow-aura">
        <div class="container mt-n10 position-relative z-3">
            <div class="row g-5">
                <!-- Profile Settings -->
                <div class="col-lg-4">
                    <div class="glass-card p-5 border-0 shadow-extreme reveal-up">
                        <form action="user-profile.php" method="POST" enctype="multipart/form-data">
                            <?php echo csrf_input(); ?>
                            <div class="text-center mb-5">
                                <div class="position-relative d-inline-block">
                                    <img src="<?php echo $user['profile_image'] ? 'img/profiles/'.$user['profile_image'] : 'img/default.jpg'; ?>" 
                                         class="rounded-pill border border-gold p-2 shadow-gold" 
                                         style="width: 180px; height: 180px; object-fit: cover;" alt="Avatar">
                                    <label for="profile_image" class="position-absolute bottom-0 end-0 bg-gold text-secondary rounded-pill p-2 cursor-pointer shadow-lg" style="width: 45px; height: 45px;">
                                        <i class="fas fa-camera"></i>
                                    </label>
                                    <input type="file" name="profile_image" id="profile_image" class="d-none">
                                </div>
                                <h3 class="serif-font text-white mt-4 mb-1"><?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></h3>
                                <p class="text-gold text-uppercase tracking-widest small fw-bold">Member Account</p>
                            </div>

                            <?php if (isset($_GET['success'])): ?>
                                <div class="bg-success text-white p-3 text-center small mb-4">Profile updated successfully!</div>
                            <?php endif; ?>
                            <?php if (isset($error)): ?>
                                <div class="bg-danger text-white p-3 text-center small mb-4"><?php echo $error; ?></div>
                            <?php endif; ?>

                            <div class="mb-4">
                                <label class="small text-gold text-uppercase tracking-widest fw-bold mb-2 d-block">First Name</label>
                                <input type="text" name="first_name" value="<?php echo htmlspecialchars($user['first_name']); ?>" class="form-control bg-transparent border-0 border-bottom border-ghost py-2 text-white shadow-none" placeholder="First Name">
                            </div>
                            <div class="mb-4">
                                <label class="small text-gold text-uppercase tracking-widest fw-bold mb-2 d-block">Last Name</label>
                                <input type="text" name="last_name" value="<?php echo htmlspecialchars($user['last_name']); ?>" class="form-control bg-transparent border-0 border-bottom border-ghost py-2 text-white shadow-none" placeholder="Last Name">
                            </div>
                            <div class="mb-4">
                                <label class="small text-gold text-uppercase tracking-widest fw-bold mb-2 d-block">Email Address</label>
                                <input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" class="form-control bg-transparent border-0 border-bottom border-ghost py-2 text-white shadow-none" placeholder="Email Address">
                            </div>
                            <div class="mb-5">
                                <label class="small text-gold text-uppercase tracking-widest fw-bold mb-2 d-block">New Password</label>
                                <input type="password" name="password" placeholder="Leave blank to keep current" class="form-control bg-transparent border-0 border-bottom border-ghost py-2 text-white shadow-none">
                            </div>
                            <div class="mb-5">
                                <label class="small text-gold text-uppercase tracking-widest fw-bold mb-2 d-block">Current Password</label>
                                <input type="password" name="old_password" class="form-control bg-transparent border-0 border-bottom border-ghost py-2 text-white shadow-none" placeholder="Type current password to save">
                            </div>

                            <button type="submit" class="btn-luxe btn-luxe-gold w-100 py-3">Save Changes</button>
                        </form>
                    </div>
                </div>

                <!-- Dashboard Area -->
                <div class="col-lg-8">
                    <!-- Notifications -->
                    <div class="glass-card p-5 mb-5 border-0 shadow-extreme reveal-up" style="background: rgba(10, 10, 11, 0.95);">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h4 class="serif-font text-white mb-0">Latest <span class="text-gold">Alerts</span></h4>
                        </div>
                        <div class="alert-list">
                            <?php
                            $nSql = "SELECT * FROM notifications WHERE user_id = ? ORDER BY created_at DESC LIMIT 3";
                            $nStmt = mysqli_prepare($con, $nSql);
                            mysqli_stmt_bind_param($nStmt, "i", $userid);
                            mysqli_stmt_execute($nStmt);
                            $nRes = mysqli_stmt_get_result($nStmt);
                            if (mysqli_num_rows($nRes) > 0):
                                while ($note = mysqli_fetch_assoc($nRes)):
                            ?>
                                <div class="p-4 mb-3 <?php echo $note['is_read'] ? 'border-ghost opacity-50' : 'border-gold bg-glass-light'; ?> border-start border-4">
                                    <div class="d-flex justify-content-between mb-2">
                                        <h6 class="text-white fw-bold mb-0"><?php echo htmlspecialchars($note['title']); ?></h6>
                                        <small class="text-gold small"><?php echo date('M d, H:i', strtotime($note['created_at'])); ?></small>
                                    </div>
                                    <p class="text-muted small mb-0"><?php echo htmlspecialchars($note['message']); ?></p>
                                </div>
                            <?php endwhile; else: ?>
                                <div class="text-center py-5 opacity-20"><i class="fas fa-bell-slash fa-3x mb-3"></i><p class="small">No notifications found.</p></div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- My Bookings -->
                    <div class="glass-card p-5 mb-5 border-0 shadow-extreme reveal-up" style="background: rgba(10, 10, 11, 0.95);">
                        <h4 class="serif-font text-white mb-5">My <span class="text-gold">Bookings</span></h4>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle custom-table-luxe">
                                <thead>
                                    <tr>
                                        <th class="border-0 text-gold small text-uppercase tracking-widest">Trip</th>
                                        <th class="border-0 text-gold small text-uppercase tracking-widest">Status</th>
                                        <th class="border-0 text-gold small text-uppercase tracking-widest">Total</th>
                                        <th class="border-0 text-gold small text-uppercase tracking-widest text-end">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $bSql = "SELECT b.*, t.trip_name, t.trip_image FROM bookings b JOIN trips t ON b.trip_id = t.trip_id WHERE b.user_id = ? ORDER BY b.booking_date DESC LIMIT 5";
                                    $bStmt = mysqli_prepare($con, $bSql);
                                    mysqli_stmt_bind_param($bStmt, "i", $userid);
                                    mysqli_stmt_execute($bStmt);
                                    $bRes = mysqli_stmt_get_result($bStmt);
                                    if (mysqli_num_rows($bRes) > 0):
                                        while ($booking = mysqli_fetch_assoc($bRes)):
                                    ?>
                                        <tr>
                                            <td class="py-4">
                                                <div class="d-flex align-items-center">
                                                    <img src="<?php echo htmlspecialchars($booking['trip_image']); ?>" class="rounded-0 me-3 border border-ghost" style="width: 50px; height: 35px; object-fit: cover;">
                                                    <span class="text-white fw-bold"><?php echo htmlspecialchars($booking['trip_name']); ?></span>
                                                </div>
                                            </td>
                                            <td class="text-ghost small"><?php echo htmlspecialchars($booking['expedition_status'] ?? 'Upcoming'); ?></td>
                                            <td class="text-gold fw-bold">$<?php echo number_format($booking['total_price']); ?></td>
                                            <td class="text-end">
                                                <button class="btn btn-outline-gold btn-sm py-1 px-3 x-small" onclick="alert('Ticket ID: <?php echo $booking['ticket_hash']; ?>')">VIEW</button>
                                            </td>
                                        </tr>
                                    <?php endwhile; else: ?>
                                        <tr><td colspan="4" class="text-center py-5 opacity-20">No bookings found.</td></tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Wishlist -->
                    <div class="glass-card p-5 border-0 shadow-extreme reveal-up" style="background: rgba(10, 10, 11, 0.95);">
                        <h4 class="serif-font text-white mb-5">Saved <span class="text-gold">Trips</span></h4>
                        <div class="row g-4">
                            <?php
                            $wSql = "SELECT t.* FROM wishlist w JOIN trips t ON w.trip_id = t.trip_id WHERE w.user_id = ? LIMIT 3";
                            $wStmt = mysqli_prepare($con, $wSql);
                            mysqli_stmt_bind_param($wStmt, "i", $userid);
                            mysqli_stmt_execute($wStmt);
                            $wRes = mysqli_stmt_get_result($wStmt);
                            if (mysqli_num_rows($wRes) > 0):
                                while ($wTrip = mysqli_fetch_assoc($wRes)):
                            ?>
                                <div class="col-md-4">
                                    <div class="bg-surface border border-ghost p-3 h-100 transition-all hover-border-gold">
                                        <img src="<?php echo htmlspecialchars($wTrip['trip_image']); ?>" class="w-100 mb-3" style="height: 100px; object-fit: cover; filter: brightness(0.8);">
                                        <h6 class="serif-font text-white mb-1 small"><?php echo htmlspecialchars($wTrip['trip_name']); ?></h6>
                                        <p class="text-gold x-small fw-bold mb-3">$<?php echo number_format($wTrip['budget']); ?></p>
                                        <a href="trip_details.php?id=<?php echo $wTrip['trip_id']; ?>" class="btn-luxe btn-luxe-outline py-1 w-100 x-small">Details</a>
                                    </div>
                                </div>
                            <?php endwhile; else: ?>
                                <div class="col-12 py-4 text-center opacity-20">No saved trips yet.</div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

<?php include 'footer.php'; ?>

<style>
    .custom-table-luxe th, .custom-table-luxe td { background: transparent !important; color: #94a3b8 !important; border-bottom: 1px solid rgba(255,255,255,0.05) !important; vertical-align: middle; }
    .custom-table-luxe tbody tr:hover td { background: rgba(255,255,255,0.02) !important; color: #fff !important; }
    .btn-outline-gold { border: 1px solid var(--accent); color: var(--accent); background: transparent; transition: all 0.3s ease; }
    .btn-outline-gold:hover { background: var(--accent); color: #000; }
    .bg-glass-light { background: rgba(212, 175, 55, 0.05) !important; }
</style>