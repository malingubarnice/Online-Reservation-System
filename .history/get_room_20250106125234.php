<?php
// Connect to MySQL (using XAMPP's default settings)
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "coppers_ivy"; // Ensure this database exists

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Set content type to JSON
header('Content-Type: application/json');

// Fetch rooms from the database
$sql = "SELECT id, room_name, room_type, price, description, image_url FROM rooms";
$result = $conn->query($sql);

// Prepare response
$rooms = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $rooms[] = $row;
    }
    echo json_encode($rooms); // Return rooms as JSON
} else {
    echo json_encode(['status' => 'error', 'message' => 'No rooms available']);
}

$conn->close();
?>
