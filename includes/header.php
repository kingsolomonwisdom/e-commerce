<?php
session_start();
require_once 'db.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? $pageTitle : 'Shopway'; ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Reset and common styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Arial', sans-serif;
        }

        body {
            background-color: #f9f9f9;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        a {
            text-decoration: none;
            color: inherit;
        }

        ul {
            list-style-type: none;
        }

        .container {
            width: 100%;
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
            flex: 1;
        }

        /* Header styles */
        header {
            background-color: #111;
            color: white;
            padding: 15px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: sticky;
            top: 0;
            z-index: 100;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
        }

        .logo h3 {
            color: yellow;
            font-size: 24px;
            font-weight: bold;
        }

        .headernav ul {
            display: flex;
            gap: 20px;
        }

        .headernav a {
            color: white;
            opacity: 0.7;
            transition: opacity 0.3s ease;
            padding: 5px 0;
        }

        .headernav a:hover {
            opacity: 1;
            border-bottom: 2px solid yellow;
        }

        .search-cart {
            display: flex;
            align-items: center;
        }

        .searchbar {
            display: flex;
            background-color: rgba(255, 255, 255, 0.1);
            border-radius: 20px;
            padding: 8px 15px;
            margin-right: 15px;
        }

        .searchbar input {
            background: transparent;
            border: none;
            outline: none;
            color: white;
            width: 200px;
        }

        .searchbar button {
            background: transparent;
            border: none;
            color: white;
            cursor: pointer;
        }

        .cart-icon {
            color: white;
            font-size: 20px;
            position: relative;
        }

        .cart-count {
            position: absolute;
            top: -8px;
            right: -8px;
            background-color: yellow;
            color: black;
            border-radius: 50%;
            font-size: 10px;
            width: 16px;
            height: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* Product Card Styles */
        .product-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }

        .product-card {
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }

        .product-image {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }

        .product-info {
            padding: 15px;
        }

        .product-title {
            font-size: 18px;
            margin-bottom: 8px;
        }

        .product-price {
            color: green;
            font-weight: bold;
            margin-bottom: 15px;
        }

        .product-actions {
            display: flex;
            justify-content: space-between;
        }

        .add-to-cart {
            background-color: #007bff;
            color: white;
            border: none;
            padding: 8px 15px;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .add-to-cart:hover {
            background-color: #0056b3;
        }

        /* Responsive styles */
        @media (max-width: 768px) {
            header {
                flex-direction: column;
                gap: 10px;
            }
            
            .search-cart {
                width: 100%;
                justify-content: center;
                margin-top: 10px;
            }
            
            .headernav ul {
                justify-content: center;
                flex-wrap: wrap;
            }
            
            .logo {
                margin-bottom: 10px;
            }
            
            .product-grid {
                grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            }
        }

        @media (max-width: 480px) {
            .headernav ul {
                gap: 10px;
            }
            
            .searchbar {
                width: 100%;
            }
            
            .searchbar input {
                width: 100%;
            }
            
            .product-grid {
                grid-template-columns: 1fr;
            }
        }
        
        /* Notification System */
        .notification-container {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 9999;
        }
        
        .notification {
            display: flex;
            align-items: center;
            background-color: white;
            color: #333;
            border-radius: 8px;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.15);
            padding: 15px 20px;
            margin-bottom: 10px;
            max-width: 350px;
            transform: translateX(400px);
            opacity: 0;
            transition: all 0.3s cubic-bezier(0.18, 0.89, 0.32, 1.28);
        }
        
        .notification.show {
            transform: translateX(0);
            opacity: 1;
        }
        
        .notification .icon {
            margin-right: 15px;
            font-size: 20px;
        }
        
        .notification.success {
            border-left: 4px solid #4CAF50;
        }
        
        .notification.success .icon {
            color: #4CAF50;
        }
        
        .notification.error {
            border-left: 4px solid #F44336;
        }
        
        .notification.error .icon {
            color: #F44336;
        }
        
        .notification.info {
            border-left: 4px solid #2196F3;
        }
        
        .notification.info .icon {
            color: #2196F3;
        }
        
        .notification.warning {
            border-left: 4px solid #FF9800;
        }
        
        .notification.warning .icon {
            color: #FF9800;
        }
        
        .notification .content {
            flex: 1;
        }
        
        .notification .title {
            font-weight: 600;
            margin-bottom: 5px;
        }
        
        .notification .message {
            font-size: 14px;
            color: #555;
        }
        
        .notification .close {
            background: none;
            border: none;
            color: #999;
            font-size: 16px;
            cursor: pointer;
            margin-left: 10px;
            padding: 0;
            line-height: 1;
        }
        
        .notification .close:hover {
            color: #555;
        }
        
        @media (max-width: 480px) {
            .notification-container {
                left: 20px;
                right: 20px;
            }
            
            .notification {
                max-width: none;
            }
        }
        
        <?php if (isset($extraCSS) && is_array($extraCSS)): ?>
            <?php foreach($extraCSS as $cssFile): ?>
                <?php include_once "assets/css/{$cssFile}.css"; ?>
            <?php endforeach; ?>
        <?php endif; ?>
    </style>
</head>
<body>
    <header>
        <div class="logo">
            <h3>Shopway</h3>
        </div>
        <nav class="headernav">
            <ul>
                <li><a href="index.php">Home</a></li>
                <li><a href="products.php">Products</a></li>
                <li><a href="about.php">About Us</a></li>
                <li><a href="contact.php">Contact</a></li>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <li><a href="account.php">My Account</a></li>
                    <li><a href="logout.php">Logout</a></li>
                <?php else: ?>
                    <li><a href="login.php">Login</a></li>
                    <li><a href="register.php">Register</a></li>
                <?php endif; ?>
            </ul>
        </nav>
        <div class="search-cart">
            <form action="search.php" method="GET" class="searchbar">
                <input type="text" name="q" placeholder="Search...">
                <button type="submit"><i class="fas fa-search"></i></button>
            </form>
            <a href="cart.php" class="cart-icon">
                <i class="fas fa-shopping-cart"></i>
                <?php if (isset($_SESSION['cart']) && count($_SESSION['cart']) > 0): ?>
                <span class="cart-count"><?php echo count($_SESSION['cart']); ?></span>
                <?php endif; ?>
            </a>
        </div>
    </header>
    <main class="container"> 