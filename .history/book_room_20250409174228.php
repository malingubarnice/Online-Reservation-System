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
echo "Booking was successful";
// Set content type to JSON
header('Content-Type: application/json');

// Handle POST request for different actions
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $action = $_POST['action'] ?? '';

    // Handle room booking
    if ($action === 'reserve') {
        // Validate and sanitize inputs
        $check_in_date = $_POST['check_in_date'] ?? '';
        $check_out_date = $_POST['check_out_date'] ?? '';
        $guest_count = $_POST['guest_count'] ?? '';
        $contact_info = $_POST['contact-info'] ?? '';

        // Ensure all fields are filled
        if (empty($check_in_date) || empty($check_out_date) || empty($guest_count) || empty($contact_info)) {
            echo json_encode(['status' => 'error', 'message' => 'All fields are required.']);
            exit;
        }

        // Check if the room is already reserved for the selected dates
        $sql_check = "SELECT * FROM bookings WHERE check_in_date = ? AND check_out_date = ?";
        $stmt_check = $conn->prepare($sql_check);
        $stmt_check->bind_param("ss", $check_in_date, $check_out_date);
        $stmt_check->execute();
        $result_check = $stmt_check->get_result();

        if ($result_check->num_rows > 0) {
            echo json_encode(['status' => 'error', 'message' => 'This room is already reserved for the selected dates.']);
            exit;
        }

        // Generate a unique booking ID
        $booking_id = "BOOK-" . date("Ymd") . "-" . rand(100, 999);

        // Insert reservation into the database
        $sql_insert = "INSERT INTO bookings2 (booking_id, check_in_date, check_out_date, guest_count, contact_info) 
                       VALUES (?, ?, ?, ?, ?)";
        $stmt_insert = $conn->prepare($sql_insert);
        $stmt_insert->bind_param("sssss", $booking_id, $check_in_date, $check_out_date, $guest_count, $contact_info);

        // Check if the query executes successfully
        if ($stmt_insert->execute()) {
            // Send confirmation email
            require 'send_room.php'; // Include the email sending script
            $_POST['contact-info'] = $contact_info;
            $_POST['check_in_date'] = $check_in_date;
            $_POST['check_out_date'] = $check_out_date;
            $_POST['guest_count'] = $guest_count;
            include 'send_room.php'; // Send the email

            echo json_encode(['status' => 'success', 'message' => 'Booking created successfully!', 'booking_id' => $booking_id]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Error: ' . $stmt_insert->error]);  // Improved error reporting
        }

        exit;
    }
}

$conn->close();
?>
