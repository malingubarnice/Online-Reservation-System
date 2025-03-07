<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "coppers_ivy";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die(json_encode(['status' => 'error', 'message' => 'Database connection failed: ' . $conn->connect_error]));
}

header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $action = $_POST['action'] ?? '';

    if ($action === 'reserve') {
        // Debug: Check if POST data is received
        if (empty($_POST)) {
            die(json_encode(['status' => 'error', 'message' => 'No data received from form.']));
        }

        $date = $_POST['date'] ?? '';
        $time = $_POST['time'] ?? '';
        $party_size = $_POST['party_size'] ?? '';
        $contact_info = $_POST['contact_info'] ?? '';
        $special_requests = $_POST['special_requests'] ?? '';
        $selected_table = $_POST['selected_table'] ?? '';

        // Debug received values
        die(json_encode(['status' => 'debug', 'received' => compact('date', 'time', 'party_size', 'contact_info', 'selected_table')]));

        if (empty($date) || empty($time) || empty($party_size) || empty($contact_info) || empty($selected_table)) {
            die(json_encode(['status' => 'error', 'message' => 'All fields are required.']));
        }

        $time = date("H:i:s", strtotime($time));

        if ($date < date('Y-m-d')) {
            die(json_encode(['status' => 'error', 'message' => 'Invalid date! You cannot select a past date.']));
        }

        $sql_check = "SELECT * FROM reservations WHERE date = ? AND time = ? AND table_number = ?";
        $stmt_check = $conn->prepare($sql_check);
        $stmt_check->bind_param("sss", $date, $time, $selected_table);
        $stmt_check->execute();
        $result_check = $stmt_check->get_result();

        if ($result_check->num_rows > 0) {
            die(json_encode(['status' => 'error', 'message' => 'Table already reserved.']));
        }

        // Debug: Reaching insertion step
        die(json_encode(['status' => 'debug', 'message' => 'Preparing to insert data...']));

        // Hardcoded reservation ID for testing
        $reservation_id = "RES-20250306-999";

        $sql_insert = "INSERT INTO reservations (reservation_id, date, time, party_size, contact_info, special_requests, table_number) 
                       VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt_insert = $conn->prepare($sql_insert);
        $stmt_insert->bind_param("ssissss", $reservation_id, $date, $time, $party_size, $contact_info, $special_requests, $selected_table);

        if (!$stmt_insert->execute()) {
            die(json_encode(['status' => 'error', 'message' => 'MySQL Error: ' . $stmt_insert->error]));
        }

        echo json_encode(['status' => 'success', 'message' => 'Reservation created successfully!', 'reservation_id' => $reservation_id]);
    }
}

$conn->close();
?>
