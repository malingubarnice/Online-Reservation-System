<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "coppers_ivy";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die(json_encode(['status' => 'error', 'message' => 'Database connection failed: ' . $conn->connect_error]));
}

// Set content type to JSON
header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $action = $_POST['action'] ?? '';

    // Handle reservation creation
    if ($action === 'reserve') {
        // Validate and sanitize inputs
        $roomId = isset($_POST['room_id']) ? intval($_POST['room_id']) : 0;
        $roomName = $_POST['room_name'] ?? '';
        $checkInDate = $_POST['check_in_date'] ?? '';
        $checkOutDate = $_POST['check_out_date'] ?? '';
        $guestCount = isset($_POST['guest_count']) ? intval($_POST['guest_count']) : 0;
        $contactInfo = $_POST['contact_info'] ?? '';

        // Ensure all fields are filled
        if ($roomId == 0 || empty($checkInDate) || empty($checkOutDate) || $guestCount < 1 || empty($contactInfo)) {
            echo json_encode(['status' => 'error', 'message' => 'All fields are required.']);
            exit;
        }

        // Check for overlapping bookings
        $sql = "SELECT * FROM bookings WHERE room_id = ? AND NOT (check_out_date <= ? OR check_in_date >= ?);";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('iss', $roomId, $checkInDate, $checkOutDate);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            echo json_encode(['status' => 'error', 'message' => 'This room is already booked for the selected dates.']);
            exit;
        }

        // Generate a unique booking ID in the format BKG-XXXXX
        $bookingId = "BKG-" . rand(10000, 99999);

        // Insert booking into the database
        $sql = "INSERT INTO bookings (booking_id, room_id, check_in_date, check_out_date, guest_count, contact_info, created_at) 
                VALUES (?, ?, ?, ?, ?, ?, NOW())";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('sissis', $bookingId, $roomId, $checkInDate, $checkOutDate, $guestCount, $contactInfo);

        if ($stmt->execute()) {
            echo json_encode(['status' => 'success', 'message' => 'Booking successful!', 'booking_id' => $bookingId]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Booking failed: ' . $stmt->error]);
        }
        exit;
    }
}

$conn->close();
?>
