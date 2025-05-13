<?php
require_once 'config.php';

// Database connection with error handling
function connectDB() {
    static $conn = null;
    
    if ($conn === null) {
        // Create connection
        $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        
        // Check connection
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }
        
        // Set charset to prevent SQL injection
        $conn->set_charset("utf8");
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