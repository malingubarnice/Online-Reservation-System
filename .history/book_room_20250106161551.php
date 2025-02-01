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

// Handle the booking form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $room_id = $_POST['room_id'];
    $check_in = $_POST['check_in'];
    $check_out = $_POST['check_out'];
    $guest_count = $_POST['guest_count'];
    $contact_info = $_POST['contact_information'];

    // Validate inputs
    if ($guest_count <= 0) {
        echo "Invalid guest count. Please enter a positive number.";
        exit;
    }

    if (strtotime($check_in) >= strtotime($check_out)) {
        echo "Invalid date range. Check-out date must be after check-in date.";
        exit;
    }

    // Insert the booking details into the database
    $query = "INSERT INTO bookings (room_id, check_in_date, check_out_date, guest_count, contact_info, created_at) 
              VALUES (?, ?, ?, ?, ?, NOW())";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("issis", $room_id, $check_in, $check_out, $guest_count, $contact_info);

    if ($stmt->execute()) {
        echo "Booking successful! Your booking has been confirmed.";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}

$conn->close();
?>
