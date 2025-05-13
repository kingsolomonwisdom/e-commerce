<?php
session_start();
require_once '../includes/config.php';
require_once '../db.php';

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || !isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1) {
    header("Location: ../login.php");
    exit;
}

// Define variables for messages
$success = '';
$error = '';

// Handle delete action
if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    $productId = (int)$_GET['id'];
    
    // Get product image filename before deletion
    $product = fetchRow("SELECT image FROM products WHERE id = ?", [$productId]);
    
    if ($product) {
        // Delete the product
        $deleted = modifyData("DELETE FROM products WHERE id = ?", [$productId]);
        
        if ($deleted) {
            // Delete the product image if it exists and is not a default image
            $imagePath = "../assets/images/" . $product['image'];
            if (file_exists($imagePath) && $product['image'] != 'default.jpg') {
                unlink($imagePath);
            }
            
            $success = "Product has been deleted successfully.";
        } else {
            $error = "Failed to delete the product.";
        }
    } else {
        $error = "Product not found.";
    }
}

// Get products with pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 10;
$offset = ($page - 1) * $limit;

// Get total number of products
$totalProducts = fetchRow("SELECT COUNT(*) as total FROM products")['total'];
$totalPages = ceil($totalProducts / $limit);

// Get products for current page
$products = fetchResults("SELECT p.*, c.name as category_name 
                         FROM products p 
                         LEFT JOIN categories c ON p.category_id = c.id 
                         ORDER BY p.id DESC LIMIT ? OFFSET ?", 
                         [$limit, $offset], 
                         "ii");

// Set page title
$pageTitle = "Manage Products - Admin Dashboard";
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
            <a href="index.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
            <a href="products.php" class="active"><i class="fas fa-box"></i> Products</a>
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
            <h1>Manage Products</h1>
            <div class="user">
                <img src="<?php echo BASE_URL; ?>/assets/images/me.jpg" alt="Admin">
                <span><?php echo htmlspecialchars($_SESSION['first_name']); ?></span>
            </div>
        </div>
        
        <!-- Messages -->
        <?php if (!empty($success)): ?>
        <div class="message success"><?php echo $success; ?></div>
        <?php endif; ?>
        
        <?php if (!empty($error)): ?>
        <div class="message error"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <!-- Products Table -->
        <div class="table-container">
            <div class="table-header">
                <h2>All Products</h2>
                <a href="add_product.php" class="btn-primary"><i class="fas fa-plus"></i> Add New Product</a>
            </div>
            
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Image</th>
                        <th>Name</th>
                        <th>Category</th>
                        <th>Price</th>
                        <th>Stock</th>
                        <th>Featured</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($products)): ?>
                    <tr>
                        <td colspan="8" style="text-align: center;">No products found.</td>
                    </tr>
                    <?php else: ?>
                        <?php foreach ($products as $product): ?>
                        <tr>
                            <td><?php echo $product['id']; ?></td>
                            <td>
                                <img src="<?php echo BASE_URL; ?>/assets/images/<?php echo htmlspecialchars($product['image']); ?>" 
                                     alt="<?php echo htmlspecialchars($product['name']); ?>" 
                                     style="width: 50px; height: 50px; object-fit: cover;">
                            </td>
                            <td><?php echo htmlspecialchars($product['name']); ?></td>
                            <td><?php echo htmlspecialchars($product['category_name'] ?? 'Uncategorized'); ?></td>
                            <td>₱<?php echo number_format($product['price'], 2); ?></td>
                            <td><?php echo $product['stock'] ?? 'N/A'; ?></td>
                            <td><?php echo $product['featured'] ? 'Yes' : 'No'; ?></td>
                            <td class="actions">
                                <a href="edit_product.php?id=<?php echo $product['id']; ?>" class="edit">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                                <a href="products.php?action=delete&id=<?php echo $product['id']; ?>" 
                                   class="delete" 
                                   onclick="return confirm('Are you sure you want to delete this product?');">
                                    <i class="fas fa-trash"></i> Delete
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
            
            <!-- Pagination -->
            <?php if ($totalPages > 1): ?>
            <div class="pagination">
                <?php if ($page > 1): ?>
                <a href="products.php?page=<?php echo $page - 1; ?>">&laquo; Previous</a>
                <?php endif; ?>
                
                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <a href="products.php?page=<?php echo $i; ?>" <?php echo $i == $page ? 'class="active"' : ''; ?>>
                    <?php echo $i; ?>
                </a>
                <?php endfor; ?>
                
                <?php if ($page < $totalPages): ?>
                <a href="products.php?page=<?php echo $page + 1; ?>">Next &raquo;</a>
                <?php endif; ?>
            </div>
            <?php endif; ?>
        </div>
    </div>
    
    <script src="<?php echo BASE_URL; ?>/assets/js/main.js"></script>
</body>
</html> 