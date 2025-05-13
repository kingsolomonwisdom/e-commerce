<?php
// Page configuration
$pageTitle = "Shopway - Your One-Stop Shop";

// Include necessary files
require_once 'db.php';
require_once 'includes/header.php';

// Get featured products
$featuredProducts = fetchResults("SELECT * FROM products WHERE featured = 1 ORDER BY created_at DESC LIMIT 8");

// Get latest products
$latestProducts = fetchResults("SELECT * FROM products ORDER BY created_at DESC LIMIT 12");

// Get all categories
$categories = fetchResults("SELECT * FROM categories ORDER BY name ASC LIMIT 8");
?>

<style>
/* Slideshow Styles */
.slideshow-container {
    position: relative;
    width: 100%;
    max-height: 500px;
    overflow: hidden;
    margin-bottom: 30px;
}

.slide {
    display: none;
    width: 100%;
}

.slide img {
    width: 100%;
    height: 500px;
    object-fit: cover;
}

.slide-content {
    position: absolute;
    top: 50%;
    left: 10%;
    transform: translateY(-50%);
    color: white;
    text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.7);
    max-width: 500px;
}

.slide-content h2 {
    font-size: 3rem;
    margin-bottom: 15px;
}

.slide-content p {
    font-size: 1.2rem;
    margin-bottom: 20px;
}

.btn {
    display: inline-block;
    background-color: #007bff;
    color: white;
    padding: 10px 25px;
    border-radius: 25px;
    text-decoration: none;
    font-weight: bold;
    transition: background-color 0.3s ease;
}

.btn:hover {
    background-color: #0056b3;
}

.prev, .next {
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    font-size: 30px;
    font-weight: bold;
    color: white;
    background-color: rgba(0, 0, 0, 0.5);
    padding: 10px 15px;
    cursor: pointer;
    border-radius: 5px;
    transition: background-color 0.3s ease;
}

.prev {
    left: 20px;
}

.next {
    right: 20px;
}

.dots-container {
    text-align: center;
    position: absolute;
    bottom: 20px;
    width: 100%;
}

.dot {
    display: inline-block;
    width: 12px;
    height: 12px;
    margin: 0 5px;
    background-color: rgba(255, 255, 255, 0.5);
    border-radius: 50%;
    cursor: pointer;
    transition: background-color 0.3s ease;
}

.dot.active, .dot:hover {
    background-color: white;
}

/* Home Sections */
.home-section {
    margin-bottom: 50px;
}

.section-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
    border-bottom: 1px solid #eee;
    padding-bottom: 10px;
}

.section-header h2 {
    font-size: 1.8rem;
    color: #333;
    margin: 0;
}

.view-all {
    color: #007bff;
    text-decoration: none;
    font-weight: bold;
}

.view-all:hover {
    text-decoration: underline;
}

/* Categories Section */
.categories-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
    gap: 20px;
}

.category-card {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    height: 150px;
    background-color: white;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    border-radius: 8px;
    text-decoration: none;
    color: #333;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.category-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
}

.category-icon {
    font-size: 40px;
    margin-bottom: 15px;
    color: #007bff;
}

.category-card h3 {
    font-size: 1.2rem;
    text-align: center;
}

/* CTA Section */
.cta-section {
    background-color: #007bff;
    color: white;
    padding: 50px 0;
    text-align: center;
    margin-bottom: 50px;
}

.cta-content {
    max-width: 800px;
    margin: 0 auto;
}

.cta-content h2 {
    font-size: 2rem;
    margin-bottom: 15px;
}

.cta-content p {
    font-size: 1.1rem;
    margin-bottom: 30px;
}

.subscribe-form {
    display: flex;
    max-width: 500px;
    margin: 0 auto;
}

.subscribe-form input {
    flex: 1;
    padding: 12px 15px;
    border: none;
    border-radius: 25px 0 0 25px;
    font-size: 1rem;
}

.subscribe-form .btn {
    border-radius: 0 25px 25px 0;
    padding: 12px 25px;
}

/* Responsive styles */
@media (max-width: 768px) {
    .slide-content h2 {
        font-size: 2rem;
    }
    
    .slide-content p {
        font-size: 1rem;
    }
    
    .categories-grid {
        grid-template-columns: repeat(2, 1fr);
    }
    
    .subscribe-form {
        flex-direction: column;
        padding: 0 20px;
    }
    
    .subscribe-form input {
        border-radius: 25px;
        margin-bottom: 10px;
    }
    
    .subscribe-form .btn {
        border-radius: 25px;
        width: 100%;
    }
}

@media (max-width: 480px) {
    .slide-content {
        left: 5%;
        max-width: 90%;
    }
}
</style>

<!-- Hero Slideshow Section -->
<div class="slideshow-container">
    <div class="slide fade">
        <img src="assets/images/slide1.jpg" alt="Special Offers">
        <div class="slide-content">
            <h2>Special Offers</h2>
            <p>Get up to 50% off on selected items</p>
            <a href="products.php?sort=price_low" class="btn">Shop Now</a>
        </div>
    </div>
    
    <div class="slide fade">
        <img src="assets/images/slide2.jpg" alt="New Arrivals">
        <div class="slide-content">
            <h2>New Arrivals</h2>
            <p>Check out our latest products</p>
            <a href="products.php?sort=newest" class="btn">Explore</a>
        </div>
    </div>
    
    <div class="slide fade">
        <img src="assets/images/slide3.jpg" alt="Limited Edition">
        <div class="slide-content">
            <h2>Limited Edition Items</h2>
            <p>Exclusive products for a limited time only</p>
            <a href="products.php?category=3" class="btn">View Collection</a>
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
        <a href="products.php" class="view-all">View All</a>
    </div>
    
    <div class="categories-grid">
        <?php foreach ($categories as $category): ?>
        <a href="products.php?category=<?php echo $category['id']; ?>" class="category-card">
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
        <a href="products.php?featured=1" class="view-all">View All</a>
    </div>
    
    <div class="product-grid">
        <?php foreach ($featuredProducts as $product): ?>
        <div class="product-card">
            <a href="product.php?id=<?php echo $product['id']; ?>" class="product-image-link">
                <img src="assets/images/<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" class="product-image">
            </a>
            <div class="product-info">
                <h3 class="product-title">
                    <a href="product.php?id=<?php echo $product['id']; ?>">
                        <?php echo htmlspecialchars($product['name']); ?>
                    </a>
                </h3>
                <div class="product-price">₱<?php echo number_format($product['price'], 2); ?></div>
                <div class="product-actions">
                    <a href="product.php?id=<?php echo $product['id']; ?>" class="view-product">View Details</a>
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
        <a href="products.php?sort=newest" class="view-all">View All</a>
    </div>
    
    <div class="product-grid">
        <?php foreach ($latestProducts as $product): ?>
        <div class="product-card">
            <a href="product.php?id=<?php echo $product['id']; ?>" class="product-image-link">
                <img src="assets/images/<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" class="product-image">
            </a>
            <div class="product-info">
                <h3 class="product-title">
                    <a href="product.php?id=<?php echo $product['id']; ?>">
                        <?php echo htmlspecialchars($product['name']); ?>
                    </a>
                </h3>
                <div class="product-price">₱<?php echo number_format($product['price'], 2); ?></div>
                <div class="product-actions">
                    <a href="product.php?id=<?php echo $product['id']; ?>" class="view-product">View Details</a>
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