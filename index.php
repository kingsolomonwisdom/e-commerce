<?php
// Page configuration
$pageTitle = "Shopway - Your One-Stop Shop";
$extraCSS = ['home'];
$extraJS = ['slideshow'];

// Include header
require_once 'includes/config.php';
require_once 'includes/db.php';
require_once 'includes/header.php';

// Get featured products
$featuredProducts = fetchResults("SELECT * FROM products WHERE featured = 1 ORDER BY created_at DESC LIMIT 8");

// Get latest products
$latestProducts = fetchResults("SELECT * FROM products ORDER BY created_at DESC LIMIT 12");

// Get all categories
$categories = fetchResults("SELECT * FROM categories ORDER BY name ASC LIMIT 8");
?>

<!-- Hero Slideshow Section -->
<div class="slideshow-container">
    <div class="slide fade">
        <img src="<?php echo BASE_URL; ?>/assets/images/slide1.jpg" alt="Special Offers">
        <div class="slide-content">
            <h2>Special Offers</h2>
            <p>Get up to 50% off on selected items</p>
            <a href="<?php echo BASE_URL; ?>/products.php?sort=price_low" class="btn">Shop Now</a>
        </div>
    </div>
    
    <div class="slide fade">
        <img src="<?php echo BASE_URL; ?>/assets/images/slide2.jpg" alt="New Arrivals">
        <div class="slide-content">
            <h2>New Arrivals</h2>
            <p>Check out our latest products</p>
            <a href="<?php echo BASE_URL; ?>/products.php?sort=newest" class="btn">Explore</a>
        </div>
    </div>
    
    <div class="slide fade">
        <img src="<?php echo BASE_URL; ?>/assets/images/slide3.jpg" alt="Limited Edition">
        <div class="slide-content">
            <h2>Limited Edition Items</h2>
            <p>Exclusive products for a limited time only</p>
            <a href="<?php echo BASE_URL; ?>/products.php?category=3" class="btn">View Collection</a>
        </div>
    </div>
    
    <!-- Next and previous buttons -->
    <a class="prev">&#10094;</a>
    <a class="next">&#10095;</a>
    
    <!-- The dots/circles -->
    <div class="dots-container">
        <span class="dot"></span>
        <span class="dot"></span>
        <span class="dot"></span>
    </div>
</div>

<!-- Categories Section -->
<section class="home-section categories-section">
    <div class="section-header">
        <h2>Shop by Category</h2>
        <a href="<?php echo BASE_URL; ?>/products.php" class="view-all">View All</a>
    </div>
    
    <div class="categories-grid">
        <?php foreach ($categories as $category): ?>
        <a href="<?php echo BASE_URL; ?>/products.php?category=<?php echo $category['id']; ?>" class="category-card">
            <div class="category-icon">
                <i class="fas fa-<?php echo getCategoryIcon($category['name']); ?>"></i>
            </div>
            <h3><?php echo htmlspecialchars($category['name']); ?></h3>
        </a>
        <?php endforeach; ?>
    </div>
</section>

<!-- Featured Products Section -->
<section class="home-section featured-products">
    <div class="section-header">
        <h2>Featured Products</h2>
        <a href="<?php echo BASE_URL; ?>/products.php?featured=1" class="view-all">View All</a>
    </div>
    
    <div class="product-grid">
        <?php foreach ($featuredProducts as $product): ?>
        <div class="product-card">
            <a href="<?php echo BASE_URL; ?>/product.php?id=<?php echo $product['id']; ?>" class="product-image-link">
                <img src="<?php echo BASE_URL; ?>/assets/images/<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" class="product-image">
            </a>
            <div class="product-info">
                <h3 class="product-title">
                    <a href="<?php echo BASE_URL; ?>/product.php?id=<?php echo $product['id']; ?>">
                        <?php echo htmlspecialchars($product['name']); ?>
                    </a>
                </h3>
                <div class="product-price">₱<?php echo number_format($product['price'], 2); ?></div>
                <div class="product-actions">
                    <a href="<?php echo BASE_URL; ?>/product.php?id=<?php echo $product['id']; ?>" class="view-product">View Details</a>
                    <button class="add-to-cart" onclick="addToCart(<?php echo $product['id']; ?>)">Add to Cart</button>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</section>

<!-- Latest Products Section -->
<section class="home-section latest-products">
    <div class="section-header">
        <h2>Latest Products</h2>
        <a href="<?php echo BASE_URL; ?>/products.php?sort=newest" class="view-all">View All</a>
    </div>
    
    <div class="product-grid">
        <?php foreach ($latestProducts as $product): ?>
        <div class="product-card">
            <a href="<?php echo BASE_URL; ?>/product.php?id=<?php echo $product['id']; ?>" class="product-image-link">
                <img src="<?php echo BASE_URL; ?>/assets/images/<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" class="product-image">
            </a>
            <div class="product-info">
                <h3 class="product-title">
                    <a href="<?php echo BASE_URL; ?>/product.php?id=<?php echo $product['id']; ?>">
                        <?php echo htmlspecialchars($product['name']); ?>
                    </a>
                </h3>
                <div class="product-price">₱<?php echo number_format($product['price'], 2); ?></div>
                <div class="product-actions">
                    <a href="<?php echo BASE_URL; ?>/product.php?id=<?php echo $product['id']; ?>" class="view-product">View Details</a>
                    <button class="add-to-cart" onclick="addToCart(<?php echo $product['id']; ?>)">Add to Cart</button>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</section>

<!-- Call-to-Action Section -->
<section class="cta-section">
    <div class="cta-content">
        <h2>Subscribe for Exclusive Offers</h2>
        <p>Be the first to know about new products and special promotions</p>
        <form class="subscribe-form" action="#" method="POST">
            <input type="email" placeholder="Your email address" required>
            <button type="submit" class="btn">Subscribe</button>
        </form>
    </div>
</section>

<?php
// Helper function to get category icon based on name
function getCategoryIcon($categoryName) {
    $name = strtolower($categoryName);
    
    if (strpos($name, 'electron') !== false) {
        return 'laptop';
    } elseif (strpos($name, 'fashion') !== false || strpos($name, 'cloth') !== false) {
        return 'tshirt';
    } elseif (strpos($name, 'access') !== false) {
        return 'gem';
    } elseif (strpos($name, 'home') !== false || strpos($name, 'living') !== false) {
        return 'couch';
    } elseif (strpos($name, 'beauty') !== false) {
        return 'spa';
    } elseif (strpos($name, 'sport') !== false) {
        return 'futbol';
    } elseif (strpos($name, 'toy') !== false) {
        return 'gamepad';
    } elseif (strpos($name, 'book') !== false) {
        return 'book';
    } else {
        return 'tag';
    }
}

// Include footer
require_once 'includes/footer.php';
?> 