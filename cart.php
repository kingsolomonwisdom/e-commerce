<?php
// Page configuration
$pageTitle = "Shopping Cart - Shopway";
$extraCSS = ['cart'];
$extraJS = ['cart'];

// Include header
require_once 'includes/config.php';
require_once 'includes/db.php';
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