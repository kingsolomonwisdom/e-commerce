<?php
// Include database connection
require_once 'db.php';

// Function to add a product
function addProduct($name, $description, $price, $stock, $image, $category, $featured = 0) {
    // Get category ID
    $categoryRow = fetchRow("SELECT id FROM categories WHERE name = ?", [$category]);
    $categoryId = $categoryRow ? $categoryRow['id'] : null;
    
    // Check if category exists, if not create it
    if (!$categoryId) {
        modifyData("INSERT INTO categories (name, description) VALUES (?, ?)", 
                  [$category, "Products in the $category category"]);
        $categoryId = fetchRow("SELECT id FROM categories WHERE name = ?", [$category])['id'];
    }
    
    // Check if product with this name already exists
    $existingProduct = fetchRow("SELECT id FROM products WHERE name = ?", [$name]);
    
    if ($existingProduct) {
        echo "Product '$name' already exists with ID: " . $existingProduct['id'] . "<br>";
        return false;
    }
    
    // Insert the product
    $result = modifyData(
        "INSERT INTO products (name, description, price, stock, image, category_id, featured) VALUES (?, ?, ?, ?, ?, ?, ?)",
        [$name, $description, $price, $stock, $image, $categoryId, $featured],
        "ssdisii"
    );
    
    if ($result) {
        echo "Added: $name - ₱" . number_format($price, 2) . " ($category)<br>";
        return true;
    } else {
        echo "Failed to add: $name<br>";
        return false;
    }
}

// Start adding products
echo "<h1>Adding Products</h1>";

// Add Apple products
addProduct(
    "Apple AirPods Pro", 
    "The Apple AirPods Pro features Active Noise Cancellation for immersive sound. Transparency mode for hearing what's happening around you. They're sweat and water resistant, with a customizable fit that keeps them comfortably in place all day.", 
    12990.00, 
    25, 
    "airpods.jpg", 
    "Electronics",
    1 // Featured
);

addProduct(
    "iPhone 14 Pro", 
    "The iPhone 14 Pro comes with the A16 Bionic chip, a 48MP main camera, and a Dynamic Island display feature. It offers all-day battery life, Crash Detection, and is made with Ceramic Shield for better durability.", 
    54999.00, 
    15, 
    "iphone.jpg", 
    "Electronics",
    1 // Featured
);

addProduct(
    "Apple Watch Series 8", 
    "The Apple Watch Series 8 has advanced health features including temperature sensing for cycle tracking, crash detection, and enhanced workout metrics. It has a large, always-on display that's easy to read in any light.", 
    22990.00, 
    20, 
    "watch.jpg", 
    "Electronics",
    1 // Featured
);

// Add Fashion products
addProduct(
    "Basic White Pocket T-Shirt", 
    "A premium quality white t-shirt with a small pocket detail. Made from 100% soft cotton for maximum comfort and durability. Perfect for everyday casual wear.", 
    899.00, 
    100, 
    "tshirt.jpg", 
    "Fashion"
);

// Add Accessories
addProduct(
    "Silver Stud Earrings", 
    "Elegant and stylish silver earrings that are perfect for any occasion. These minimalist studs are crafted from high-quality sterling silver that won't irritate your ears.", 
    1499.00, 
    30, 
    "earings.jpg", 
    "Accessories"
);

addProduct(
    "Under Armour Backpack", 
    "A durable and practical backpack from Under Armour. Features multiple compartments for organization, water-resistant material, and comfortable padded straps. Perfect for school, gym, or everyday use.", 
    2499.00, 
    25, 
    "UA.jpg", 
    "Fashion"
);

// Add additional product
addProduct(
    "Premium Men's Wristwatch", 
    "A luxurious wristwatch with a metallic strap and classic design. Features waterproof construction, precision quartz movement, and a durable stainless steel case.", 
    3999.00, 
    15, 
    "wow.jpg", 
    "Accessories"
);

echo "<p>Product import complete. <a href='products.php'>View Products</a></p>";
?> 