<?php
include 'chatbot-loader.php';
session_start();
include 'admin/config.php';
include 'csrf.php';

// Check if the user is logged in
if (!isset($_SESSION['email'])) {
    $_SESSION['redirect_after_login'] = 'contact.php';
    header("Location: login/account.php");
    exit();
}

// Handle form submission (Original logic preserved)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['contactsubmit'])) {
    // Verify CSRF
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        die("Security validation failed.");
    }

    $name = mysqli_real_escape_string($con, $_POST['name']);
    $email = mysqli_real_escape_string($con, $_POST['email']);
    $subject = mysqli_real_escape_string($con, $_POST['subject']);
    $message = mysqli_real_escape_string($con, $_POST['message']);
    
    if (!empty($name) && !empty($email) && !empty($subject) && !empty($message)) {
        $sql = "INSERT INTO contactus (name, email, subject, message) VALUES ('$name', '$email', '$subject', '$message')";
        if (mysqli_query($con, $sql)) {
            $_SESSION['contact_success'] = true;
        }
    }
    header("Location: contact.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Contact Us | ExpenseVoyage</title>
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
        .contact-hero {
            height: 50vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(rgba(10, 12, 16, 0.8), rgba(10, 12, 16, 0.8)), 
                        url('img/contact-header.jpg') center/cover no-repeat;
            text-align: center;
        }

        .contact-method {
            transition: all 0.4s ease;
        }

        .contact-method:hover {
            transform: translateY(-5px);
            border-color: var(--gold) !important;
        }

        .contact-icon {
            width: 60px;
            height: 60px;
            background: rgba(212, 175, 55, 0.1);
            color: var(--gold);
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            margin-bottom: 20px;
            font-size: 1.5rem;
        }
    </style>
</head>
<body>
    <div id="particles-js"></div>

    <nav class="navbar navbar-expand-lg sticky-top glass-panel mx-4 mt-3 py-3">
        <div class="container">
            <a href="index.php" class="navbar-brand">
                <span class="text-gold">Expense</span><span class="text-white">Voyage</span>
            </a>
            <div class="navbar-nav ms-auto d-none d-lg-flex">
                <a class="nav-link px-3" href="index.php">Home</a>
                <a class="nav-link px-3" href="package.php">Packages</a>
                <a class="nav-link px-3" href="about.php">About</a>
                <a class="nav-link px-3 active text-gold" href="contact.php">Contact</a>
            </div>
        </div>
    </nav>

    <header class="contact-hero">
        <div class="container">
            <h6 class="text-gold text-uppercase tracking-widest mb-3 animate__animated animate__fadeIn">Get In Touch</h6>
            <h1 class="display-3 text-white serif-font animate__animated animate__fadeInUp">We are here for you</h1>
        </div>
    </header>

    <section class="py-5">
        <div class="container">
            <div class="row g-5">
                <div class="col-lg-4">
                    <div class="glass-panel p-4 mb-4 contact-method border-secondary">
                        <div class="contact-icon"><i class="fas fa-map-marker-alt"></i></div>
                        <h4>The Atelier</h4>
                        <p class="text-white-50 small mb-0">123 Luxe Avenue, Dubai Marina<br>United Arab Emirates</p>
                    </div>
                    <div class="glass-panel p-4 mb-4 contact-method border-secondary">
                        <div class="contact-icon"><i class="fas fa-phone-alt"></i></div>
                        <h4>Private Line</h4>
                        <p class="text-white-50 small mb-0">+971 4 555 LUXE<br>Available 24/7 for Elite Members</p>
                    </div>
                    <div class="glass-panel p-4 contact-method border-secondary">
                        <div class="contact-icon"><i class="fas fa-envelope"></i></div>
                        <h4>Inquiries</h4>
                        <p class="text-white-50 small mb-0">concierge@expensevoyage.com<br>Response within 2 hours</p>
                    </div>
                </div>

                <div class="col-lg-8">
                    <div class="glass-panel p-5 h-100">
                        <h2 class="serif-font mb-4">Send a Message</h2>
                        <?php if (isset($_SESSION['contact_success'])): ?>
                            <div class="alert alert-success bg-transparent border-gold text-gold mb-4">
                                Our concierge has received your request and will contact you shortly.
                            </div>
                            <?php unset($_SESSION['contact_success']); ?>
                        <?php endif; ?>
                        <form action="contact.php" method="POST">
                            <?php echo csrf_input(); ?>
                            <div class="row g-3">
                                <div class="col-md-6 mb-3">
                                    <input type="text" name="name" class="form-control bg-transparent text-white border-secondary py-3" placeholder="Full Name" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <input type="email" name="email" class="form-control bg-transparent text-white border-secondary py-3" placeholder="Email Address" required>
                                </div>
                                <div class="col-12 mb-3">
                                    <input type="text" name="subject" class="form-control bg-transparent text-white border-secondary py-3" placeholder="Subject" required>
                                </div>
                                <div class="col-12 mb-4">
                                    <textarea name="message" rows="5" class="form-control bg-transparent text-white border-secondary py-3" placeholder="How can we assist you?" required></textarea>
                                </div>
                                <div class="col-12 text-end">
                                    <button type="submit" name="contactsubmit" class="btn btn-primary px-5 py-3 rounded-0">SEND MESSAGE</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <footer class="py-5 border-top border-secondary mt-5">
        <div class="container text-center">
            <h4 class="text-gold mb-3">ExpenseVoyage</h4>
            <p class="text-white-50 small mb-0">&copy; 2026 ExpenseVoyage. Crafted for the extraordinary.</p>
        </div>
    </footer>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/custom.js"></script>
</body>
</html>