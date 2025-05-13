<?php
// Start output buffering to prevent "headers already sent" errors
ob_start();

// Page configuration
$pageTitle = "Checkout - Shopway";
$extraCSS = ['checkout'];
$extraJS = ['checkout'];

// Include header
require_once 'db.php';
require_once 'includes/header.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    $_SESSION['redirect_after_login'] = 'checkout.php';
    $_SESSION['error_message'] = "Please log in to complete your order.";
    header("Location: login.php");
    exit;
}

// Check if cart is empty
if (empty($_SESSION['cart'])) {
    $_SESSION['error_message'] = "Your cart is empty. Please add products before checkout.";
    header("Location: cart.php");
    exit;
}

// Get user information
$user = fetchRow("SELECT * FROM users WHERE id = ?", [$_SESSION['user_id']]);

// Initialize variables
$errors = [];
$success = false;
$orderId = null;

// Calculate order totals
$subtotal = 0;
$totalItems = 0;

foreach ($_SESSION['cart'] as $item) {
    $subtotal += $item['price'] * $item['quantity'];
    $totalItems += $item['quantity'];
}

// Set shipping cost
$shipping = 150; // ₱150 shipping fee

// Calculate total
$total = $subtotal + $shipping;

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $shippingAddress = trim($_POST['shipping_address'] ?? '');
    $contactPhone = trim($_POST['contact_phone'] ?? '');
    $paymentMethod = trim($_POST['payment_method'] ?? '');
    
    // Validate form data
    if (empty($shippingAddress)) {
        $errors[] = "Shipping address is required.";
    }
    
    if (empty($contactPhone)) {
        $errors[] = "Contact phone is required.";
    } elseif (!preg_match('/^\d{10,15}$/', preg_replace('/\D/', '', $contactPhone))) {
        $errors[] = "Please enter a valid phone number.";
    }
    
    if (empty($paymentMethod)) {
        $errors[] = "Payment method is required.";
    }
    
    // Check stock availability
    $stockErrors = [];
    foreach ($_SESSION['cart'] as $productId => $item) {
        $product = fetchRow("SELECT id, name, stock FROM products WHERE id = ?", [$productId]);
        
        if (!$product) {
            $stockErrors[] = "Product '{$item['name']}' is no longer available.";
            continue;
        }
        
        if ($product['stock'] < $item['quantity']) {
            if ($product['stock'] > 0) {
                $stockErrors[] = "Only {$product['stock']} units of '{$product['name']}' are available. You requested {$item['quantity']}.";
            } else {
                $stockErrors[] = "'{$product['name']}' is out of stock.";
            }
        }
    }
    
    if (!empty($stockErrors)) {
        $errors = array_merge($errors, $stockErrors);
    }
    
    // Process order if no errors
    if (empty($errors)) {
        // Start transaction
        $conn = connectDB();
        $conn->begin_transaction();
        
        try {
            // Insert order
            $orderSql = "INSERT INTO orders (user_id, total_amount, status, payment_method, shipping_address, contact_phone) 
                        VALUES (?, ?, 'Pending', ?, ?, ?)";
            $orderParams = [$_SESSION['user_id'], $total, $paymentMethod, $shippingAddress, $contactPhone];
            
            $orderStmt = $conn->prepare($orderSql);
            $orderStmt->bind_param("idsss", ...$orderParams);
            $orderStmt->execute();
            
            $orderId = $conn->insert_id;
            $orderStmt->close();
            
            // Insert order items
            $itemSql = "INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)";
            $itemStmt = $conn->prepare($itemSql);
            $itemStmt->bind_param("iiid", $orderId, $productId, $quantity, $price);
            
            foreach ($_SESSION['cart'] as $productId => $item) {
                $quantity = $item['quantity'];
                $price = $item['price'];
                $itemStmt->execute();
                
                // Update product stock
                $conn->query("UPDATE products SET stock = stock - $quantity WHERE id = $productId");
            }
            
            $itemStmt->close();
            
            // Commit transaction
            $conn->commit();
            
            // Clear cart if order was successful
            $_SESSION['cart'] = [];
            $success = true;
            
            // Store order ID in session for confirmation page
            $_SESSION['last_order_id'] = $orderId;
            
            // Redirect to order confirmation page
            header("Location: order_confirmation.php?id=$orderId");
            exit;
            
        } catch (Exception $e) {
            // Rollback transaction if any error occurred
            $conn->rollback();
            $errors[] = "Failed to process your order: " . $e->getMessage();
        }
    }
}
?>

<style>
    .checkout-container {
        padding: 30px 0;
    }
    
    .page-title {
        font-size: 2rem;
        margin-bottom: 30px;
        color: #333;
        border-bottom: 2px solid #f0f0f0;
        padding-bottom: 10px;
    }
    
    .checkout-form {
        background-color: #fff;
        border-radius: 8px;
        box-shadow: 0 2px 15px rgba(0, 0, 0, 0.08);
        padding: 20px;
    }
    
    .checkout-grid {
        display: grid;
        grid-template-columns: 1.5fr 1fr;
        gap: 30px;
    }
    
    .form-section {
        margin-bottom: 30px;
    }
    
    .form-section h2 {
        font-size: 1.5rem;
        margin-bottom: 20px;
        padding-bottom: 10px;
        border-bottom: 1px solid #f0f0f0;
        color: #333;
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
    
    .radio-group {
        margin-top: 10px;
    }
    
    .radio-option {
        display: flex;
        align-items: center;
        margin-bottom: 15px;
        padding: 10px 15px;
        border: 1px solid #ddd;
        border-radius: 5px;
        transition: all 0.3s ease;
        cursor: pointer;
    }
    
    .radio-option:hover {
        background-color: #f9f9f9;
    }
    
    .radio-option input {
        margin-right: 10px;
    }
    
    .radio-option.selected {
        border-color: #007bff;
        background-color: rgba(0, 123, 255, 0.05);
    }
    
    .order-summary {
        background-color: #f8f9fa;
        padding: 20px;
        border-radius: 8px;
    }
    
    .order-summary h2 {
        font-size: 1.5rem;
        margin-bottom: 20px;
        padding-bottom: 10px;
        border-bottom: 1px solid #f0f0f0;
        color: #333;
    }
    
    .order-items {
        margin-bottom: 25px;
    }
    
    .order-item {
        display: flex;
        align-items: center;
        margin-bottom: 15px;
        padding-bottom: 15px;
        border-bottom: 1px solid #f0f0f0;
    }
    
    .order-item img {
        width: 60px;
        height: 60px;
        object-fit: cover;
        border-radius: 5px;
        margin-right: 15px;
    }
    
    .item-details {
        flex: 1;
    }
    
    .item-name {
        font-weight: 500;
        margin-bottom: 5px;
    }
    
    .item-price {
        color: #666;
        font-size: 14px;
    }
    
    .item-quantity {
        font-weight: 500;
        color: #333;
        margin-left: 15px;
    }
    
    .summary-totals {
        background-color: white;
        padding: 15px;
        border-radius: 5px;
        margin-bottom: 25px;
    }
    
    .summary-row {
        display: flex;
        justify-content: space-between;
        padding: 10px 0;
        color: #555;
    }
    
    .summary-row.total {
        border-top: 2px solid #f0f0f0;
        margin-top: 10px;
        padding-top: 15px;
        font-weight: bold;
        font-size: 18px;
        color: #333;
    }
    
    .place-order-btn {
        display: block;
        width: 100%;
        padding: 15px;
        font-size: 16px;
        font-weight: 500;
        background-color: #28a745;
        color: white;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        transition: background-color 0.3s ease;
    }
    
    .place-order-btn:hover {
        background-color: #218838;
    }
    
    .error-message {
        background-color: rgba(244, 67, 54, 0.1);
        border-left: 4px solid #F44336;
        color: #c62828;
        padding: 15px;
        border-radius: 5px;
        margin-bottom: 20px;
    }
    
    .error-list {
        margin: 10px 0 0 20px;
    }
    
    @media (max-width: 991px) {
        .checkout-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="checkout-container">
    <h1 class="page-title">Checkout</h1>
    
    <?php if (!empty($errors)): ?>
    <div class="error-message">
        <strong>Please correct the following errors:</strong>
        <ul class="error-list">
            <?php foreach ($errors as $error): ?>
            <li><?php echo htmlspecialchars($error); ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
    <?php endif; ?>
    
    <form action="checkout.php" method="POST" class="checkout-form">
        <div class="checkout-grid">
            <div class="checkout-info">
                <div class="form-section">
                    <h2>Shipping Information</h2>
                    
                    <div class="form-group">
                        <label for="shipping_address" class="form-label">Shipping Address *</label>
                        <textarea id="shipping_address" name="shipping_address" class="form-control" rows="3" required><?php echo htmlspecialchars($user['address'] ?? ''); ?></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label for="contact_phone" class="form-label">Contact Phone *</label>
                        <input type="tel" id="contact_phone" name="contact_phone" class="form-control" value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>" required>
                    </div>
                </div>
                
                <div class="form-section">
                    <h2>Payment Method</h2>
                    
                    <div class="radio-group">
                        <div class="radio-option">
                            <input type="radio" id="cod" name="payment_method" value="Cash on Delivery" checked>
                            <label for="cod">Cash on Delivery</label>
                        </div>
                        
                        <div class="radio-option">
                            <input type="radio" id="bank_transfer" name="payment_method" value="Bank Transfer">
                            <label for="bank_transfer">Bank Transfer</label>
                        </div>
                        
                        <div class="radio-option">
                            <input type="radio" id="gcash" name="payment_method" value="GCash">
                            <label for="gcash">GCash</label>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="order-summary">
                <h2>Order Summary</h2>
                
                <div class="order-items">
                    <?php foreach ($_SESSION['cart'] as $productId => $item): ?>
                    <div class="order-item">
                        <img src="<?php echo BASE_URL; ?>/assets/images/<?php echo htmlspecialchars($item['image']); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>">
                        <div class="item-details">
                            <div class="item-name"><?php echo htmlspecialchars($item['name']); ?></div>
                            <div class="item-price">₱<?php echo number_format($item['price'], 2); ?></div>
                        </div>
                        <div class="item-quantity">x<?php echo $item['quantity']; ?></div>
                    </div>
                    <?php endforeach; ?>
                </div>
                
                <div class="summary-totals">
                    <div class="summary-row">
                        <span>Subtotal (<?php echo $totalItems; ?> items)</span>
                        <span>₱<?php echo number_format($subtotal, 2); ?></span>
                    </div>
                    <div class="summary-row">
                        <span>Shipping Fee</span>
                        <span>₱<?php echo number_format($shipping, 2); ?></span>
                    </div>
                    <div class="summary-row total">
                        <span>Total</span>
                        <span>₱<?php echo number_format($total, 2); ?></span>
                    </div>
                </div>
                
                <button type="submit" class="place-order-btn">Place Order</button>
            </div>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle payment method selection highlighting
    const radioOptions = document.querySelectorAll('.radio-option');
    
    radioOptions.forEach(option => {
        const radio = option.querySelector('input[type="radio"]');
        
        // Set initial selected state
        if (radio.checked) {
            option.classList.add('selected');
        }
        
        // Handle click on the div
        option.addEventListener('click', function() {
            // Unselect all options
            radioOptions.forEach(opt => {
                opt.classList.remove('selected');
                opt.querySelector('input[type="radio"]').checked = false;
            });
            
            // Select clicked option
            radio.checked = true;
            option.classList.add('selected');
        });
        
        // Handle direct radio button clicks
        radio.addEventListener('change', function() {
            if (this.checked) {
                radioOptions.forEach(opt => opt.classList.remove('selected'));
                option.classList.add('selected');
            }
        });
    });
});
</script>

<?php
// Include footer
require_once 'includes/footer.php';
?> 