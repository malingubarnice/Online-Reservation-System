<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Database credentials
$servername = "localhost";
$username = "root";  // Change to your MySQL username
$password = "";      // Change to your MySQL password
$dbname = "coppers_ivy";  // Change to your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Read raw POST data from the request body
$input = file_get_contents('php://input');
$data = json_decode($input, true);

// Check if all required fields are present
if (isset($data['room_id'], $data['check_in_date'], $data['check_out_date'], $data['guest_count'], $data['contact_info'])) {
    // Sanitize and assign the data to variables
    $roomId = intval($data['room_id']);
    $checkInDate = $data['check_in_date'];
    $checkOutDate = $data['check_out_date'];
    $guestCount = intval($data['guest_count']);
    $contactInfo = $data['contact_info'];

    // Debugging: Output the contact info to check if it's being passed
    error_log("Received contact_info: " . $contactInfo);  // This logs the contact info to the error log

    // Validate guest count (make sure it's a positive number)
    if ($guestCount <= 0) {
        echo json_encode(['status' => 'error', 'message' => 'Guest count must be a positive number.']);
        exit;
    }

    // Date validation: Ensure check-in is not in the past and check-out is after check-in
    $today = date('Y-m-d');
    if ($checkInDate < $today) {
        echo json_encode(['status' => 'error', 'message' => 'Check-in date cannot be in the past.']);
        exit;
    }
    if ($checkInDate >= $checkOutDate) {
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

    // Insert booking data into the database, including contact_info
    $sql = "INSERT INTO bookings (room_id, check_in_date, check_out_date, guest_count, contact_info, created_at)
            VALUES (?, ?, ?, ?, ?, NOW())";

    // Prepare and execute the statement
    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        error_log("Failed to prepare the SQL statement.");
        echo json_encode(['status' => 'error', 'message' => 'Failed to prepare the SQL statement.']);
        exit;
    }

    // Bind parameters and execute
    $stmt->bind_param('issis', $roomId, $checkInDate, $checkOutDate, $guestCount, $contactInfo);

    // Execute the query
    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Booking successfully made!']);
    } else {
        error_log("Error executing query: " . $stmt->error);  // Log query execution errors
        echo json_encode(['status' => 'error', 'message' => 'Error: ' . $stmt->error]);
    }

    // Close statement
    $stmt->close();
} else {
    echo json_encode(['status' => 'error', 'message' => 'Missing required fields.']);
}

// Close the database connection
$conn->close();
?>
