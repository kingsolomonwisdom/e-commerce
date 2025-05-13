-- Database: shop

-- Drop tables if they exist to prevent errors (be careful with this in production)
DROP TABLE IF EXISTS order_items;
DROP TABLE IF EXISTS orders;
DROP TABLE IF EXISTS products;
DROP TABLE IF EXISTS categories;
DROP TABLE IF EXISTS users;

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
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Create categories table
CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Create products table
CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    price DECIMAL(10,2) NOT NULL,
    stock INT DEFAULT 0,
    image VARCHAR(255) DEFAULT 'default.jpg',
    category_id INT,
    featured TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL
);

-- Create orders table
CREATE TABLE orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    total_amount DECIMAL(10,2) NOT NULL,
    status ENUM('Pending', 'Processing', 'Shipped', 'Delivered', 'Cancelled') DEFAULT 'Pending',
    payment_method VARCHAR(50),
    shipping_address TEXT NOT NULL,
    contact_phone VARCHAR(15) NOT NULL,
    order_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Create order items table
CREATE TABLE order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

-- Insert sample categories
INSERT INTO categories (name, description) VALUES
('Electronics', 'Smartphones, laptops, tablets, and other electronic gadgets'),
('Fashion', 'Clothing, footwear, and accessories for men and women'),
('Accessories', 'Watches, jewelry, bags, and other accessories'),
('Home & Living', 'Furniture, decor, and kitchen appliances');

-- Insert sample products
INSERT INTO products (name, description, price, stock, image, category_id, featured) VALUES
('iPhone 14 Pro', 'Latest Apple iPhone with amazing features', 54999.00, 20, 'iphone.jpg', 1, 1),
('AirPods Pro', 'Wireless earbuds with noise cancellation', 12990.00, 50, 'airpods.jpg', 1, 1),
('Men\'s Casual T-shirt', 'Comfortable cotton t-shirt for everyday wear', 899.00, 100, 'tshirt.jpg', 2, 1),
('Apple Watch Series 8', 'Smart watch with health monitoring features', 22990.00, 15, 'watch.jpg', 3, 1),
('Silver Earrings', 'Elegant silver earrings for women', 1499.00, 30, 'earings.jpg', 3, 0),
('Under Armour Backpack', 'Durable backpack for daily use', 2499.00, 25, 'UA.jpg', 2, 0);

-- Insert admin user (password is 'admin123' hashed with PASSWORD_DEFAULT)
INSERT INTO users (first_name, last_name, username, email, phone, address, password, is_admin) VALUES
('Admin', 'User', 'admin', 'admin@shopway.com', '1234567890', '123 Admin St, Admin City', '$2y$10$rA5OzHoG2IJ.H8Mu5.h.1eL.srQUXBl9yjn9JAnKsfFv7H.vYkFAu', 1);

-- Insert regular user (password is 'password123' hashed with PASSWORD_DEFAULT)
INSERT INTO users (first_name, last_name, username, email, phone, address, password, is_admin) VALUES
('John', 'Doe', 'johndoe', 'john@example.com', '9876543210', '456 User St, User City', '$2y$10$F2Oi1AYj9QNoWGTOHLBz5OaFDrW7dP1Y5Sq8i0JqiPmrSzZC14yKi', 0);

-- Insert sample orders
INSERT INTO orders (user_id, total_amount, status, payment_method, shipping_address, contact_phone) VALUES
(2, 55898.00, 'Delivered', 'Credit Card', '456 User St, User City', '9876543210'),
(2, 13889.00, 'Shipped', 'PayPal', '456 User St, User City', '9876543210');

-- Insert sample order items
INSERT INTO order_items (order_id, product_id, quantity, price) VALUES
(1, 1, 1, 54999.00),
(1, 3, 1, 899.00),
(2, 2, 1, 12990.00),
(2, 3, 1, 899.00); 