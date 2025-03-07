<?php
// Database configuration
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "coppers_ivy";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die(json_encode(['status' => 'error', 'message' => 'Database Connection Failed: ' . $conn->connect_error]));
}
?>
