<?php
// Database connection
$host = 'localhost';
$user = 'root';
$password = ''; // Update with your actual database password
$dbname = 'coppers_ivy';

$conn = new mysqli($host, $user, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch room list
$result = $conn->query("SELECT id, room_name FROM rooms");
$rooms = [];

while ($row = $result->fetch_assoc()) {
    $rooms[] = $row;
}

// Return rooms as JSON
echo json_encode($rooms);

$conn->close();
?>
