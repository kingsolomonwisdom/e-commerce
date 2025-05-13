<?php
// Page configuration
$extraCSS = ['product'];
$extraJS = ['product'];

// Include header
require_once 'includes/config.php';
require_once 'includes/db.php';
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

<div class="product-container">
    <?php if (!empty($success_message)): ?>
    <div class="message success"><?php echo $success_message; ?></div>
    <?php endif; ?>
    
    <?php if (!empty($error_message)): ?>
    <div class="message error"><?php echo $error_message; ?></div>
    <?php endif; ?>
    
    <div class="product-details">
        <div class="product-gallery">
            <div class="product-main-image-container">
                <img src="<?php echo BASE_URL; ?>/assets/images/<?php echo htmlspecialchars($product['image']); ?>" 
                     alt="<?php echo htmlspecialchars($product['name']); ?>" 
                     class="product-main-image">
            </div>
            
            <!-- Additional product images would go here -->
            <div class="product-thumbnails">
                <img src="<?php echo BASE_URL; ?>/assets/images/<?php echo htmlspecialchars($product['image']); ?>" 
                     alt="<?php echo htmlspecialchars($product['name']); ?>" 
                     class="product-thumbnail active">
                <!-- More thumbnails would be added dynamically -->
            </div>
        </div>
        
        <div class="product-info">
            <h1 class="product-title"><?php echo htmlspecialchars($product['name']); ?></h1>
            
            <div class="product-meta">
                <span class="product-category">
                    <i class="fas fa-tag"></i> 
                    <a href="<?php echo BASE_URL; ?>/products.php?category=<?php echo $product['category_id']; ?>">
                        <?php echo htmlspecialchars($product['category_name'] ?? 'Uncategorized'); ?>
                    </a>
                </span>
                
                <span class="product-stock <?php echo $product['stock'] > 0 ? 'in-stock' : 'out-of-stock'; ?>">
                    <?php if ($product['stock'] > 0): ?>
                        <i class="fas fa-check-circle"></i> In Stock (<?php echo $product['stock']; ?> available)
                    <?php else: ?>
                        <i class="fas fa-times-circle"></i> Out of Stock
                    <?php endif; ?>
                </span>
            </div>
            
            <div class="product-price">
                <span class="price">₱<?php echo number_format($product['price'], 2); ?></span>
            </div>
            
            <div class="product-description">
                <?php echo nl2br(htmlspecialchars($product['description'])); ?>
            </div>
            
            <?php if ($product['stock'] > 0): ?>
            <form action="<?php echo BASE_URL; ?>/add_to_cart.php" method="POST" class="add-to-cart-form">
                <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                
                <div class="quantity-selector">
                    <label for="quantity">Quantity:</label>
                    <div class="quantity-controls">
                        <button type="button" class="quantity-minus">-</button>
                        <input type="number" name="quantity" id="quantity" value="1" min="1" max="<?php echo $product['stock']; ?>" class="quantity-input">
                        <button type="button" class="quantity-plus">+</button>
                    </div>
                </div>
                
                <button type="submit" class="add-to-cart-btn">
                    <i class="fas fa-shopping-cart"></i> Add to Cart
                </button>
            </form>
            <?php else: ?>
            <div class="out-of-stock-actions">
                <button disabled class="add-to-cart-btn disabled">
                    <i class="fas fa-times-circle"></i> Out of Stock
                </button>
                <a href="<?php echo BASE_URL; ?>/products.php" class="browse-products-btn">
                    <i class="fas fa-search"></i> Browse Other Products
                </a>
            </div>
            <?php endif; ?>
            
            <div class="product-actions">
                <a href="#" class="wishlist-btn" data-product-id="<?php echo $product['id']; ?>">
                    <i class="far fa-heart"></i> Add to Wishlist
                </a>
                <a href="#" class="share-btn">
                    <i class="fas fa-share-alt"></i> Share
                </a>
            </div>
        </div>
    </div>
    
    <!-- Product Tabs -->
    <div class="product-tabs">
        <div class="tab-header">
            <button class="tab-btn active" data-tab="details">Product Details</button>
            <button class="tab-btn" data-tab="shipping">Shipping & Returns</button>
            <button class="tab-btn" data-tab="reviews">Reviews</button>
        </div>
        
        <div class="tab-content">
            <div id="details" class="tab-pane active">
                <h3>Product Specifications</h3>
                <div class="product-specs">
                    <p><?php echo nl2br(htmlspecialchars($product['description'])); ?></p>
                    <ul>
                        <!-- Specifications would be dynamically generated here -->
                        <li><strong>Category:</strong> <?php echo htmlspecialchars($product['category_name'] ?? 'Uncategorized'); ?></li>
                        <li><strong>SKU:</strong> PROD-<?php echo $product['id']; ?></li>
                    </ul>
                </div>
            </div>
            
            <div id="shipping" class="tab-pane">
                <h3>Shipping Information</h3>
                <p>We deliver to all areas within the Philippines.</p>
                <p><strong>Standard Shipping:</strong> 3-5 business days (₱150)</p>
                <p><strong>Express Shipping:</strong> 1-2 business days (₱300)</p>
                
                <h3>Return Policy</h3>
                <p>You may return items within 7 days of delivery for a full refund. The product must be unused and in the same condition that you received it. The product must be in the original packaging.</p>
            </div>
            
            <div id="reviews" class="tab-pane">
                <h3>Customer Reviews</h3>
                <div class="reviews-container">
                    <!-- Reviews would be dynamically loaded here -->
                    <p>No reviews yet. Be the first to review this product!</p>
                </div>
                
                <div class="review-form">
                    <h4>Write a Review</h4>
                    <form action="#" method="POST">
                        <div class="form-group">
                            <label for="rating">Rating</label>
                            <div class="rating-select">
                                <i class="far fa-star" data-rating="1"></i>
                                <i class="far fa-star" data-rating="2"></i>
                                <i class="far fa-star" data-rating="3"></i>
                                <i class="far fa-star" data-rating="4"></i>
                                <i class="far fa-star" data-rating="5"></i>
                                <input type="hidden" name="rating" id="rating" value="0">
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="review_title">Title</label>
                            <input type="text" name="review_title" id="review_title" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="review_content">Review</label>
                            <textarea name="review_content" id="review_content" rows="5" required></textarea>
                        </div>
                        
                        <button type="submit" class="submit-review-btn">Submit Review</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Related Products -->
    <?php if (!empty($relatedProducts)): ?>
    <div class="related-products">
        <h2>Related Products</h2>
        <div class="product-grid">
            <?php foreach ($relatedProducts as $related): ?>
            <div class="product-card">
                <a href="<?php echo BASE_URL; ?>/product.php?id=<?php echo $related['id']; ?>" class="product-image-link">
                    <img src="<?php echo BASE_URL; ?>/assets/images/<?php echo htmlspecialchars($related['image']); ?>" 
                         alt="<?php echo htmlspecialchars($related['name']); ?>" 
                         class="product-image">
                </a>
                <div class="product-info">
                    <h3 class="product-title">
                        <a href="<?php echo BASE_URL; ?>/product.php?id=<?php echo $related['id']; ?>">
                            <?php echo htmlspecialchars($related['name']); ?>
                        </a>
                    </h3>
                    <div class="product-price">₱<?php echo number_format($related['price'], 2); ?></div>
                    <div class="product-actions">
                        <a href="<?php echo BASE_URL; ?>/product.php?id=<?php echo $related['id']; ?>" class="view-product">View Details</a>
                        <button class="add-to-cart" onclick="addToCart(<?php echo $related['id']; ?>)">Add to Cart</button>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Tab functionality
    const tabBtns = document.querySelectorAll('.tab-btn');
    const tabPanes = document.querySelectorAll('.tab-pane');
    
    tabBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const tabId = this.getAttribute('data-tab');
            
            // Remove active class from all buttons and panes
            tabBtns.forEach(b => b.classList.remove('active'));
            tabPanes.forEach(p => p.classList.remove('active'));
            
            // Add active class to current button and pane
            this.classList.add('active');
            document.getElementById(tabId).classList.add('active');
        });
    });
    
    // Rating selection
    const stars = document.querySelectorAll('.rating-select i');
    const ratingInput = document.getElementById('rating');
    
    stars.forEach(star => {
        star.addEventListener('mouseover', function() {
            const rating = this.getAttribute('data-rating');
            updateStars(rating);
        });
        
        star.addEventListener('mouseout', function() {
            updateStars(ratingInput.value);
        });
        
        star.addEventListener('click', function() {
            const rating = this.getAttribute('data-rating');
            ratingInput.value = rating;
            updateStars(rating);
        });
    });
    
    function updateStars(rating) {
        stars.forEach(s => {
            const starRating = s.getAttribute('data-rating');
            if (starRating <= rating) {
                s.classList.remove('far');
                s.classList.add('fas');
            } else {
                s.classList.remove('fas');
                s.classList.add('far');
            }
        });
    }
});
</script>

<?php
// Include footer
require_once 'includes/footer.php';
?> 