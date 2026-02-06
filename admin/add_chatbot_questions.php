<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once("config.php");

// Session Check
if(!isset($_SESSION['aid'])) {
    header("location:index.php");
    exit();
}

$success = "";
$error = "";

// Check if the form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $question = mysqli_real_escape_string($con, trim($_POST['question']));
    $answer = mysqli_real_escape_string($con, trim($_POST['answer']));

    $sql = "INSERT INTO faq (question, answer) VALUES (?, ?)";
    $stmt = $con->prepare($sql);
    $stmt->bind_param('ss', $question, $answer);

    if ($stmt->execute()) {
        $success = "Automated response node deployed successfully.";
    } else {
        $error = "Deployment failure: " . $stmt->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AI Response Logic - ExpenseVoyage Intelligence</title>
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
                <h1 class="mb-1">Automated <span class="text-indigo">Response Logic</span></h1>
                <p class="text-muted mb-0">Program the tactical chatbot with new operational knowledge.</p>
            </div>
            <div class="date-node text-end">
                <span class="badge bg-indigo-light text-indigo">Logic Node Active</span>
            </div>
        </div>

        <div class="row justify-content-center">
            <div class="col-xl-8 col-lg-10">
                <div class="intelligence-card animate__animated animate__fadeInUp">
                    <h5 class="section-title mb-4">New Logic Parameter</h5>
                    
                    <?php if($success): ?>
                        <div class="alert alert-success mb-4">
                            <i class="fa-solid fa-robot me-2"></i> <?php echo $success; ?>
                        </div>
                    <?php endif; ?>
                    
                    <?php if($error): ?>
                        <div class="alert alert-danger mb-4">
                            <i class="fa-solid fa-triangle-exclamation me-2"></i> <?php echo $error; ?>
                        </div>
                    <?php endif; ?>

                    <form action="add_chatbot_questions.php" method="POST">
                        <div class="mb-4">
                            <label class="form-label fs-xs text-uppercase fw-bold">Incoming Query (Question)</label>
                            <input type="text" name="question" class="form-control" required placeholder="e.g. What is the visa policy for...?" style="font-weight: 600;">
                        </div>
                        <div class="mb-4">
                            <label class="form-label fs-xs text-uppercase fw-bold text-indigo">Automated Response (Answer)</label>
                            <textarea name="answer" class="form-control" rows="5" required placeholder="Formulate the standard response..."></textarea>
                        </div>
                        <div class="d-grid pt-2">
                            <button type="submit" class="btn btn-primary rounded-pill py-3 shadow-sm">
                                <i class="fa-solid fa-microchip me-2"></i>Deploy Logic Node
                            </button>
                        </div>
                    </form>
                </div>

                <div class="mt-4 text-center">
                    <p class="text-muted fs-xs">
                        <i class="fa-solid fa-circle-info me-1"></i> 
                        These logic nodes directly inform the client-facing AI Chatbot. Ensure accuracy in all deployments.
                    </p>
                </div>
            </div>
        </div>
    </main>
    <?php include("footer.php"); ?>
