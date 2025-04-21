<?php
// Database connection details
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "coppers_ivy"; // Database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    // Connection failed
    die("Connection failed: " . $conn->connect_error);
} else {
    // Connection successful
    echo "Successfully connected to the database!<br>";
}

// Manually inserting data into the bookings2 table
$booking_id = "BKG-" . date("Ymd") . "-" . rand(100, 999);  // Generate unique booking ID
$room_id = 1;  // Room ID
$check_in_date = "2025-03-17";  // Check-in date
$check_out_date = "2025-03-21";  // Check-out date
$guest_count = 8;  // Number of guests
$contact_info = "royaltyher002@gmail.com";  // User's contact info
$status = "pending";  // Status of the booking
$created_at = date("Y-m-d H:i:s");  // Current timestamp

// SQL query to insert data into bookings2 table
$sql = "INSERT INTO bookings2 (booking_id, room_id, check_in_date, check_out_date, guest_count, contact_info, status, created_at)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

// Prepare and bind
$stmt = $conn->prepare($sql);
$stmt->bind_param("sissssss", $booking_id, $room_id, $check_in_date, $check_out_date, $guest_count, $contact_info, $status, $created_at);

// Execute the query
if ($stmt->execute()) {
    echo "Data inserted successfully!";
} else {
    echo "Error: " . $stmt->error;
}

// Close the connection
$stmt->close();
$conn->close();
?>
