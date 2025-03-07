<?php
require 'backend.php'; // Import database connection

// Set response type to JSON
header('Content-Type: application/json');

// Check request method
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $action = $_POST['action'] ?? '';

    // ✅ 1. Reservation Handling
    if ($action === 'reserve') {
        // Get and sanitize inputs
        $date = trim($_POST['date'] ?? '');
        $time = trim($_POST['time'] ?? '');
        $party_size = intval($_POST['party_size'] ?? 0);
        $contact_info = trim($_POST['contact_info'] ?? '');
        $special_requests = trim($_POST['special_requests'] ?? '');
        $selected_table = trim($_POST['selected_table'] ?? '');

        // Validate input
        if (empty($date) || empty($time) || $party_size <= 0 || empty($contact_info) || empty($selected_table)) {
            echo json_encode(['status' => 'error', 'message' => 'All fields are required, and party size must be valid.']);
            exit;
        }

        // Prevent past dates
        if ($date < date('Y-m-d')) {
            echo json_encode(['status' => 'error', 'message' => 'Invalid date! You cannot select a past date.']);
            exit;
        }

        // ✅ Check if the table is already reserved
        $sql_check = "SELECT * FROM reservations WHERE date = ? AND time = ? AND table_number = ?";
        $stmt_check = $conn->prepare($sql_check);
        $stmt_check->bind_param("sss", $date, $time, $selected_table);
        $stmt_check->execute();
        $result_check = $stmt_check->get_result();

        if ($result_check->num_rows > 0) {
            echo json_encode(['status' => 'error', 'message' => 'This table is already reserved for the selected date and time.']);
            exit;
        }

        // ✅ Generate Unique Reservation ID (Format: RES-YYYYMMDD-XXX)
        $reservation_id = "RES-" . date("Ymd") . "-" . rand(100, 999);

        error_log("DEBUG: Inserting Reservation -> ID: $reservation_id, Date: $date, Time: $time, Party Size: $party_size, Contact: $contact_info, Requests: $special_requests, Table: $selected_table");


        // Insert reservation into the database
$sql_insert = "INSERT INTO reservations (reservation_id, date, time, party_size, contact_info, special_requests, table_number) 
VALUES (?, ?, ?, ?, ?, ?, ?)";
$stmt_insert = $conn->prepare($sql_insert);

if (!$stmt_insert) {
die(json_encode(['status' => 'error', 'message' => 'SQL Prepare Error: ' . $conn->error]));
}

// ✅ Ensure `party_size` is an integer
$party_size = (int) $party_size;

$stmt_insert->bind_param("ssissss", $reservation_id, $date, $time, $party_size, $contact_info, $special_requests, $selected_table);

if (!$stmt_insert->execute()) {
die(json_encode(['status' => 'error', 'message' => 'SQL Execution Error: ' . $stmt_insert->error]));
} else {
echo json_encode(['status' => 'success', 'message' => 'Reservation created successfully!', 'reservation_id' => $reservation_id]);
}


        if ($stmt_insert->execute()) {
            // ✅ Send confirmation email
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
            echo json_encode(['status' => 'error', 'message' => 'Error: ' . $stmt_insert->error]);
        }
        exit;
    }

    // ✅ 2. Fetching Menu Items
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
        exit;
    }
}

$conn->close();
?>
