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

// Read the raw POST data from the request body
$input = file_get_contents('php://input');

// Log the raw request for debugging (optional)
error_log("Received data: " . $input);

// Decode the JSON data
$data = json_decode($input, true);

// Check if the required fields are provided
if (isset($data['room_id'], $data['check_in_date'], $data['check_out_date'], $data['guest_count'], $data['contact_info'])) {
    // Sanitize the data
    $roomId = intval($data['room_id']);
    $checkInDate = $data['check_in_date'];
    $checkOutDate = $data['check_out_date'];
    $guestCount = intval($data['guest_count']);
    $contactInfo = $data['contact_info'];

    // Make sure the guest count is a positive integer
    if ($guestCount <= 0) {
        echo json_encode(['status' => 'error', 'message' => 'Guest count must be a positive number.']);
        exit;
    }

    // Check if the dates are valid
    $today = date('Y-m-d');
    if ($checkInDate < $today) {
        echo json_encode(['status' => 'error', 'message' => 'Check-in date cannot be in the past.']);
        exit;
    }

    if ($checkInDate >= $checkOutDate) {
        echo json_encode(['status' => 'error', 'message' => 'Check-out date must be after the check-in date.']);
        exit;
    }

    // Now, insert the booking data into the database
    try {
        // Prepare the SQL statement for insertion
        $sql = "INSERT INTO bookings (room_id, check_in_date, check_out_date, guest_count, contact_info, created_at)
                VALUES (?, ?, ?, ?, ?, NOW())";

        // Prepare statement
        $stmt = $conn->prepare($sql);
        if ($stmt === false) {
            throw new Exception("Failed to prepare the SQL statement.");
        }

        // Bind parameters
        $stmt->bind_param('issis', $roomId, $checkInDate, $checkOutDate, $guestCount, $contactInfo);

        // Execute the query
        if ($stmt->execute()) {
            echo json_encode(['status' => 'success', 'message' => 'Booking successfully made!']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Error: ' . $stmt->error]);
        }

        // Close the statement
        $stmt->close();

    } catch (Exception $e) {
        // Handle any errors with the database or SQL query
        error_log("Database error: " . $e->getMessage());
        echo json_encode(['status' => 'error', 'message' => 'Database error. Please try again later.']);
    }
} else {
    // Return an error message if the required fields are missing
    echo json_encode(['status' => 'error', 'message' => 'Missing required fields.']);
}

// Close the database connection
$conn->close();
?>
