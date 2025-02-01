<?php
$data = json_decode(file_get_contents('php://input'), true);
$conn = new mysqli("localhost", "root", "", "coppers_ivy");
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

$name = ""; // Use customer name from login if available
$email = ""; // Use customer email from login if available
$items = json_decode($data['order']);
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