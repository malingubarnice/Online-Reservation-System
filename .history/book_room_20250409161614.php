<?php
// Connecting to MySQL
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "coppers_ivy"; // Database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die(json_encode(['status' => 'error', 'message' => 'Database connection failed: ' . $conn->connect_error]));
}

// Set content type to JSON
header('Content-Type: application/json');

// Handle POST request for booking room
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $action = $_POST['action'] ?? '';

    if ($action === 'book_room') {
        // Validate and sanitize inputs
        $room_id = $_POST['room_id'] ?? '';
        $check_in_date = $_POST['check_in_date'] ?? '';
        $check_out_date = $_POST['check_out_date'] ?? '';
        $guest_count = $_POST['guest_count'] ?? '';
        $contact_info = $_POST['contact_info'] ?? '';

        // Ensure all fields are filled
        if (empty($room_id) || empty($check_in_date) || empty($check_out_date) || empty($guest_count) || empty($contact_info)) {
            echo json_encode(['status' => 'error', 'message' => 'All booking fields are required.']);
            exit;
        }

        // Generate unique booking_id in the format BKG-YYYYMMDD-### 
        $booking_id = "BKG-" . date("Ymd") . "-" . str_pad(rand(0, 999), 3, '0', STR_PAD_LEFT);
        $status = 'pending'; // Set initial status to pending

        // Prepare the SQL insert statement
        $sql = "INSERT INTO bookings2 (booking_id, room_id, check_in_date, check_out_date, guest_count, contact_info, status, created_at) 
                VALUES (?, ?, ?, ?, ?, ?, ?, NOW())";

        // Prepare statement
        $stmt = $conn->prepare($sql);

        if (!$stmt) {
            echo json_encode(['status' => 'error', 'message' => 'Prepare failed: ' . $conn->error]);
            exit;
        }

        // Bind parameters to the SQL query
        $stmt->bind_param("sississ", $booking_id, $room_id, $check_in_date, $check_out_date, $guest_count, $contact_info, $status);

        // Execute the query and check for success
        if ($stmt->execute()) {
            // Send a confirmation email to the user (if needed)
            require 'send.php';  // Include the email sending script if necessary
            $_POST['contact-info'] = $contact_info;
            $_POST['customer-name'] = 'Valued Customer';  // Replace with actual customer name if available
            $_POST['date'] = $check_in_date;
            $_POST['time'] = '';  // Not relevant for room booking
            $_POST['party-size'] = $guest_count;
            $_POST['special-requests'] = '';  // Add special requests if available
            include 'send.php'; // Sending email to user

            // Return success response
            echo json_encode([
                'status' => 'success',
                'message' => 'Room booked successfully!',
                'booking_id' => $booking_id
            ]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Error booking room: ' . $stmt->error]);
        }

        exit;
    }
}

$conn->close();
?>
