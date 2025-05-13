<?php
session_start();
require_once 'db.php';

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

$pageTitle = "Register - Shopway";
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
            padding: 20px;
        }
        
        /* Registration container */
        .auth-container {
            width: 100%;
            max-width: 500px;
            background: white;
            color: #333;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }
        
        /* Header */
        .auth-container h1 {
            font-size: 28px;
            text-align: center;
            margin-bottom: 20px;
            color: #333;
        }
        
        /* Messages */
        .message {
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 20px;
            text-align: center;
        }
        
        .message.error {
            background-color: #ffe6e6;
            color: #d33;
        }
        
        .message.success {
            background-color: #e6ffe6;
            color: #3c763d;
        }
        
        /* Form row for side-by-side inputs */
        .form-row {
            display: flex;
            gap: 15px;
        }
        
        @media (max-width: 576px) {
            .form-row {
                flex-direction: column;
                gap: 0;
            }
        }
        
        /* Input box styling */
        .inputbox {
            position: relative;
            width: 100%;
            height: 50px;
            margin: 15px 0;
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
            padding: 0 45px 0 20px;
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
            right: 20px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 20px;
            color: #999;
        }
        
        /* Checkbox styling */
        .form-check {
            display: flex;
            align-items: center;
            margin: 15px 0;
        }
        
        .form-check input {
            margin-right: 10px;
            transform: scale(1.2);
        }
        
        /* Button styling */
        .auth-btn {
            width: 100%;
            height: 50px;
            background: #007bff;
            border: none;
            outline: none;
            border-radius: 40px;
            cursor: pointer;
            font-size: 16px;
            color: white;
            font-weight: 600;
            transition: background-color 0.3s;
            margin-top: 10px;
        }
        
        .auth-btn:hover {
            background: #0056b3;
        }
        
        /* Login link */
        .login-link {
            text-align: center;
            margin-top: 20px;
            font-size: 14px;
        }
        
        .login-link a {
            color: #007bff;
            text-decoration: none;
            font-weight: 600;
        }
        
        .login-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="auth-container">
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
    
    <script>
        // Simple form validation
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.querySelector('form');
            const inputs = form.querySelectorAll('input[required]');
            const terms = document.getElementById('terms');
            
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
                
                if (!terms.checked) {
                    isValid = false;
                    terms.style.outline = '2px solid #d33';
                } else {
                    terms.style.outline = 'none';
                }
                
                if (!isValid) {
                    e.preventDefault();
                }
            });
        });
    </script>
</body>
</html>