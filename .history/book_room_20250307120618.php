<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "coppers_ivy";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $roomId = isset($_POST['room_id']) ? intval($_POST['room_id']) : 0;
    $roomName = $_POST['room_name'] ?? '';
    $checkInDate = $_POST['check_in_date'] ?? '';
    $checkOutDate = $_POST['check_out_date'] ?? '';
    $guestCount = isset($_POST['guest_count']) ? intval($_POST['guest_count']) : 0;
    $contactInfo = $_POST['contact_info'] ?? ''; // Ensure correct input name

    // Validate input fields
    if ($roomId == 0 || empty($checkInDate) || empty($checkOutDate) || $guestCount < 1 || empty($contactInfo)) {
        echo "<script>alert('Please fill all fields correctly.'); window.history.back();</script>";
        exit;
    }

    // Debugging - Show received values
    echo "<pre>";
    print_r($_POST);
    echo "</pre>";

    // Check for overlapping bookings
    $sql = "SELECT * FROM bookings WHERE room_id = ? AND NOT (check_out_date <= ? OR check_in_date >= ?)";
    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        die("Error in SQL Prepare: " . $conn->error);
    }
    $stmt->bind_param('iss', $roomId, $checkInDate, $checkOutDate);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result === false) {
        die("Error in executing query: " . $stmt->error);
    }

    if ($result->num_rows > 0) {
        echo "<script>alert('This room is already booked for the selected dates.'); window.history.back();</script>";
        exit;
    }

    // Generate a unique booking ID
    $bookingId = uniqid('BOOK_');

    // Insert booking into the database
    $sql = "INSERT INTO bookings (booking_id, room_id, check_in_date, check_out_date, guest_count, contact_info, created_at) 
            VALUES (?, ?, ?, ?, ?, ?, NOW())";

    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        die("Prepare failed: " . $conn->error);
    }
    $stmt->bind_param('sissis', $bookingId, $roomId, $checkInDate, $checkOutDate, $guestCount, $contactInfo);

    if ($stmt->execute()) {
        echo "<script>alert('Booking successful!'); window.location.href='confirmation.php';</script>";
    } else {
        die("Booking failed: " . $stmt->error);
    }

    $stmt->close();
}

$conn->close();
?>
