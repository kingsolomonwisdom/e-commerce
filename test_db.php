<?php
// Include the database configuration
require_once 'db.php';

// Try to connect to the database
try {
    $conn = connectDB();
    echo "<h2 style='color: green;'>Database connection successful!</h2>";
    
    // Test query to fetch users
    $sql = "SELECT id, username, is_admin FROM users";
    $users = fetchResults($sql);
    
    echo "<h3>Users in the database:</h3>";
    echo "<ul>";
    
    foreach ($users as $user) {
        echo "<li>ID: " . $user['id'] . ", Username: " . $user['username'] . 
             ", Admin: " . ($user['is_admin'] ? 'Yes' : 'No') . "</li>";
    }
    
    echo "</ul>";
    
} catch (Exception $e) {
    echo "<h2 style='color: red;'>Database connection failed!</h2>";
    echo "<p>Error: " . $e->getMessage() . "</p>";
}
?> 