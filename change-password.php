<?php
// Page configuration
$pageTitle = "Change Password - Shopway";
$extraCSS = ['account'];
$extraJS = ['account'];

// Include header
require_once 'db.php';
require_once 'includes/header.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$success = '';
$error = '';

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $currentPassword = isset($_POST['current_password']) ? trim($_POST['current_password']) : '';
    $newPassword = isset($_POST['new_password']) ? trim($_POST['new_password']) : '';
    $confirmPassword = isset($_POST['confirm_password']) ? trim($_POST['confirm_password']) : '';
    
    // Validate form data
    if (empty($currentPassword) || empty($newPassword) || empty($confirmPassword)) {
        $error = "All fields are required.";
    } elseif ($newPassword !== $confirmPassword) {
        $error = "New passwords do not match.";
    } elseif (strlen($newPassword) < 8) {
        $error = "New password must be at least 8 characters long.";
    } else {
        // Get user's current password from database
        $sql = "SELECT password FROM users WHERE id = ?";
        $user = fetchRow($sql, [$_SESSION['user_id']]);
        
        if (!$user) {
            $error = "User not found.";
        } elseif (!password_verify($currentPassword, $user['password'])) {
            $error = "Current password is incorrect.";
        } else {
            // Hash the new password
            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
            
            // Update password in database
            $sql = "UPDATE users SET password = ?, updated_at = NOW() WHERE id = ?";
            $params = [$hashedPassword, $_SESSION['user_id']];
            
            try {
                $affected = modifyData($sql, $params);
                
                if ($affected) {
                    // Set success message for account page
                    $_SESSION['success_message'] = "Your password has been changed successfully.";
                    
                    // Show success on this page too
                    $success = "Your password has been changed successfully.";
                    
                    // Don't redirect immediately - allow success message to be seen
                    // header("Location: account.php");
                    // exit;
                } else {
                    $error = "No changes were made. Please try a different password.";
                }
            } catch (Exception $e) {
                $error = "Failed to update password: " . $e->getMessage();
            }
        }
    }
}
?>

<style>
    .change-password-container {
        padding: 30px 0;
    }
    
    .page-title {
        font-size: 2rem;
        margin-bottom: 30px;
        color: #333;
        border-bottom: 2px solid #f0f0f0;
        padding-bottom: 10px;
    }
    
    .form-container {
        background-color: #fff;
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
        padding: 30px;
        max-width: 600px;
        margin: 0 auto;
    }
    
    .form-title {
        font-size: 1.5rem;
        color: #333;
        margin-bottom: 20px;
        padding-bottom: 10px;
        border-bottom: 1px solid #f0f0f0;
    }
    
    .form-group {
        margin-bottom: 20px;
    }
    
    .form-label {
        display: block;
        margin-bottom: 8px;
        color: #555;
        font-weight: 500;
    }
    
    .form-control {
        width: 100%;
        padding: 12px 15px;
        border: 1px solid #ddd;
        border-radius: 5px;
        font-size: 15px;
        transition: border-color 0.3s ease;
    }
    
    .form-control:focus {
        border-color: #007bff;
        outline: none;
        box-shadow: 0 0 0 2px rgba(0, 123, 255, 0.25);
    }
    
    .form-text {
        color: #777;
        font-size: 12px;
        margin-top: 5px;
    }
    
    .form-buttons {
        display: flex;
        gap: 15px;
        margin-top: 30px;
    }
    
    .btn {
        padding: 12px 20px;
        border-radius: 5px;
        cursor: pointer;
        font-weight: 500;
        text-decoration: none;
        display: inline-block;
        text-align: center;
        border: none;
        transition: all 0.3s ease;
    }
    
    .btn-primary {
        background-color: #007bff;
        color: white;
    }
    
    .btn-primary:hover {
        background-color: #0056b3;
    }
    
    .btn-outline {
        background-color: transparent;
        color: #555;
        border: 1px solid #ddd;
    }
    
    .btn-outline:hover {
        background-color: #f8f9fa;
        color: #333;
    }
    
    .message {
        padding: 15px;
        border-radius: 5px;
        margin-bottom: 20px;
    }
    
    .message.error {
        background-color: rgba(231, 76, 60, 0.1);
        color: #c0392b;
        border-left: 4px solid #e74c3c;
    }
    
    .message.success {
        background-color: rgba(46, 204, 113, 0.1);
        color: #27ae60;
        border-left: 4px solid #2ecc71;
    }
    
    .password-strength {
        margin-top: 8px;
        height: 5px;
        border-radius: 5px;
        background-color: #eee;
        overflow: hidden;
    }
    
    .password-strength-meter {
        height: 100%;
        width: 0%;
        transition: width 0.3s ease, background-color 0.3s ease;
    }
    
    .weak {
        width: 33%;
        background-color: #e74c3c;
    }
    
    .medium {
        width: 66%;
        background-color: #f39c12;
    }
    
    .strong {
        width: 100%;
        background-color: #2ecc71;
    }
</style>

<div class="change-password-container">
    <h1 class="page-title">Change Password</h1>
    
    <div class="form-container">
        <h2 class="form-title">Update Your Password</h2>
        
        <?php if ($error): ?>
        <div class="message error"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <?php if ($success): ?>
        <div class="message success"><?php echo $success; ?></div>
        <?php endif; ?>
        
        <form action="change-password.php" method="POST">
            <div class="form-group">
                <label for="current_password" class="form-label">Current Password *</label>
                <input type="password" class="form-control" id="current_password" name="current_password" required>
            </div>
            
            <div class="form-group">
                <label for="new_password" class="form-label">New Password *</label>
                <input type="password" class="form-control" id="new_password" name="new_password" required>
                <div class="password-strength">
                    <div class="password-strength-meter" id="password-strength-meter"></div>
                </div>
                <div class="form-text">Password must be at least 8 characters long.</div>
            </div>
            
            <div class="form-group">
                <label for="confirm_password" class="form-label">Confirm New Password *</label>
                <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                <div class="form-text" id="password-match-message"></div>
            </div>
            
            <div class="form-buttons">
                <button type="submit" class="btn btn-primary">Change Password</button>
                <a href="account.php" class="btn btn-outline">Cancel</a>
            </div>
        </form>
    </div>
</div>

<script>
    // Function to check password strength
    function checkPasswordStrength(password) {
        const meter = document.getElementById('password-strength-meter');
        const meterText = document.querySelector('#new_password + .form-text');
        
        // Remove classes
        meter.classList.remove('weak', 'medium', 'strong');
        
        if (password.length === 0) {
            meter.style.width = '0%';
            meterText.textContent = 'Password must be at least 8 characters long.';
            return;
        }
        
        // Check strength
        let strength = 0;
        
        // Length check
        if (password.length >= 8) strength += 1;
        
        // Contains number
        if (/\d/.test(password)) strength += 1;
        
        // Contains special character
        if (/[!@#$%^&*(),.?":{}|<>]/.test(password)) strength += 1;
        
        // Contains uppercase and lowercase
        if (/[a-z]/.test(password) && /[A-Z]/.test(password)) strength += 1;
        
        // Update meter
        if (strength <= 1) {
            meter.classList.add('weak');
            meterText.textContent = 'Weak password. Try adding numbers and special characters.';
        } else if (strength <= 3) {
            meter.classList.add('medium');
            meterText.textContent = 'Medium strength password.';
        } else {
            meter.classList.add('strong');
            meterText.textContent = 'Strong password!';
        }
    }
    
    // Function to check if passwords match
    function checkPasswordMatch() {
        const newPassword = document.getElementById('new_password').value;
        const confirmPassword = document.getElementById('confirm_password').value;
        const matchMessage = document.getElementById('password-match-message');
        
        if (confirmPassword.length === 0) {
            matchMessage.textContent = '';
            return;
        }
        
        if (newPassword === confirmPassword) {
            matchMessage.textContent = 'Passwords match.';
            matchMessage.style.color = '#2ecc71';
        } else {
            matchMessage.textContent = 'Passwords do not match.';
            matchMessage.style.color = '#e74c3c';
        }
    }
    
    // Add event listeners
    document.addEventListener('DOMContentLoaded', function() {
        const newPasswordInput = document.getElementById('new_password');
        if (newPasswordInput) {
            newPasswordInput.addEventListener('input', function() {
                checkPasswordStrength(this.value);
                checkPasswordMatch();
            });
        }
        
        const confirmPasswordInput = document.getElementById('confirm_password');
        if (confirmPasswordInput) {
            confirmPasswordInput.addEventListener('input', checkPasswordMatch);
        }
    });
</script>

<?php
// Include footer
require_once 'includes/footer.php';
?> 