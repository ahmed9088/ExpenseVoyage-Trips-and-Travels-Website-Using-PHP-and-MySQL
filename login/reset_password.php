<?php
session_start();
include 'db.php';
$message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['reset_password'])) {
    $token = $_POST['token'];
    $new_password = $_POST['password'];
    
    // Check if the token is valid and not expired
    $stmt = $con->prepare("SELECT * FROM user WHERE reset_token = ? AND token_expiry > NOW()");
    $stmt->execute([$token]);
    $user = $stmt->fetch();
    
    if ($user) {
        // Hash the new password
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        
        // Update the password and clear the reset token
        $stmt = $con->prepare("UPDATE user SET password = ?, reset_token = NULL, token_expiry = NULL WHERE reset_token = ?");
        if ($stmt->execute([$hashed_password, $token])) {
            $message = "Password has been reset successfully.";
            
            // Redirect to account.php after a short delay
            header("refresh:3;url=account.php");
            exit;
        } else {
            $message = "Failed to reset password.";
        }
    } else {
        $message = "Invalid or expired token.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - ExpenseVoyage</title>
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
        
        /* Reset Password Card */
        .reset-card {
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
        
        .reset-header {
            background: var(--gradient);
            color: white;
            padding: 30px;
            text-align: center;
        }
        
        .reset-header h2 {
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 10px;
        }
        
        .reset-header p {
            opacity: 0.9;
            font-size: 16px;
        }
        
        .reset-body {
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
        
        /* Password Strength Indicator */
        .password-strength {
            height: 5px;
            background-color: #e5e7eb;
            border-radius: 3px;
            margin-top: 8px;
            overflow: hidden;
        }
        
        .password-strength-meter {
            height: 100%;
            width: 0;
            border-radius: 3px;
            transition: all 0.3s ease;
        }
        
        .password-strength-meter.weak {
            width: 33.33%;
            background-color: #ef4444;
        }
        
        .password-strength-meter.medium {
            width: 66.66%;
            background-color: #f59e0b;
        }
        
        .password-strength-meter.strong {
            width: 100%;
            background-color: #10b981;
        }
        
        .password-strength-text {
            font-size: 12px;
            margin-top: 5px;
            color: #6b7280;
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
            
            .reset-body {
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
        <div class="reset-card">
            <div class="reset-header">
                <h2>Reset Password</h2>
                <p>Enter your new password below</p>
            </div>
            
            <div class="reset-body">
                <?php if (!empty($message)): ?>
                    <div class="message <?php echo strpos($message, 'success') !== false ? 'success' : 'error'; ?>">
                        <?php echo htmlspecialchars($message); ?>
                    </div>
                <?php endif; ?>
                
                <form action="reset_password.php" method="POST">
                    <input type="hidden" name="token" value="<?php echo isset($_GET['token']) ? htmlspecialchars($_GET['token']) : ''; ?>">
                    
                    <div class="form-group input-icon">
                        <i class="fas fa-lock"></i>
                        <input type="password" name="password" id="password" class="form-control" placeholder="New Password" required>
                        <div class="password-strength">
                            <div class="password-strength-meter" id="strength-meter"></div>
                        </div>
                        <div class="password-strength-text" id="strength-text"></div>
                    </div>
                    
                    <div class="form-group input-icon">
                        <i class="fas fa-lock"></i>
                        <input type="password" id="confirm_password" class="form-control" placeholder="Confirm New Password" required>
                    </div>
                    
                    <button type="submit" name="reset_password" class="btn btn-primary">Reset Password</button>
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
        
        // Password strength checker
        document.addEventListener('DOMContentLoaded', function() {
            const password = document.getElementById('password');
            const confirm_password = document.getElementById('confirm_password');
            const strength_meter = document.getElementById('strength-meter');
            const strength_text = document.getElementById('strength-text');
            
            password.addEventListener('input', function() {
                const pwd = this.value;
                let strength = 0;
                
                if (pwd.length >= 8) {
                    strength += 1;
                }
                
                if (pwd.match(/[a-z]+/)) {
                    strength += 1;
                }
                
                if (pwd.match(/[A-Z]+/)) {
                    strength += 1;
                }
                
                if (pwd.match(/[0-9]+/)) {
                    strength += 1;
                }
                
                if (pwd.match(/[$@#&!]+/)) {
                    strength += 1;
                }
                
                // Update strength meter
                strength_meter.className = 'password-strength-meter';
                
                if (strength <= 2) {
                    strength_meter.classList.add('weak');
                    strength_text.textContent = 'Weak password';
                    strength_text.style.color = '#ef4444';
                } else if (strength <= 3) {
                    strength_meter.classList.add('medium');
                    strength_text.textContent = 'Medium password';
                    strength_text.style.color = '#f59e0b';
                } else {
                    strength_meter.classList.add('strong');
                    strength_text.textContent = 'Strong password';
                    strength_text.style.color = '#10b981';
                }
            });
            
            // Form validation
            document.querySelector('form').addEventListener('submit', function(e) {
                if (password.value !== confirm_password.value) {
                    e.preventDefault();
                    
                    // Create error message
                    const errorMsg = document.createElement('div');
                    errorMsg.className = 'message error';
                    errorMsg.textContent = 'Passwords do not match!';
                    
                    // Insert error message before the form
                    this.parentNode.insertBefore(errorMsg, this);
                    
                    // Remove error message after 5 seconds
                    setTimeout(() => {
                        errorMsg.remove();
                    }, 5000);
                }
            });
        });
    </script>
</body>
</html>