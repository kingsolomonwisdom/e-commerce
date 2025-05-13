<?php
session_start();
require_once 'db.php';

// Redirect if already logged in
if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

$error = "";
$username = "";

// Process login form
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    
    // Validate form data
    if (empty($username) || empty($password)) {
        $error = "Please enter both username and password.";
    } else {
        // Fetch user from database
        $sql = "SELECT id, username, password, first_name, is_admin FROM users WHERE username = ?";
        $user = fetchRow($sql, [$username]);
        
        if ($user && password_verify($password, $user['password'])) {
            // Set session variables
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['first_name'] = $user['first_name'];
            $_SESSION['is_admin'] = $user['is_admin'];
            
            // Initialize empty cart if needed
            if (!isset($_SESSION['cart'])) {
                $_SESSION['cart'] = [];
            }
            
            // Redirect to appropriate page
            if ($user['is_admin']) {
                header("Location: admin/index.php");
            } else {
                header("Location: index.php");
            }
            exit;
        } else {
            $error = "Invalid username or password.";
        }
    }
}

$pageTitle = "Login - Shopway";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Basic Reset */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: "Poppins", sans-serif;
        }
        
        /* Body styling */
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background-color: #f8f9fa;
            background-image: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
        }
        
        /* Login container */
        .auth-container {
            width: 420px;
            background: white;
            color: #333;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1), 0 5px 15px rgba(0, 0, 0, 0.07);
            transition: transform 0.3s ease;
        }
        
        .auth-container:hover {
            transform: translateY(-5px);
        }
        
        /* Login header */
        .auth-header {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .auth-header h1 {
            font-size: 28px;
            color: #333;
            margin-bottom: 10px;
        }
        
        .auth-header .brand {
            font-size: 32px;
            font-weight: 700;
            color: #007bff;
            margin-bottom: 5px;
        }
        
        .auth-header p {
            color: #6c757d;
            font-size: 14px;
        }
        
        /* Error message */
        .message.error {
            background-color: rgba(244, 67, 54, 0.1);
            color: #e53935;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 25px;
            text-align: center;
            border-left: 4px solid #e53935;
            font-size: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .message.error i {
            margin-right: 10px;
            font-size: 16px;
        }
        
        /* Input box styling */
        .inputbox {
            position: relative;
            width: 100%;
            height: 55px;
            margin: 20px 0;
        }
        
        .inputbox input {
            height: 100%;
            width: 100%;
            background: #f8f9fa;
            border: 2px solid #e9ecef;
            outline: none;
            border-radius: 10px;
            font-size: 16px;
            color: #495057;
            padding: 18px 45px 18px 20px;
            transition: all 0.3s ease;
        }
        
        .inputbox input:focus {
            border-color: #007bff;
            background: white;
            box-shadow: 0 0 0 3px rgba(0, 123, 255, 0.1);
        }
        
        .inputbox input::placeholder {
            color: #adb5bd;
        }
        
        .inputbox i {
            position: absolute;
            right: 20px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 20px;
            color: #6c757d;
            transition: color 0.3s ease;
        }
        
        .inputbox input:focus + i {
            color: #007bff;
        }
        
        /* Remember/forget section */
        .remember-forget {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
            font-size: 14px;
        }
        
        .remember-forget label {
            display: flex;
            align-items: center;
            color: #495057;
            cursor: pointer;
        }
        
        .remember-forget input[type="checkbox"] {
            margin-right: 8px;
            transform: scale(1.2);
            cursor: pointer;
        }
        
        .remember-forget a {
            color: #007bff;
            text-decoration: none;
            transition: color 0.3s ease;
        }
        
        .remember-forget a:hover {
            color: #0056b3;
            text-decoration: underline;
        }
        
        /* Login button */
        .auth-btn {
            width: 100%;
            height: 55px;
            background: #007bff;
            border: none;
            outline: none;
            border-radius: 10px;
            cursor: pointer;
            font-size: 16px;
            color: white;
            font-weight: 600;
            transition: all 0.3s ease;
            box-shadow: 0 4px 6px rgba(0, 123, 255, 0.11), 0 1px 3px rgba(0, 123, 255, 0.08);
        }
        
        .auth-btn:hover {
            background: #0069d9;
            transform: translateY(-2px);
            box-shadow: 0 7px 14px rgba(0, 123, 255, 0.1), 0 3px 6px rgba(0, 123, 255, 0.07);
        }
        
        .auth-btn:active {
            transform: translateY(1px);
            box-shadow: 0 2px 4px rgba(0, 123, 255, 0.1);
        }
        
        /* Register link */
        .register-link {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #f0f0f0;
            font-size: 15px;
            color: #6c757d;
        }
        
        .register-link a {
            color: #007bff;
            text-decoration: none;
            font-weight: 600;
            transition: color 0.3s ease;
        }
        
        .register-link a:hover {
            color: #0056b3;
            text-decoration: underline;
        }
        
        /* Return to store link */
        .return-link {
            position: fixed;
            top: 30px;
            left: 30px;
            background-color: white;
            color: #333;
            padding: 10px 20px;
            border-radius: 50px;
            text-decoration: none;
            font-weight: 500;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.1);
            display: flex;
            align-items: center;
            transition: all 0.3s ease;
        }
        
        .return-link i {
            margin-right: 8px;
        }
        
        .return-link:hover {
            background-color: #f8f9fa;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.15);
        }
        
        /* Responsive styles */
        @media (max-width: 480px) {
            .auth-container {
                width: 90%;
                padding: 30px 20px;
            }
            
            .return-link {
                top: 20px;
                left: 20px;
                padding: 8px 15px;
                font-size: 14px;
            }
        }
    </style>
</head>
<body>
    <a href="index.php" class="return-link">
        <i class="fas fa-arrow-left"></i> Back to Shop
    </a>
    
    <div class="auth-container">
        <div class="auth-header">
            <div class="brand">Shopway</div>
            <h1>Welcome Back</h1>
            <p>Please enter your credentials to access your account</p>
        </div>
        
        <?php if (!empty($error)): ?>
        <div class="message error">
            <i class="fas fa-exclamation-circle"></i>
            <?php echo $error; ?>
        </div>
        <?php endif; ?>
        
        <form action="login.php" method="POST">
            <div class="inputbox">
                <input type="text" name="username" placeholder="Username or Email" value="<?php echo htmlspecialchars($username); ?>" required>
                <i class="fas fa-user"></i>
            </div>
            
            <div class="inputbox">
                <input type="password" name="password" id="password" placeholder="Password" required>
                <i class="fas fa-lock" id="password-toggle"></i>
            </div>
            
            <div class="remember-forget">
                <label>
                    <input type="checkbox" name="remember"> Remember me
                </label>
                <a href="forgot-password.php">Forgot password?</a>
            </div>
            
            <button type="submit" class="auth-btn">Login</button>
            
            <div class="register-link">
                <p>Don't have an account? <a href="register.php">Register now</a></p>
            </div>
        </form>
    </div>
    
    <script>
        // Toggle password visibility
        document.addEventListener('DOMContentLoaded', function() {
            const passwordToggle = document.getElementById('password-toggle');
            const passwordInput = document.getElementById('password');
            
            passwordToggle.addEventListener('click', function() {
                const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordInput.setAttribute('type', type);
                
                // Toggle icon
                this.classList.toggle('fa-lock');
                this.classList.toggle('fa-eye');
            });
            
            // Input focus styling
            const inputs = document.querySelectorAll('.inputbox input');
            inputs.forEach(input => {
                input.addEventListener('focus', function() {
                    this.parentElement.classList.add('focus');
                });
                
                input.addEventListener('blur', function() {
                    this.parentElement.classList.remove('focus');
                });
            });
        });
    </script>
</body>
</html> 