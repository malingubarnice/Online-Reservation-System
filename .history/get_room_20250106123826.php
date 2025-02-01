<?php
// Include database connection
include 'backend.php'; // Make sure this file is included

// Fetch rooms from the database
$sql = "SELECT id, name, description, price_per_night, image_url FROM rooms";
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

$conn->close(); // Close the connection
?>
