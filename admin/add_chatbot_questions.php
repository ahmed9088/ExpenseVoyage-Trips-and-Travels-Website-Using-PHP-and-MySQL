<?php
// Start the session only if it hasn't been started yet
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include the database configuration
include 'config.php';

// Check if the form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $question = $con->real_escape_string(trim($_POST['question']));
    $answer = $con->real_escape_string(trim($_POST['answer']));

    // Prepare the SQL statement
    $sql = "INSERT INTO faq (question, answer) VALUES ('$question', '$answer')";

    // Execute the query and check for success
    if ($con->query($sql) === TRUE) {
        $_SESSION['message'] = "Your question has been added successfully.";
    } else {
        $_SESSION['message'] = "Error: " . $con->error;
    }

    // Redirect back to the form page (add_questions.php)
    header("Location: add_chatbot_questions.php");
    exit();
}

// Close the database connection
$con->close();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
    <title>Tameer.com</title>
    <link rel="shortcut icon" type="image/x-icon" href="assets/img/favicon-32x32.png">
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/font-awesome.min.css">
    <link rel="stylesheet" href="assets/css/feathericon.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <!--[if lt IE 9]>
        <script src="assets/js/html5shiv.min.js"></script>
        <script src="assets/js/respond.min.js"></script>
    <![endif]-->
</head>
<body>
    <!-- Header -->
    <?php include("header.php"); ?>
    <!-- /Sidebar -->
    
    <!-- Page Wrapper -->
    <div class="page-wrapper">
        <div class="content container-fluid">
            <!-- Page Header -->
            <div class="page-header">
                <div class="row">
                    <div class="col">
                        <h3 class="page-title">Add Questions</h3>
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item"><a href="index.html">Dashboard</a></li>
                            <li class="breadcrumb-item active">Add Questions</li>
                        </ul>
                    </div>
                </div>
            </div>
            <!-- /Page Header -->

            <!-- Display success or error message -->
            <?php
            if (isset($_SESSION['message'])) {
                echo '<div class="alert alert-info">' . $_SESSION['message'] . '</div>';
                unset($_SESSION['message']); // Clear message after displaying
            }
            ?>

            <!-- Add Question Form -->
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body">
                            <form action="add_chatbot_questions.php" method="POST">
                                <div class="form-group">
                                    <label for="question">Question</label>
                                    <input type="text" id="question" name="question" class="form-control" required>
                                </div>
                                <div class="form-group">
                                    <label for="answer">Answer</label>
                                    <textarea id="answer" name="answer" class="form-control" rows="5" required></textarea>
                                </div>
                                <button type="submit" class="btn btn-primary">Add Question</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <!-- /Add Question Form -->

        </div>
    </div>
    <!-- /Page Wrapper -->

    <!-- jQuery -->
    <script src="assets/js/jquery-3.2.1.min.js"></script>
    <!-- Bootstrap Core JS -->
    <script src="assets/js/popper.min.js"></script>
    <script src="assets/js/bootstrap.min.js"></script>
    <!-- Slimscroll JS -->
    <script src="assets/plugins/slimscroll/jquery.slimscroll.min.js"></script>
    <!-- Custom JS -->
    <script src="assets/js/script.js"></script>
</body>
</html>
