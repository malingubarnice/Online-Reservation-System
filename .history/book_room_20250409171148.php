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

// Handle POST request for different actions
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $action = $_POST['action'] ?? '';

    // Handle reservation creation
    if ($action === 'reserve') {
        // Validate and sanitize inputs
        $date1 = $_POST['check-in-date'] ?? '';
        $date2 = $_POST['check-out-date'] ?? '';
        $guests = $_POST['guest-count']?? '';
        $contact = $_POST['contact-info']?? '';
        
        

        // Ensure all fields are filled
        if (empty($date1) || empty($date2) || empty($guests) || empty($contact) {
            echo json_encode(['status' => 'error', 'message' => 'All fields are required.']);
            exit;
        }

       


        // Check if the room is already reserved
        $sql_check = "SELECT * FROM bookings2 WHERE check_in_date = ? AND check_out_date = ?";
        $stmt_check = $conn->prepare($sql_check);
        $stmt_check->bind_param("ssiss", $date1, $date2, $guests, $contact);  // Bind parameters: 'ssiss' - string, string, integer, string
        $stmt_check->execute();
        $result_check = $stmt_check->get_result();

        if ($result_check->num_rows > 0) {
            echo json_encode(['status' => 'error', 'message' => 'This room is already reserved for the selected date and time.']);
            exit;
        }

        // Generate a unique reservation ID
        $booking_id = "RES-" . date("Ymd") . "-" . rand(100, 999);

        // Insert reservation into the database
        $sql_insert = "INSERT INTO bookings2 (booking_id, room_id, check_in_date, check_out_date, guest_count, contact_info, status, created_at)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt_insert = $conn->prepare($sql_insert);
        $stmt_insert->bind_param("ssissss", $booking_id, $room_id, $check_in_date, $check_out_date, $guest_count, $contact_info, $status);

        if ($stmt_insert->execute()) {
            // Send confirmation email
            require 'send_room.php';
            $_POST['check-in-date'] =$check_in_date;
            $_POST['check-out-date'] = $check_out_date;
            $_POST['guest-count'] = $guest_count;
            $_POST['contact-info'] = $contact_info;
            include 'send_room.php';

            echo json_encode(['status' => 'success', 'message' => 'Booking created successfully!', 'booking_id' => $booking_id]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Error: ' . $conn->error]);
        }

        exit;
    }

}

$conn->close();
?>
