<?php
$conn = new mysqli("localhost", "root", "", "coppers_ivy");

header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="reservations_report.csv"');

$output = fopen('php://output', 'w');
fputcsv($output, ['Date', 'Time', 'Party Size', 'Contact Info', 'Special Requests', 'Table Number']);

$query = "SELECT date, time, party_size, contact_info, special_requests, table_number FROM reservations";
$result = $conn->query($query);

while ($row = $result->fetch_assoc()) {
    fputcsv($output, $row);
}

fclose($output);
exit();
?>
