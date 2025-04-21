<?php
// Database connection details
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "coppers_ivy"; // Database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    // Connection failed
    die("Connection failed: " . $conn->connect_error);
} else {
    // Connection successful
    echo "Successfully connected to the database!<br>";
}

// Check if all required form fields are set
if (isset($_POST['room_id'], $_POST['check_in_date'], $_POST['check_out_date'], $_POST['guest_count'], $_POST['contact-info'])) {
    // Retrieve data from the form
    $room_id = $_POST['room_id'];  // Room ID
    $check_in_date = $_POST['check_in_date'];  // Check-in date
    $check_out_date = $_POST['check_out_date'];  // Check-out date
    $guest_count = $_POST['guest_count'];  // Number of guests
    $contact_info = $_POST['contact-info'];  // User's contact info
    $status = "pending";  // Status of the booking
    $created_at = date("Y-m-d H:i:s");  // Current timestamp

    // Generate unique booking ID
    $booking_id = "BKG-" . date("Ymd") . "-" . rand(100, 999);  

    // SQL query to insert data into bookings2 table
    $sql = "INSERT INTO bookings2 (booking_id, room_id, check_in_date, check_out_date, guest_count, contact_info, status, created_at)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

    // Prepare and bind
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sissssss", $booking_id, $room_id, $check_in_date, $check_out_date, $guest_count, $contact_info, $status, $created_at);

    // Execute the query
    if ($stmt->execute()) {
        echo "Data inserted successfully!";
    } else {
        echo "Error: " . $stmt->error;
    }

    // Close the connection
    $stmt->close();
} else {
    echo "Error: Missing required form fields.";
}

// Close the connection
$conn->close();
?>
