<?php
session_start();
include 'config.php';

if (isset($_GET['id'])) {
    $reviewId = intval($_GET['id']); // Ensure the ID is an integer
    // Fetch the image name to delete from the server
    $query = mysqli_query($con, "SELECT image FROM review WHERE id = $reviewId");
    $row = mysqli_fetch_assoc($query);
    
    if ($row) {
        $imagePath = '../img/reviewerimages/' . $row['image'];
        
        // Delete the review from the database
        $deleteQuery = mysqli_query($con, "DELETE FROM review WHERE id = $reviewId");
        
        if ($deleteQuery) {
            // Delete the image file from the server
            if (file_exists($imagePath)) {
                unlink($imagePath);
            }
            echo "<script>alert('Review deleted successfully.'); window.location.href = 'feedbackview.php';</script>";
        } else {
            echo "Error deleting review: " . mysqli_error($con);
        }
    } else {
        echo "Review not found.";
    }
} else {
    echo "No review ID specified.";
}
?>
