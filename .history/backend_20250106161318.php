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

    // Handle reservation creation
    if ($action === 'reserve') {
        $date = $_POST['date'];
        $time = $_POST['time'];
        $party_size = $_POST['party_size'];
        $contact_info = $_POST['contact_info'];
        $special_requests = $_POST['special_requests'];
        $selected_table = $_POST['selected_table'];

        // Simple validation (make sure all fields are filled)
        if (empty($date) || empty($time) || empty($party_size) || empty($contact_info) || empty($selected_table)) {
            echo json_encode(['status' => 'error', 'message' => 'All fields are required.']);
            exit;
        }

        // Check if the table is already reserved for the given date and time
        $sql_check = "SELECT * FROM reservations WHERE date = ? AND time = ? AND table_number = ?";
        $stmt_check = $conn->prepare($sql_check);
        $stmt_check->bind_param("sss", $date, $time, $selected_table);
        $stmt_check->execute();
        $result_check = $stmt_check->get_result();

        if ($result_check->num_rows > 0) {
            // If the table is already reserved, return an error
            echo json_encode(['status' => 'error', 'message' => 'This table is already reserved for the selected date and time, Please select another table.']);
            exit;
        }

        // Insert the reservation into the database
        $sql_insert = "INSERT INTO reservations (date, time, party_size, contact_info, special_requests, table_number) 
                       VALUES (?, ?, ?, ?, ?, ?)";
        $stmt_insert = $conn->prepare($sql_insert);
        $stmt_insert->bind_param("ssssss", $date, $time, $party_size, $contact_info, $special_requests, $selected_table);

        if ($stmt_insert->execute()) {
            echo json_encode(['status' => 'success', 'message' => 'Reservation created successfully!']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Error: ' . $conn->error]);
        }
    }

    // Handle fetching menu items
    if ($action === 'get_menu') {
        // Fetch menu items from database
        $sql = "SELECT name, description, price, image_url FROM menu_items"; // Make sure your table name is correct
        $result = $conn->query($sql);

        // Check if any menu items exist
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





// Handle the booking form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $room_id = $_POST['room_id'];
    $check_in = $_POST['check_in'];
    $check_out = $_POST['check_out'];
    $guest_count = $_POST['guest_count'];
    $contact_info = $_POST['contact_information'];

    // Validate inputs
    if ($guest_count <= 0) {
        echo "Invalid guest count. Please enter a positive number.";
        exit;
    }

    if (strtotime($check_in) >= strtotime($check_out)) {
        echo "Invalid date range. Check-out date must be after check-in date.";
        exit;
    }

    // Insert the booking details into the database
    $query = "INSERT INTO bookings (room_id, check_in_date, check_out_date, guest_count, contact_info, created_at) 
              VALUES (?, ?, ?, ?, ?, NOW())";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("issis", $room_id, $check_in, $check_out, $guest_count, $contact_info);

    if ($stmt->execute()) {
        echo "Booking successful! Your booking has been confirmed.";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}

$conn->close();
?>
