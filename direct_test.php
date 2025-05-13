<?php
// Basic configuration
$host = 'localhost';
$username = 'root';
$password = '';
$database = 'shop_direct_test';

// Show errors
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Direct Database Test</h1>";

try {
    // Create a direct connection
    $conn = new mysqli($host, $username, $password);
    
    if ($conn->connect_error) {
        throw new Exception("Connection failed: " . $conn->connect_error);
    }
    
    echo "<p style='color:green'>Connected to MySQL server successfully!</p>";
    
    // Create the database
    $sql = "DROP DATABASE IF EXISTS $database";
    if ($conn->query($sql) === TRUE) {
        echo "<p>Database $database dropped successfully</p>";
    } else {
        echo "<p>Error dropping database: " . $conn->error . "</p>";
    }
    
    $sql = "CREATE DATABASE $database";
    if ($conn->query($sql) === TRUE) {
        echo "<p>Database $database created successfully</p>";
    } else {
        echo "<p>Error creating database: " . $conn->error . "</p>";
    }
    
    // Select the database
    $conn->select_db($database);
    
    // Create a users table
    $sql = "CREATE TABLE users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        first_name VARCHAR(50) NOT NULL,
        last_name VARCHAR(50) NOT NULL,
        username VARCHAR(50) NOT NULL UNIQUE,
        email VARCHAR(100) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        is_admin TINYINT(1) DEFAULT 0
    )";
    
    if ($conn->query($sql) === TRUE) {
        echo "<p>Table 'users' created successfully</p>";
    } else {
        echo "<p>Error creating table: " . $conn->error . "</p>";
    }
    
    // Insert a test user
    $sql = "INSERT INTO users (first_name, last_name, username, email, password, is_admin) 
            VALUES ('Test', 'User', 'testuser', 'test@example.com', 'password123', 0)";
            
    if ($conn->query($sql) === TRUE) {
        echo "<p>Test user created successfully</p>";
    } else {
        echo "<p>Error creating test user: " . $conn->error . "</p>";
    }
    
    // Show the inserted user
    $sql = "SELECT * FROM users";
    $result = $conn->query($sql);
    
    if ($result->num_rows > 0) {
        echo "<h3>Users in the database:</h3>";
        echo "<ul>";
        while($row = $result->fetch_assoc()) {
            echo "<li>ID: " . $row["id"] . " - Username: " . $row["username"] . " - Email: " . $row["email"] . "</li>";
        }
        echo "</ul>";
    } else {
        echo "<p>No users found</p>";
    }
    
    // Close the connection
    $conn->close();
    
} catch (Exception $e) {
    echo "<h2 style='color:red'>ERROR: " . $e->getMessage() . "</h2>";
}
?> 