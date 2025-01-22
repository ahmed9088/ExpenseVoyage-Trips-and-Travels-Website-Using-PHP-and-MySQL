<?php
session_start();
require 'admin/config.php'; // Ensure your config file connects to the DB

$trip_id = isset($_GET['trip_id']) ? intval($_GET['trip_id']) : null;
$trip_name = ""; // Initialize trip name

if ($trip_id) {
    // Fetch trip name from database
    $query = "SELECT trip_name FROM trips WHERE trip_id = ?";
    $stmt = $con->prepare($query);
    $stmt->bind_param("i", $trip_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $trip = $result->fetch_assoc();
        $trip_name = htmlspecialchars($trip['trip_name']);
    } else {
        $trip_name = "the selected trip";
    }
} else {
    $trip_name = "the selected trip";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Payment Successful</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <link href="css/style.css" rel="stylesheet">
    <style>
        body { font-family: 'Poppins', sans-serif; background-color: #f4f4f4; color: #333; margin: 0; padding: 0; }
        .success-container { max-width: 600px; margin: 100px auto; background: #fff; padding: 40px; border-radius: 10px; box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1); text-align: center; }
        h2 { color: #427c00; }
        p { font-size: 18px; }
        .btn-home { background-color: #427c00; color: white; padding: 15px 20px; border: none; border-radius: 5px; cursor: pointer; font-size: 18px; margin-top: 20px; display: inline-block; }
        .btn-home:hover { background-color: #7AB730; }
    </style>
</head>
<body>
<style>
    
    body {
      --sb-track-color: #232E33;
      --sb-thumb-color: #7AB730;
      --sb-size: 14px;
    }
    
    body::-webkit-scrollbar {
      width: 12px;
    }
    
    body::-webkit-scrollbar-track {
      background: var(--sb-track-color);
      border-radius: 1px;
    }
    
    body::-webkit-scrollbar-thumb {
      background: var(--sb-thumb-color);
      border-radius: 3px;
      
    }
    
    @supports not selector(::-webkit-scrollbar) {
      body {
        scrollbar-color: var(--sb-thumb-color)
                         var(--sb-track-color);
      }
    }
    </style>
    <div class="success-container">
        <h2>Payment Successful!</h2>
        <p>Thank you for your payment for <?php echo $trip_name; ?>.</p>
        <a href="index.php" class="btn-home">Return to Home</a>
    </div>
</body>
</html>
