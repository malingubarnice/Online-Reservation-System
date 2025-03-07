<?php
// Database connection details
$servername = "localhost"; // e.g., localhost
$username = "root";
$password = "";
$dbname = "coppers_ivy";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize and retrieve form data
    $room_id = mysqli_real_escape_string($conn, $_POST["room_id"]);
    $room_name = mysqli_real_escape_string($conn, $_POST["room_name"]);
    $check_in_date = mysqli_real_escape_string($conn, $_POST["check_in_date"]);
    $check_out_date = mysqli_real_escape_string($conn, $_POST["check_out_date"]);
    $guest_count = mysqli_real_escape_string($conn, $_POST["guest_count"]);
    $contact_info = mysqli_real_escape_string($conn, $_POST["contact-info"]);
    $price = mysqli_real_escape_string($conn, $_POST["price"]);

    // SQL query to insert data into the 'bookings' table
    $sql = "INSERT INTO bookings (room_id, room_name, check_in_date, check_out_date, guest_count, contact_info, price)
            VALUES ('$room_id', '$room_name', '$check_in_date', '$check_out_date', '$guest_count', '$contact_info', '$price')";

    if ($conn->query($sql) === TRUE) {
        echo "New record created successfully";
        // Optionally, redirect the user to a success page
        // header("Location: success.php");
        // exit; // Important to stop further execution
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

$conn->close();
?>