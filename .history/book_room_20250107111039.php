<?php
// Connect to MySQL (using XAMPP's default settings)
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "coppers_ivy";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Set content type to JSON
header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $room_id = $_POST['room_id'];
    $check_in_date = $_POST['check_in_date'];
    $check_out_date = $_POST['check_out_date'];
    $guest_count = $_POST['guest_count'];
    $contact_info = $_POST['contact_info'];

    // Validate required fields
    if (empty($room_id) || empty($check_in_date) || empty($check_out_date) || empty($guest_count) || empty($contact_info)) {
        echo json_encode(['status' => 'error', 'message' => 'All fields are required.']);
        exit;
    }

    // Validate guest count
    if (!is_numeric($guest_count) || $guest_count <= 0) {
        echo json_encode(['status' => 'error', 'message' => 'Guest count must be a positive number.']);
        exit;
    }

    // Validate date range
    if (strtotime($check_in_date) >= strtotime($check_out_date)) {
        echo json_encode(['status' => 'error', 'message' => 'Check-in date must be before check-out date.']);
        exit;
    }

    // Check for existing bookings with overlapping dates
    $sql_check = "SELECT * FROM bookings 
                  WHERE room_id = ? 
                  AND check_in_date < ? 
                  AND check_out_date > ?";
    $stmt_check = $conn->prepare($sql_check);
    $stmt_check->bind_param("iss", $room_id, $check_out_date, $check_in_date);
    $stmt_check->execute();
    $result_check = $stmt_check->get_result();

    if ($result_check->num_rows > 0) {
        echo json_encode(['status' => 'error', 'message' => 'This room is already booked for the selected dates. Please choose different dates or another room.']);
        exit;
    }

    // Insert the new booking
    $sql_insert = "INSERT INTO bookings (room_id, check_in_date, check_out_date, guest_count, contact_info) 
                   VALUES (?, ?, ?, ?, ?)";
    $stmt_insert = $conn->prepare($sql_insert);
    $stmt_insert->bind_param("issis", $room_id, $check_in_date, $check_out_date, $guest_count, $contact_info);

    if ($stmt_insert->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Room booked successfully!']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Error: ' . $conn->error]);
    }
}

$conn->close();
?>
