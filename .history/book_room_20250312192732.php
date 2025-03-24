<?php
require 'backend.php';

header('Content-Type: application/json');

$data = json_decode(file_get_contents("php://input"), true);

$room_id = $data['room_id'];
$check_in_date = $data['check_in_date'];
$check_out_date = $data['check_out_date'];
$guest_count = $data['guest_count'];
$contact_info = $data['contact_info'];

if (empty($room_id) || empty($check_in_date) || empty($check_out_date) || empty($guest_count) || empty($contact_info)) {
    echo json_encode(["status" => "error", "message" => "All fields are required."]);
    exit;
}

// Check if room is available
$checkRoom = $conn->prepare("SELECT status FROM rooms WHERE room_id = ?");
$checkRoom->bind_param("i", $room_id);
$checkRoom->execute();
$checkRoom->bind_result($room_status);
$checkRoom->fetch();
$checkRoom->close();

if ($room_status !== "available") {
    echo json_encode(["status" => "error", "message" => "Selected room is not available."]);
    exit;
}

// Insert booking details
$stmt = $conn->prepare("INSERT INTO bookings (room_id, check_in_date, check_out_date, guest_count, contact_info, created_at) VALUES (?, ?, ?, ?, ?, NOW())");
$stmt->bind_param("issis", $room_id, $check_in_date, $check_out_date, $guest_count, $contact_info);

if ($stmt->execute()) {
    // Update room status to 'occupied'
    $updateRoom = $conn->prepare("UPDATE rooms SET status = 'occupied' WHERE room_id = ?");
    $updateRoom->bind_param("i", $room_id);
    $updateRoom->execute();
    $updateRoom->close();

    echo json_encode(["status" => "success", "message" => "Booking confirmed!"]);
} else {
    echo json_encode(["status" => "error", "message" => "Failed to book room."]);
}

$stmt->close();
$conn->close();
?>
