<?php
$conn = new mysqli("localhost", "root", "", "coppers_ivy");

header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="bookings_report.csv"');

$output = fopen('php://output', 'w');
fputcsv($output, ['Room ID', 'Check-in Date', 'Check-out Date', 'Guest Count', 'Contact Info']);

$query = "SELECT room_id, check_in_date, check_out_date, guest_count, contact_info FROM bookings";
$result = $conn->query($query);

while ($row = $result->fetch_assoc()) {
    fputcsv($output, $row);
}

fclose($output);
exit();
?>
