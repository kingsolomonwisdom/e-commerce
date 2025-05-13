<?php
session_start();
require_once 'db.php';

// Initialize response array
$response = [
    'success' => false,
    'message' => '',
    'cartCount' => 0
];

// Check if request is AJAX
$isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
          strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';

// Initialize cart if not exists
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Process add to cart request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $productId = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;
    $quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;
    
    // Validate input
    if ($productId <= 0) {
        $response['message'] = 'Invalid product ID';
    } elseif ($quantity <= 0) {
        $response['message'] = 'Quantity must be greater than zero';
    } else {
        // Get product from database
        $product = fetchRow("SELECT * FROM products WHERE id = ?", [$productId]);
        
        if (!$product) {
            $response['message'] = 'Product not found';
        } else {
            // Check stock (if tracking stock)
            if (isset($product['stock']) && $product['stock'] !== null) {
                // Get current quantity in cart
                $currentQty = 0;
                if (isset($_SESSION['cart'][$productId])) {
                    $currentQty = $_SESSION['cart'][$productId]['quantity'];
                }
                
                // Calculate new total quantity
                $newTotalQty = $currentQty + $quantity;
                
                // Check if we have enough stock
                if ($newTotalQty > $product['stock']) {
                    $response['message'] = 'Not enough stock available. Only ' . $product['stock'] . ' item(s) left.';
                    
                    // If we already have items in cart and trying to add more
                    if ($currentQty > 0) {
                        $response['message'] .= ' You already have ' . $currentQty . ' in your cart.';
                    }
                    
                    // Output response and exit
                    if ($isAjax) {
                        header('Content-Type: application/json');
                        echo json_encode($response);
                    } else {
                        $_SESSION['error_message'] = $response['message'];
                        header("Location: product.php?id=$productId");
                    }
                    exit;
                }
            }
            
            // Add product to cart
            if (isset($_SESSION['cart'][$productId])) {
                // Product already in cart, update quantity
                $_SESSION['cart'][$productId]['quantity'] += $quantity;
            } else {
                // New product, add to cart
                $_SESSION['cart'][$productId] = [
                    'id' => $productId,
                    'name' => $product['name'],
                    'price' => $product['price'],
                    'image' => $product['image'],
                    'quantity' => $quantity
                ];
            }
            
            // Success response
            $response['success'] = true;
            $response['message'] = 'Product added to cart successfully!';
            $response['cartCount'] = count($_SESSION['cart']);
        }
    }
}

// Send response
if ($isAjax) {
    header('Content-Type: application/json');
    echo json_encode($response);
} else {
    if ($response['success']) {
        $_SESSION['success_message'] = $response['message'];
    } else {
        $_SESSION['error_message'] = $response['message'];
    }
    
    // Redirect back to product page or referrer
    $redirect = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'index.php';
    header("Location: $redirect");
}
exit;
?> 