<?php
// Database connection (adjust your credentials if necessary)
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "coppers_ivy";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle POST request
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $data = json_decode(file_get_contents('php://input'), true);  // Get the JSON data from the frontend

    $roomName = $data['roomName'];
    $checkInDate = $data['checkInDate'];
    $checkOutDate = $data['checkOutDate'];
    $guestCount = $data['guestCount'];
    $contactInfo = $data['contactInfo'];

    // Insert the booking into the database
    $sql = "INSERT INTO bookings (room_name, check_in_date, check_out_date, guest_count, contact_info) 
            VALUES (?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssis", $roomName, $checkInDate, $checkOutDate, $guestCount, $contactInfo);

    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Booking confirmed!']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Booking failed: ' . $conn->error]);
    }

    $stmt->close();
}

$conn->close();
?>
