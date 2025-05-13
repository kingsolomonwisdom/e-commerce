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
$success = "";
$formData = [
    'first_name' => '',
    'last_name' => '',
    'username' => '',
    'email' => '',
    'phone' => '',
    'address' => ''
];

// Process registration form
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $formData = [
        'first_name' => trim($_POST['first_name'] ?? ''),
        'last_name' => trim($_POST['last_name'] ?? ''),
        'username' => trim($_POST['username'] ?? ''),
        'email' => trim($_POST['email'] ?? ''),
        'phone' => trim($_POST['phone'] ?? ''),
        'address' => trim($_POST['address'] ?? ''),
    ];
    
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $terms = isset($_POST['terms']) ? 1 : 0;
    
    // Validate form data
    if (empty($formData['first_name']) || empty($formData['last_name']) || 
        empty($formData['username']) || empty($formData['email']) || 
        empty($password) || empty($confirm_password)) {
        $error = "Please fill out all required fields.";
    } elseif ($password !== $confirm_password) {
        $error = "Passwords do not match.";
    } elseif (strlen($password) < 8) {
        $error = "Password must be at least 8 characters long.";
    } elseif (!filter_var($formData['email'], FILTER_VALIDATE_EMAIL)) {
        $error = "Please enter a valid email address.";
    } elseif (!$terms) {
        $error = "You must agree to the Terms and Privacy Policy.";
    } else {
        // Check if username exists
        $sql = "SELECT id FROM users WHERE username = ?";
        $existingUser = fetchRow($sql, [$formData['username']]);
        
        if ($existingUser) {
            $error = "Username already exists. Please choose another one.";
        } else {
            // Check if email exists
            $sql = "SELECT id FROM users WHERE email = ?";
            $existingEmail = fetchRow($sql, [$formData['email']]);
            
            if ($existingEmail) {
                $error = "Email already registered. Please use another email or login.";
            } else {
                // Hash password
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                
                // Insert user into database
                $sql = "INSERT INTO users (first_name, last_name, username, email, phone, address, password, is_admin, created_at) 
                        VALUES (?, ?, ?, ?, ?, ?, ?, 0, NOW())";
                
                $params = [
                    $formData['first_name'],
                    $formData['last_name'],
                    $formData['username'],
                    $formData['email'],
                    $formData['phone'],
                    $formData['address'],
                    $hashed_password
                ];
                
                $affected = modifyData($sql, $params);
                
                if ($affected) {
                    $success = "Registration successful! You can now log in.";
                    $formData = [
                        'first_name' => '',
                        'last_name' => '',
                        'username' => '',
                        'email' => '',
                        'phone' => '',
                        'address' => ''
                    ];
                } else {
                    $error = "Registration failed. Please try again.";
                }
            }
        }
    }
}

// Set page title
$pageTitle = "Register - Shopway";
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
    <div class="auth-container" style="width: 500px;">
        <h1>Create Account</h1>
        
        <?php if (!empty($error)): ?>
        <div class="message error"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <?php if (!empty($success)): ?>
        <div class="message success"><?php echo $success; ?></div>
        <?php endif; ?>
        
        <form action="register.php" method="POST">
            <div class="form-row">
                <div class="inputbox">
                    <input type="text" name="first_name" placeholder="First Name" value="<?php echo htmlspecialchars($formData['first_name']); ?>" required>
                    <i class="fas fa-user"></i>
                </div>
                
                <div class="inputbox">
                    <input type="text" name="last_name" placeholder="Last Name" value="<?php echo htmlspecialchars($formData['last_name']); ?>" required>
                    <i class="fas fa-user"></i>
                </div>
            </div>
            
            <div class="inputbox">
                <input type="text" name="username" placeholder="Username" value="<?php echo htmlspecialchars($formData['username']); ?>" required>
                <i class="fas fa-user-circle"></i>
            </div>
            
            <div class="inputbox">
                <input type="email" name="email" placeholder="Email Address" value="<?php echo htmlspecialchars($formData['email']); ?>" required>
                <i class="fas fa-envelope"></i>
            </div>
            
            <div class="inputbox">
                <input type="tel" name="phone" placeholder="Phone Number" value="<?php echo htmlspecialchars($formData['phone']); ?>">
                <i class="fas fa-phone"></i>
            </div>
            
            <div class="inputbox">
                <input type="password" name="password" placeholder="Password" required>
                <i class="fas fa-lock"></i>
            </div>
            
            <div class="inputbox">
                <input type="password" name="confirm_password" placeholder="Confirm Password" required>
                <i class="fas fa-lock"></i>
            </div>
            
            <div class="inputbox">
                <input type="text" name="address" placeholder="Address" value="<?php echo htmlspecialchars($formData['address']); ?>">
                <i class="fas fa-map-marker-alt"></i>
            </div>
            
            <div class="form-check">
                <input type="checkbox" name="terms" id="terms">
                <label for="terms">I agree to the Terms and Privacy Policy</label>
            </div>
            
            <button type="submit" class="auth-btn">Sign Up</button>
            
            <div class="login-link">
                <p>Already have an account? <a href="login.php">Login</a></p>
            </div>
        </form>
    </div>
    
    <script src="<?php echo BASE_URL; ?>/assets/js/main.js"></script>
</body>
</html> 