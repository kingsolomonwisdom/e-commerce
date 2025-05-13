<?php
session_start();
require_once 'includes/config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? $pageTitle : 'Shopway'; ?></title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <?php if (isset($extraCSS)): foreach($extraCSS as $css): ?>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/<?php echo $css; ?>.css">
    <?php endforeach; endif; ?>
</head>
<body>
    <header>
        <div class="logo">
            <h3>Shopway</h3>
        </div>
        <nav class="headernav">
            <ul>
                <li><a href="<?php echo BASE_URL; ?>/index.php">Home</a></li>
                <li><a href="<?php echo BASE_URL; ?>/products.php">Products</a></li>
                <li><a href="<?php echo BASE_URL; ?>/about.php">About Us</a></li>
                <li><a href="<?php echo BASE_URL; ?>/contact.php">Contact</a></li>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <li><a href="<?php echo BASE_URL; ?>/account.php">My Account</a></li>
                    <li><a href="<?php echo BASE_URL; ?>/logout.php">Logout</a></li>
                <?php else: ?>
                    <li><a href="<?php echo BASE_URL; ?>/login.php">Login</a></li>
                    <li><a href="<?php echo BASE_URL; ?>/register.php">Register</a></li>
                <?php endif; ?>
            </ul>
        </nav>
        <div class="search-cart">
            <form action="<?php echo BASE_URL; ?>/search.php" method="GET" class="searchbar">
                <input type="text" name="q" placeholder="Search...">
                <button type="submit"><i class="fas fa-search"></i></button>
            </form>
            <a href="<?php echo BASE_URL; ?>/cart.php" class="cart-icon">
                <i class="fas fa-shopping-cart"></i>
                <?php if (isset($_SESSION['cart']) && count($_SESSION['cart']) > 0): ?>
                <span class="cart-count"><?php echo count($_SESSION['cart']); ?></span>
                <?php endif; ?>
            </a>
        </div>
    </header>
    <main class="container"> 