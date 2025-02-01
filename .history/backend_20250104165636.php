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
        $sql_check = "SELECT * FROM reservations WHERE date = '$date' AND time = '$time' AND table_number = '$selected_table'";
        $result_check = $conn->query($sql_check);

        if ($result_check->num_rows > 0) {
            // If the table is already reserved, return an error
            echo json_encode(['status' => 'error', 'message' => 'This table is already reserved for the selected date and time, Please select another table.']);
            exit;
        }

        // Insert the reservation into the database
        $sql_insert = "INSERT INTO reservations (date, time, party_size, contact_info, special_requests, table_number) 
                       VALUES ('$date', '$time', '$party_size', '$contact_info', '$special_requests', '$selected_table')";

        if ($conn->query($sql_insert) === TRUE) {
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

$conn->close();
?> get_menu.php has the following code <?php
$conn = new mysqli("localhost", "root", "", "coppers_ivy");
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

$result = $conn->query("SELECT * FROM menu");
$menu = [];
while ($row = $result->fetch_assoc()) {
    $menu[] = $row;
}
echo json_encode($menu);
$conn->close();
?>
place_order.php has the following code <?php
$data = json_decode(file_get_contents('php://input'), true);
$conn = new mysqli("localhost", "root", "", "coppers_ivy");
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

$name = "Anonymous"; // Use customer name from login if available
$email = "test@example.com"; // Use customer email from login if available
$items = json_encode($data['order']);
$total_price = array_sum(array_column($data['order'], 'price'));

$stmt = $conn->prepare("INSERT INTO orders (customer_name, customer_email, items, total_price) VALUES (?, ?, ?, ?)");
$stmt->bind_param("sssd", $name, $email, $items, $total_price);

if ($stmt->execute()) {
    echo json_encode(['status' => 'success']);
} else {
    echo json_encode(['status' => 'error', 'message' => $conn->error]);
}
$conn->close();
?>