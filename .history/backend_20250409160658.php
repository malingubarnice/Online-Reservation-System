<?php
// Connecting to MySQL
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "coppers_ivy";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die(json_encode(['status' => 'error', 'message' => 'Database connection failed: ' . $conn->connect_error]));
}

// Set content type to JSON
header('Content-Type: application/json');

// Handle POST request for different actions
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $action = $_POST['action'] ?? '';

    // 1. HANDLE RESERVATIONS
    if ($action === 'reserve') {
        $date = $_POST['date'] ?? '';
        $time = $_POST['time'] ?? '';
        $party_size = $_POST['party_size'] ?? '';
        $contact_info = $_POST['contact_info'] ?? '';
        $special_requests = $_POST['special_requests'] ?? '';
        $selected_table = $_POST['selected_table'] ?? '';

        if (empty($date) || empty($time) || empty($party_size) || empty($contact_info) || empty($selected_table)) {
            echo json_encode(['status' => 'error', 'message' => 'All fields are required.']);
            exit;
        }

        $formatted_time = date("h:i A", strtotime($time));
        if ($formatted_time === "12:00 AM") {
            echo json_encode(['status' => 'error', 'message' => 'Invalid time format.']);
            exit;
        }

        // Check if table is already reserved
        $sql_check = "SELECT * FROM reservations WHERE date = ? AND time = ? AND table_number = ?";
        $stmt_check = $conn->prepare($sql_check);
        $stmt_check->bind_param("sss", $date, $time, $selected_table);
        $stmt_check->execute();
        $result_check = $stmt_check->get_result();

        if ($result_check->num_rows > 0) {
            echo json_encode(['status' => 'error', 'message' => 'This table is already reserved for the selected date and time.']);
            exit;
        }

        $reservation_id = "RES-" . date("Ymd") . "-" . rand(100, 999);

        $sql_insert = "INSERT INTO reservations (reservation_id, date, time, party_size, contact_info, special_requests, table_number) 
                       VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt_insert = $conn->prepare($sql_insert);
        $stmt_insert->bind_param("ssissss", $reservation_id, $date, $time, $party_size, $contact_info, $special_requests, $selected_table);

        if ($stmt_insert->execute()) {
            require 'send.php';
            $_POST['contact-info'] = $contact_info;
            $_POST['customer-name'] = 'Valued Customer';
            $_POST['date'] = $date;
            $_POST['time'] = $time;
            $_POST['party-size'] = $party_size;
            $_POST['special-requests'] = $special_requests;
            include 'send.php';

            echo json_encode(['status' => 'success', 'message' => 'Reservation created successfully!', 'reservation_id' => $reservation_id]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Error: ' . $conn->error]);
        }

        exit;
    }

    // 2. HANDLE ROOM BOOKING
    if ($action === 'book_room') {
        $room_id = $_POST['room_id'] ?? '';
        $check_in_date = $_POST['check_in_date'] ?? '';
        $check_out_date = $_POST['check_out_date'] ?? '';
        $guest_count = $_POST['guest_count'] ?? '';
        $contact_info = $_POST['contact_info'] ?? '';

        if (empty($room_id) || empty($check_in_date) || empty($check_out_date) || empty($guest_count) || empty($contact_info)) {
            echo json_encode(['status' => 'error', 'message' => 'All booking fields are required.']);
            exit;
        }

        $booking_id = "BKG-" . date("Ymd") . "-" . str_pad(rand(0, 999), 3, '0', STR_PAD_LEFT);
        $status = 'pending';

        $sql = "INSERT INTO bookings (booking_id, room_id, check_in_date, check_out_date, guest_count, contact_info, status) 
                VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sississ", $booking_id, $room_id, $check_in_date, $check_out_date, $guest_count, $contact_info, $status);

        if ($stmt->execute()) {
            echo json_encode([
                'status' => 'success',
                'message' => 'Room booked successfully!',
                'booking_id' => $booking_id
            ]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Booking failed: ' . $conn->error]);
        }

        exit;
    }

    // 3. GET MENU ITEMS
    if ($action === 'get_menu') {
        $sql = "SELECT name, description, price, image_url FROM menu_items";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            $menuItems = [];
            while ($row = $result->fetch_assoc()) {
                $menuItems[] = $row;
            }
            echo json_encode($menuItems);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'No menu items found']);
        }
    }
}

$conn->close();
?>
