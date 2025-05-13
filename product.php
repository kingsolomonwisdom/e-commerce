<?php
// Page configuration
$extraCSS = ['product'];
$extraJS = ['product'];
$pageTitle = "Product Details - Shopway";

// Include header
require_once 'db.php';
require_once 'includes/header.php';

// Get product ID from URL
$productId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Get product details
$product = fetchRow("SELECT p.*, c.name as category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id WHERE p.id = ?", [$productId]);

// If product not found, redirect to products page
if (!$product) {
    header("Location: products.php");
    exit;
}

// Set page title
$pageTitle = $product['name'] . " - Shopway";

// Get related products from same category
$relatedProducts = fetchResults("SELECT * FROM products WHERE category_id = ? AND id != ? LIMIT 4", 
                             [$product['category_id'], $productId]);

// Handle success and error messages
$success_message = isset($_SESSION['success_message']) ? $_SESSION['success_message'] : '';
$error_message = isset($_SESSION['error_message']) ? $_SESSION['error_message'] : '';

// Clear session messages
if (isset($_SESSION['success_message'])) unset($_SESSION['success_message']);
if (isset($_SESSION['error_message'])) unset($_SESSION['error_message']);
?>

<style>
/* Individual Product Page Styles */
.message {
    padding: 15px;
    margin-bottom: 20px;
    border-radius: 5px;
    text-align: center;
    animation: fadeOut 5s forwards;
}

@keyframes fadeOut {
    0% { opacity: 1; }
    70% { opacity: 1; }
    100% { opacity: 0; }
}

.message.success {
    background-color: rgba(76, 175, 80, 0.1);
    border-left: 4px solid #4CAF50;
    color: #2e7031;
}

.message.error {
    background-color: rgba(244, 67, 54, 0.1);
    border-left: 4px solid #F44336;
    color: #c62828;
}

/* Breadcrumbs */
.breadcrumbs {
    display: flex;
    margin-bottom: 20px;
    padding: 10px 0;
    font-size: 14px;
    color: #6c757d;
}

.breadcrumbs a {
    color: #007bff;
    text-decoration: none;
}

.breadcrumbs a:hover {
    text-decoration: underline;
}

.breadcrumbs .separator {
    margin: 0 10px;
    color: #ccc;
}

/* Product Details Layout */
.product-details {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 40px;
    margin-bottom: 50px;
    background-color: #fff;
    border-radius: 10px;
    box-shadow: 0 3px 15px rgba(0, 0, 0, 0.08);
    padding: 30px;
}

/* Product Gallery */
.product-gallery {
    position: sticky;
    top: 100px;
}

.product-main-image-container {
    margin-bottom: 15px;
    border-radius: 10px;
    overflow: hidden;
    background-color: #f8f9fa;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.product-main-image-container:hover {
    transform: translateY(-3px);
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
}

.product-main-image {
    width: 100%;
    height: 400px;
    object-fit: contain;
    display: block;
}

.product-thumbnails {
    display: flex;
    gap: 10px;
    justify-content: center;
}

.product-thumbnail {
    width: 80px;
    height: 80px;
    object-fit: cover;
    border-radius: 5px;
    cursor: pointer;
    opacity: 0.7;
    transition: all 0.3s ease;
    border: 2px solid transparent;
}

.product-thumbnail:hover {
    opacity: 1;
    transform: scale(1.05);
}

.product-thumbnail.active {
    opacity: 1;
    border-color: #007bff;
}

/* Product Info */
.product-title {
    font-size: 32px;
    margin-bottom: 15px;
    color: #333;
}

.product-meta {
    display: flex;
    justify-content: space-between;
    margin-bottom: 20px;
    font-size: 14px;
}

.product-category {
    color: #6c757d;
}

.product-category a {
    color: #007bff;
    text-decoration: none;
}

.product-category a:hover {
    text-decoration: underline;
}

.product-stock {
    padding: 5px 12px;
    border-radius: 20px;
    font-weight: 500;
}

.product-stock.in-stock {
    color: #28a745;
    background-color: rgba(40, 167, 69, 0.1);
}

.product-stock.out-of-stock {
    color: #dc3545;
    background-color: rgba(220, 53, 69, 0.1);
}

.product-price {
    margin: 25px 0;
    display: flex;
    align-items: center;
}

.price {
    font-size: 32px;
    font-weight: bold;
    color: #28a745;
}

.product-description {
    margin-bottom: 30px;
    line-height: 1.7;
    color: #555;
    padding: 20px 0;
    border-top: 1px solid #eee;
    border-bottom: 1px solid #eee;
}

/* Add to Cart Form */
.add-to-cart-form {
    margin-bottom: 30px;
}

.quantity-selector {
    margin-bottom: 20px;
}

.quantity-selector label {
    display: block;
    margin-bottom: 12px;
    font-weight: 500;
    color: #555;
}

.quantity-controls {
    display: flex;
    align-items: center;
    max-width: 150px;
}

.quantity-minus,
.quantity-plus {
    width: 40px;
    height: 40px;
    background-color: #f8f9fa;
    border: 1px solid #dee2e6;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 18px;
    font-weight: bold;
    cursor: pointer;
    transition: all 0.3s ease;
}

.quantity-minus:hover,
.quantity-plus:hover {
    background-color: #e9ecef;
}

.quantity-input {
    width: 60px;
    height: 40px;
    border: 1px solid #dee2e6;
    text-align: center;
    font-size: 16px;
    margin: 0 10px;
    padding: 0 5px;
}

.add-to-cart-btn {
    width: 100%;
    padding: 15px 0;
    background-color: #007bff;
    color: white;
    border: none;
    border-radius: 5px;
    font-size: 16px;
    font-weight: 500;
    cursor: pointer;
    transition: background-color 0.3s ease;
    display: flex;
    align-items: center;
    justify-content: center;
}

.add-to-cart-btn:hover {
    background-color: #0056b3;
}

.add-to-cart-btn i {
    margin-right: 10px;
}

/* Additional Info Tabs */
.product-tabs {
    margin-bottom: 50px;
}

.tabs-header {
    display: flex;
    border-bottom: 1px solid #dee2e6;
    margin-bottom: 20px;
}

.tab-link {
    padding: 10px 20px;
    cursor: pointer;
    font-weight: 500;
    color: #6c757d;
    position: relative;
}

.tab-link.active {
    color: #007bff;
}

.tab-link.active::after {
    content: '';
    position: absolute;
    bottom: -1px;
    left: 0;
    width: 100%;
    height: 2px;
    background-color: #007bff;
}

.tab-content {
    display: none;
    padding: 20px 0;
}

.tab-content.active {
    display: block;
}

/* Related Products */
.related-products {
    margin-top: 50px;
}

.related-products-title {
    font-size: 24px;
    margin-bottom: 20px;
    padding-bottom: 10px;
    border-bottom: 1px solid #dee2e6;
    color: #333;
}

.related-products-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
    gap: 20px;
}

/* Customer Reviews */
.reviews-section {
    margin-top: 40px;
}

.reviews-title {
    font-size: 20px;
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.add-review-btn {
    background-color: transparent;
    color: #007bff;
    border: 1px solid #007bff;
    padding: 8px 15px;
    border-radius: 5px;
    font-size: 14px;
    cursor: pointer;
    transition: all 0.3s ease;
}

.add-review-btn:hover {
    background-color: #007bff;
    color: white;
}

.review-card {
    background-color: #f8f9fa;
    border-radius: 8px;
    padding: 20px;
    margin-bottom: 20px;
}

.review-header {
    display: flex;
    justify-content: space-between;
    margin-bottom: 10px;
}

.reviewer-name {
    font-weight: 500;
}

.review-date {
    color: #6c757d;
    font-size: 14px;
}

.review-rating {
    margin-bottom: 15px;
    color: #ffc107;
}

.review-text {
    color: #555;
    line-height: 1.5;
}

/* Responsiveness */
@media (max-width: 991px) {
    .product-details {
        grid-template-columns: 1fr;
    }
    
    .product-gallery {
        position: static;
        margin-bottom: 30px;
    }
}

@media (max-width: 768px) {
    .related-products-grid {
        grid-template-columns: repeat(2, 1fr);
    }
    
    .tabs-header {
        flex-wrap: wrap;
    }
    
    .breadcrumbs {
        flex-wrap: wrap;
    }
}

@media (max-width: 576px) {
    .product-thumbnails {
        flex-wrap: wrap;
    }
    
    .related-products-grid {
        grid-template-columns: 1fr;
    }
    
    .product-title {
        font-size: 24px;
    }
    
    .price {
        font-size: 24px;
    }
}
</style>

<!-- Breadcrumbs -->
<div class="breadcrumbs">
    <a href="index.php">Home</a>
    <span class="separator">›</span>
    <a href="products.php">Products</a>
    <span class="separator">›</span>
    <?php if ($product['category_name']): ?>
    <a href="products.php?category=<?php echo $product['category_id']; ?>"><?php echo htmlspecialchars($product['category_name']); ?></a>
    <span class="separator">›</span>
    <?php endif; ?>
    <span><?php echo htmlspecialchars($product['name']); ?></span>
</div>

<?php if ($success_message): ?>
<div class="message success"><?php echo $success_message; ?></div>
<?php endif; ?>

<?php if ($error_message): ?>
<div class="message error"><?php echo $error_message; ?></div>
<?php endif; ?>

<div class="product-details">
    <!-- Product Gallery -->
    <div class="product-gallery">
        <div class="product-main-image-container">
            <img src="<?php echo BASE_URL; ?>/assets/images/<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" class="product-main-image">
        </div>
        <div class="product-thumbnails">
            <img src="<?php echo BASE_URL; ?>/assets/images/<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" class="product-thumbnail active">
            <!-- Additional thumbnails would go here -->
        </div>
    </div>
    
    <!-- Product Info -->
    <div class="product-info">
        <h1 class="product-title"><?php echo htmlspecialchars($product['name']); ?></h1>
        
        <div class="product-meta">
            <?php if ($product['category_name']): ?>
            <div class="product-category">
                Category: <a href="products.php?category=<?php echo $product['category_id']; ?>"><?php echo htmlspecialchars($product['category_name']); ?></a>
            </div>
            <?php endif; ?>
            
            <div class="product-stock <?php echo $product['stock'] > 0 ? 'in-stock' : 'out-of-stock'; ?>">
                <?php echo $product['stock'] > 0 ? 'In Stock' : 'Out of Stock'; ?>
            </div>
        </div>
        
        <div class="product-price">
            <span class="price">₱<?php echo number_format($product['price'], 2); ?></span>
        </div>
        
        <div class="product-description">
            <?php echo nl2br(htmlspecialchars($product['description'])); ?>
        </div>
        
        <?php if ($product['stock'] > 0): ?>
        <!-- Add to Cart Form -->
        <form action="add_to_cart.php" method="POST" class="add-to-cart-form">
            <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
            
            <div class="quantity-selector">
                <label for="quantity">Quantity:</label>
                <div class="quantity-controls">
                    <button type="button" class="quantity-minus">-</button>
                    <input type="number" id="quantity" name="quantity" value="1" min="1" max="<?php echo $product['stock']; ?>" class="quantity-input">
                    <button type="button" class="quantity-plus">+</button>
                </div>
            </div>
            
            <button type="submit" class="add-to-cart-btn">
                <i class="fas fa-shopping-cart"></i> Add to Cart
            </button>
        </form>
        <?php else: ?>
        <div class="out-of-stock-message">
            <p>This item is currently out of stock. Please check back later.</p>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- Responsive fix for db.php location -->
<script>
    // Product quantity selector
    document.addEventListener('DOMContentLoaded', function() {
        const minusBtn = document.querySelector('.quantity-minus');
        const plusBtn = document.querySelector('.quantity-plus');
        const quantityInput = document.querySelector('.quantity-input');
        
        if (minusBtn && plusBtn && quantityInput) {
            minusBtn.addEventListener('click', function() {
                const currentValue = parseInt(quantityInput.value);
                if (currentValue > 1) {
                    quantityInput.value = currentValue - 1;
                }
            });
            
            plusBtn.addEventListener('click', function() {
                const currentValue = parseInt(quantityInput.value);
                const maxValue = parseInt(quantityInput.getAttribute('max'));
                if (currentValue < maxValue) {
                    quantityInput.value = currentValue + 1;
                }
            });
        }
    });
</script>

<!-- Related Products Section -->
<?php if (!empty($relatedProducts)): ?>
<section class="related-products">
    <h2 class="related-products-title">Related Products</h2>
    
    <div class="related-products-grid">
        <?php foreach ($relatedProducts as $relatedProduct): ?>
        <div class="product-card">
            <a href="product.php?id=<?php echo $relatedProduct['id']; ?>" class="product-image-link">
                <img src="<?php echo BASE_URL; ?>/assets/images/<?php echo htmlspecialchars($relatedProduct['image']); ?>" alt="<?php echo htmlspecialchars($relatedProduct['name']); ?>" class="product-image">
            </a>
            <div class="product-info">
                <h3 class="product-title">
                    <a href="product.php?id=<?php echo $relatedProduct['id']; ?>">
                        <?php echo htmlspecialchars($relatedProduct['name']); ?>
                    </a>
                </h3>
                <div class="product-price">₱<?php echo number_format($relatedProduct['price'], 2); ?></div>
                <div class="product-actions">
                    <a href="product.php?id=<?php echo $relatedProduct['id']; ?>" class="view-product">View Details</a>
                    <button class="add-to-cart" onclick="addToCart(<?php echo $relatedProduct['id']; ?>)">Add to Cart</button>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</section>
<?php endif; ?>

<?php
// Include footer
require_once 'includes/footer.php';
?> 