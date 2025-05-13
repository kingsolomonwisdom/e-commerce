<?php
// Page configuration
$pageTitle = "My Orders - Shopway";
$extraCSS = ['orders'];
$extraJS = ['orders'];

// Include header
require_once 'db.php';
require_once 'includes/header.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Get user's orders with pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 5;
$offset = ($page - 1) * $limit;
$userId = $_SESSION['user_id'];

// Get total orders count for pagination
$totalOrders = fetchRow("SELECT COUNT(*) as count FROM orders WHERE user_id = ?", [$userId])['count'] ?? 0;
$totalPages = ceil($totalOrders / $limit);

// Get orders for current page
$orders = fetchResults("SELECT * FROM orders WHERE user_id = ? ORDER BY order_date DESC LIMIT ? OFFSET ?", 
                    [$userId, $limit, $offset], 
                    "iii");

// Get specific order details if an order_id is provided
$orderDetails = [];
$orderItems = [];
if (isset($_GET['order_id']) && is_numeric($_GET['order_id'])) {
    $orderId = (int)$_GET['order_id'];
    
    // Get order details
    $orderDetails = fetchRow("SELECT * FROM orders WHERE id = ? AND user_id = ?", [$orderId, $userId]);
    
    if ($orderDetails) {
        // Get order items
        $orderItems = fetchResults(
            "SELECT oi.*, p.name, p.image FROM order_items oi 
            JOIN products p ON oi.product_id = p.id 
            WHERE oi.order_id = ?", 
            [$orderId]
        );
    }
}
?>

<!-- Orders Container -->
<div class="container orders-container" style="padding: 20px 0;">
    <?php if ($orderDetails): ?>
        <!-- Order Details View -->
        <a href="orders.php" style="display: inline-flex; align-items: center; color: #007bff; text-decoration: none; margin-bottom: 20px;">
            <i class="fas fa-arrow-left" style="margin-right: 8px;"></i> Back to All Orders
        </a>
        
        <div style="background-color: #fff; border-radius: 8px; box-shadow: 0 2px 20px rgba(0, 0, 0, 0.1); padding: 20px; margin-bottom: 30px;">
            <div style="display: flex; justify-content: space-between; align-items: center; padding-bottom: 15px; border-bottom: 1px solid #f0f0f0; margin-bottom: 20px;">
                <div style="font-size: 24px; color: #333;">
                    Order #<?php echo $orderDetails['id']; ?>
                </div>
                <span style="display: inline-block; padding: 5px 10px; border-radius: 20px; font-size: 14px; font-weight: 500; 
                      background-color: <?php echo strtolower($orderDetails['status']) === 'delivered' ? '#badc58' : 
                                  (strtolower($orderDetails['status']) === 'shipped' ? '#c7ecee' : 
                                  (strtolower($orderDetails['status']) === 'processing' ? '#a0e7e5' : 
                                  (strtolower($orderDetails['status']) === 'cancelled' ? '#ffcccc' : '#ffeaa7'))); ?>; 
                      color: <?php echo strtolower($orderDetails['status']) === 'delivered' ? '#2d5108' : 
                                 (strtolower($orderDetails['status']) === 'shipped' ? '#3c6382' : 
                                 (strtolower($orderDetails['status']) === 'processing' ? '#0a766e' : 
                                 (strtolower($orderDetails['status']) === 'cancelled' ? '#d63031' : '#d68102'))); ?>;">
                    <?php echo $orderDetails['status']; ?>
                </span>
            </div>
            
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-bottom: 30px;">
                <div>
                    <div style="margin-bottom: 20px;">
                        <div style="color: #777; margin-bottom: 5px; font-size: 14px;">Order Date</div>
                        <div style="color: #333; font-weight: 500;"><?php echo date("F j, Y, g:i a", strtotime($orderDetails['order_date'])); ?></div>
                    </div>
                    
                    <div style="margin-bottom: 20px;">
                        <div style="color: #777; margin-bottom: 5px; font-size: 14px;">Payment Method</div>
                        <div style="color: #333; font-weight: 500;"><?php echo htmlspecialchars($orderDetails['payment_method'] ?? 'Not specified'); ?></div>
                    </div>
                </div>
                
                <div>
                    <div style="margin-bottom: 20px;">
                        <div style="color: #777; margin-bottom: 5px; font-size: 14px;">Shipping Address</div>
                        <div style="color: #333; font-weight: 500;"><?php echo nl2br(htmlspecialchars($orderDetails['shipping_address'])); ?></div>
                    </div>
                    
                    <div style="margin-bottom: 20px;">
                        <div style="color: #777; margin-bottom: 5px; font-size: 14px;">Contact Phone</div>
                        <div style="color: #333; font-weight: 500;"><?php echo htmlspecialchars($orderDetails['contact_phone']); ?></div>
                    </div>
                </div>
            </div>
            
            <h3>Ordered Items</h3>
            <table style="width: 100%; border-collapse: collapse; margin-bottom: 30px;">
                <thead>
                    <tr>
                        <th style="background-color: #f8f9fa; padding: 12px 15px; text-align: left; color: #555; font-weight: 600;">Product</th>
                        <th style="background-color: #f8f9fa; padding: 12px 15px; text-align: left; color: #555; font-weight: 600;">Price</th>
                        <th style="background-color: #f8f9fa; padding: 12px 15px; text-align: left; color: #555; font-weight: 600;">Quantity</th>
                        <th style="background-color: #f8f9fa; padding: 12px 15px; text-align: left; color: #555; font-weight: 600;">Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $subtotal = 0;
                    foreach ($orderItems as $item): 
                        $itemTotal = $item['price'] * $item['quantity'];
                        $subtotal += $itemTotal;
                    ?>
                    <tr>
                        <td style="padding: 15px; border-bottom: 1px solid #f0f0f0;">
                            <div style="display: flex; align-items: center;">
                                <img src="<?php echo BASE_URL; ?>/assets/images/<?php echo htmlspecialchars($item['image']); ?>" 
                                     alt="<?php echo htmlspecialchars($item['name']); ?>"
                                     style="width: 60px; height: 60px; object-fit: cover; border-radius: 5px; margin-right: 15px;">
                                <div><?php echo htmlspecialchars($item['name']); ?></div>
                            </div>
                        </td>
                        <td style="padding: 15px; border-bottom: 1px solid #f0f0f0;">₱<?php echo number_format($item['price'], 2); ?></td>
                        <td style="padding: 15px; border-bottom: 1px solid #f0f0f0;"><?php echo $item['quantity']; ?></td>
                        <td style="padding: 15px; border-bottom: 1px solid #f0f0f0;">₱<?php echo number_format($itemTotal, 2); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            
            <div style="background-color: #f8f9fa; padding: 20px; border-radius: 8px; margin-bottom: 20px;">
                <div style="display: flex; justify-content: space-between; padding: 10px 0; color: #555;">
                    <span>Subtotal</span>
                    <span>₱<?php echo number_format($subtotal, 2); ?></span>
                </div>
                <div style="display: flex; justify-content: space-between; padding: 10px 0; color: #555;">
                    <span>Shipping Fee</span>
                    <span>₱<?php echo number_format($orderDetails['total_amount'] - $subtotal, 2); ?></span>
                </div>
                <div style="display: flex; justify-content: space-between; padding: 10px 0; color: #333; border-top: 2px solid #f0f0f0; margin-top: 10px; padding-top: 15px; font-weight: bold; font-size: 18px;">
                    <span>Total</span>
                    <span>₱<?php echo number_format($orderDetails['total_amount'], 2); ?></span>
                </div>
            </div>
        </div>
    <?php else: ?>
        <!-- Orders List View -->
        <h1 style="font-size: 2rem; margin-bottom: 30px; color: #333; border-bottom: 2px solid #f0f0f0; padding-bottom: 10px;">My Orders</h1>
        
        <?php if (empty($orders)): ?>
        <div style="text-align: center; padding: 50px 20px; background-color: #f9f9f9; border-radius: 8px; box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);">
            <i class="fas fa-shopping-bag fa-4x" style="color: #ccc; margin-bottom: 20px;"></i>
            <h2 style="font-size: 24px; margin-bottom: 15px; color: #555;">No orders yet</h2>
            <p style="color: #777; margin-bottom: 25px;">You haven't placed any orders with us. Start shopping to see your orders here.</p>
            <a href="<?php echo BASE_URL; ?>/products.php" style="display: inline-block; background-color: #007bff; color: white; padding: 8px 15px; border-radius: 5px; text-decoration: none;">Start Shopping</a>
        </div>
        <?php else: ?>
        <div style="display: grid; gap: 20px;">
            <?php foreach ($orders as $order): ?>
            <div style="background-color: #fff; border-radius: 8px; box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08); padding: 20px; transition: transform 0.3s ease;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px; border-bottom: 1px solid #f0f0f0; padding-bottom: 15px;">
                    <div style="font-size: 18px; font-weight: 600; color: #333;">Order #<?php echo $order['id']; ?></div>
                    <div style="color: #777; font-size: 14px;"><?php echo date("F j, Y", strtotime($order['order_date'])); ?></div>
                </div>
                <div style="display: flex; justify-content: space-between; margin-bottom: 20px;">
                    <div style="font-size: 20px; font-weight: 600; color: #2ecc71;">₱<?php echo number_format($order['total_amount'], 2); ?></div>
                    <span style="display: inline-block; padding: 5px 10px; border-radius: 20px; font-size: 14px; font-weight: 500;
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
                <a href="orders.php?order_id=<?php echo $order['id']; ?>" style="display: inline-block; background-color: #007bff; color: white; padding: 8px 15px; border-radius: 5px; text-decoration: none; transition: background-color 0.3s ease;">View Details</a>
            </div>
            <?php endforeach; ?>
        </div>
        
        <?php if ($totalPages > 1): ?>
        <div style="display: flex; justify-content: center; margin-top: 30px;">
            <?php if ($page > 1): ?>
            <a href="orders.php?page=<?php echo $page - 1; ?>" style="display: inline-block; padding: 8px 16px; margin: 0 5px; border: 1px solid #ddd; color: #333; border-radius: 4px; text-decoration: none;">&laquo; Previous</a>
            <?php endif; ?>
            
            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
            <a href="orders.php?page=<?php echo $i; ?>" 
               style="display: inline-block; padding: 8px 16px; margin: 0 5px; border: 1px solid <?php echo $i == $page ? '#007bff' : '#ddd'; ?>; color: <?php echo $i == $page ? 'white' : '#333'; ?>; background-color: <?php echo $i == $page ? '#007bff' : 'transparent'; ?>; border-radius: 4px; text-decoration: none;">
                <?php echo $i; ?>
            </a>
            <?php endfor; ?>
            
            <?php if ($page < $totalPages): ?>
            <a href="orders.php?page=<?php echo $page + 1; ?>" style="display: inline-block; padding: 8px 16px; margin: 0 5px; border: 1px solid #ddd; color: #333; border-radius: 4px; text-decoration: none;">Next &raquo;</a>
            <?php endif; ?>
        </div>
        <?php endif; ?>
        <?php endif; ?>
    <?php endif; ?>
</div>

<?php
// Include footer
require_once 'includes/footer.php';
?> 