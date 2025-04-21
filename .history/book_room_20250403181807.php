<?php
include 'connection.php';

header('Content-Type: application/json'); // Ensure response is JSON

$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['room_id'], $data['check_in_date'], $data['check_out_date'], $data['guest_count'], $data['contact_info'])) {
    echo json_encode(["status" => "error", "message" => "Missing required fields"]);
    exit;
}

$room_id = $conn->real_escape_string($data['room_id']);
$check_in_date = $conn->real_escape_string($data['check_in_date']);
$check_out_date = $conn->real_escape_string($data['check_out_date']);
$guest_count = $conn->real_escape_string($data['guest_count']);
$contact_info = $conn->real_escape_string($data['contact_info']);
$status = "pending";
$created_at = date("Y-m-d H:i:s");

$sql = "INSERT INTO bookings (room_id, check_in_date, check_out_date, guest_count, contact_info, status, created_at) 
        VALUES ('$room_id', '$check_in_date', '$check_out_date', '$guest_count', '$contact_info', '$status', '$created_at')";

if ($conn->query($sql) === TRUE) {
    mail($contact_info, "Booking Confirmation", "Your booking is confirmed.", "From: bookings@yourhotel.com");
    echo json_encode(["status" => "success", "message" => "Booking confirmed! Email sent."]);
} else {
    echo json_encode(["status" => "error", "message" => "Error: " . $conn->error]);
}

$conn->close();
?>
