<?php
session_start();
require_once '../includes/config.php';
require_once '../db.php';

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || !isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1) {
    header("Location: ../login.php");
    exit;
}

// Initialize variables
$error = '';
$success = '';
$product = [
    'name' => '',
    'price' => '',
    'description' => '',
    'stock' => '',
    'category_id' => '',
    'featured' => 0
];

// Get all categories
$categories = fetchResults("SELECT * FROM categories ORDER BY name ASC");

// Process form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get form data
    $product = [
        'name' => trim($_POST['name'] ?? ''),
        'price' => floatval($_POST['price'] ?? 0),
        'description' => trim($_POST['description'] ?? ''),
        'stock' => intval($_POST['stock'] ?? 0),
        'category_id' => intval($_POST['category_id'] ?? 0),
        'featured' => isset($_POST['featured']) ? 1 : 0
    ];
    
    // Validate form data
    if (empty($product['name'])) {
        $error = "Product name is required.";
    } elseif ($product['price'] <= 0) {
        $error = "Price must be greater than zero.";
    } else {
        // Handle image upload
        $imageFileName = 'default.jpg'; // Default if no image uploaded
        
        if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
            $allowedTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif'];
            $maxSize = 5 * 1024 * 1024; // 5MB
            
            if (!in_array($_FILES['image']['type'], $allowedTypes)) {
                $error = "Invalid file type. Only JPG, PNG and GIF files are allowed.";
            } elseif ($_FILES['image']['size'] > $maxSize) {
                $error = "File size exceeds limit. Maximum file size is 5MB.";
            } else {
                // Generate unique filename
                $fileName = pathinfo($_FILES['image']['name'], PATHINFO_FILENAME);
                $extension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
                $imageFileName = $fileName . '_' . time() . '.' . $extension;
                
                // Upload path
                $uploadPath = "../assets/images/" . $imageFileName;
                
                // Move uploaded file
                if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadPath)) {
                    // File uploaded successfully
                } else {
                    $error = "Failed to upload image. Please try again.";
                }
            }
        }
        
        if (empty($error)) {
            // Insert product into database
            $sql = "INSERT INTO products (name, price, description, image, stock, category_id, featured, created_at) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, NOW())";
            
            $params = [
                $product['name'],
                $product['price'],
                $product['description'],
                $imageFileName,
                $product['stock'],
                $product['category_id'] ?: null,
                $product['featured']
            ];
            
            $types = "sdssiii";
            
            $affected = modifyData($sql, $params, $types);
            
            if ($affected) {
                $success = "Product has been added successfully.";
                // Reset form
                $product = [
                    'name' => '',
                    'price' => '',
                    'description' => '',
                    'stock' => '',
                    'category_id' => '',
                    'featured' => 0
                ];
            } else {
                $error = "Failed to add product. Please try again.";
            }
        }
    }
}

// Set page title
$pageTitle = "Add Product - Admin Dashboard";
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
            <h1>Add New Product</h1>
            <div class="user">
                <img src="<?php echo BASE_URL; ?>/assets/images/me.jpg" alt="Admin">
                <span><?php echo htmlspecialchars($_SESSION['first_name']); ?></span>
            </div>
        </div>
        
        <!-- Messages -->
        <?php if (!empty($error)): ?>
        <div class="message error"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <?php if (!empty($success)): ?>
        <div class="message success"><?php echo $success; ?></div>
        <?php endif; ?>
        
        <!-- Add Product Form -->
        <form class="admin-form" method="POST" enctype="multipart/form-data">
            <div class="form-row">
                <div class="form-group">
                    <label for="name">Product Name</label>
                    <input type="text" id="name" name="name" class="form-control" value="<?php echo htmlspecialchars($product['name']); ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="price">Price (₱)</label>
                    <input type="number" id="price" name="price" step="0.01" class="form-control" value="<?php echo htmlspecialchars($product['price']); ?>" required>
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="category_id">Category</label>
                    <select id="category_id" name="category_id" class="form-control">
                        <option value="">-- Select Category --</option>
                        <?php foreach ($categories as $category): ?>
                        <option value="<?php echo $category['id']; ?>" <?php echo $product['category_id'] == $category['id'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($category['name']); ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="stock">Stock Quantity</label>
                    <input type="number" id="stock" name="stock" class="form-control" value="<?php echo htmlspecialchars($product['stock']); ?>">
                </div>
            </div>
            
            <div class="form-group">
                <label for="description">Description</label>
                <textarea id="description" name="description" class="form-control" rows="5"><?php echo htmlspecialchars($product['description']); ?></textarea>
            </div>
            
            <div class="form-group">
                <label for="image">Product Image</label>
                <input type="file" id="image" name="image" class="form-control" accept="image/*">
                <small class="form-text">Recommended size: 800x800 pixels. Max size: 5MB. Formats: JPG, PNG, GIF.</small>
            </div>
            
            <div class="form-group">
                <label class="checkbox-label">
                    <input type="checkbox" name="featured" value="1" <?php echo $product['featured'] ? 'checked' : ''; ?>>
                    Featured Product (will be displayed on homepage)
                </label>
            </div>
            
            <div class="form-buttons">
                <a href="products.php" class="btn-secondary">Cancel</a>
                <button type="submit" class="btn-primary">Add Product</button>
            </div>
        </form>
    </div>
    
    <script src="<?php echo BASE_URL; ?>/assets/js/main.js"></script>
</body>
</html> 