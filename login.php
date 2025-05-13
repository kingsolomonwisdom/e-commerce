<?php
session_start();
require_once 'includes/config.php';
require_once 'includes/db.php';

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

// Set page title
$pageTitle = "Login - Shopway";
$extraCSS = ['auth'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?></title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/style.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/auth.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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
    
    <script src="<?php echo BASE_URL; ?>/assets/js/main.js"></script>
</body>
</html> 