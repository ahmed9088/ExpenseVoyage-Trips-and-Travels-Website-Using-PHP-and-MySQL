<?php
session_start();
include 'db.php';
require '../vendor/autoload.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['send_reset_link'])) {
    $email = $_POST['email'];
    
    // Check if the email exists in the database
    $stmt = $con->prepare("SELECT * FROM user WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();
    
    if ($user) {
        // Generate a unique token
        $token = bin2hex(random_bytes(32));
        
        // Set token expiry time (1 hour from now)
        $expiry = date('Y-m-d H:i:s', time() + 3600);
        
        // Store the token and expiry in the database
        $stmt = $con->prepare("UPDATE user SET reset_token = ?, token_expiry = ? WHERE email = ?");
        if ($stmt->execute([$token, $expiry, $email])) {
            // Send reset email
            $reset_link = "http://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . "/reset_password.php?token=$token";
            
            // Use PHPMailer to send the reset email
            $mail = new PHPMailer(true);
            try {
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com';
                $mail->SMTPAuth = true;
                $mail->Username = 'memon1ahmed@gmail.com';
                $mail->Password = 'hvol hgpf fdra wrkb';
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port = 587;
                
                $mail->setFrom('memon1ahmed@gmail.com', 'ExpenseVoyage');
                $mail->addAddress($email);
                $mail->isHTML(true);
                $mail->Subject = 'Password Reset Request';
                
                // Design the email body as an HTML card
                $mail->Body = '
                    <div style="max-width: 600px; margin: auto; font-family: Arial, sans-serif; padding: 20px; background-color: #f4f4f4; border-radius: 10px; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);">
                        <div style="background-color: #4361ee; padding: 20px; text-align: center; border-radius: 10px 10px 0 0;">
                            <h2 style="color: #fff; margin: 0;">Password Reset Request</h2>
                        </div>
                        <div style="padding: 20px; background-color: #fff; border-radius: 0 0 10px 10px;">
                            <p style="font-size: 16px; color: #333;">We received a request to reset your password. Click the button below to create a new password:</p>
                            <div style="text-align: center; margin: 20px 0;">
                                <a href="' . $reset_link . '" style="display: inline-block; padding: 12px 24px; background-color: #4361ee; color: #fff; text-decoration: none; border-radius: 5px; font-weight: bold;">Reset Password</a>
                            </div>
                            <p style="color: #666; font-size: 14px;">If you did not request this, please ignore this email. This link will expire in 1 hour.</p>
                        </div>
                        <div style="text-align: center; padding: 10px; color: #999; font-size: 12px;">
                            <p>&copy; ' . date('Y') . ' ExpenseVoyage. All rights reserved.</p>
                        </div>
                    </div>
                ';
                
                $mail->AltBody = "Please click the following link to reset your password: $reset_link";
                
                if($mail->send()) {
                    $message = "A password reset link has been sent to your email address.";
                } else {
                    $message = "Failed to send reset email. Please try again later.";
                }
            } catch (Exception $e) {
                $message = "Failed to send reset email. Please try again later.";
                error_log("Mailer Error: " . $mail->ErrorInfo);
            }
        } else {
            $message = "Failed to generate reset link. Please try again.";
        }
    } else {
        $message = "No account found with that email address.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password - ExpenseVoyage</title>
    <meta name="description" content="Reset your ExpenseVoyage account password">
    
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
    
    <!-- Animate.css -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    
    <!-- Particles.js for animated background -->
    <script src="https://cdn.jsdelivr.net/particles.js/2.0.0/particles.min.js"></script>
    
    <style>
        :root {
            --primary: #4361ee;
            --secondary: #3f37c9;
            --accent: #4cc9f0;
            --light: #f8f9fa;
            --dark: #212529;
            --gradient: linear-gradient(135deg, var(--primary), var(--secondary));
            --gradient-light: linear-gradient(120deg, rgba(67, 97, 238, 0.1), rgba(76, 201, 240, 0.1));
            --success: #10b981;
            --error: #ef4444;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f5f7ff;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            overflow-x: hidden;
            position: relative;
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
        
        /* Scrollbar Styling */
        ::-webkit-scrollbar {
            width: 10px;
        }
        
        ::-webkit-scrollbar-track {
            background: #f1f1f1;
        }
        
        ::-webkit-scrollbar-thumb {
            background: var(--primary);
            border-radius: 5px;
        }
        
        ::-webkit-scrollbar-thumb:hover {
            background: var(--secondary);
        }
        
        /* Container */
        .container {
            width: 100%;
            max-width: 450px;
            margin: 0 auto;
            padding: 20px;
            position: relative;
            z-index: 1;
        }
        
        /* Forgot Password Card */
        .forgot-card {
            background: white;
            border-radius: 16px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            animation: fadeInUp 0.6s ease forwards;
        }
        
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .forgot-header {
            background: var(--gradient);
            color: white;
            padding: 30px;
            text-align: center;
        }
        
        .forgot-header h2 {
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 10px;
        }
        
        .forgot-header p {
            opacity: 0.9;
            font-size: 16px;
        }
        
        .forgot-body {
            padding: 40px 30px;
        }
        
        .form-group {
            margin-bottom: 24px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-size: 14px;
            font-weight: 500;
            color: #374151;
        }
        
        .form-control {
            width: 100%;
            padding: 14px 16px;
            font-size: 15px;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            transition: all 0.3s ease;
            outline: none;
        }
        
        .form-control:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(67, 97, 238, 0.1);
        }
        
        .input-icon {
            position: relative;
        }
        
        .input-icon .form-control {
            padding-left: 42px;
        }
        
        .input-icon i {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: #9ca3af;
            font-size: 16px;
        }
        
        .input-icon .form-control:focus + i {
            color: var(--primary);
        }
        
        /* Buttons */
        .btn {
            display: inline-block;
            padding: 14px 24px;
            font-size: 15px;
            font-weight: 600;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
            border: none;
            outline: none;
            text-align: center;
        }
        
        .btn-primary {
            background: var(--gradient);
            color: white;
            width: 100%;
            margin-bottom: 20px;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(67, 97, 238, 0.2);
        }
        
        .btn-outline {
            background: transparent;
            border: 1px solid var(--primary);
            color: var(--primary);
            width: 48%;
        }
        
        .btn-outline:hover {
            background: var(--primary);
            color: white;
        }
        
        /* Message */
        .message {
            padding: 12px 16px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 14px;
            text-align: center;
        }
        
        .message.success {
            background-color: rgba(16, 185, 129, 0.1);
            color: var(--success);
            border: 1px solid rgba(16, 185, 129, 0.3);
        }
        
        .message.error {
            background-color: rgba(239, 68, 68, 0.1);
            color: var(--error);
            border: 1px solid rgba(239, 68, 68, 0.3);
        }
        
        /* Back to Account */
        .back-to-account {
            display: flex;
            justify-content: space-between;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
        }
        
        /* Responsive */
        @media (max-width: 480px) {
            .container {
                padding: 15px;
            }
            
            .forgot-body {
                padding: 30px 20px;
            }
            
            .back-to-account {
                flex-direction: column;
                gap: 10px;
            }
            
            .btn-outline {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <!-- Animated Background -->
    <div id="particles-js"></div>
    
    <div class="container">
        <div class="forgot-card">
            <div class="forgot-header">
                <h2>Forgot Password</h2>
                <p>Enter your email to reset your password</p>
            </div>
            
            <div class="forgot-body">
                <?php if (!empty($message)): ?>
                    <div class="message <?php echo strpos($message, 'sent') !== false ? 'success' : 'error'; ?>">
                        <?php echo htmlspecialchars($message); ?>
                    </div>
                <?php endif; ?>
                
                <form action="forget_password.php" method="POST">
                    <div class="form-group input-icon">
                        <i class="fas fa-envelope"></i>
                        <input type="email" name="email" class="form-control" placeholder="Email Address" required>
                    </div>
                    
                    <button type="submit" name="send_reset_link" class="btn btn-primary">Send Reset Link</button>
                </form>
                
                <div class="back-to-account">
                    <a href="account.php" class="btn btn-outline">Back to Login</a>
                    <a href="account.php" class="btn btn-outline">Create Account</a>
                </div>
            </div>
        </div>
    </div>
    
    <!-- JavaScript Libraries -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
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
    </script>
</body>
</html>