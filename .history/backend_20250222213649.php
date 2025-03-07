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

// Set content type to JSON
header('Content-Type: application/json');

// Handle POST requests
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $action = $_POST['action'] ?? '';

    // Handle reservation creation
    if ($action === 'reserve') {
        // Validate required fields
        $requiredFields = ['date', 'time', 'party_size', 'contact_info', 'selected_table'];
        foreach ($requiredFields as $field) {
            if (empty($_POST[$field])) {
                echo json_encode(['status' => 'error', 'message' => 'All fields are required.']);
                exit;
            }
        }

        $date = $_POST['date'];
        $time = $_POST['time'];
        $party_size = $_POST['party_size'];
        $contact_info = $_POST['contact_info'];
        $special_requests = $_POST['special_requests'] ?? '';
        $selected_table = $_POST['selected_table'];

        // Validate date (prevent past dates)
        $currentDate = date('Y-m-d');
        if ($date < $currentDate) {
            echo json_encode(['status' => 'error', 'message' => 'Invalid date! You cannot select a past date.']);
            exit;
        }

        // Check if the table is already reserved
        $sql_check = "SELECT * FROM reservations WHERE date = ? AND time = ? AND table_number = ?";
        $stmt_check = $conn->prepare($sql_check);
        $stmt_check->bind_param("sss", $date, $time, $selected_table);
        $stmt_check->execute();
        $result_check = $stmt_check->get_result();

        if ($result_check->num_rows > 0) {
            echo json_encode(['status' => 'error', 'message' => 'This table is already reserved for the selected date and time.']);
            exit;
        }

        // Insert reservation into the database
        $sql_insert = "INSERT INTO reservations (date, time, party_size, contact_info, special_requests, table_number) 
                       VALUES (?, ?, ?, ?, ?, ?)";
        $stmt_insert = $conn->prepare($sql_insert);
        $stmt_insert->bind_param("ssssss", $date, $time, $party_size, $contact_info, $special_requests, $selected_table);

        if ($stmt_insert->execute()) {
            // Send email only after successful reservation
            $emailSent = sendReservationEmail($contact_info, 'Valued Customer', $date, $time, $party_size, $special_requests);

            if ($emailSent) {
                echo json_encode(['status' => 'success', 'message' => 'Reservation created successfully!']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Reservation created, but email could not be sent.']);
            }
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Error: ' . $conn->error]);
        }

        exit;
    }

    // Handle fetching menu items
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

/**
 * Send reservation confirmation email
 */
function sendReservationEmail($contact_info, $customer_name, $date, $time, $party_size, $special_requests) {
    require 'phpmailer/src/Exception.php';
    require 'phpmailer/src/PHPMailer.php';
    require 'phpmailer/src/SMTP.php';

    $mail = new PHPMailer(true);

    try {
        // SMTP settings
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'malingubarnice@gmail.com';
        $mail->Password = 'olxf hiln uxom xenr'; // Use an App Password
        $mail->SMTPSecure = 'ssl';
        $mail->Port = 465;

        // Sender and recipient
        $mail->setFrom('malingubarnice@gmail.com', 'Coppers Ivy Reservation Team');
        $mail->addAddress($contact_info);

        // Email content
        $message = "
        <h2>Reservation Confirmation</h2>
        <p><strong>Name:</strong> " . htmlspecialchars($customer_name) . "</p>
        <p><strong>Date:</strong> " . htmlspecialchars($date) . "</p>
        <p><strong>Time:</strong> " . htmlspecialchars($time) . "</p>
        <p><strong>Party Size:</strong> " . htmlspecialchars($party_size) . "</p>
        <p><strong>Special Requests:</strong> " . nl2br(htmlspecialchars($special_requests)) . "</p>
        <p>Thank you for reserving with Coppers Ivy!</p>
        ";

        $mail->isHTML(true);
        $mail->Subject = "Your Reservation Confirmation";
        $mail->Body = $message;

        // Send email
        $mail->send();
        return true; // Email sent successfully
    } catch (Exception $e) {
        error_log("Email sending failed: " . $e->getMessage());
        return false; // Email sending failed
    }
}
?>