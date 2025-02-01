<?php
// Database connection
include 'connect.php'; // Assuming you have a connect.php file with the connection code

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $room_id = $_POST['room_selection'];
    $check_in = $_POST['check_in_date'];
    $check_out = $_POST['check_out_date'];
    $guest_count = $_POST['guest_count'];
    $contact_info = $_POST['contact_info'];

    // Insert booking into database
    $stmt = $conn->prepare("INSERT INTO bookings (room_id, check_in_date, check_out_date, guest_count, contact_info) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("issis", $room_id, $check_in, $check_out, $guest_count, $contact_info);

    if ($stmt->execute()) {
        echo "Booking successful!";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>
