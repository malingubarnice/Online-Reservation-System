<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database connection
$host = "localhost";  
$username = "root";   
$password = "";       
$database = "coppers_ivy"; 

$conn = new mysqli($host, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Debugging: Check if form data is received
    print_r($_POST);

    // Retrieve form data
    $room_id = isset($_POST['room_id']) ? $_POST['room_id'] : '';
    $room_name = isset($_POST['room_name']) ? $_POST['room_name'] : '';
    $check_in_date = isset($_POST['check_in_date']) ? $_POST['check_in_date'] : '';
    $check_out_date = isset($_POST['check_out_date']) ? $_POST['check_out_date'] : '';
    $guest_count = isset($_POST['guest_count']) ? $_POST['guest_count'] : '';
    $contact_info = isset($_POST['contact_info']) ? $_POST['contact_info'] : '';

    // Validate inputs
    if (empty($room_id) || empty($check_in_date) || empty($check_out_date) || empty($guest_count) || empty($contact_info)) {
        die("Error: All fields are required.");
    }

    if ($guest_count <= 0) {
        die("Error: Invalid guest count.");
    }

    // Generate a unique booking ID
    $booking_id = "BKG-" . rand(10000, 99999);

    // Insert booking into database
    $query = "INSERT INTO bookings (booking_id, room_id, check_in_date, check_out_date, guest_count, contact_info, created_at) 
              VALUES (?, ?, ?, ?, ?, ?, NOW())";

    if ($stmt = $conn->prepare($query)) {
        $stmt->bind_param("sissis", $booking_id, $room_id, $check_in_date, $check_out_date, $guest_count, $contact_info);

        if ($stmt->execute()) {
            // Booking successful, redirect to send_room.php to send confirmation email
            header("Location: send_room.php?booking_id=$booking_id&room_name=$room_name&check_in=$check_in_date&check_out=$check_out_date&guests=$guest_count&contact=$contact_info");
            exit;
        } else {
            echo "Error inserting data: " . $stmt->error;
        }

        $stmt->close();
    } else {
        echo "Error preparing statement: " . $conn->error;
    }

    // Close the connection
    $conn->close();
} else {
    echo "Invalid request.";
}
?>
