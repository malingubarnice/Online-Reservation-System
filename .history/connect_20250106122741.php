<?php
// Database credentials
$host = 'localhost';
$user = 'root';        // Update if your username is different
$password = '';        // Update with your actual password
$dbname = 'coppers_ivy';

// Create connection
$conn = new mysqli($host, $user, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
