<?php
// Connect to MySQL (using XAMPP's default settings)
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "coppers_ivy"; // Ensure this database exists

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Set content type to JSON
header('Content-Type: application/json');

// Handle POST request for different actions
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $action = $_POST['action'] ?? '';

    // Handle room booking creation
    if ($action === 'book_room') {
        $room_id = $_POST['room_id'];
        $check_in_date = $_POST['check_in_date'];
        $check_out_date = $_POST['check_out_date'];
        $guest_count = $_POST['guest_count'];
        $contact_info = $_POST['contact_info'];

        // Simple validation (ensure required fields are filled)
        if (empty($room_id) || empty($check_in_date) || empty($check_out_date) || empty($guest_count) || empty($contact_info)) {
            echo json_encode(['status' => 'error', 'message' => 'All fields are required.']);
            exit;
        }

        // Check if the room is already booked for the given date range
        $sql_check = "SELECT * FROM bookings 
                      WHERE room_id = ? 
                      AND ((check_in_date <= ? AND check_out_date > ?) 
                      OR (check_in_date < ? AND check_out_date >= ?))";
        $stmt_check = $conn->prepare($sql_check);
        $stmt_check->bind_param("sssss", $room_id, $check_out_date, $check_in_date, $check_out_date, $check_in_date);
        $stmt_check->execute();
        $result_check = $stmt_check->get_result();

        if ($result_check->num_rows > 0) {
            echo json_encode(['status' => 'error', 'message' => 'This room is already booked for the selected dates.']);
            exit;
        }

        // Insert the booking into the database
        $sql_insert = "INSERT INTO bookings (room_id, check_in_date, check_out_date, guest_count, contact_info, created_at) 
                       VALUES (?, ?, ?, ?, ?, NOW())";
        $stmt_insert = $conn->prepare($sql_insert);
        $stmt_insert->bind_param("sssss", $room_id, $check_in_date, $check_out_date, $guest_count, $contact_info);

        if ($stmt_insert->execute()) {
            echo json_encode(['status' => 'success', 'message' => 'Room booked successfully!']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Error: ' . $conn->error]);
        }
    }

    // Handle fetching available rooms
    if ($action === 'get_rooms') {
        // Fetch available rooms from database
        $sql = "SELECT id, type, price FROM rooms"; // Ensure your table and column names match
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            $rooms = [];
            while ($row = $result->fetch_assoc()) {
                $rooms[] = $row;
            }
            echo json_encode($rooms);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'No rooms available']);
        }
    }
}

$conn->close();
?>
