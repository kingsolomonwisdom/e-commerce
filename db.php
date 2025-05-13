<?php
// Database configuration
$host = 'localhost';
$username = 'root';
$password = ''; // MySQL password - XAMPP default is empty, change if your MySQL has a password
$database = 'shop';

// Base URL
define('BASE_URL', 'http://localhost/maoni');

error_reporting(E_ALL);
ini_set('display_errors', 1);

function connectDB() {
    global $host, $username, $password, $database;
    static $conn = null;
    
    if ($conn === null) {
        try {
            // Create connection
            $conn = new mysqli($host, $username, $password, $database);
            
            // Check connection
            if ($conn->connect_error) {
                throw new Exception("Connection failed: " . $conn->connect_error);
            }
            
            // Set charset to prevent SQL injection
            $conn->set_charset("utf8");
        } catch (Exception $e) {
            // Instead of die, we'll output a user-friendly message and log the error
            echo "<div style='background-color: #ffdddd; color: #990000; padding: 15px; margin: 10px 0; border-radius: 5px;'>
                <h3>Database Connection Error</h3>
                <p>Unable to connect to the database. Please check your configuration or contact the administrator.</p>
                <p><strong>Error details (for admin only):</strong> " . $e->getMessage() . "</p>
                <p><strong>Troubleshooting:</strong> If you're seeing 'Access denied', your MySQL password might be incorrect. 
                Edit the db.php file and update the password.</p>
              </div>";
            exit;
        }
    }
    
    return $conn;
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
?>