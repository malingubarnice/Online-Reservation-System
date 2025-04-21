<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "coppers_ivy"; // Database name

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Collect form data
    $roomId = isset($_POST['room_id']) ? intval($_POST['room_id']) : 0;
    $checkInDate = $_POST['check_in_date'] ?? '';
    $checkOutDate = $_POST['check_out_date'] ?? '';
    $guestCount = isset($_POST['guest_count']) ? intval($_POST['guest_count']) : 0;
    $contactInfo = $_POST['contact-info'] ?? '';

    // Ensure all fields are filled
    if ($roomId == 0 || empty($roomName) || empty($checkInDate) || empty($checkOutDate) || $guestCount < 1 || empty($contactInfo)) {
        echo "All fields are required.";
        exit;
    }

    // Check for overlapping bookings
    $sql = "SELECT * FROM bookings2 WHERE room_id = ? AND NOT (check_out_date <= ? OR check_in_date >= ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('iss', $roomId, $checkInDate, $checkOutDate);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo "This room is already booked for the selected dates.";
        exit;
    }

    // Generate a unique booking ID in the format BKG-XXXXX
    $bookingId = "BKG-" . rand(10000, 99999);

    // Insert booking into the database
    $sql = "INSERT INTO bookings2 (booking_id, room_id, check_in_date, check_out_date, guest_count, contact_info, created_at) 
            VALUES (?, ?, ?, ?, ?, ?, ?, NOW())";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('sisssis', $bookingId, $roomId, $roomName, $checkInDate, $checkOutDate, $guestCount, $contactInfo);

}

$conn->close();
?>
