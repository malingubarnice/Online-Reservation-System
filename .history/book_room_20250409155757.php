<?php
// book_room.php

// Database connection
$host = 'localhost';
$db = ''; // change to your actual DB
$user = 'root';     // change to your DB username
$pass = 'your_db_password'; // change to your DB password

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("❌ Database connection failed: " . $conn->connect_error);
}

// Read JSON input
$data = json_decode(file_get_contents('php://input'), true);

// Check if all required data is present
if (
    !isset($data['room_id']) || !isset($data['check_in_date']) ||
    !isset($data['check_out_date']) || !isset($data['guest_count']) ||
    !isset($data['contact_info'])
) {
    die("❌ Error: Missing booking data.");
}

$room_id = intval($data['room_id']);
$check_in_date = $conn->real_escape_string($data['check_in_date']);
$check_out_date = $conn->real_escape_string($data['check_out_date']);
$guest_count = intval($data['guest_count']);
$contact_info = $conn->real_escape_string($data['contact_info']);
$status = 'pending';
$created_at = date('Y-m-d H:i:s');

// Generate booking ID like BKG-YYYYMMDD-XXX
$today = date('Ymd');
$countResult = $conn->query("SELECT COUNT(*) as count FROM bookings2 WHERE DATE(created_at) = CURDATE()");
$countRow = $countResult->fetch_assoc();
$nextCount = str_pad($countRow['count'] + 1, 3, '0', STR_PAD_LEFT);
$booking_id = "BKG-$today-$nextCount";

// Insert into bookings2 table
$sql = "INSERT INTO bookings2 (
    booking_id, room_id, check_in_date, check_out_date,
    guest_count, contact_info, status, created_at
) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

$stmt = $conn->prepare($sql);
$stmt->bind_param(
    "sississs",
    $booking_id,
    $room_id,
    $check_in_date,
    $check_out_date,
    $guest_count,
    $contact_info,
    $status,
    $created_at
);

if ($stmt->execute()) {
    echo "✅ Booking successful!<br>";
    echo "Your Booking ID is: <strong>$booking_id</strong><br>";
    echo "Room ID: $room_id<br>";
    echo "Check-in: $check_in_date<br>";
    echo "Check-out: $check_out_date<br>";
    echo "Guests: $guest_count<br>";
    echo "Contact: $contact_info<br>";
} else {
    echo "❌ Error saving booking: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
