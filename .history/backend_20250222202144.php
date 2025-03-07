<?php
// Connecting to MySQL
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "coppers_ivy"; // My database 

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
?>
