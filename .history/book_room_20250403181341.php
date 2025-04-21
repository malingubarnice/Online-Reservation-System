<?php
include 'connection.php'; // Include database connection

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

// Insert into `bookings` table
$sql = "INSERT INTO bookings (room_id, check_in_date, check_out_date, guest_count, contact_info, status, created_at) 
        VALUES ('$room_id', '$check_in_date', '$check_out_date', '$guest_count', '$contact_info', '$status', '$created_at')";

if ($conn->query($sql) === TRUE) {
    // **Send email notification**
    $to = $contact_info; // User email
    $subject = "Booking Confirmation";
    $message = "Your booking has been confirmed. Thank you for choosing us.";
    $headers = "From: bookings@yourhotel.com"; // Change this to your actual email

    mail($to, $subject, $message, $headers); // Send email

    // **Return simple message**
    echo json_encode(["status" => "success", "message" => "Booking confirmed! Email sent."]);
} else {
    echo json_encode(["status" => "error", "message" => "Error: " . $conn->error]);
}

// Close database connection
$conn->close();
?>
