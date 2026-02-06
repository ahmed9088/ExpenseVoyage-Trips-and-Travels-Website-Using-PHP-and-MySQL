<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once("config.php");
if (!isset($_SESSION['aid'])) {
    header("Location: index.php");
    exit();
}

// Handle User Deletion
if (isset($_GET['delete_id'])) {
    $delete_id = intval($_GET['delete_id']);
    // Verify it's a traveler being deleted
    $res = $con->query("SELECT role FROM users WHERE id = $delete_id");
    if($row = $res->fetch_assoc()) {
        if($row['role'] === 'traveler') {
            $con->query("DELETE FROM users WHERE id = $delete_id");
            header("Location: userlist.php?msg=Traveler account deleted&type=success");
            exit();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User List - ExpenseVoyage Dashboard</title>
    
    <link rel="shortcut icon" type="image/x-icon" href="../img/favicon-32x32.png">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="assets/css/admin_modern.css">
</head>
<body>

<div class="admin-wrapper">
    <?php include("header.php"); ?>

    <main class="modern-main">
        <div class="page-header d-flex justify-content-between align-items-center mb-5">
            <div class="animate__animated animate__fadeIn">
                <h1 class="mb-1">User <span class="text-indigo">List</span></h1>
                <p class="text-muted mb-0">View and manage all registered customers.</p>
            </div>
            <div class="date-node text-end">
                <span class="badge bg-indigo-light text-indigo">User Management</span>
            </div>
        </div>

        <?php if(isset($_GET['msg'])): ?>
        <div class="alert alert-<?php echo $_GET['type'] ?? 'info'; ?> animate__animated animate__headShake">
            <i class="fa-solid fa-circle-check me-2"></i> <?php echo htmlspecialchars($_GET['msg']); ?>
        </div>
        <?php endif; ?>

        <div class="intelligence-card animate__animated animate__fadeInUp">
            <div class="table-responsive">
                <table id="user-table" class="table modern-table align-middle">
                    <thead class="bg-indigo-light">
                        <tr>
                            <th>#</th>
                            <th>User ID</th>
                            <th>Name</th>
                            <th>Email Address</th>
                            <th>Account Type</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $query = mysqli_query($con, "SELECT id, first_name, last_name, email FROM users WHERE role = 'traveler' ORDER BY id DESC");
                        $cnt = 1;
                        while ($row = mysqli_fetch_assoc($query)) {
                        ?>
                        <tr>
                            <td><span class="fw-bold text-muted"><?php echo str_pad($cnt, 2, '0', STR_PAD_LEFT); ?></span></td>
                            <td><span class="badge bg-slate-100 text-slate-800">#VY-<?php echo $row['id']; ?></span></td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar-node avatar-xs me-2">
                                        <div class="bg-slate-200 text-slate-600 rounded-circle d-flex align-items-center justify-content-center fw-bold" style="width: 32px; height: 32px; font-size: 0.8rem;">
                                            <?php echo strtoupper(substr($row['first_name'], 0, 1)); ?>
                                        </div>
                                    </div>
                                    <div class="fw-bold text-slate-800"><?php echo htmlspecialchars($row['first_name'] . ' ' . $row['last_name']); ?></div>
                                </div>
                            </td>
                            <td>
                                <span class="fw-medium text-slate-600"><?php echo htmlspecialchars($row['email']); ?></span>
                            </td>
                            <td>
                                <span class="badge bg-success-light text-success rounded-pill px-3">Standard User</span>
                            </td>
                            <td class="text-end">
                                <div class="d-flex justify-content-end gap-1">
                                    <a href="useredit.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-outline-indigo border-0">
                                        <i class="fa-solid fa-user-pen"></i>
                                    </a>
                                    <a href="?delete_id=<?php echo $row['id']; ?>" class="btn btn-sm btn-outline-danger border-0" onclick="return confirm('Delete this user account #<?php echo $row['id']; ?>?')">
                                        <i class="fa-solid fa-trash-can"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php $cnt++; } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

<script>
    $(document).ready(function() {
        $('#user-table').DataTable({
            responsive: true,
            pageLength: 10,
            language: {
                search: "_INPUT_",
                searchPlaceholder: "Search users..."
            }
        });
    });
</script>
</body>
</html>