<?php
// Page configuration
$pageTitle = "My Account - Shopway";
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

// Get recent orders
$recentOrders = fetchResults(
    "SELECT * FROM orders WHERE user_id = ? ORDER BY order_date DESC LIMIT 3", 
    [$_SESSION['user_id']]
);

// Success/error messages
$successMessage = $_SESSION['success_message'] ?? '';
$errorMessage = $_SESSION['error_message'] ?? '';

// Clear session messages
if (isset($_SESSION['success_message'])) unset($_SESSION['success_message']);
if (isset($_SESSION['error_message'])) unset($_SESSION['error_message']);
?>

<style>
    .account-container {
        padding: 30px 0;
    }
    
    .page-title {
        font-size: 2rem;
        margin-bottom: 30px;
        color: #333;
        border-bottom: 2px solid #f0f0f0;
        padding-bottom: 10px;
    }
    
    .account-content {
        display: grid;
        grid-template-columns: 1fr 3fr;
        gap: 30px;
    }
    
    .account-sidebar {
        background-color: #fff;
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
        padding: 20px;
    }
    
    .user-profile {
        text-align: center;
        padding-bottom: 20px;
        margin-bottom: 20px;
        border-bottom: 1px solid #f0f0f0;
    }
    
    .user-avatar {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        background-color: #f0f0f0;
        margin: 0 auto 15px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        color: #777;
    }
    
    .user-name {
        font-size: 18px;
        font-weight: 600;
        margin-bottom: 5px;
    }
    
    .user-email {
        color: #777;
        font-size: 14px;
    }
    
    .account-menu {
        list-style: none;
        padding: 0;
        margin: 0;
    }
    
    .account-menu li {
        margin-bottom: 10px;
    }
    
    .account-menu a {
        display: flex;
        align-items: center;
        padding: 10px 15px;
        border-radius: 5px;
        color: #555;
        text-decoration: none;
        transition: all 0.3s ease;
    }
    
    .account-menu a:hover, .account-menu a.active {
        background-color: #f8f9fa;
        color: #007bff;
    }
    
    .account-menu i {
        margin-right: 10px;
        width: 20px;
        text-align: center;
    }
    
    .account-main {
        background-color: #fff;
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
        padding: 20px;
    }
    
    .account-section {
        margin-bottom: 30px;
    }
    
    .section-title {
        font-size: 20px;
        margin-bottom: 20px;
        color: #333;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    
    .view-all {
        font-size: 14px;
        color: #007bff;
        text-decoration: none;
    }
    
    .info-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 20px;
    }
    
    .info-item {
        margin-bottom: 15px;
    }
    
    .info-label {
        color: #777;
        margin-bottom: 5px;
        font-size: 14px;
    }
    
    .info-value {
        color: #333;
        font-weight: 500;
    }
    
    .edit-profile-btn {
        background-color: #007bff;
        color: white;
        border: none;
        padding: 10px 15px;
        border-radius: 5px;
        text-decoration: none;
        display: inline-block;
        margin-top: 15px;
        transition: background-color 0.3s ease;
    }
    
    .edit-profile-btn:hover {
        background-color: #0056b3;
    }
    
    .orders-grid {
        display: grid;
        gap: 15px;
    }
    
    .order-card {
        background-color: #f8f9fa;
        border-radius: 5px;
        padding: 15px;
        display: grid;
        grid-template-columns: 1fr auto auto;
        align-items: center;
    }
    
    .order-info {
        display: flex;
        flex-direction: column;
    }
    
    .order-id {
        font-weight: 600;
        margin-bottom: 5px;
    }
    
    .order-date {
        color: #777;
        font-size: 13px;
    }
    
    .order-total {
        font-weight: 600;
        color: #2ecc71;
    }
    
    .order-status {
        display: inline-block;
        padding: 5px 10px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 500;
    }
    
    .message {
        padding: 15px;
        border-radius: 5px;
        margin-bottom: 20px;
        text-align: center;
    }
    
    .message.success {
        background-color: rgba(46, 204, 113, 0.1);
        color: #27ae60;
        border-left: 4px solid #2ecc71;
    }
    
    .message.error {
        background-color: rgba(231, 76, 60, 0.1);
        color: #c0392b;
        border-left: 4px solid #e74c3c;
    }
    
    .no-orders {
        text-align: center;
        padding: 20px;
        color: #777;
        background-color: #f9f9f9;
        border-radius: 5px;
    }
    
    .logout-btn {
        background-color: #dc3545;
        color: white;
        border: none;
        width: 100%;
        padding: 10px 15px;
        border-radius: 5px;
        cursor: pointer;
        margin-top: 20px;
        transition: background-color 0.3s ease;
    }
    
    .logout-btn:hover {
        background-color: #c82333;
    }
    
    @media (max-width: 768px) {
        .account-content {
            grid-template-columns: 1fr;
        }
        
        .account-sidebar {
            margin-bottom: 20px;
        }
        
        .info-grid {
            grid-template-columns: 1fr;
        }
        
        .order-card {
            grid-template-columns: 1fr;
            gap: 10px;
        }
    }
</style>

<div class="account-container">
    <h1 class="page-title">My Account</h1>
    
    <?php if ($successMessage): ?>
    <div class="message success"><?php echo $successMessage; ?></div>
    <?php endif; ?>
    
    <?php if ($errorMessage): ?>
    <div class="message error"><?php echo $errorMessage; ?></div>
    <?php endif; ?>
    
    <div class="account-content">
        <!-- Sidebar -->
        <div class="account-sidebar">
            <div class="user-profile">
                <div class="user-avatar">
                    <?php echo strtoupper(substr($userData['first_name'], 0, 1)); ?>
                </div>
                <div class="user-name"><?php echo htmlspecialchars($userData['first_name'] . ' ' . $userData['last_name']); ?></div>
                <div class="user-email"><?php echo htmlspecialchars($userData['email']); ?></div>
            </div>
            
            <ul class="account-menu">
                <li><a href="account.php" class="active"><i class="fas fa-user"></i> My Profile</a></li>
                <li><a href="orders.php"><i class="fas fa-shopping-bag"></i> My Orders</a></li>
                <li><a href="edit-profile.php"><i class="fas fa-cog"></i> Account Settings</a></li>
                <li><a href="change-password.php"><i class="fas fa-lock"></i> Change Password</a></li>
            </ul>
            
            <a href="logout.php" class="logout-btn"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </div>
        
        <!-- Main Content -->
        <div class="account-main">
            <!-- Personal Information -->
            <div class="account-section">
                <h2 class="section-title">Personal Information</h2>
                
                <div class="info-grid">
                    <div class="info-item">
                        <div class="info-label">First Name</div>
                        <div class="info-value"><?php echo htmlspecialchars($userData['first_name']); ?></div>
                    </div>
                    
                    <div class="info-item">
                        <div class="info-label">Last Name</div>
                        <div class="info-value"><?php echo htmlspecialchars($userData['last_name']); ?></div>
                    </div>
                    
                    <div class="info-item">
                        <div class="info-label">Email</div>
                        <div class="info-value"><?php echo htmlspecialchars($userData['email']); ?></div>
                    </div>
                    
                    <div class="info-item">
                        <div class="info-label">Phone</div>
                        <div class="info-value"><?php echo htmlspecialchars($userData['phone'] ?: 'Not specified'); ?></div>
                    </div>
                    
                    <div class="info-item" style="grid-column: span 2;">
                        <div class="info-label">Address</div>
                        <div class="info-value"><?php echo htmlspecialchars($userData['address'] ?: 'Not specified'); ?></div>
                    </div>
                </div>
                
                <a href="edit-profile.php" class="edit-profile-btn"><i class="fas fa-edit"></i> Edit Profile</a>
            </div>
            
            <!-- Recent Orders -->
            <div class="account-section">
                <h2 class="section-title">
                    Recent Orders
                    <a href="orders.php" class="view-all">View All</a>
                </h2>
                
                <?php if (empty($recentOrders)): ?>
                <div class="no-orders">
                    <p>You haven't placed any orders yet.</p>
                    <a href="<?php echo BASE_URL; ?>/products.php" class="edit-profile-btn" style="margin-top: 10px;">Start Shopping</a>
                </div>
                <?php else: ?>
                <div class="orders-grid">
                    <?php foreach ($recentOrders as $order): ?>
                    <div class="order-card">
                        <div class="order-info">
                            <div class="order-id">Order #<?php echo $order['id']; ?></div>
                            <div class="order-date"><?php echo date("F j, Y", strtotime($order['order_date'])); ?></div>
                        </div>
                        <div class="order-total">₱<?php echo number_format($order['total_amount'], 2); ?></div>
                        <span class="order-status" style="
                            background-color: <?php echo strtolower($order['status']) === 'delivered' ? '#badc58' : 
                                        (strtolower($order['status']) === 'shipped' ? '#c7ecee' : 
                                        (strtolower($order['status']) === 'processing' ? '#a0e7e5' : 
                                        (strtolower($order['status']) === 'cancelled' ? '#ffcccc' : '#ffeaa7'))); ?>; 
                            color: <?php echo strtolower($order['status']) === 'delivered' ? '#2d5108' : 
                                       (strtolower($order['status']) === 'shipped' ? '#3c6382' : 
                                       (strtolower($order['status']) === 'processing' ? '#0a766e' : 
                                       (strtolower($order['status']) === 'cancelled' ? '#d63031' : '#d68102'))); ?>;">
                            <?php echo $order['status']; ?>
                        </span>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php
// Include footer
require_once 'includes/footer.php';
?> 