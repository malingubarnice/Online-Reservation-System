<?php
// Database connection details
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "coppers_ivy"; // Database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    // Connection failed
    die("Connection failed: " . $conn->connect_error);
} else {
    // Connection successful
    echo "Successfully connected to the database!";
}

// Close the connection
$conn->close();
?>
