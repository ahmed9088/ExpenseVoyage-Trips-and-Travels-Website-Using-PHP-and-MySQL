<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once("config.php");

if(!isset($_SESSION['auser'])) {
    header("location:index.php");
    exit();
}

$success = "";
$error = "";

// Handle Delete
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    if (mysqli_query($con, "DELETE FROM chatbot_knowledge WHERE id = $id")) {
        $success = "Pattern deleted successfully!";
    }
}

// Handle Add
if (isset($_POST['add_pattern'])) {
    $pattern = mysqli_real_escape_string($con, $_POST['question_pattern']);
    $answer = mysqli_real_escape_string($con, $_POST['answer_template']);
    $category = mysqli_real_escape_string($con, $_POST['category']);

    if (mysqli_query($con, "INSERT INTO chatbot_knowledge (question_pattern, answer_template, category) VALUES ('$pattern', '$answer', '$category')")) {
        $success = "New knowledge pattern added!";
    } else {
        $error = "Failed to add pattern.";
    }
}

$knowledge = mysqli_query($con, "SELECT * FROM chatbot_knowledge ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chatbot Intelligence - ExpenseVoyage Admin</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/admin_modern.css">
    <style>
        .pattern-list { max-height: 600px; overflow-y: auto; }
        .kb-card { transition: all 0.3s ease; border-left: 4px solid var(--indigo); }
        .kb-card:hover { transform: translateX(5px); box-shadow: 0 5px 15px rgba(0,0,0,0.1); }
        .badge-cat { font-size: 0.7rem; text-transform: uppercase; letter-spacing: 1px; }
    </style>
</head>
<body>

<div class="admin-wrapper">
    <?php include("header.php"); ?>

    <main class="modern-main">
        <div class="page-header d-flex justify-content-between align-items-center mb-5">
            <div>
                <h1 class="mb-1">Chatbot <span class="text-indigo">Intelligence</span></h1>
                <p class="text-muted mb-0">Manage the AI's training data and question patterns.</p>
            </div>
            <button class="btn btn-indigo rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#addModal">
                <i class="fa-solid fa-plus me-2"></i>Add Pattern
            </button>
        </div>

        <?php if($success): ?>
            <div class="alert alert-success border-0 shadow-sm mb-4"><i class="fa-solid fa-circle-check me-2"></i> <?php echo $success; ?></div>
        <?php endif; ?>

        <div class="row">
            <div class="col-12">
                <div class="intelligence-card p-4">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h5 class="m-0">Trained Knowledge Base (<?php echo mysqli_num_rows($knowledge); ?> Patterns)</h5>
                    </div>
                    
                    <div class="pattern-list pe-2">
                        <div class="row g-3">
                            <?php while($row = mysqli_fetch_assoc($knowledge)): ?>
                            <div class="col-md-6">
                                <div class="card kb-card border-0 shadow-sm p-3">
                                    <div class="d-flex justify-content-between mb-2">
                                        <span class="badge bg-indigo-light text-indigo badge-cat"><?php echo $row['category']; ?></span>
                                        <a href="?delete=<?php echo $row['id']; ?>" class="text-danger" onclick="return confirm('Delete this pattern?')">
                                            <i class="fa-solid fa-trash-can"></i>
                                        </a>
                                    </div>
                                    <div class="fw-bold fs-sm text-slate-800 mb-1">
                                        <i class="fa-solid fa-quote-left text-muted me-2" style="font-size: 0.8rem;"></i>
                                        <?php echo htmlspecialchars($row['question_pattern']); ?>
                                    </div>
                                    <div class="text-muted fs-xs">
                                        <?php echo htmlspecialchars($row['answer_template']); ?>
                                    </div>
                                </div>
                            </div>
                            <?php endwhile; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>

<!-- Add Modal -->
<div class="modal fade" id="addModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <form method="post">
                <div class="modal-header bg-indigo text-white">
                    <h5 class="modal-title">Add AI Intelligence Pattern</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label">Category</label>
                        <select name="category" class="form-select">
                            <option value="general">General</option>
                            <option value="policy">Policy & Terms</option>
                            <option value="safety">Safety</option>
                            <option value="booking">Booking Info</option>
                            <option value="facilities">Facilities</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Question Patterns (Regex/Pipe separated)</label>
                        <input type="text" name="question_pattern" class="form-control" required placeholder="e.g. food|meal|eat|breakfast">
                        <div class="form-text">Matching words separated by | (pipe)</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">AI Answer Template</label>
                        <textarea name="answer_template" class="form-control" rows="4" required placeholder="Enter the response the bot should give..."></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" name="add_pattern" class="btn btn-indigo rounded-pill px-4">Train Bot</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
