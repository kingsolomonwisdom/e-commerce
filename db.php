<?php
// Database configuration
$host = 'localhost';
$username = 'root';
$password = ''; // Empty password for XAMPP default setup
$database = 'shop';

// Base URL
define('BASE_URL', 'http://localhost/maoni');

// Error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

function connectDB() {
    global $host, $username, $password, $database;
    static $conn = null;
    
    if ($conn === null) {
        try {
            // First, check if we can connect to MySQL
            $conn = new mysqli($host, $username, $password);
            
            if ($conn->connect_error) {
                throw new Exception("Failed to connect to MySQL: " . $conn->connect_error);
            }
            
            // Check if database exists, create it if it doesn't
            if (!databaseExists($conn, $database)) {
                $conn->query("CREATE DATABASE IF NOT EXISTS $database");
            }
            
            // Select the database
            $conn->select_db($database);
            
            // Set charset to prevent SQL injection
            $conn->set_charset("utf8");
            
        } catch (Exception $e) {
            // Output a user-friendly message and log the error
            echo "<div style='background-color: #ffdddd; color: #990000; padding: 15px; margin: 10px 0; border-radius: 5px;'>
                <h3>Database Connection Error</h3>
                <p>Unable to connect to the database. Please check your configuration or contact the administrator.</p>
                <p><strong>Error details (for admin only):</strong> " . $e->getMessage() . "</p>
                <p><strong>Troubleshooting:</strong> If you're seeing 'Access denied', check that your MySQL server is running and that your credentials are correct.</p>
              </div>";
            exit;
        }
    }
    
    return $conn;
}

// Helper function to check if database exists
function databaseExists($conn, $database) {
    $result = $conn->query("SHOW DATABASES LIKE '$database'");
    return ($result->num_rows > 0);
}

// Helper function for safe SQL queries with prepared statements
function executeQuery($sql, $params = [], $types = "") {
    $conn = connectDB();
    
    $stmt = $conn->prepare($sql);
    
    if (!$stmt) {
        die("Error in preparing statement: " . $conn->error);
    }
    
    if (!empty($params)) {
        if (empty($types)) {
            // Auto-determine types if not provided
            $types = str_repeat("s", count($params));
        }
        
        $stmt->bind_param($types, ...$params);
    }
    
    $stmt->execute();
    
    return $stmt;
}

// Get results as an associative array from a SELECT query
function fetchResults($sql, $params = [], $types = "") {
    $stmt = executeQuery($sql, $params, $types);
    $result = $stmt->get_result();
    
    $data = [];
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
    
    $stmt->close();
    return $data;
}

// Get a single row from a SELECT query
function fetchRow($sql, $params = [], $types = "") {
    $data = fetchResults($sql, $params, $types);
    return !empty($data) ? $data[0] : null;
}

// Insert, update, or delete records
function modifyData($sql, $params = [], $types = "") {
    $stmt = executeQuery($sql, $params, $types);
    $affected = $stmt->affected_rows;
    $stmt->close();
    return $affected;
}

// Initialize database and tables if they don't exist
function initializeDatabase() {
    $conn = connectDB();
    
    // Check if users table exists
    $result = $conn->query("SHOW TABLES LIKE 'users'");
    
    if ($result->num_rows === 0) {
        // Create users table
        $sql = "CREATE TABLE users (
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
        )";
        
        $conn->query($sql);
        
        // Insert admin user
        $adminPassword = password_hash('admin123', PASSWORD_DEFAULT);
        $sql = "INSERT INTO users (first_name, last_name, username, email, phone, address, password, is_admin) 
                VALUES ('Admin', 'User', 'admin', 'admin@shopway.com', '1234567890', '123 Admin St', ?, 1)";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $adminPassword);
        $stmt->execute();
        $stmt->close();
        
        // Create other essential tables
        createTable($conn, 'categories');
        createTable($conn, 'products');
        createTable($conn, 'orders');
        createTable($conn, 'order_items');
        createTable($conn, 'sessions');
    }
    
    return true;
}

// Create a specific table if it doesn't exist
function createTable($conn, $tableName) {
    switch ($tableName) {
        case 'categories':
            $sql = "CREATE TABLE categories (
                id INT AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(100) NOT NULL,
                description TEXT,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                INDEX idx_category_name (name)
            )";
            break;
            
        case 'products':
            $sql = "CREATE TABLE products (
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
            )";
            break;
            
        case 'orders':
            $sql = "CREATE TABLE orders (
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
            )";
            break;
            
        case 'order_items':
            $sql = "CREATE TABLE order_items (
                id INT AUTO_INCREMENT PRIMARY KEY,
                order_id INT NOT NULL,
                product_id INT NOT NULL,
                quantity INT NOT NULL,
                price DECIMAL(10,2) NOT NULL,
                FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
                FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
            )";
            break;
            
        case 'sessions':
            $sql = "CREATE TABLE sessions (
                id VARCHAR(128) NOT NULL PRIMARY KEY,
                user_id INT,
                ip_address VARCHAR(45) NOT NULL,
                user_agent TEXT,
                payload TEXT NOT NULL,
                last_activity INT NOT NULL,
                FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
            )";
            break;
            
        default:
            return false;
    }
    
    $conn->query($sql);
    return true;
}

// Initialize database at startup
initializeDatabase();
?>