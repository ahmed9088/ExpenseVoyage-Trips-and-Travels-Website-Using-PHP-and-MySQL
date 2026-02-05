<?php
include 'chatbot-loader.php';
session_start();
include 'admin/config.php';
include 'csrf.php';

if (!isset($_SESSION['userid'])) {
    header("Location: login/account.php");
    exit();
}

$userid = $_SESSION['userid'];

// Fetch user data early for both POST and GET logic
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
        if (move_uploaded_file($_FILES['profile_image']['tmp_name'], 'img/profiles/' . $new_filename)) {
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
                $error = "Current password incorrect.";
            }
        } else {
            $sql_update = "UPDATE users SET first_name = ?, last_name = ?, email = ?, profile_image = ? WHERE id = ?";
            $stmt = mysqli_prepare($con, $sql_update);
            mysqli_stmt_bind_param($stmt, 'ssssi', $first_name, $last_name, $email, $profile_image, $userid);
        }
        
        if (isset($stmt) && mysqli_stmt_execute($stmt)) {
            $_SESSION['name'] = $first_name . ' ' . $last_name;
            $success = "Profile updated.";
            header("Location: user-profile.php?success=1");
            exit();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>My Account | ExpenseVoyage</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;800&family=Playfair+Display:ital,wght@0,400..900;1,400..900&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <script src="https://cdn.jsdelivr.net/particles.js/2.0.0/particles.min.js"></script>
    
    <link href="css/custom.css" rel="stylesheet">
    
    <style>
        .profile-hero {
            height: 30vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(rgba(248, 250, 252, 0.8), rgba(248, 250, 252, 0.9)), 
                        url('img/profile-bg.jpg') center/cover no-repeat;
        }

        .avatar-wrap {
            position: relative;
            width: 150px;
            height: 150px;
            margin: -75px auto 20px;
        }

        .avatar-main {
            width: 150px;
            height: 150px;
            object-fit: cover;
            border-radius: 50%;
            border: 4px solid var(--primary);
            padding: 5px;
            background: #fff;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }

        .cam-btn {
            position: absolute;
            bottom: 5px;
            right: 5px;
            background: var(--primary);
            color: white;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            border: 2px solid #fff;
            transition: all 0.3s ease;
        }
        
        .cam-btn:hover {
            background: #4338ca;
            transform: scale(1.1);
        }
        
        .mt-n5 { margin-top: -3rem !important; }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg sticky-top py-3">
        <div class="container">
            <a href="index.php" class="navbar-brand">
                <span class="text-primary fw-bold">Expense</span><span class="text-dark">Voyage</span>
            </a>
            <div class="navbar-nav ms-auto">
                <a class="nav-link px-3" href="index.php">Return to Home</a>
            </div>
        </div>
    </nav>

    <header class="profile-hero"></header>

    <section class="pb-5">
        <div class="container">
            <div class="glass-panel p-5 mt-n5 position-relative bg-white shadow-lg border-0 animate__animated animate__fadeIn">
                <div class="avatar-wrap">
                    <img src="<?php echo $user['profile_image'] ? 'img/profiles/'.$user['profile_image'] : 'img/default.jpg'; ?>" class="avatar-main" alt="Avatar">
                    <label for="profile_image" class="cam-btn"><i class="fas fa-camera"></i></label>
                </div>

                <div class="text-center mb-5">
                    <h2 class="serif-font mb-0"><?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></h2>
                    <p class="text-primary small tracking-widest text-uppercase fw-bold"><?php echo $user['is_verified'] ? 'Verified Voyager' : 'New Client'; ?></p>
                </div>

                <?php if (isset($_GET['success'])): ?>
                    <div class="alert alert-success border-0 bg-success-subtle text-success text-center mb-4">
                        Profile credentials synchronized successfully.
                    </div>
                <?php endif; ?>

                <form action="user-profile.php" method="POST" enctype="multipart/form-data">
                    <?php echo csrf_input(); ?>
                    <input type="file" name="profile_image" id="profile_image" class="d-none">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <label class="small text-muted mb-2">First Name</label>
                            <input type="text" name="first_name" value="<?php echo htmlspecialchars($user['first_name']); ?>" class="form-control bg-light border-0 py-3">
                        </div>
                        <div class="col-md-6">
                            <label class="small text-muted mb-2">Last Name</label>
                            <input type="text" name="last_name" value="<?php echo htmlspecialchars($user['last_name']); ?>" class="form-control bg-light border-0 py-3">
                        </div>
                        <div class="col-12">
                            <label class="small text-muted mb-2">Email Address</label>
                            <input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" class="form-control bg-light border-0 py-3">
                        </div>
                        
                        <div class="col-12 mt-5">
                            <h5 class="serif-font mb-3">Security & Recovery</h5>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="small text-muted mb-2">New Password (leave blank to keep current)</label>
                                    <input type="password" name="password" class="form-control bg-light border-0 py-3">
                                </div>
                                <div class="col-md-6">
                                    <label class="small text-muted mb-2">Current Password (required for changes)</label>
                                    <input type="password" name="old_password" class="form-control bg-light border-0 py-3">
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-12 text-center mt-5">
                            <button type="submit" class="btn btn-primary px-5 py-3">UPDATE ACCOUNT</button>
                        </div>
                    </div>
                </form>
            <div class="glass-panel p-5 mt-5 bg-white shadow-lg border-0 animate__animated animate__fadeIn">
                <h3 class="serif-font mb-4">My Expeditions</h3>
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="bg-light">
                            <tr>
                                <th class="py-3 border-0 small text-uppercase tracking-widest text-muted">Voyage</th>
                                <th class="py-3 border-0 small text-uppercase tracking-widest text-muted">Travel Date</th>
                                <th class="py-3 border-0 small text-uppercase tracking-widest text-muted">Guests</th>
                                <th class="py-3 border-0 small text-uppercase tracking-widest text-muted">Total</th>
                                <th class="py-3 border-0 small text-uppercase tracking-widest text-muted">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $bSql = "SELECT b.*, t.trip_name, t.trip_image FROM bookings b JOIN trips t ON b.trip_id = t.trip_id WHERE b.user_id = ? ORDER BY b.booking_date DESC";
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
                                            <img src="<?php echo htmlspecialchars($booking['trip_image']); ?>" class="rounded-2 me-3" style="width: 60px; height: 40px; object-fit: cover;">
                                            <span class="fw-bold"><?php echo htmlspecialchars($booking['trip_name']); ?></span>
                                        </div>
                                    </td>
                                    <td><?php echo date('M d, Y', strtotime($booking['travel_date'])); ?></td>
                                    <td><?php echo $booking['guests']; ?></td>
                                    <td><span class="text-primary fw-bold">$<?php echo number_format($booking['total_price']); ?></span></td>
                                    <td>
                                        <span class="badge rounded-pill <?php echo $booking['status'] == 'confirmed' ? 'bg-success-subtle text-success' : 'bg-warning-subtle text-warning'; ?> px-3 py-2 text-uppercase" style="font-size: 0.7rem;">
                                            <?php echo $booking['status']; ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php 
                                endwhile;
                            else:
                            ?>
                                <tr>
                                    <td colspan="5" class="py-5 text-center text-muted">
                                        <i class="fas fa-compass fa-3x mb-3 opacity-25"></i>
                                        <p class="mb-0">No expeditions booked yet. The world is waiting for you.</p>
                                        <a href="package.php" class="btn btn-link text-primary mt-2">Explore Destinations</a>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="glass-panel p-5 mt-5 bg-white shadow-lg border-0 animate__animated animate__fadeIn">
                <h3 class="serif-font mb-4">Saved Voyages</h3>
                <div class="row g-4">
                    <?php
                    $wSql = "SELECT t.* FROM wishlist w JOIN trips t ON w.trip_id = t.trip_id WHERE w.user_id = ?";
                    $wStmt = mysqli_prepare($con, $wSql);
                    mysqli_stmt_bind_param($wStmt, "i", $userid);
                    mysqli_stmt_execute($wStmt);
                    $wRes = mysqli_stmt_get_result($wStmt);
                    
                    if (mysqli_num_rows($wRes) > 0):
                        while ($wTrip = mysqli_fetch_assoc($wRes)):
                    ?>
                        <div class="col-md-4">
                            <div class="card border-0 shadow-sm rounded-4 overflow-hidden h-100">
                                <img src="<?php echo htmlspecialchars($wTrip['trip_image']); ?>" class="card-img-top" style="height: 150px; object-fit: cover;">
                                <div class="p-3">
                                    <h6 class="serif-font mb-1"><?php echo htmlspecialchars($wTrip['trip_name']); ?></h6>
                                    <p class="text-primary small fw-bold mb-3">$<?php echo number_format($wTrip['budget']); ?></p>
                                    <a href="trip_details.php?id=<?php echo $wTrip['trip_id']; ?>" class="btn btn-outline-primary btn-sm w-100">Explore</a>
                                </div>
                            </div>
                        </div>
                    <?php 
                        endwhile;
                    else:
                    ?>
                        <div class="col-12 py-4 text-center text-muted">
                            <p class="small mb-0">Your collection is empty. Discover your next adventure on the <a href="index.php#packages">Packages</a> section.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>

    <footer class="py-5 border-top bg-white mt-5">
        <div class="container text-center">
            <h4 class="text-primary mb-3">ExpenseVoyage</h4>
            <p class="text-muted small mb-0">&copy; 2026 ExpenseVoyage. Crafted for the extraordinary.</p>
        </div>
    </footer>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/custom.js"></script>
</body>
</html>