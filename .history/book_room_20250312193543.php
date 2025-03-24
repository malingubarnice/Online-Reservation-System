<?php
include 'connection.php'; // Include the database connection file

// Read JSON input from the request
$data = json_decode(file_get_contents("php://input"), true);

// Check if all required fields are present
if (!isset($data['room_id'], $data['check_in_date'], $data['check_out_date'], $data['guest_count'], $data['contact_info'])) {
    echo json_encode(["status" => "error", "message" => "Missing required fields"]);
    exit;
}

// Sanitize input values
$room_id = $conn->real_escape_string($data['room_id']);
$check_in_date = $conn->real_escape_string($data['check_in_date']);
$check_out_date = $conn->real_escape_string($data['check_out_date']);
$guest_count = $conn->real_escape_string($data['guest_count']);
$contact_info = $conn->real_escape_string($data['contact_info']);
$status = "pending"; // Default status
$created_at = date("Y-m-d H:i:s"); // Current timestamp

// Generate unique booking ID
$booking_id = "BKG-" . date("Ymd") . "-" . rand(100, 999);

// Insert data into the `bookings` table
$sql = "INSERT INTO bookings (booking_id, room_id, check_in_date, check_out_date, guest_count, contact_info, status, created_at) 
        VALUES ('$booking_id', '$room_id', '$check_in_date', '$check_out_date', '$guest_count', '$contact_info', '$status', '$created_at')";

if ($conn->query($sql) === TRUE) {
    echo json_encode(["status" => "success", "message" => "Booking confirmed!", "booking_id" => $booking_id]);
} else {
    echo json_encode(["status" => "error", "message" => "Error: " . $conn->error]);
}

// Close database connection
$conn->close();
?>
