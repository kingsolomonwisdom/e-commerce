<?php
// Page configuration
$pageTitle = "Shopping Cart - Shopway";
$extraCSS = ['cart'];
$extraJS = ['cart'];

// Include header
require_once 'db.php';
require_once 'includes/header.php';

// Initialize cart if not exists
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Process cart actions
if (isset($_POST['action'])) {
    $action = $_POST['action'];
    $productId = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;
    
    if ($action === 'update' && $productId > 0) {
        $quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;
        
        if ($quantity > 0) {
            // Check stock availability
            $product = fetchRow("SELECT stock FROM products WHERE id = ?", [$productId]);
            
            if ($product && (!isset($product['stock']) || $product['stock'] >= $quantity)) {
                $_SESSION['cart'][$productId]['quantity'] = $quantity;
                $_SESSION['success_message'] = "Cart updated successfully.";
            } else {
                $_SESSION['error_message'] = "Not enough stock available.";
            }
        }
    } elseif ($action === 'remove' && $productId > 0) {
        if (isset($_SESSION['cart'][$productId])) {
            unset($_SESSION['cart'][$productId]);
            $_SESSION['success_message'] = "Item removed from cart.";
        }
    } elseif ($action === 'clear') {
        $_SESSION['cart'] = [];
        $_SESSION['success_message'] = "Cart cleared successfully.";
    }
    
    // Redirect to refresh page after cart modification
    header("Location: cart.php");
    exit;
}

// Calculate cart totals
$subtotal = 0;
$totalItems = 0;

foreach ($_SESSION['cart'] as $item) {
    $subtotal += $item['price'] * $item['quantity'];
    $totalItems += $item['quantity'];
}

// Set shipping cost
$shipping = $subtotal > 0 ? 150 : 0; // ₱150 shipping fee

// Calculate total
$total = $subtotal + $shipping;
?>

<style>
    /* Cart Page Styles */
    .cart-container {
        padding: 20px 0;
    }
    
    .page-title {
        font-size: 2rem;
        margin-bottom: 30px;
        color: #333;
        border-bottom: 2px solid #f0f0f0;
        padding-bottom: 10px;
    }
    
    .message {
        padding: 15px;
        border-radius: 5px;
        margin-bottom: 20px;
        animation: fadeOut 5s forwards;
    }
    
    @keyframes fadeOut {
        0% { opacity: 1; }
        70% { opacity: 1; }
        100% { opacity: 0; }
    }
    
    .success {
        background-color: rgba(76, 175, 80, 0.1);
        border-left: 4px solid #4CAF50;
        color: #2e7031;
    }
    
    .error {
        background-color: rgba(244, 67, 54, 0.1);
        border-left: 4px solid #F44336;
        color: #c62828;
    }
    
    .empty-cart {
        text-align: center;
        padding: 50px 20px;
        background-color: #f9f9f9;
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
    }
    
    .empty-cart i {
        color: #ccc;
        margin-bottom: 20px;
    }
    
    .empty-cart h2 {
        font-size: 24px;
        margin-bottom: 15px;
        color: #555;
    }
    
    .empty-cart p {
        color: #777;
        margin-bottom: 25px;
    }
    
    .cart-content {
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: 30px;
    }
    
    .cart-items {
        background-color: #fff;
        border-radius: 8px;
        box-shadow: 0 2px 15px rgba(0, 0, 0, 0.08);
        padding: 20px;
    }
    
    .cart-table {
        width: 100%;
        border-collapse: collapse;
    }
    
    .cart-table th {
        text-align: left;
        padding: 12px 15px;
        background-color: #f8f9fa;
        color: #555;
        font-weight: 600;
        border-bottom: 2px solid #eee;
    }
    
    .cart-table td {
        padding: 15px;
        border-bottom: 1px solid #f0f0f0;
        vertical-align: middle;
    }
    
    .product-info {
        display: flex;
        align-items: center;
    }
    
    .product-info img {
        width: 80px;
        height: 80px;
        object-fit: cover;
        border-radius: 5px;
        margin-right: 15px;
    }
    
    .product-info h3 {
        font-size: 16px;
        margin-bottom: 5px;
    }
    
    .product-info a {
        color: #007bff;
        font-size: 14px;
        text-decoration: none;
    }
    
    .product-info a:hover {
        text-decoration: underline;
    }
    
    .price, .subtotal {
        color: #333;
        font-weight: 500;
    }
    
    .quantity-controls {
        display: flex;
        align-items: center;
        margin-bottom: 10px;
    }
    
    .quantity-controls button {
        width: 30px;
        height: 30px;
        background-color: #f0f0f0;
        border: none;
        border-radius: 50%;
        font-weight: bold;
        cursor: pointer;
        transition: background-color 0.3s ease;
    }
    
    .quantity-controls button:hover {
        background-color: #e0e0e0;
    }
    
    .quantity-input {
        width: 40px;
        height: 30px;
        text-align: center;
        border: 1px solid #ddd;
        margin: 0 10px;
        padding: 0 5px;
    }
    
    .update-btn {
        background-color: transparent;
        color: #007bff;
        border: 1px solid #007bff;
        border-radius: 4px;
        padding: 5px 10px;
        font-size: 13px;
        cursor: pointer;
        transition: all 0.3s ease;
    }
    
    .update-btn:hover {
        background-color: #007bff;
        color: white;
    }
    
    .remove-btn {
        background-color: transparent;
        color: #dc3545;
        border: none;
        cursor: pointer;
        transition: color 0.3s ease;
    }
    
    .remove-btn:hover {
        color: #bd2130;
    }
    
    .cart-actions {
        display: flex;
        justify-content: space-between;
        margin-top: 20px;
        padding-top: 20px;
        border-top: 1px solid #eee;
    }
    
    .btn {
        display: inline-block;
        padding: 10px 20px;
        text-align: center;
        border-radius: 4px;
        cursor: pointer;
        font-weight: 500;
        text-decoration: none;
        transition: all 0.3s ease;
    }
    
    .btn-primary {
        background-color: #007bff;
        color: white;
        border: none;
    }
    
    .btn-primary:hover {
        background-color: #0056b3;
    }
    
    .btn-secondary {
        background-color: #6c757d;
        color: white;
        border: none;
    }
    
    .btn-secondary:hover {
        background-color: #5a6268;
    }
    
    .btn-danger {
        background-color: #dc3545;
        color: white;
        border: none;
    }
    
    .btn-danger:hover {
        background-color: #bd2130;
    }
    
    .cart-summary {
        background-color: #fff;
        border-radius: 8px;
        box-shadow: 0 2px 15px rgba(0, 0, 0, 0.08);
        padding: 20px;
        position: sticky;
        top: 20px;
    }
    
    .cart-summary h2 {
        font-size: 20px;
        margin-bottom: 20px;
        padding-bottom: 10px;
        border-bottom: 1px solid #eee;
        color: #333;
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
    
    .checkout-btn {
        display: block;
        width: 100%;
        padding: 12px;
        margin-top: 20px;
        font-size: 16px;
        background-color: #28a745;
    }
    
    .checkout-btn:hover {
        background-color: #218838;
    }
    
    @media (max-width: 991px) {
        .cart-content {
            grid-template-columns: 1fr;
        }
        
        .cart-summary {
            position: static;
            margin-top: 30px;
        }
    }
    
    @media (max-width: 768px) {
        .cart-table {
            display: block;
            overflow-x: auto;
        }
        
        .cart-actions {
            flex-direction: column;
            gap: 10px;
        }
        
        .cart-actions .btn {
            width: 100%;
        }
    }
    
    @media (max-width: 576px) {
        .product-info {
            flex-direction: column;
            text-align: center;
        }
        
        .product-info img {
            margin-right: 0;
            margin-bottom: 10px;
        }
    }
</style>

<div class="cart-container">
    <h1 class="page-title">Shopping Cart</h1>
    
    <?php if (isset($_SESSION['success_message'])): ?>
    <div class="message success"><?php echo $_SESSION['success_message']; ?></div>
    <?php unset($_SESSION['success_message']); ?>
    <?php endif; ?>
    
    <?php if (isset($_SESSION['error_message'])): ?>
    <div class="message error"><?php echo $_SESSION['error_message']; ?></div>
    <?php unset($_SESSION['error_message']); ?>
    <?php endif; ?>
    
    <?php if (empty($_SESSION['cart'])): ?>
    <div class="empty-cart">
        <i class="fas fa-shopping-cart fa-4x"></i>
        <h2>Your cart is empty</h2>
        <p>Looks like you haven't added any products to your cart yet.</p>
        <a href="<?php echo BASE_URL; ?>/products.php" class="btn btn-primary">Start Shopping</a>
    </div>
    <?php else: ?>
    <div class="cart-content">
        <div class="cart-items">
            <table class="cart-table">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Price</th>
                        <th>Quantity</th>
                        <th>Total</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($_SESSION['cart'] as $productId => $item): ?>
                    <tr>
                        <td class="product-info">
                            <img src="<?php echo BASE_URL; ?>/assets/images/<?php echo htmlspecialchars($item['image']); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>">
                            <div>
                                <h3><?php echo htmlspecialchars($item['name']); ?></h3>
                                <a href="<?php echo BASE_URL; ?>/product.php?id=<?php echo $productId; ?>">View Details</a>
                            </div>
                        </td>
                        <td class="price">₱<?php echo number_format($item['price'], 2); ?></td>
                        <td class="quantity">
                            <form action="cart.php" method="POST" class="quantity-form">
                                <input type="hidden" name="action" value="update">
                                <input type="hidden" name="product_id" value="<?php echo $productId; ?>">
                                <div class="quantity-controls">
                                    <button type="button" class="quantity-minus">-</button>
                                    <input type="number" name="quantity" value="<?php echo $item['quantity']; ?>" min="1" class="quantity-input">
                                    <button type="button" class="quantity-plus">+</button>
                                </div>
                                <button type="submit" class="update-btn">Update</button>
                            </form>
                        </td>
                        <td class="subtotal">₱<?php echo number_format($item['price'] * $item['quantity'], 2); ?></td>
                        <td class="action">
                            <form action="cart.php" method="POST">
                                <input type="hidden" name="action" value="remove">
                                <input type="hidden" name="product_id" value="<?php echo $productId; ?>">
                                <button type="submit" class="remove-btn"><i class="fas fa-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            
            <div class="cart-actions">
                <a href="<?php echo BASE_URL; ?>/products.php" class="btn btn-secondary">Continue Shopping</a>
                <form action="cart.php" method="POST">
                    <input type="hidden" name="action" value="clear">
                    <button type="submit" class="btn btn-danger">Clear Cart</button>
                </form>
            </div>
        </div>
        
        <div class="cart-summary">
            <h2>Order Summary</h2>
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
            <a href="<?php echo BASE_URL; ?>/checkout.php" class="btn btn-primary checkout-btn">Proceed to Checkout</a>
        </div>
    </div>
    <?php endif; ?>
</div>

<?php
// Include footer
require_once 'includes/footer.php';
?> 