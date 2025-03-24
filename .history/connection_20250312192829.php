<?php
$host = "localhost";  // Change if using a remote database
$username = "root";  // Change to your actual database username
$password = "";  // Change to your actual database password
$database = "coppers_ivy";  // Your database name

// Create connection
$conn = new mysqli($host, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
