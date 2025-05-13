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
            background-color: #f9f9f9;
            background-size: cover;
            background-position: center;
        }
        
        /* Login container */
        .auth-container {
            width: 420px;
            background: white;
            color: #333;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }
        
        /* Login header */
        .auth-container h1 {
            font-size: 28px;
            text-align: center;
            margin-bottom: 20px;
            color: #333;
        }
        
        /* Error message */
        .message.error {
            background-color: #ffe6e6;
            color: #d33;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 20px;
            text-align: center;
        }
        
        /* Input box styling */
        .inputbox {
            position: relative;
            width: 100%;
            height: 50px;
            margin: 20px 0;
        }
        
        .inputbox input {
            height: 100%;
            width: 100%;
            background: transparent;
            border: 1px solid #ccc;
            outline: none;
            border-radius: 40px;
            font-size: 16px;
            color: #333;
            padding: 20px 45px 20px 20px;
            transition: border-color 0.3s;
        }
        
        .inputbox input:focus {
            border-color: #007bff;
        }
        
        .inputbox input::placeholder {
            color: #999;
        }
        
        .inputbox i {
            position: absolute;
            right: 25px;
            top: 15px;
            font-size: 20px;
            color: #999;
        }
        
        /* Remember/forget section */
        .remember-forget {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
            font-size: 14px;
        }
        
        .remember-forget label {
            display: flex;
            align-items: center;
        }
        
        .remember-forget input[type="checkbox"] {
            margin-right: 5px;
            transform: scale(1.2);
        }
        
        .remember-forget a {
            color: #007bff;
            text-decoration: none;
        }
        
        .remember-forget a:hover {
            text-decoration: underline;
        }
        
        /* Login button */
        .auth-btn {
            width: 100%;
            height: 45px;
            background: #007bff;
            border: none;
            outline: none;
            border-radius: 40px;
            cursor: pointer;
            font-size: 16px;
            color: white;
            font-weight: 600;
            transition: background-color 0.3s;
        }
        
        .auth-btn:hover {
            background: #0056b3;
        }
        
        /* Register link */
        .register-link {
            text-align: center;
            margin-top: 20px;
            font-size: 14px;
        }
        
        .register-link a {
            color: #007bff;
            text-decoration: none;
            font-weight: 600;
        }
        
        .register-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="auth-container">
        <h1>Login to Shopway</h1>
        
        <?php if (!empty($error)): ?>
        <div class="message error"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <form action="login.php" method="POST">
            <div class="inputbox">
                <input type="text" name="username" placeholder="Username" value="<?php echo htmlspecialchars($username); ?>" required>
                <i class="fas fa-user"></i>
            </div>
            
            <div class="inputbox">
                <input type="password" name="password" placeholder="Password" required>
                <i class="fas fa-lock"></i>
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
        // Simple form validation
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.querySelector('form');
            const inputs = form.querySelectorAll('input[required]');
            
            form.addEventListener('submit', function(e) {
                let isValid = true;
                
                inputs.forEach(input => {
                    if (!input.value.trim()) {
                        isValid = false;
                        input.style.borderColor = '#d33';
                    } else {
                        input.style.borderColor = '#ccc';
                    }
                });
                
                if (!isValid) {
                    e.preventDefault();
                }
            });
        });
    </script>
</body>
</html> 