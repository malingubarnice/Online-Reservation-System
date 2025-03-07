<?php
// Database connection
$host = "localhost";  // Change this if needed
$username = "root";   // Your database username
$password = "";       // Your database password
$database = "coppers_ivy"; // Your database name

// Create connection
$conn = new mysqli($host, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $room_id = $_POST['room_id'];
    $check_in_date = $_POST['check_in_date'];
    $check_out_date = $_POST['check_out_date'];
    $guest_count = $_POST['guest_count'];
    $contact_info = $_POST['contact-info'];

    // Validate inputs
    if (empty($room_id) || empty($check_in_date) || empty($check_out_date) || empty($guest_count) || empty($contact_info)) {
        die("All fields are required.");
    }

    if ($guest_count <= 0) {
        die("Invalid guest count.");
    }

    // Generate a simple unique booking ID (BKG-XXXXX)
    $booking_id = "BKG-" . rand(10000, 99999); // Random 5-digit number

    // Prepare and execute the insert query
    $query = "INSERT INTO bookings (booking_id, room_id, check_in_date, check_out_date, guest_count, contact_info, created_at) 
              VALUES (?, ?, ?, ?, ?, ?, NOW())";

    if ($stmt = $conn->prepare($query)) {
        $stmt->bind_param("sissis", $booking_id, $room_id, $check_in_date, $check_out_date, $guest_count, $contact_info);

        if ($stmt->execute()) {
            echo "Booking successful! Your Booking ID is: " . $booking_id;
        } else {
            echo "Error: " . $stmt->error;
        }

        $stmt->close();
    } else {
        echo "Error preparing statement.";
    }

    // Close the connection
    $conn->close();
} else {
    echo "Invalid request.";
}
?>
