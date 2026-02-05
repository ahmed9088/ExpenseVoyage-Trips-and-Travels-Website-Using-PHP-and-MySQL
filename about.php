<?php
include 'chatbot-loader.php';
session_start();
include 'admin/config.php';

// Handle review submission (Original logic preserved)
if (isset($_POST['sendreview'])) {
    if (isset($_SESSION['email'])) {
        $useremail = mysqli_real_escape_string($con, $_POST['useremail']);
        $usermessage = mysqli_real_escape_string($con, $_POST['usermessage']);
        $userid = $_SESSION['userid'];
        
        $stmt = $con->prepare("SELECT first_name, last_name FROM users WHERE id = ?");
        $stmt->bind_param("i", $userid);
        $stmt->execute();
        $result = $stmt->get_result();
        $userRow = $result->fetch_assoc();
        $username = $userRow ? ($userRow['first_name'] . ' ' . $userRow['last_name']) : 'Voyageur';
        $stmt->close();
        
        if (isset($_FILES['profile']) && $_FILES['profile']['error'] == UPLOAD_ERR_OK) {
            $fileExtension = strtolower(pathinfo($_FILES['profile']['name'], PATHINFO_EXTENSION));
            $newFileName = uniqid() . '.' . $fileExtension;
            $uploadFileDir = './uploads/';
            if (!file_exists($uploadFileDir)) mkdir($uploadFileDir, 0755, true);
            
            if (move_uploaded_file($_FILES['profile']['tmp_name'], $uploadFileDir . $newFileName)) {
                $stmt = $con->prepare("INSERT INTO review (userid, email, image, usermessage, date_time, username) VALUES (?, ?, ?, ?, NOW(), ?)");
                $stmt->bind_param("issss", $userid, $useremail, $newFileName, $usermessage, $username);
                $stmt->execute();
                $stmt->close();
                $_SESSION['review_submitted'] = true;
                header("Location: about.php");
                exit();
            }
        }
    } else {
        $_SESSION['login_required'] = true;
        header("Location: login/account.php");
        exit();
    }
}

// Fetch agents
$agentResult = mysqli_query($con, "SELECT * FROM agent");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Our Story | ExpenseVoyage</title>
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
        .about-hero {
            height: 60vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(rgba(248, 250, 252, 0.6), rgba(248, 250, 252, 0.7)), 
                        url('img/about-bg.jpg') center/cover no-repeat;
            text-align: center;
        }

        .agent-card {
            text-align: center;
        }

        .agent-card img {
            width: 150px;
            height: 150px;
            object-fit: cover;
            border-radius: 50%;
            border: 3px solid var(--primary);
            margin-bottom: 20px;
        }

        .review-form-wrap {
            max-width: 600px;
            margin: 0 auto;
        }
        
        .hover-gold:hover {
            color: var(--primary) !important;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg sticky-top py-3">
        <div class="container">
            <a href="index.php" class="navbar-brand">
                <span class="text-primary fw-bold">Expense</span><span class="text-dark">Voyage</span>
            </a>
            <div class="navbar-nav ms-auto d-none d-lg-flex">
                <a class="nav-link px-3" href="index.php">Home</a>
                <a class="nav-link px-3" href="package.php">Packages</a>
                <a class="nav-link px-3 active" href="about.php">About</a>
                <a class="nav-link px-3" href="contact.php">Contact</a>
            </div>
        </div>
    </nav>

    <header class="about-hero">
        <div class="container">
            <h6 class="text-primary text-uppercase tracking-widest mb-3 animate__animated animate__fadeIn">The Legacy</h6>
            <h1 class="display-3 text-dark serif-font animate__animated animate__fadeInUp">Crafting Memories</h1>
        </div>
    </header>

    <section class="py-5">
        <div class="container">
            <div class="row align-items-center mb-5">
                <div class="col-lg-6 mb-4 mb-lg-0 animate-on-scroll">
                    <img src="img/about-side.jpg" class="img-fluid glass-panel p-2 shadow-sm" alt="About">
                </div>
                <div class="col-lg-6 ps-lg-5 animate-on-scroll" data-animation="animate__fadeInRight">
                    <h2 class="serif-font mb-4">Our Commitment</h2>
                    <p class="text-muted">ExpenseVoyage was founded on the principle that travel should be more than just visiting a place—it should be a soul-stirring experience. We curate high-end journeys for those who seek the extraordinary.</p>
                    <p class="text-muted">From luxury villas in the Mediterranean to private safaris in the Serengeti, every detail is hand-picked by our experts.</p>
                </div>
            </div>

            <div class="row g-4 mt-5">
                <div class="col-md-4 text-center animate-on-scroll">
                    <div class="glass-panel p-4 h-100 bg-white shadow-sm">
                        <i class="fas fa-award text-primary fa-3x mb-3"></i>
                        <h4 class="mb-2">Quality First</h4>
                        <p class="small text-muted mb-0">We partner only with the finest luxury providers.</p>
                    </div>
                </div>
                <div class="col-md-4 text-center animate-on-scroll" data-animation="animate__fadeInUp" data-delay="0.2s">
                    <div class="glass-panel p-4 h-100 bg-white shadow-sm">
                        <i class="fas fa-globe-americas text-primary fa-3x mb-3"></i>
                        <h4 class="mb-2">Global Reach</h4>
                        <p class="small text-muted mb-0">Experience the world without boundaries.</p>
                    </div>
                </div>
                <div class="col-md-4 text-center animate-on-scroll" data-animation="animate__fadeInUp" data-delay="0.4s">
                    <div class="glass-panel p-4 h-100 bg-white shadow-sm">
                        <i class="fas fa-heart text-primary fa-3x mb-3"></i>
                        <h4 class="mb-2">Personal Touch</h4>
                        <p class="small text-muted mb-0">Hand-crafted itineraries tailored to you.</p>
                    </div>
                </div>
            </div>

            <!-- Dynamic Agents Section -->
            <div class="mt-5 pt-5">
                <h2 class="serif-font text-center mb-5 animate-on-scroll">Our Curators</h2>
                <div class="row g-4">
                    <?php if (mysqli_num_rows($agentResult) > 0): ?>
                        <?php while($agent = mysqli_fetch_assoc($agentResult)): ?>
                            <div class="col-lg-3 col-md-6 animate-on-scroll">
                                <div class="agent-card glass-panel p-4 h-100 bg-white">
                                    <img src="admin/user/<?php echo htmlspecialchars($agent['a_image']); ?>" alt="Agent" class="mb-3">
                                    <h4 class="h5 mb-1"><?php echo htmlspecialchars($agent['a_name']); ?></h4>
                                    <p class="text-primary small text-uppercase tracking-widest mb-3"><?php echo htmlspecialchars($agent['a_profetion']); ?></p>
                                    <div class="d-flex justify-content-center gap-3">
                                        <a href="contact.php" class="btn btn-outline-primary btn-sm rounded-pill px-3">Consult Now</a>
                                    </div>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <div class="col-12 text-center">
                            <p class="text-muted">Our curators are currently exploring new horizons.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>

    <!-- Share Your Experience -->
    <section class="py-5 bg-light">
        <div class="container">
            <div class="review-form-wrap mx-auto bg-white p-5 rounded-4 shadow-sm animate-on-scroll">
                <h2 class="serif-font text-center mb-4">Leave a Legacy</h2>
                <?php if (isset($_SESSION['review_submitted'])): ?>
                    <div class="alert alert-success border-0 bg-success-subtle text-success text-center">
                        Thank you for sharing your journey with us.
                    </div>
                    <?php unset($_SESSION['review_submitted']); ?>
                <?php endif; ?>
                <form action="about.php" method="POST" enctype="multipart/form-data">
                    <div class="mb-3">
                        <input type="email" name="useremail" class="form-control bg-light border-0 py-3" placeholder="Your Email" required>
                    </div>
                    <div class="mb-3">
                        <textarea name="usermessage" rows="4" class="form-control bg-light border-0 py-3" placeholder="Tell us about your journey..." required></textarea>
                    </div>
                    <div class="mb-4">
                        <label class="small text-muted mb-2">A memory (photo)</label>
                        <input type="file" name="profile" class="form-control bg-light border-0" accept="image/*" required>
                    </div>
                    <button type="submit" name="sendreview" class="btn btn-primary w-100 py-3">SUBMIT REVIEW</button>
                </form>
            </div>
        </div>
    </section>

    <footer class="py-5 border-top bg-white">
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