<?php
$conn = new mysqli("localhost", "root", "", "coppers_ivy");

header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="orders_report.csv"');

$output = fopen('php://output', 'w');
fputcsv($output, ['Customer Email', 'Items', 'Customer Name', 'Total Price']);

$query = "SELECT customer_email, items, customer_name, total_price FROM orders";
$result = $conn->query($query);

while ($row = $result->fetch_assoc()) {
    fputcsv($output, $row);
}

fclose($output);
exit();
?>
