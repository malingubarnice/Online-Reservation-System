<?php
// Connecting to MySQL
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "coppers_ivy";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die(json_encode(['status' => 'error', 'message' => 'Database connection failed: ' . $conn->connect_error]));
}

// Set content type to JSON
header('Content-Type: application/json');

// Handle POST request for room booking
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate and sanitize inputs
    $room_id = $_POST['room_id'] ?? '';
    $check_in_date = $_POST['check_in_date'] ?? '';
    $check_out_date = $_POST['check_out_date'] ?? '';
    $guest_count = $_POST['guest_count'] ?? '';
    $contact_info = $_POST['contact-info'] ?? '';

    // Ensure all required fields are filled
    if (empty($room_id) || empty($check_in_date) || empty($check_out_date) || empty($guest_count) || empty($contact_info)) {
        echo json_encode(['status' => 'error', 'message' => 'All fields are required.']);
        exit;
    }

    // Check if room is already booked for the selected dates
    $sql_check = "SELECT * FROM bookings WHERE room_id = ? AND (check_in_date <= ? AND check_out_date >= ? )";
    $stmt_check = $conn->prepare($sql_check);
    $stmt_check->bind_param("iss", $room_id, $check_out_date, $check_in_date);
    $stmt_check->execute();
    $result_check = $stmt_check->get_result();

    if ($result_check->num_rows > 0) {
        echo json_encode(['status' => 'error', 'message' => 'This room is already booked for the selected dates.']);
        exit;
    }

    // Generate a unique booking ID
    $booking_id = "BKG-" . rand(10000, 99999);

    // Insert booking into the database
    $sql_insert = "INSERT INTO bookings (booking_id, room_id, check_in_date, check_out_date, guest_count, contact_info, created_at) 
                   VALUES (?, ?, ?, ?, ?, ?, NOW())";
    $stmt_insert = $conn->prepare($sql_insert);
    $stmt_insert->bind_param("sissis", $booking_id, $room_id, $check_in_date, $check_out_date, $guest_count, $contact_info);

    if ($stmt_insert->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Room booked successfully!', 'booking_id' => $booking_id]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Error: ' . $conn->error]);
    }
}

$conn->close();
?>
