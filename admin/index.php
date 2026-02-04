<?php  
session_start();
include("config.php");
$error = "";

// Redirect to dashboard if already logged in
if(isset($_SESSION['auser'])) {
    header("Location: dashboard.php");
    exit();
}

if (isset($_POST['login'])) {
    $user = $_POST['user'];
    $pass = $_POST['pass'];
    
    if (!empty($user) && !empty($pass)) {
        // Fetch the user data based on the username
        $query = "SELECT auser, apass FROM admin WHERE auser = ?";
        $stmt = mysqli_prepare($con, $query);
        mysqli_stmt_bind_param($stmt, 's', $user);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        if ($row = mysqli_fetch_assoc($result)) {
            $stored_hash = $row['apass'];
            
            // Compare the password using password_verify
            if (password_verify($pass, $stored_hash) || $pass === $stored_hash) {
                // If it was plain text (direct match), we should ideally re-hash it, 
                // but for now we allow both to prevent lockouts during transition.
                $_SESSION['auser'] = $user;
                header("Location: dashboard.php");
                exit();
            } else {
                $error = '* Invalid username or password';
            }
        } else {
            $error = '* Invalid username or password';
        }
    } else {
        $error = "* Please fill in all fields!";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - ExpenseVoyage</title>
    <meta content="ExpenseVoyage Admin Panel" name="description">
    
    <!-- Favicon -->
    <link href="../img/favicon.ico" rel="icon">
    <link rel="apple-touch-icon" sizes="180x180" href="../img/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="../img/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="../img/favicon-16x16.png">
    <link rel="manifest" href="../img/site.webmanifest">
    
    <!-- Google Web Fonts -->
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Animate.css -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    
    <!-- Particles.js for animated background -->
    <script src="https://cdn.jsdelivr.net/particles.js/2.0.0/particles.min.js"></script>
    
    <!-- Customized Stylesheet -->
    <style>
        :root {
            --primary: #4361ee;
            --secondary: #3f37c9;
            --accent: #4cc9f0;
            --light: #f8f9fa;
            --dark: #212529;
            --gradient: linear-gradient(135deg, var(--primary), var(--secondary));
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            color: var(--dark);
            background-color: #f5f7ff;
            overflow-x: hidden;
        }
        
        /* Animated Background */
        #particles-js {
            position: fixed;
            width: 100%;
            height: 100%;
            top: 0;
            left: 0;
            z-index: -1;
            background: linear-gradient(135deg, #1a1c20, #2d3436);
        }
        
        /* Login Container */
        .login-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 30px 0;
        }
        
        .login-wrapper {
            width: 100%;
            max-width: 450px;
        }
        
        .login-box {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
            padding: 40px;
            position: relative;
            z-index: 10;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        
        .login-header {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .login-header h1 {
            font-size: 2.5rem;
            font-weight: 800;
            color: var(--primary);
            margin-bottom: 10px;
        }
        
        .login-header p {
            color: #6c757d;
            font-size: 1.1rem;
        }
        
        .form-group {
            margin-bottom: 25px;
        }
        
        .form-control {
            border: none;
            border-radius: 50px;
            padding: 15px 25px;
            background: #f8f9fa;
            font-weight: 500;
            height: auto;
            transition: all 0.3s ease;
        }
        
        .form-control:focus {
            box-shadow: none;
            background: white;
            border: 2px solid var(--primary);
        }
        
        .btn {
            padding: 15px 30px;
            font-weight: 600;
            border-radius: 50px;
            transition: all 0.3s ease;
            border: none;
            text-transform: uppercase;
            letter-spacing: 1px;
            width: 100%;
        }
        
        .btn-primary {
            background: var(--gradient);
            color: white;
            box-shadow: 0 4px 15px rgba(67, 97, 238, 0.3);
        }
        
        .btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 7px 20px rgba(67, 97, 238, 0.4);
            color: white;
        }
        
        .error-message {
            color: #dc3545;
            font-size: 0.9rem;
            margin-top: 10px;
            display: block;
        }
        
        .logo {
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 20px;
        }
        
        .logo i {
            font-size: 3rem;
            color: var(--primary);
            margin-right: 15px;
        }
        
        .logo-text {
            font-size: 2rem;
            font-weight: 800;
        }
        
        .logo-text span {
            color: var(--primary);
        }
        
        .back-to-site {
            text-align: center;
            margin-top: 30px;
        }
        
        .back-to-site a {
            color: var(--primary);
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        
        .back-to-site a:hover {
            color: var(--secondary);
        }
        
        .back-to-site a i {
            margin-right: 8px;
        }
        
        /* Responsive */
        @media (max-width: 576px) {
            .login-box {
                padding: 30px 20px;
            }
            
            .login-header h1 {
                font-size: 2rem;
            }
        }
    </style>
</head>
<body>
    <!-- Animated Background -->
    <div id="particles-js"></div>
    
    <div class="login-container">
        <div class="login-wrapper">
            <div class="login-box animate__animated animate__fadeInUp">
                <div class="login-header">
                    <div class="logo">
                        <i class="fas fa-globe-americas"></i>
                        <div class="logo-text">Expense<span>Voyage</span></div>
                    </div>
                    <h1>Admin Login</h1>
                    <p>Access to your dashboard</p>
                </div>
                
                <form method="post">
                    <div class="form-group">
                        <div class="input-group">
                            <span class="input-group-text bg-transparent border-0">
                                <i class="fas fa-user text-primary"></i>
                            </span>
                            <input class="form-control" name="user" type="text" placeholder="Username" required>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <div class="input-group">
                            <span class="input-group-text bg-transparent border-0">
                                <i class="fas fa-lock text-primary"></i>
                            </span>
                            <input class="form-control" type="password" name="pass" placeholder="Password" required>
                        </div>
                    </div>
                    
                    <?php if(!empty($error)): ?>
                        <span class="error-message"><?php echo $error; ?></span>
                    <?php endif; ?>
                    
                    <div class="form-group">
                        <button class="btn btn-primary" name="login" type="submit">Login</button>
                    </div>
                </form>
                
                <div class="back-to-site">
                    <a href="../index.php"><i class="fas fa-arrow-left"></i> Back to Main Site</a>
                </div>
            </div>
        </div>
    </div>
    
    <!-- JavaScript Libraries -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Template Javascript -->
    <script>
        // Initialize particles.js for animated background
        particlesJS('particles-js', {
            particles: {
                number: {
                    value: 80,
                    density: {
                        enable: true,
                        value_area: 800
                    }
                },
                color: {
                    value: '#4cc9f0'
                },
                shape: {
                    type: 'circle'
                },
                opacity: {
                    value: 0.5,
                    random: true
                },
                size: {
                    value: 3,
                    random: true
                },
                line_linked: {
                    enable: true,
                    distance: 150,
                    color: '#4361ee',
                    opacity: 0.4,
                    width: 1
                },
                move: {
                    enable: true,
                    speed: 2,
                    direction: 'none',
                    random: true,
                    straight: false,
                    out_mode: 'out',
                    bounce: false
                }
            },
            interactivity: {
                detect_on: 'canvas',
                events: {
                    onhover: {
                        enable: true,
                        mode: 'grab'
                    },
                    onclick: {
                        enable: true,
                        mode: 'push'
                    },
                    resize: true
                },
                modes: {
                    grab: {
                        distance: 140,
                        line_linked: {
                            opacity: 1
                        }
                    },
                    push: {
                        particles_nb: 4
                    }
                }
            },
            retina_detect: true
        });
        
        // Animation on page load
        $(window).on('load', function() {
            $('.animate__animated').css('opacity', '0');
            
            $(window).scroll(function() {
                var windowBottom = $(this).scrollTop() + $(this).innerHeight();
                
                $('.animate__animated').each(function() {
                    var objectBottom = $(this).offset().top + $(this).outerHeight() / 2;
                    
                    if (objectBottom < windowBottom) {
                        $(this).animate({'opacity': '1'}, 500);
                    }
                });
            }).scroll();
        });
    </script>
</body>
</html>