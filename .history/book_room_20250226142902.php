<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Database credentials
$servername = "localhost";
$username = "root";  // MySQL username
$password = "";      // MySQL password
$dbname = "coppers_ivy";  // My database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Read raw POST data from the request body
$input = file_get_contents('php://input');
$data = json_decode($input, true);

// Log received data
error_log("Received data: " . print_r($data, true));

// Check if all required fields are present
if (isset($data['room_id'], $data['check_in_date'], $data['check_out_date'], $data['guest_count'], $data['contact_info'])) {
    // Sanitize and assign the data to variables
    $roomId = intval($data['room_id']);
    $checkInDate = $data['check_in_date'];
    $checkOutDate = $data['check_out_date'];
    $guestCount = intval($data['guest_count']);
    $contactInfo = $data['contact_info'];

    // Convert dates to DateTime objects for accurate comparison
    $today = new DateTime();
    $checkIn = new DateTime($checkInDate);
    $checkOut = new DateTime($checkOutDate);

    // Validate guest count (make sure it's a positive number)
    if ($guestCount <= 0) {
        echo json_encode(['status' => 'error', 'message' => 'Guest count must be a positive number.']);
        exit;
    }

    // Validate check-in date (must not be in the past)
    if ($checkIn < $today) {
        echo json_encode(['status' => 'error', 'message' => 'Check-in date cannot be in the past.']);
        exit;
    }

    // Validate check-out date (must be after check-in)
    if ($checkIn >= $checkOut) {
        echo json_encode(['status' => 'error', 'message' => 'Check-out date must be after the check-in date.']);
        exit;
    }

    // Check if the room is already booked for the selected dates
    $sql = "SELECT * FROM bookings WHERE room_id = ? AND ((check_in_date BETWEEN ? AND ?) OR (check_out_date BETWEEN ? AND ?))";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('issss', $roomId, $checkInDate, $checkOutDate, $checkInDate, $checkOutDate);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo json_encode(['status' => 'error', 'message' => 'This room is already booked for the selected dates.']);
        exit;
    }

    // Insert booking data into the database
    $sql = "INSERT INTO bookings (room_id, check_in_date, check_out_date, guest_count, contact_info, created_at)
            VALUES (?, ?, ?, ?, ?, NOW())";

    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        error_log("Failed to prepare the SQL statement.");
        echo json_encode(['status' => 'error', 'message' => 'Failed to prepare the SQL statement.']);
        exit;
    }

    $stmt->bind_param('issis', $roomId, $checkInDate, $checkOutDate, $guestCount, $contactInfo);

    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Booking successfully made!']);
    } else {
        error_log("Error executing query: " . $stmt->error);
        echo json_encode(['status' => 'error', 'message' => 'Error: ' . $stmt->error]);
    }

    $stmt->close();
} else {
    echo json_encode(['status' => 'error', 'message' => 'Missing required fields.']);
}

// Close the database connection
$conn->close();
?>
