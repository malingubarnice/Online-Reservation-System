<?php
// Database connection
$host = "localhost";
$user = "root";
$password = "";
$database = "coppers_ivy";

$conn = mysqli_connect($host, $user, $password, $database);
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Fetch data
$query = "SELECT room_id, check_in_date, check_out_date, guest_count, contact_info FROM bookings";
$result = mysqli_query($conn, $query);

header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="bookings_report.csv"');

$output = fopen("php://output", "w");
fputcsv($output, array('Room ID', 'Check-in Date', 'Check-out Date', 'Guest Count', 'Contact Info'));

while ($row = mysqli_fetch_assoc($result)) {
    fputcsv($output, $row);
}
fclose($output);
mysqli_close($conn);
?>
