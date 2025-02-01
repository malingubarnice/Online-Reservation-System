<?php
// Database connection
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
    // Check if all required fields are filled
    if (empty($_POST['room_id']) || empty($_POST['check_in']) || empty($_POST['check_out']) || empty($_POST['guest_count']) || empty($_POST['contact_information']) || empty($_POST['total_price'])) {
        echo "All fields are required.";
    } else {
        $room_id = $_POST['room_id'];
        $check_in = $_POST['check_in'];
        $check_out = $_POST['check_out'];
        $guest_count = $_POST['guest_count'];
        $contact_information = $_POST['contact_information'];
        $total_price = $_POST['total_price'];

        // Validate dates (make sure check-out is after check-in)
        if (strtotime($check_in) >= strtotime($check_out)) {
            echo "Check-out date must be after check-in date.";
        } else {
            // Insert booking details into the database
            $query = "INSERT INTO bookings (room_id, check_in_date, check_out_date, guest_count, contact_information, total_price, created_at) 
                      VALUES (?, ?, ?, ?, ?, ?, NOW())";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("issdss", $room_id, $check_in, $check_out, $guest_count, $contact_information, $total_price);

            if ($stmt->execute()) {
                echo "Booking successful!";
                // Optionally, redirect to a confirmation page or display more info
                // header("Location: confirmation.php"); exit;
            } else {
                echo "Error: " . $stmt->error;
            }
        }
    }
}

$conn->close();
?>
