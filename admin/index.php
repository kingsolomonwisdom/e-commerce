<?php
session_start();
require_once '../includes/config.php';
require_once '../db.php';

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || !isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1) {
    header("Location: ../login.php");
    exit;
}

// Get dashboard statistics
$totalSales = fetchRow("SELECT SUM(total_amount) as sales FROM orders WHERE status != 'Cancelled'")['sales'] ?? 0;
$totalOrders = fetchRow("SELECT COUNT(*) as count FROM orders")['count'] ?? 0;
$totalProducts = fetchRow("SELECT COUNT(*) as count FROM products")['count'] ?? 0;
$totalCustomers = fetchRow("SELECT COUNT(*) as count FROM users WHERE is_admin = 0")['count'] ?? 0;
$lowStockProducts = fetchRow("SELECT COUNT(*) as count FROM products WHERE stock <= 5 AND stock > 0")['count'] ?? 0;
$outOfStockProducts = fetchRow("SELECT COUNT(*) as count FROM products WHERE stock = 0")['count'] ?? 0;

// Get recent orders
$recentOrders = fetchResults("SELECT o.*, u.first_name, u.last_name 
                            FROM orders o 
                            JOIN users u ON o.user_id = u.id 
                            ORDER BY o.order_date DESC LIMIT 5");

// Get top selling products
$topProducts = fetchResults("SELECT p.*, SUM(oi.quantity) as total_sold 
                           FROM order_items oi 
                           JOIN products p ON oi.product_id = p.id 
                           GROUP BY p.id 
                           ORDER BY total_sold DESC LIMIT 5");

// Set page title
$pageTitle = "Admin Dashboard - Shopway";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?></title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="logo">
            <span class="bold">Shopway</span>Dashboard
        </div>
        <div class="menu">
            <a href="index.php" class="active"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
            <a href="products.php"><i class="fas fa-box"></i> Products</a>
            <a href="categories.php"><i class="fas fa-tags"></i> Categories</a>
            <a href="orders.php"><i class="fas fa-shopping-cart"></i> Orders</a>
            <a href="customers.php"><i class="fas fa-users"></i> Customers</a>
            <a href="reports.php"><i class="fas fa-chart-bar"></i> Reports</a>
            <a href="settings.php"><i class="fas fa-cog"></i> Settings</a>
            <a href="../logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </div>
    </div>
    
    <!-- Main Content -->
    <div class="main-content">
        <div class="header">
            <h1>Welcome back, <?php echo htmlspecialchars($_SESSION['first_name']); ?></h1>
            <div class="user">
                <img src="<?php echo BASE_URL; ?>/assets/images/me.jpg" alt="Admin">
                <span><?php echo htmlspecialchars($_SESSION['first_name']); ?></span>
            </div>
        </div>
        
        <!-- Stats Cards -->
        <div class="cards">
            <div class="card">
                <div class="icon">📊</div>
                <h3>Total Sales</h3>
                <p>₱<?php echo number_format($totalSales, 2); ?></p>
            </div>
            <div class="card">
                <div class="icon">📦</div>
                <h3>Orders</h3>
                <p><?php echo $totalOrders; ?></p>
            </div>
            <div class="card">
                <div class="icon">🛒</div>
                <h3>Products</h3>
                <p><?php echo $totalProducts; ?></p>
            </div>
            <div class="card">
                <div class="icon">👥</div>
                <h3>Customers</h3>
                <p><?php echo $totalCustomers; ?></p>
            </div>
        </div>
        
        <div class="dashboard-sections">
            <!-- Recent Orders -->
            <div class="table-container">
                <div class="table-header">
                    <h2>Recent Orders</h2>
                    <a href="orders.php" class="btn-secondary">View All</a>
                </div>
                
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Customer</th>
                            <th>Date</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($recentOrders)): ?>
                        <tr>
                            <td colspan="6" style="text-align: center;">No orders found.</td>
                        </tr>
                        <?php else: ?>
                            <?php foreach ($recentOrders as $order): ?>
                            <tr>
                                <td>#<?php echo $order['id']; ?></td>
                                <td><?php echo htmlspecialchars($order['first_name'] . ' ' . $order['last_name']); ?></td>
                                <td><?php echo date('M d, Y', strtotime($order['order_date'])); ?></td>
                                <td>₱<?php echo number_format($order['total_amount'], 2); ?></td>
                                <td>
                                    <span class="status-badge status-<?php echo strtolower($order['status']); ?>">
                                        <?php echo $order['status']; ?>
                                    </span>
                                </td>
                                <td>
                                    <a href="view_order.php?id=<?php echo $order['id']; ?>" class="view">View</a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            
            <!-- Top Selling Products -->
            <div class="table-container">
                <div class="table-header">
                    <h2>Top Selling Products</h2>
                    <a href="products.php" class="btn-secondary">View All Products</a>
                </div>
                
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Image</th>
                            <th>Product</th>
                            <th>Price</th>
                            <th>Stock</th>
                            <th>Sold</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($topProducts)): ?>
                        <tr>
                            <td colspan="5" style="text-align: center;">No products found.</td>
                        </tr>
                        <?php else: ?>
                            <?php foreach ($topProducts as $product): ?>
                            <tr>
                                <td>
                                    <img src="<?php echo BASE_URL; ?>/assets/images/<?php echo htmlspecialchars($product['image']); ?>" 
                                         alt="<?php echo htmlspecialchars($product['name']); ?>" 
                                         style="width: 40px; height: 40px; object-fit: cover;">
                                </td>
                                <td><?php echo htmlspecialchars($product['name']); ?></td>
                                <td>₱<?php echo number_format($product['price'], 2); ?></td>
                                <td><?php echo $product['stock'] ?? 'N/A'; ?></td>
                                <td><?php echo $product['total_sold']; ?></td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        
        <!-- Inventory Stats -->
        <div class="cards">
            <div class="card">
                <div class="icon" style="color: #dc3545;">⚠️</div>
                <h3>Low Stock Products</h3>
                <p><?php echo $lowStockProducts; ?></p>
                <a href="products.php?filter=low_stock" class="btn-secondary" style="margin-top: 10px; display: inline-block;">View Products</a>
            </div>
            <div class="card">
                <div class="icon" style="color: #dc3545;">❌</div>
                <h3>Out of Stock</h3>
                <p><?php echo $outOfStockProducts; ?></p>
                <a href="products.php?filter=out_of_stock" class="btn-secondary" style="margin-top: 10px; display: inline-block;">View Products</a>
            </div>
        </div>
    </div>
    
    <script src="<?php echo BASE_URL; ?>/assets/js/main.js"></script>
</body>
</html> 