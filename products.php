<?php
// Page configuration
$pageTitle = "Products - Shopway";

// Include header
require_once 'db.php';
require_once 'includes/header.php';

// Get filter parameters
$categoryId = isset($_GET['category']) ? (int)$_GET['category'] : 0;
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'newest';
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 12; // Products per page
$offset = ($page - 1) * $limit;

// Prepare query parts
$whereClause = [];
$params = [];
$orderBy = "";

// Apply category filter
if ($categoryId > 0) {
    $whereClause[] = "p.category_id = ?";
    $params[] = $categoryId;
}

// Apply search filter
if (!empty($search)) {
    $whereClause[] = "(p.name LIKE ? OR p.description LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

// Build where clause
$whereSQL = !empty($whereClause) ? "WHERE " . implode(" AND ", $whereClause) : "";

// Apply sorting
switch ($sort) {
    case 'price_low':
        $orderBy = "ORDER BY p.price ASC";
        break;
    case 'price_high':
        $orderBy = "ORDER BY p.price DESC";
        break;
    case 'name_asc':
        $orderBy = "ORDER BY p.name ASC";
        break;
    case 'name_desc':
        $orderBy = "ORDER BY p.name DESC";
        break;
    case 'newest':
    default:
        $orderBy = "ORDER BY p.created_at DESC";
        break;
}

// Count total products for pagination
$countQuery = "SELECT COUNT(*) as total FROM products p $whereSQL";
$totalProducts = fetchRow($countQuery, $params)['total'];
$totalPages = ceil($totalProducts / $limit);

// Get products
$query = "SELECT p.*, c.name as category_name 
          FROM products p 
          LEFT JOIN categories c ON p.category_id = c.id 
          $whereSQL 
          $orderBy 
          LIMIT ? OFFSET ?";

// Add limit and offset parameters
$params[] = $limit;
$params[] = $offset;

// Get product list
$products = fetchResults($query, $params);

// Get all categories for filter
$categories = fetchResults("SELECT * FROM categories ORDER BY name ASC");

// Get current category name if filtered
$currentCategory = '';
if ($categoryId > 0) {
    $category = fetchRow("SELECT name FROM categories WHERE id = ?", [$categoryId]);
    $currentCategory = $category ? $category['name'] : '';
}
?>

<style>
/* Products Page Styles */
.products-container {
    padding: 20px 0;
}

.products-header {
    margin-bottom: 30px;
}

.page-title {
    font-size: 28px;
    margin-bottom: 10px;
    color: #333;
}

.products-count {
    color: #666;
    font-size: 14px;
}

/* Grid layout */
.products-grid {
    display: grid;
    grid-template-columns: 250px 1fr;
    gap: 30px;
}

/* Sidebar filters */
.filters-sidebar {
    background-color: #f8f9fa;
    padding: 20px;
    border-radius: 5px;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
}

.filter-section {
    margin-bottom: 25px;
}

.filter-section h3 {
    font-size: 18px;
    margin-bottom: 15px;
    padding-bottom: 8px;
    border-bottom: 1px solid #dee2e6;
    color: #333;
}

.category-list {
    list-style: none;
    padding: 0;
}

.category-list li {
    margin-bottom: 8px;
}

.category-list a {
    display: block;
    padding: 8px 10px;
    color: #555;
    text-decoration: none;
    border-radius: 4px;
    transition: all 0.2s ease;
}

.category-list a:hover {
    background-color: #f0f0f0;
    color: #007bff;
}

.category-list a.active {
    background-color: #007bff;
    color: white;
    font-weight: 500;
}

.price-slider {
    padding: 10px 0;
}

.price-inputs {
    display: flex;
    align-items: center;
    margin-bottom: 15px;
}

.price-inputs input {
    width: 80px;
    padding: 8px;
    border: 1px solid #ced4da;
    border-radius: 4px;
}

.price-inputs span {
    margin: 0 10px;
    color: #777;
}

.btn-filter {
    background-color: #007bff;
    color: white;
    border: none;
    padding: 8px 15px;
    border-radius: 4px;
    cursor: pointer;
    font-size: 14px;
    transition: background-color 0.3s ease;
}

.btn-filter:hover {
    background-color: #0056b3;
}

.search-form input {
    width: 100%;
    padding: 10px;
    border: 1px solid #ced4da;
    border-radius: 4px;
    margin-bottom: 10px;
}

/* Products list */
.products-tools {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
    padding-bottom: 15px;
    border-bottom: 1px solid #dee2e6;
}

.sort-options {
    display: flex;
    align-items: center;
}

.sort-options label {
    margin-right: 10px;
    color: #555;
}

.sort-options select {
    padding: 8px;
    border: 1px solid #ced4da;
    border-radius: 4px;
    background-color: white;
    cursor: pointer;
}

.view-options {
    display: flex;
    gap: 5px;
}

.view-options button {
    background-color: #f8f9fa;
    border: 1px solid #dee2e6;
    width: 40px;
    height: 40px;
    border-radius: 4px;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s ease;
}

.view-options button.active,
.view-options button:hover {
    background-color: #007bff;
    color: white;
}

/* Product grid */
.product-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
    gap: 20px;
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

.product-image-link {
    display: block;
    position: relative;
    overflow: hidden;
    height: 200px;
}

.product-image {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.5s ease;
}

.product-card:hover .product-image {
    transform: scale(1.05);
}

.product-info {
    padding: 15px;
}

.product-title {
    font-size: 16px;
    margin-bottom: 5px;
}

.product-title a {
    color: #333;
    text-decoration: none;
}

.product-title a:hover {
    color: #007bff;
}

.product-category {
    color: #6c757d;
    font-size: 12px;
    margin-bottom: 8px;
}

.product-price {
    color: green;
    font-weight: bold;
    margin-bottom: 15px;
    font-size: 18px;
}

.product-actions {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.view-product {
    background-color: transparent;
    border: 1px solid #007bff;
    color: #007bff;
    text-align: center;
    padding: 8px 0;
    border-radius: 4px;
    text-decoration: none;
    font-size: 14px;
    transition: all 0.3s ease;
}

.view-product:hover {
    background-color: #007bff;
    color: white;
}

.add-to-cart {
    background-color: #007bff;
    color: white;
    border: none;
    padding: 8px 0;
    border-radius: 4px;
    cursor: pointer;
    font-size: 14px;
    transition: background-color 0.3s ease;
}

.add-to-cart:hover {
    background-color: #0056b3;
}

/* List view */
.product-grid.list-view {
    grid-template-columns: 1fr;
}

.product-grid.list-view .product-card {
    display: flex;
}

.product-grid.list-view .product-image-link {
    width: 200px;
    height: 200px;
    flex-shrink: 0;
}

.product-grid.list-view .product-info {
    flex: 1;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
}

.product-grid.list-view .product-actions {
    flex-direction: row;
}

.product-grid.list-view .view-product,
.product-grid.list-view .add-to-cart {
    flex: 1;
    padding: 8px 15px;
}

/* Empty products */
.no-products {
    text-align: center;
    padding: 50px 0;
    background-color: #f8f9fa;
    border-radius: 8px;
}

.no-products i {
    color: #ccc;
    margin-bottom: 20px;
}

.no-products h2 {
    font-size: 24px;
    margin-bottom: 10px;
    color: #333;
}

.no-products p {
    color: #666;
    margin-bottom: 20px;
    max-width: 500px;
    margin-left: auto;
    margin-right: auto;
}

.btn-secondary {
    display: inline-block;
    background-color: #6c757d;
    color: white;
    padding: 10px 20px;
    border-radius: 4px;
    text-decoration: none;
}

.btn-secondary:hover {
    background-color: #5a6268;
}

/* Pagination */
.pagination {
    display: flex;
    justify-content: center;
    margin-top: 30px;
    flex-wrap: wrap;
}

.page-link {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 40px;
    height: 40px;
    margin: 0 5px;
    border-radius: 50%;
    text-decoration: none;
    color: #007bff;
    background-color: white;
    border: 1px solid #dee2e6;
    transition: all 0.2s ease;
}

.page-link.active {
    background-color: #007bff;
    color: white;
    border-color: #007bff;
}

.page-link:hover:not(.active) {
    background-color: #f8f9fa;
}

.page-link i {
    font-size: 12px;
}

.pagination a:first-child,
.pagination a:last-child {
    width: auto;
    padding: 0 15px;
    border-radius: 20px;
}

.page-dots {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 40px;
    height: 40px;
}

/* Responsive adjustments */
@media (max-width: 992px) {
    .products-grid {
        grid-template-columns: 1fr;
    }
    
    .filters-sidebar {
        order: 1;
        margin-bottom: 20px;
    }
    
    .products-list {
        order: 2;
    }
}

@media (max-width: 768px) {
    .product-grid {
        grid-template-columns: repeat(2, 1fr);
    }
    
    .product-grid.list-view .product-card {
        flex-direction: column;
    }
    
    .product-grid.list-view .product-image-link {
        width: 100%;
    }
    
    .product-grid.list-view .product-actions {
        flex-direction: column;
    }
    
    .products-tools {
        flex-direction: column;
        gap: 10px;
        align-items: flex-start;
    }
    
    .view-options {
        align-self: flex-end;
    }
}

@media (max-width: 576px) {
    .product-grid {
        grid-template-columns: 1fr;
    }
    
    .page-link {
        width: 35px;
        height: 35px;
        margin: 0 3px;
    }
}
</style>

<div class="products-container">
    <div class="products-header">
        <h1 class="page-title">
            <?php if (!empty($currentCategory)): ?>
                <?php echo htmlspecialchars($currentCategory); ?> Products
            <?php elseif (!empty($search)): ?>
                Search Results for: "<?php echo htmlspecialchars($search); ?>"
            <?php else: ?>
                All Products
            <?php endif; ?>
        </h1>
        
        <div class="products-count">
            <?php echo $totalProducts; ?> product<?php echo $totalProducts != 1 ? 's' : ''; ?> found
        </div>
    </div>
    
    <div class="products-grid">
        <div class="filters-sidebar">
            <div class="filter-section">
                <h3>Categories</h3>
                <ul class="category-list">
                    <li>
                        <a href="products.php" <?php echo $categoryId == 0 ? 'class="active"' : ''; ?>>
                            All Categories
                        </a>
                    </li>
                    <?php foreach ($categories as $category): ?>
                    <li>
                        <a href="products.php?category=<?php echo $category['id']; ?>" 
                           <?php echo $categoryId == $category['id'] ? 'class="active"' : ''; ?>>
                            <?php echo htmlspecialchars($category['name']); ?>
                        </a>
                    </li>
                    <?php endforeach; ?>
                </ul>
            </div>
            
            <div class="filter-section">
                <h3>Price Range</h3>
                <div class="price-slider">
                    <div class="price-inputs">
                        <input type="number" id="min-price" placeholder="Min">
                        <span>-</span>
                        <input type="number" id="max-price" placeholder="Max">
                    </div>
                    <button id="apply-price" class="btn-filter">Apply</button>
                </div>
            </div>
            
            <div class="filter-section">
                <h3>Search</h3>
                <form action="products.php" method="GET" class="search-form">
                    <?php if ($categoryId > 0): ?>
                    <input type="hidden" name="category" value="<?php echo $categoryId; ?>">
                    <?php endif; ?>
                    <input type="text" name="search" placeholder="Search products..." value="<?php echo htmlspecialchars($search); ?>">
                    <button type="submit" class="btn-filter">Search</button>
                </form>
            </div>
        </div>
        
        <div class="products-list">
            <div class="products-tools">
                <div class="sort-options">
                    <label for="sort">Sort by:</label>
                    <select id="sort" onchange="window.location.href=this.value">
                        <option value="products.php?<?php echo http_build_query(array_merge($_GET, ['sort' => 'newest', 'page' => 1])); ?>" <?php echo $sort == 'newest' ? 'selected' : ''; ?>>
                            Newest
                        </option>
                        <option value="products.php?<?php echo http_build_query(array_merge($_GET, ['sort' => 'price_low', 'page' => 1])); ?>" <?php echo $sort == 'price_low' ? 'selected' : ''; ?>>
                            Price: Low to High
                        </option>
                        <option value="products.php?<?php echo http_build_query(array_merge($_GET, ['sort' => 'price_high', 'page' => 1])); ?>" <?php echo $sort == 'price_high' ? 'selected' : ''; ?>>
                            Price: High to Low
                        </option>
                        <option value="products.php?<?php echo http_build_query(array_merge($_GET, ['sort' => 'name_asc', 'page' => 1])); ?>" <?php echo $sort == 'name_asc' ? 'selected' : ''; ?>>
                            Name: A to Z
                        </option>
                        <option value="products.php?<?php echo http_build_query(array_merge($_GET, ['sort' => 'name_desc', 'page' => 1])); ?>" <?php echo $sort == 'name_desc' ? 'selected' : ''; ?>>
                            Name: Z to A
                        </option>
                    </select>
                </div>
                
                <div class="view-options">
                    <button class="view-grid active"><i class="fas fa-th"></i></button>
                    <button class="view-list"><i class="fas fa-list"></i></button>
                </div>
            </div>
            
            <?php if (empty($products)): ?>
            <div class="no-products">
                <i class="fas fa-search fa-3x"></i>
                <h2>No products found</h2>
                <p>Try adjusting your search or filter to find what you're looking for.</p>
                <a href="products.php" class="btn-secondary">Clear Filters</a>
            </div>
            <?php else: ?>
            <div class="product-grid">
                <?php foreach ($products as $product): ?>
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
                        <div class="product-category">
                            <?php echo htmlspecialchars($product['category_name'] ?? 'Uncategorized'); ?>
                        </div>
                        <div class="product-price">₱<?php echo number_format($product['price'], 2); ?></div>
                        <div class="product-actions">
                            <a href="product.php?id=<?php echo $product['id']; ?>" class="view-product">View Details</a>
                            <button class="add-to-cart" onclick="addToCart(<?php echo $product['id']; ?>)">Add to Cart</button>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            
            <!-- Pagination -->
            <?php if ($totalPages > 1): ?>
            <div class="pagination">
                <?php if ($page > 1): ?>
                <a href="products.php?<?php echo http_build_query(array_merge($_GET, ['page' => $page - 1])); ?>" class="page-link">
                    <i class="fas fa-chevron-left"></i> Previous
                </a>
                <?php endif; ?>
                
                <?php
                // Determine range of page numbers to show
                $range = 2;
                $showLeft = $page - $range;
                $showRight = $page + $range;
                
                // Adjust if we're near the beginning or end
                if ($showLeft < 1) {
                    $showRight += (1 - $showLeft);
                    $showLeft = 1;
                }
                
                if ($showRight > $totalPages) {
                    $showLeft -= ($showRight - $totalPages);
                    $showRight = $totalPages;
                    
                    if ($showLeft < 1) {
                        $showLeft = 1;
                    }
                }
                ?>
                
                <?php if ($showLeft > 1): ?>
                <a href="products.php?<?php echo http_build_query(array_merge($_GET, ['page' => 1])); ?>" class="page-link">1</a>
                <?php if ($showLeft > 2): ?>
                <span class="page-dots">...</span>
                <?php endif; ?>
                <?php endif; ?>
                
                <?php for ($i = $showLeft; $i <= $showRight; $i++): ?>
                <a href="products.php?<?php echo http_build_query(array_merge($_GET, ['page' => $i])); ?>" 
                   class="page-link <?php echo $i == $page ? 'active' : ''; ?>">
                    <?php echo $i; ?>
                </a>
                <?php endfor; ?>
                
                <?php if ($showRight < $totalPages): ?>
                <?php if ($showRight < $totalPages - 1): ?>
                <span class="page-dots">...</span>
                <?php endif; ?>
                <a href="products.php?<?php echo http_build_query(array_merge($_GET, ['page' => $totalPages])); ?>" class="page-link">
                    <?php echo $totalPages; ?>
                </a>
                <?php endif; ?>
                
                <?php if ($page < $totalPages): ?>
                <a href="products.php?<?php echo http_build_query(array_merge($_GET, ['page' => $page + 1])); ?>" class="page-link">
                    Next <i class="fas fa-chevron-right"></i>
                </a>
                <?php endif; ?>
            </div>
            <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
// JavaScript for view toggle
document.addEventListener('DOMContentLoaded', function() {
    const gridBtn = document.querySelector('.view-grid');
    const listBtn = document.querySelector('.view-list');
    const productGrid = document.querySelector('.product-grid');
    
    if (gridBtn && listBtn && productGrid) {
        gridBtn.addEventListener('click', function() {
            productGrid.classList.remove('list-view');
            gridBtn.classList.add('active');
            listBtn.classList.remove('active');
            localStorage.setItem('shopway_view', 'grid');
        });
        
        listBtn.addEventListener('click', function() {
            productGrid.classList.add('list-view');
            listBtn.classList.add('active');
            gridBtn.classList.remove('active');
            localStorage.setItem('shopway_view', 'list');
        });
        
        // Check saved preference
        const savedView = localStorage.getItem('shopway_view');
        if (savedView === 'list') {
            productGrid.classList.add('list-view');
            listBtn.classList.add('active');
            gridBtn.classList.remove('active');
        }
    }
    
    // Price filter
    const applyPriceBtn = document.getElementById('apply-price');
    if (applyPriceBtn) {
        applyPriceBtn.addEventListener('click', function() {
            const minPrice = document.getElementById('min-price').value;
            const maxPrice = document.getElementById('max-price').value;
            
            // Get current URL parameters
            const urlParams = new URLSearchParams(window.location.search);
            
            // Update min_price and max_price parameters
            if (minPrice) urlParams.set('min_price', minPrice);
            else urlParams.delete('min_price');
            
            if (maxPrice) urlParams.set('max_price', maxPrice);
            else urlParams.delete('max_price');
            
            // Reset to page 1
            urlParams.set('page', '1');
            
            // Redirect to new URL
            window.location.href = 'products.php?' + urlParams.toString();
        });
    }
});
</script>

<?php
// Include footer
require_once 'includes/footer.php';
?> 