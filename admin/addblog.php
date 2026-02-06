<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once("config.php");
require_once("../csrf.php");

// Session Check
if(!isset($_SESSION['aid'])) {
    header("location:index.php");
    exit();
}

$error = "";
$success = "";

if(isset($_POST['addblog'])){
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
         die("Security Violation");
    }

    $blogtitle = trim($_POST['title']);
    $blogdesc = trim($_POST['description']);
    
    // Handle Image
    $blog_image = "";
    $upload_ok = true;

    if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        $type = $_FILES['image']['type'];

        if (in_array($ext, $allowed) && $_FILES['image']['size'] < 5000000) {
             $img_name = uniqid('blog_') . '.' . $ext;
             $target = '../img/blogimg/' . $img_name;
             if (move_uploaded_file($_FILES['image']['tmp_name'], $target)) {
                 $blog_image = "img/blogimg/" . $img_name;
             } else {
                 $error = "File upload failed.";
                 $upload_ok = false;
             }
        } else {
             $error = "Invalid file type or size (Max 5MB).";
             $upload_ok = false;
        }
    } else {
        $error = "Visual asset required for editorial publication.";
        $upload_ok = false;
    }

    if ($upload_ok && $blog_image){
        $sql = "INSERT INTO blog (blog_title, blog_image, blog_text) VALUES (?, ?, ?)";
        $stmt = $con->prepare($sql);
        $stmt->bind_param('sss', $blogtitle, $blog_image, $blogdesc);
        if($stmt->execute()){
            $success = "Blog post added successfully!";
        } else {
            error_log("Blog DB Error: " . $stmt->error);
            $error = "Failed to add blog post.";
        }
    }
}

// Handle Blog Deletion
if(isset($_GET['delete_id'])) {
    if (!verify_csrf_token($_GET['csrf_token'] ?? '')) {
         die("Security Violation");
    }

    $did = intval($_GET['delete_id']);
    // Fetch image path to delete
    $res = $con->query("SELECT blog_image FROM blog WHERE blog_id = $did");
    if($row = $res->fetch_assoc()) {
        if(!empty($row['blog_image']) && file_exists("../" . $row['blog_image'])) {
            @unlink("../" . $row['blog_image']);
        }
    }
    
    $delStmt = $con->prepare("DELETE FROM blog WHERE blog_id = ?");
    $delStmt->bind_param("i", $did);
    $delStmt->execute();

    header("Location: addblog.php?msg=Blog post deleted successfully&type=success");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blog Management - ExpenseVoyage Dashboard</title>
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
                <h1 class="mb-1">Blog <span class="text-indigo">Management</span></h1>
                <p class="text-muted mb-0">Write and manage blog posts for your website.</p>
            </div>
            <div class="date-node text-end">
                <span class="badge bg-indigo-light text-indigo">Post Management</span>
            </div>
        </div>

        <div class="row g-4 mb-5">
            <div class="col-xl-4 col-lg-5">
                <div class="intelligence-card animate__animated animate__fadeInLeft">
                    <h5 class="section-title mb-4">New Blog Post</h5>
                    
                    <?php if($success): ?>
                        <div class="alert alert-success fs-xs mb-4">
                            <i class="fa-solid fa-feather me-2"></i> <?php echo htmlspecialchars($success); ?>
                        </div>
                    <?php endif; ?>
                    
                    <?php if($error): ?>
                        <div class="alert alert-danger fs-xs mb-4">
                            <i class="fa-solid fa-circle-exclamation me-2"></i> <?php echo htmlspecialchars($error); ?>
                        </div>
                    <?php endif; ?>

                    <form method="post" enctype="multipart/form-data">
                        <?php echo csrf_input(); ?>
                        <div class="mb-3">
                            <label class="form-label fs-xs text-uppercase fw-bold">Blog Title</label>
                            <input type="text" name="title" class="form-control" required placeholder="e.g. Hidden Gems of Pakistan">
                        </div>
                        <div class="mb-4">
                            <label class="form-label fs-xs text-uppercase fw-bold text-indigo">Blog Content</label>
                            <textarea name="description" class="form-control" rows="8" required placeholder="Write your trajectory story..."></textarea>
                        </div>
                        <div class="mb-4">
                            <label class="form-label fs-xs text-uppercase fw-bold">Lead Visual Asset</label>
                            <input type="file" name="image" class="form-control" accept="image/*" required>
                        </div>
                        <div class="d-grid">
                            <button type="submit" name="addblog" class="btn btn-primary rounded-pill py-3 shadow-sm">
                                <i class="fa-solid fa-upload me-2"></i>Publish Post
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="col-xl-8 col-lg-7">
                <div class="intelligence-card animate__animated animate__fadeInRight">
                    <h5 class="section-title mb-4">All Blog Posts</h5>
                    
                    <?php if(isset($_GET['msg'])): ?>
                        <div class="alert alert-success fs-xs mb-4">
                            <i class="fa-solid fa-book-open me-2"></i> <?php echo htmlspecialchars($_GET['msg']); ?>
                        </div>
                    <?php endif; ?>

                    <div class="table-responsive">
                        <table id="blog-table" class="table modern-table align-middle">
                            <thead class="bg-indigo-light">
                                <tr>
                                    <th>Post Title</th>
                                    <th>Image</th>
                                    <th>Content</th>
                                    <th class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $query = $con->query("SELECT * FROM blog ORDER BY blog_id DESC");
                                while($row = $query->fetch_assoc()):
                                ?>
                                <tr>
                                    <td>
                                        <div class="fw-bold text-slate-800 fs-sm"><?php echo htmlspecialchars($row['blog_title']); ?></div>
                                        <div class="fs-xs text-muted">Registry: #BLG-<?php echo $row['blog_id']; ?></div>
                                    </td>
                                    <td>
                                        <img src="../<?php echo htmlspecialchars($row['blog_image']); ?>" width="80" height="40" class="rounded object-fit-cover shadow-sm">
                                    </td>
                                    <td>
                                        <div class="text-muted small text-truncate-custom" style="max-width: 250px;">
                                            <?php echo htmlspecialchars($row['blog_text']); ?>
                                        </div>
                                    </td>
                                    <td class="text-end">
                                        <div class="d-flex justify-content-end gap-1">
                                            <a href="?delete_id=<?php echo $row['blog_id']; ?>&csrf_token=<?php echo generate_csrf_token(); ?>" class="btn btn-sm btn-outline-danger border-0" onclick="return confirm('Delete this blog post?')">
                                                <i class="fa-solid fa-trash-can"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </main>
    <?php include("footer.php"); ?>