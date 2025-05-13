<?php 
session_start();
require_once 'db.php';

// Check if user is logged in as admin
if (!isset($_SESSION['user_id']) || !isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1) {
    header("Location: login.php");
    exit;
}

$success = '';
$error = '';

if (isset($_POST['submit'])) {
    $name = trim($_POST['name']);
    $price = floatval($_POST['price']);
    $image = trim($_POST['image']);

    if (empty($name) || $price <= 0 || empty($image)) {
        $error = "Please fill all fields with valid values.";
    } else {
        $sql = "INSERT INTO products (name, price, image, created_at) VALUES (?, ?, ?, NOW())";
        $params = [$name, $price, $image];
        
        $affected = modifyData($sql, $params);
        
        if ($affected) {
            $success = "Product added successfully!";
        } else {
            $error = "Error adding product.";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Product Sample - Shopway Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        h2 {
            color: #333;
        }
        form {
            background: #f9f9f9;
            padding: 20px;
            border-radius: 5px;
        }
        label {
            font-weight: bold;
        }
        input[type="text"],
        input[type="number"] {
            width: 100%;
            padding: 8px;
            margin: 5px 0 20px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        input[type="submit"] {
            background-color: #007bff;
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 4px;
            cursor: pointer;
        }
        input[type="submit"]:hover {
            background-color: #0056b3;
        }
        .success {
            color: green;
            background-color: #f0fff0;
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 20px;
        }
        .error {
            color: red;
            background-color: #fff0f0;
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 20px;
        }
        .back-link {
            display: inline-block;
            margin-top: 20px;
            color: #007bff;
            text-decoration: none;
        }
        .back-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <h2>Sample Product Addition Form</h2>
    
    <?php if (!empty($success)): ?>
    <div class="success"><?php echo $success; ?></div>
    <?php endif; ?>
    
    <?php if (!empty($error)): ?>
    <div class="error"><?php echo $error; ?></div>
    <?php endif; ?>
    
    <p>Note: This is a simple example form. For complete product management, please use the <a href="admin/products.php">Admin Dashboard</a>.</p>
    
    <form action="add_product_sample.php" method="POST">
        <label>Product Name:</label><br>
        <input type="text" name="name" required><br>

        <label>Price:</label><br>
        <input type="number" step="0.01" name="price" required><br>

        <label>Image Filename (e.g., shirt.jpg):</label><br>
        <input type="text" name="image" required><br>

        <input type="submit" name="submit" value="Add Product">
    </form>
    
    <a href="index.php" class="back-link"><i class="fas fa-arrow-left"></i> Back to Homepage</a>
</body>
</html>