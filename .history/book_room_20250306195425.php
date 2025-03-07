<?php
// Database connection
$servername = "localhost"; // Change if necessary
$username = "root"; // Change this if needed
$password = ""; // Change this if needed
$database = "coppers_ivy"; // Your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if form data is received
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $room_id = $_POST['room_id'];
    $check_in_date = $_POST['check_in_date'];
    $check_out_date = $_POST['check_out_date'];
    $guest_count = $_POST['guest_count'];
    $contact_info = $_POST['contact-info']; // Ensure correct name attribute in the form

    // Validate inputs (prevent empty values)
    if (empty($room_id) || empty($check_in_date) || empty($check_out_date) || empty($guest_count) || empty($contact_info)) {
        die("All fields are required.");
    }

    // SQL query to insert booking data
    $sql = "INSERT INTO bookings (room_id, check_in_date, check_out_date, guest_count, contact_info, created_at) 
            VALUES (?, ?, ?, ?, ?, NOW())";

    // Prepare and bind parameters
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("issis", $room_id, $check_in_date, $check_out_date, $guest_count, $contact_info);

    // Execute the query
    if ($stmt->execute()) {
        echo "Booking successful!";
    } else {
        echo "Error: " . $stmt->error;
    }

    // Close statement and connection
    $stmt->close();
    $conn->close();
} else {
    echo "Invalid request method.";
}
?>
