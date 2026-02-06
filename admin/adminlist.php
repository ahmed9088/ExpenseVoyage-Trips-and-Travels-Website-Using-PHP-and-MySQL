<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once("config.php");
require_once("../csrf.php");

if (!isset($_SESSION['aid'])) {
    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin List - ExpenseVoyage Dashboard</title>
    
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
                <h1 class="mb-1">Admin <span class="text-indigo">List</span></h1>
                <p class="text-muted mb-0">View and manage all administrators for the system.</p>
            </div>
            <div class="date-node text-end">
                <a href="adminadd.php" class="btn btn-primary rounded-pill px-4 shadow-sm animate__animated animate__pulse animate__infinite">
                    <i class="fa-solid fa-user-shield me-2"></i>Add New Admin
                </a>
            </div>
        </div>

        <div class="intelligence-card animate__animated animate__fadeInUp">
            <?php if(isset($_GET['msg'])): ?>
                <div class="alert alert-<?php echo htmlspecialchars($_GET['type'] ?? 'info'); ?> mb-4 rounded-3 shadow-sm border-0">
                    <i class="fa-solid fa-bell me-2"></i> <?php echo htmlspecialchars($_GET['msg']); ?>
                </div>
            <?php endif; ?>

            <div class="table-responsive">
                <table id="admin-table" class="table modern-table align-middle">
                    <thead class="bg-indigo-light">
                        <tr>
                            <th>Admin ID</th>
                            <th>Name</th>
                            <th>Email Address</th>
                            <th>Status</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $query = "SELECT * FROM users WHERE role = 'admin'";
                        $result = mysqli_query($con, $query);

                        while ($row = mysqli_fetch_array($result)) {
                            $aid = htmlspecialchars($row['id']);
                        ?>
                        <tr>
                            <td><span class="badge bg-slate-100 text-slate-600 border border-slate-200">#EV-SEC-<?php echo $aid; ?></span></td>
                            <td class="fw-bold text-slate-800"><?php echo htmlspecialchars($row['first_name'] . ' ' . $row['last_name']); ?></td>
                            <td class="text-indigo fw-medium"><?php echo htmlspecialchars($row['email']); ?></td>
                            <td>
                                <span class="badge bg-success-light text-success rounded-pill px-3">
                                    <i class="fa-solid fa-circle-check me-1 fs-xs"></i> Active
                                </span>
                            </td>
                            <td class="text-end">
                        </tr>
                        <?php $cnt++; } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
    </main>
    <?php include("footer.php"); ?>
    $(document).ready(function() {
        $('#admin-table').DataTable({
            responsive: true,
            pageLength: 10,
            language: {
                search: "_INPUT_",
                searchPlaceholder: "Search admins..."
            }
        });
    });
</script>
</body>
</html>