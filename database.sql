-- Create database if not exists and select it
CREATE DATABASE IF NOT EXISTS shop;
USE shop;

-- Database: shop

-- Drop tables if they exist to prevent errors (be careful with this in production)
DROP TABLE IF EXISTS order_items;
DROP TABLE IF EXISTS orders;
DROP TABLE IF EXISTS products;
DROP TABLE IF EXISTS categories;
DROP TABLE IF EXISTS users;
DROP TABLE IF EXISTS sessions;

-- Create users table
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

-- Create categories table
CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_category_name (name)
);

-- Create products table
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

-- Create orders table
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

-- Create order items table
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

-- Create session table for better session management
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

-- Insert sample categories
INSERT INTO categories (name, description) VALUES
('Electronics', 'Smartphones, laptops, tablets, and other electronic gadgets'),
('Fashion', 'Clothing, footwear, and accessories for men and women'),
('Accessories', 'Watches, jewelry, bags, and other accessories'),
('Home & Living', 'Furniture, decor, and kitchen appliances'),
('Beauty', 'Cosmetics, skincare, and beauty products'),
('Sports', 'Sports equipment, activewear, and fitness gear');

-- Insert sample products
INSERT INTO products (name, description, price, stock, image, category_id, featured) VALUES
('iPhone 14 Pro', 'Latest Apple iPhone with amazing features and powerful processor', 54999.00, 20, 'iphone.jpg', 1, 1),
('AirPods Pro', 'Wireless earbuds with noise cancellation for immersive sound experience', 12990.00, 50, 'airpods.jpg', 1, 1),
('Men\'s Casual T-shirt', 'Comfortable cotton t-shirt for everyday wear with breathable fabric', 899.00, 100, 'tshirt.jpg', 2, 1),
('Apple Watch Series 8', 'Smart watch with health monitoring features and fitness tracking', 22990.00, 15, 'watch.jpg', 3, 1),
('Silver Earrings', 'Elegant silver earrings for women with delicate design', 1499.00, 30, 'earings.jpg', 3, 0),
('Under Armour Backpack', 'Durable backpack for daily use with multiple compartments', 2499.00, 25, 'UA.jpg', 2, 0),
('Wireless Keyboard', 'Ergonomic wireless keyboard with long battery life', 1999.00, 40, 'keyboard.jpg', 1, 0),
('Smart LED TV 55"', '4K Ultra HD Smart LED TV with HDR support', 35990.00, 10, 'tv.jpg', 1, 1),
('Yoga Mat', 'Non-slip yoga mat for comfortable workout sessions', 999.00, 45, 'yogamat.jpg', 5, 0),
('Coffee Maker', 'Automatic coffee maker for perfect brewing every time', 3499.00, 20, 'coffeemaker.jpg', 4, 0);

-- Insert admin user (password is 'admin123' hashed with PASSWORD_DEFAULT)
INSERT INTO users (first_name, last_name, username, email, phone, address, password, is_admin) VALUES
('Admin', 'User', 'admin', 'admin@shopway.com', '1234567890', '123 Admin St, Admin City', '$2y$10$rA5OzHoG2IJ.H8Mu5.h.1eL.srQUXBl9yjn9JAnKsfFv7H.vYkFAu', 1);

-- Insert regular user (password is 'password123' hashed with PASSWORD_DEFAULT)
INSERT INTO users (first_name, last_name, username, email, phone, address, password, is_admin) VALUES
('John', 'Doe', 'johndoe', 'john@example.com', '9876543210', '456 User St, User City', '$2y$10$F2Oi1AYj9QNoWGTOHLBz5OaFDrW7dP1Y5Sq8i0JqiPmrSzZC14yKi', 0),
('Jane', 'Smith', 'janesmith', 'jane@example.com', '5555555555', '789 Customer Ave, User Town', '$2y$10$F2Oi1AYj9QNoWGTOHLBz5OaFDrW7dP1Y5Sq8i0JqiPmrSzZC14yKi', 0);

-- Insert sample orders
INSERT INTO orders (user_id, total_amount, status, payment_method, shipping_address, contact_phone) VALUES
(2, 55898.00, 'Delivered', 'Credit Card', '456 User St, User City', '9876543210'),
(2, 13889.00, 'Shipped', 'PayPal', '456 User St, User City', '9876543210'),
(3, 38489.00, 'Processing', 'Bank Transfer', '789 Customer Ave, User Town', '5555555555');

-- Insert sample order items
INSERT INTO order_items (order_id, product_id, quantity, price) VALUES
(1, 1, 1, 54999.00),
(1, 3, 1, 899.00),
(2, 2, 1, 12990.00),
(2, 3, 1, 899.00),
(3, 8, 1, 35990.00),
(3, 6, 1, 2499.00);

-- Create trigger to update product stock when order is placed
DELIMITER //
CREATE TRIGGER update_product_stock AFTER INSERT ON order_items
FOR EACH ROW
BEGIN
    UPDATE products SET stock = stock - NEW.quantity WHERE id = NEW.product_id;
END//
DELIMITER ;

-- Create trigger to return stock when order is cancelled
DELIMITER //
CREATE TRIGGER return_product_stock AFTER UPDATE ON orders
FOR EACH ROW
BEGIN
    IF NEW.status = 'Cancelled' AND OLD.status != 'Cancelled' THEN
        UPDATE products p
        JOIN order_items oi ON p.id = oi.product_id
        SET p.stock = p.stock + oi.quantity
        WHERE oi.order_id = NEW.id;
    END IF;
END//
DELIMITER ; 