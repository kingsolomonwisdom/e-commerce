SET FOREIGN_KEY_CHECKS=0;

CREATE DATABASE IF NOT EXISTS shop;
USE shop;

DROP TABLE IF EXISTS order_items;
DROP TABLE IF EXISTS orders;
DROP TABLE IF EXISTS products;
DROP TABLE IF EXISTS categories;
DROP TABLE IF EXISTS users;
DROP TABLE IF EXISTS sessions;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    phone VARCHAR(15),
    address TEXT,
    password VARCHAR(255) NOT NULL,
    is_admin TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_username (username),
    INDEX idx_email (email)
);

CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_category_name (name)
);

CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    price DECIMAL(10,2) NOT NULL CHECK (price > 0),
    stock INT DEFAULT 0 CHECK (stock >= 0),
    image VARCHAR(255) DEFAULT 'default.jpg',
    category_id INT,
    featured TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL,
    INDEX idx_product_name (name),
    INDEX idx_product_price (price),
    INDEX idx_product_category (category_id),
    INDEX idx_product_featured (featured)
);

CREATE TABLE orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    total_amount DECIMAL(10,2) NOT NULL CHECK (total_amount >= 0),
    status ENUM('Pending', 'Processing', 'Shipped', 'Delivered', 'Cancelled') DEFAULT 'Pending',
    payment_method VARCHAR(50),
    shipping_address TEXT NOT NULL,
    contact_phone VARCHAR(15) NOT NULL,
    order_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_order_user (user_id),
    INDEX idx_order_status (status),
    INDEX idx_order_date (order_date)
);

CREATE TABLE order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL CHECK (quantity > 0),
    price DECIMAL(10,2) NOT NULL CHECK (price >= 0),
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    INDEX idx_orderitem_order (order_id),
    INDEX idx_orderitem_product (product_id)
);

CREATE TABLE sessions (
    id VARCHAR(128) NOT NULL PRIMARY KEY,
    user_id INT,
    ip_address VARCHAR(45) NOT NULL,
    user_agent TEXT,
    payload TEXT NOT NULL,
    last_activity INT NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_session_last_activity (last_activity)
);

-- Insert sample data only if tables are empty
INSERT INTO categories (name, description)
SELECT * FROM (
    SELECT 'Electronics' as name, 'Smartphones, laptops, tablets, and other electronic gadgets' as description
) AS tmp
WHERE NOT EXISTS (SELECT 1 FROM categories LIMIT 1);

INSERT INTO categories (name, description)
SELECT * FROM (
    SELECT 'Fashion' as name, 'Clothing, footwear, and accessories for men and women' as description
) AS tmp
WHERE NOT EXISTS (SELECT 1 FROM categories WHERE name = 'Fashion');

INSERT INTO categories (name, description)
SELECT * FROM (
    SELECT 'Accessories' as name, 'Watches, jewelry, bags, and other accessories' as description
) AS tmp
WHERE NOT EXISTS (SELECT 1 FROM categories WHERE name = 'Accessories');

INSERT INTO categories (name, description)
SELECT * FROM (
    SELECT 'Home & Living' as name, 'Furniture, decor, and kitchen appliances' as description
) AS tmp
WHERE NOT EXISTS (SELECT 1 FROM categories WHERE name = 'Home & Living');

-- Insert admin user only if no users exist
INSERT INTO users (first_name, last_name, username, email, phone, address, password, is_admin)
SELECT * FROM (
    SELECT 'Admin' as first_name, 'User' as last_name, 'admin' as username, 
           'admin@shopway.com' as email, '1234567890' as phone, 
           '123 Admin St, Admin City' as address, 
           '$2y$10$rA5OzHoG2IJ.H8Mu5.h.1eL.srQUXBl9yjn9JAnKsfFv7H.vYkFAu' as password, 
           1 as is_admin
) AS tmp
WHERE NOT EXISTS (SELECT 1 FROM users LIMIT 1);

-- Insert a sample product if no products exist
INSERT INTO products (name, description, price, stock, image, category_id, featured)
SELECT p.*, c.id as category_id FROM (
    SELECT 'Sample Product' as name, 
           'This is a sample product description' as description,
           99.99 as price, 10 as stock, 'default.jpg' as image, 1 as featured
) as p, categories c
WHERE c.name = 'Electronics' 
AND NOT EXISTS (SELECT 1 FROM products LIMIT 1);

SET FOREIGN_KEY_CHECKS=1; 