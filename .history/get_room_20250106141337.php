<?php
// Database connection (adjust your credentials if necessary)
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "coppers_ivy";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Set content type to JSON
header('Content-Type: application/json');

// Query to fetch only the required room details (id, room_name, price)
$sql = "SELECT id, room_name, price FROM rooms";
$result = $conn->query($sql);

$rooms = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $rooms[] = $row; // Store the room details
    }
    echo json_encode($rooms); // Send data back to the frontend
} else {
    echo json_encode(['status' => 'error', 'message' => 'No rooms found']);
}

$conn->close();
?>
