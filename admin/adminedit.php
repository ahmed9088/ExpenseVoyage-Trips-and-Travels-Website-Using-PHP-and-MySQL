<?php  
session_start();
include("config.php");

if (!isset($_SESSION['auser'])) {
    header("Location: index.php");
    exit();
}

if (isset($_GET['id'])) {
    $admin_id = $_GET['id'];
    
    // Fetch admin details
    $query = "SELECT * FROM admin WHERE aid = ?";
    $stmt = mysqli_prepare($con, $query);
    mysqli_stmt_bind_param($stmt, 'i', $admin_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if ($result && mysqli_num_rows($result) > 0) {
        $admin = mysqli_fetch_assoc($result);

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $name = $_POST['name'];
            $email = $_POST['email'];
            $dob = $_POST['dob'];
            $contact = $_POST['contact'];
            $password = $_POST['password'];
            
            // Prepare the update query
            $update_query = "UPDATE admin SET auser=?, aemail=?, adob=?, aphone=?";
            $params = [$name, $email, $dob, $contact];
            
            // Add password update if provided
            if (!empty($password)) {
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $update_query .= ", apass=?";
                $params[] = $hashed_password;
            }
            
            $update_query .= " WHERE aid=?";
            $params[] = $admin_id;

            if ($stmt = mysqli_prepare($con, $update_query)) {
                // Build the parameter type string
                $types = str_repeat('s', count($params) - 1) . 'i'; // 's' for string, 'i' for integer
                mysqli_stmt_bind_param($stmt, $types, ...$params);
                if (mysqli_stmt_execute($stmt)) {
                    header("Location: adminlist.php?msg=Admin details updated successfully");
                    exit();
                } else {
                    echo "Error: " . mysqli_error($con);
                }
            } else {
                die("Statement preparation failed: " . mysqli_error($con));
            }
        }
    } else {
        header("Location: adminlist.php");
        exit();
    }
} else {
    header("Location: adminlist.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
      <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
        <title>Tameer.com</title>
    <title>Edit Admin</title>
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
</head>
<body>
    <div class="container">
        <h2>Edit Admin</h2>
        <form method="post">
            <div class="form-group">
                <label for="name">Name</label>
                <input type="text" class="form-control" id="name" name="name" value="<?php echo htmlspecialchars($admin['auser']); ?>" required>
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($admin['aemail']); ?>" required>
            </div>
            <div class="form-group">
                <label for="dob">Date of Birth</label>
                <input type="date" class="form-control" id="dob" name="dob" value="<?php echo htmlspecialchars($admin['adob']); ?>" required>
            </div>
            <div class="form-group">
                <label for="contact">Contact</label>
                <input type="text" class="form-control" id="contact" name="contact" value="<?php echo htmlspecialchars($admin['aphone']); ?>" required>
            </div>
            <div class="form-group">
                <label for="password">New Password</label>
                <input type="password" class="form-control" id="password" name="password">
                <small class="form-text text-muted">Leave blank if you do not want to change the password.</small>
            </div>
            <button type="submit" class="btn btn-primary">Update</button>
        </form>
    </div>
    <script src="assets/js/bootstrap.min.js"></script>
</body>
</html>
