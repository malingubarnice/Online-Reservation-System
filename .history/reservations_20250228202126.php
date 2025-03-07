<?php
$servername = "localhost";
$username = "username";
$password = "password";
$dbname = "database_name";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Prepare and bind
$stmt = $conn->prepare("INSERT INTO reservations (reservation_id, date, time, party_size, contact_info, special_requests, table_number) VALUES (?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param("isssisi", $reservation_id, $date, $time, $party_size, $contact_info, $special_requests, $table_number);

// Set parameters and execute
$reservation_id = null; // Assuming auto-increment
$date = "2025-02-28";
$time = "20:00";
$party_size = 4;
$contact_info = "contact@example.com";
$special_requests = "Window seat";
$table_number = 10;

$stmt->execute();

echo "New record created successfully";

$stmt->close();
$conn->close();
?>
