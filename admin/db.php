<?php
// Database credentials
$host = "localhost";
$username = "root";
$password = "";  // Set your MySQL root password here if any
$db_name = "ecocycle";

// Create connection
$conn = new mysqli($host, $username, $password, $db_name);

// Check connection
if ($conn->connect_error) {
    die("❌ Connection failed: " . $conn->connect_error);
}


// Optional: set charset to avoid charset issues
$conn->set_charset("utf8mb4");
?>
