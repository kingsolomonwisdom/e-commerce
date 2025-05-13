<?php
// Page configuration
$pageTitle = "Edit Profile - Shopway";
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

// Get user data
$userData = fetchRow("SELECT * FROM users WHERE id = ?", [$_SESSION['user_id']]);

$success = '';
$error = '';

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $firstName = trim($_POST['first_name'] ?? '');
    $lastName = trim($_POST['last_name'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $address = trim($_POST['address'] ?? '');
    
    // Validate form data
    if (empty($firstName) || empty($lastName)) {
        $error = "First name and last name are required.";
    } else {
        // Update user data in database
        $sql = "UPDATE users SET first_name = ?, last_name = ?, phone = ?, address = ?, updated_at = NOW() WHERE id = ?";
        $params = [$firstName, $lastName, $phone, $address, $_SESSION['user_id']];
        
        $affected = modifyData($sql, $params);
        
        if ($affected) {
            // Update session variable if needed
            $_SESSION['first_name'] = $firstName;
            
            // Set success message for account page
            $_SESSION['success_message'] = "Your profile has been updated successfully.";
            
            // Redirect to account page
            header("Location: account.php");
            exit;
        } else {
            $error = "Failed to update profile. Please try again.";
        }
    }
}
?>

<style>
    .edit-profile-container {
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
        padding: 20px;
        max-width: 800px;
        margin: 0 auto;
    }
    
    .form-title {
        font-size: 1.5rem;
        color: #333;
        margin-bottom: 20px;
        padding-bottom: 10px;
        border-bottom: 1px solid #f0f0f0;
    }
    
    .form-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 20px;
    }
    
    .form-group {
        margin-bottom: 20px;
    }
    
    .form-group.full-width {
        grid-column: span 2;
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
        margin-top: 20px;
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
    
    @media (max-width: 768px) {
        .form-grid {
            grid-template-columns: 1fr;
        }
        
        .form-group.full-width {
            grid-column: auto;
        }
    }
</style>

<div class="edit-profile-container">
    <h1 class="page-title">Edit Profile</h1>
    
    <div class="form-container">
        <h2 class="form-title">Update Your Information</h2>
        
        <?php if ($error): ?>
        <div class="message error"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <?php if ($success): ?>
        <div class="message success"><?php echo $success; ?></div>
        <?php endif; ?>
        
        <form action="edit-profile.php" method="POST">
            <div class="form-grid">
                <div class="form-group">
                    <label for="first_name" class="form-label">First Name *</label>
                    <input type="text" class="form-control" id="first_name" name="first_name" value="<?php echo htmlspecialchars($userData['first_name']); ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="last_name" class="form-label">Last Name *</label>
                    <input type="text" class="form-control" id="last_name" name="last_name" value="<?php echo htmlspecialchars($userData['last_name']); ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="email" class="form-label">Email Address</label>
                    <input type="email" class="form-control" id="email" value="<?php echo htmlspecialchars($userData['email']); ?>" readonly disabled>
                    <div class="form-text">Email address cannot be changed. Contact support if you need to update your email.</div>
                </div>
                
                <div class="form-group">
                    <label for="phone" class="form-label">Phone Number</label>
                    <input type="tel" class="form-control" id="phone" name="phone" value="<?php echo htmlspecialchars($userData['phone'] ?? ''); ?>">
                </div>
                
                <div class="form-group full-width">
                    <label for="address" class="form-label">Address</label>
                    <textarea class="form-control" id="address" name="address" rows="4"><?php echo htmlspecialchars($userData['address'] ?? ''); ?></textarea>
                </div>
            </div>
            
            <div class="form-buttons">
                <button type="submit" class="btn btn-primary">Save Changes</button>
                <a href="account.php" class="btn btn-outline">Cancel</a>
            </div>
        </form>
    </div>
</div>

<?php
// Include footer
require_once 'includes/footer.php';
?> 