<?php
// Start output buffering to prevent "headers already sent" errors
ob_start();

// Page configuration
$pageTitle = "Order Confirmation - Shopway";
$extraCSS = ['order_confirmation'];
$extraJS = ['order_confirmation'];

// Include header
require_once 'db.php';
require_once 'includes/header.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Get order ID
$orderId = isset($_GET['id']) ? (int)$_GET['id'] : (isset($_SESSION['last_order_id']) ? $_SESSION['last_order_id'] : 0);

if ($orderId <= 0) {
    header("Location: account.php");
    exit;
}

// Get order details
$order = fetchRow(
    "SELECT o.*, u.first_name, u.last_name, u.email 
     FROM orders o 
     JOIN users u ON o.user_id = u.id 
     WHERE o.id = ? AND o.user_id = ?", 
    [$orderId, $_SESSION['user_id']]
);

// If order not found or doesn't belong to the user, redirect
if (!$order) {
    header("Location: account.php");
    exit;
}

// Get order items
$orderItems = fetchResults(
    "SELECT oi.*, p.name, p.image 
     FROM order_items oi 
     JOIN products p ON oi.product_id = p.id 
     WHERE oi.order_id = ?", 
    [$orderId]
);

// Calculate total items
$totalItems = 0;
foreach ($orderItems as $item) {
    $totalItems += $item['quantity'];
}

// Clear the last order ID from session
if (isset($_SESSION['last_order_id'])) {
    unset($_SESSION['last_order_id']);
}
?>

<style>
    .confirmation-container {
        padding: 30px 0;
    }
    
    .confirmation-card {
        background-color: #fff;
        border-radius: 8px;
        box-shadow: 0 2px 15px rgba(0, 0, 0, 0.08);
        padding: 30px;
        max-width: 800px;
        margin: 0 auto;
    }
    
    .success-header {
        text-align: center;
        margin-bottom: 30px;
    }
    
    .success-icon {
        color: #28a745;
        font-size: 60px;
        margin-bottom: 15px;
    }
    
    .success-title {
        font-size: 28px;
        margin-bottom: 10px;
        color: #333;
    }
    
    .order-number {
        font-size: 18px;
        color: #666;
        margin-bottom: 5px;
    }
    
    .email-message {
        color: #777;
        margin-bottom: 30px;
    }
    
    .divider {
        height: 1px;
        background-color: #eee;
        margin: 30px 0;
    }
    
    .section-title {
        font-size: 20px;
        color: #333;
        margin-bottom: 20px;
        padding-bottom: 10px;
        border-bottom: 1px solid #f0f0f0;
    }
    
    .order-details {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 20px;
        margin-bottom: 30px;
    }
    
    .detail-group {
        margin-bottom: 15px;
    }
    
    .detail-label {
        color: #777;
        font-size: 14px;
        margin-bottom: 5px;
    }
    
    .detail-value {
        color: #333;
        font-weight: 500;
    }
    
    .order-items {
        margin-bottom: 30px;
    }
    
    .item-row {
        display: flex;
        align-items: center;
        margin-bottom: 15px;
        padding-bottom: 15px;
        border-bottom: 1px solid #f0f0f0;
    }
    
    .item-row:last-child {
        border-bottom: none;
    }
    
    .item-image {
        width: 60px;
        height: 60px;
        object-fit: cover;
        border-radius: 5px;
        margin-right: 15px;
    }
    
    .item-info {
        flex: 1;
    }
    
    .item-name {
        font-weight: 500;
        margin-bottom: 5px;
    }
    
    .item-price {
        color: #777;
        font-size: 14px;
    }
    
    .item-quantity {
        margin-left: 15px;
        font-weight: 500;
    }
    
    .order-summary {
        background-color: #f8f9fa;
        padding: 20px;
        border-radius: 8px;
        margin-bottom: 30px;
    }
    
    .summary-row {
        display: flex;
        justify-content: space-between;
        padding: 10px 0;
        color: #555;
    }
    
    .summary-row.total {
        border-top: 2px solid #eee;
        margin-top: 10px;
        padding-top: 15px;
        font-weight: bold;
        font-size: 18px;
        color: #333;
    }
    
    .action-buttons {
        display: flex;
        justify-content: center;
        gap: 15px;
    }
    
    .btn {
        display: inline-block;
        padding: 12px 25px;
        border-radius: 5px;
        text-decoration: none;
        text-align: center;
        font-weight: 500;
        transition: all 0.3s ease;
    }
    
    .btn-primary {
        background-color: #007bff;
        color: white;
    }
    
    .btn-primary:hover {
        background-color: #0056b3;
    }
    
    .btn-secondary {
        background-color: #6c757d;
        color: white;
    }
    
    .btn-secondary:hover {
        background-color: #5a6268;
    }
    
    @media (max-width: 768px) {
        .order-details {
            grid-template-columns: 1fr;
        }
        
        .action-buttons {
            flex-direction: column;
        }
        
        .btn {
            width: 100%;
        }
    }
</style>

<div class="confirmation-container">
    <div class="confirmation-card">
        <div class="success-header">
            <div class="success-icon">
                <i class="fas fa-check-circle"></i>
            </div>
            <h1 class="success-title">Order Placed Successfully!</h1>
            <div class="order-number">Order #<?php echo $order['id']; ?></div>
            <p class="email-message">A confirmation email has been sent to <?php echo htmlspecialchars($order['email']); ?></p>
        </div>
        
        <div class="divider"></div>
        
        <h2 class="section-title">Order Details</h2>
        <div class="order-details">
            <div>
                <div class="detail-group">
                    <div class="detail-label">Order Date</div>
                    <div class="detail-value"><?php echo date("F j, Y, g:i a", strtotime($order['order_date'])); ?></div>
                </div>
                
                <div class="detail-group">
                    <div class="detail-label">Order Status</div>
                    <div class="detail-value"><?php echo htmlspecialchars($order['status']); ?></div>
                </div>
                
                <div class="detail-group">
                    <div class="detail-label">Payment Method</div>
                    <div class="detail-value"><?php echo htmlspecialchars($order['payment_method']); ?></div>
                </div>
            </div>
            
            <div>
                <div class="detail-group">
                    <div class="detail-label">Shipping Address</div>
                    <div class="detail-value"><?php echo nl2br(htmlspecialchars($order['shipping_address'])); ?></div>
                </div>
                
                <div class="detail-group">
                    <div class="detail-label">Contact Phone</div>
                    <div class="detail-value"><?php echo htmlspecialchars($order['contact_phone']); ?></div>
                </div>
            </div>
        </div>
        
        <h2 class="section-title">Ordered Items</h2>
        <div class="order-items">
            <?php foreach ($orderItems as $item): ?>
            <div class="item-row">
                <img src="<?php echo BASE_URL; ?>/assets/images/<?php echo htmlspecialchars($item['image']); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>" class="item-image">
                <div class="item-info">
                    <div class="item-name"><?php echo htmlspecialchars($item['name']); ?></div>
                    <div class="item-price">₱<?php echo number_format($item['price'], 2); ?></div>
                </div>
                <div class="item-quantity">x<?php echo $item['quantity']; ?></div>
            </div>
            <?php endforeach; ?>
        </div>
        
        <div class="order-summary">
            <div class="summary-row">
                <span>Subtotal (<?php echo $totalItems; ?> items)</span>
                <?php
                $subtotal = $order['total_amount'] - 150; // Assuming shipping is ₱150
                ?>
                <span>₱<?php echo number_format($subtotal, 2); ?></span>
            </div>
            <div class="summary-row">
                <span>Shipping Fee</span>
                <span>₱150.00</span>
            </div>
            <div class="summary-row total">
                <span>Total</span>
                <span>₱<?php echo number_format($order['total_amount'], 2); ?></span>
            </div>
        </div>
        
        <div class="action-buttons">
            <a href="<?php echo BASE_URL; ?>/orders.php" class="btn btn-primary">View All Orders</a>
            <a href="<?php echo BASE_URL; ?>/products.php" class="btn btn-secondary">Continue Shopping</a>
        </div>
    </div>
</div>

<?php
// Include footer
require_once 'includes/footer.php';
?> 