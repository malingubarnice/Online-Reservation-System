<?php
require 'backend.php'; // Ensure this connects to your database

header('Content-Type: application/json');

$sql = "SELECT room_id, room_name, room_type, capacity, price_per_night, image_url, description, status FROM rooms WHERE status = 'available'";
$result = $conn->query($sql);

$rooms = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $rooms[] = $row;
    }
}

echo json_encode($rooms);
?>
