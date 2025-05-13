<?php
$host = "localhost";
$username = "root";
$password = "";
$database = "shop";

$conn = new mysqli($host, $username, $password, $database);

if ($conn->connect_error) {
    die("sipyat sigeg pataka: " . $conn->connect_error);
} else {
    echo "Connected successfully!";
}
?>