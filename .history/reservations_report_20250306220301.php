<?php
// Database connection
$conn = new mysqli("localhost", "root", "", "coppers_ivy");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch reservations from the database
$query = "SELECT reservation_id, date, time, party_size, contact_info, special_requests, table_number FROM reservations ORDER BY date DESC";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reservations Report</title>
</head>
<body>

    <h2>Reservations Report</h2>

    <table border="1">
        <tr>
            <th>Reservation ID</th>
            <th>Date</th>
            <th>Time</th>
            <th>Party Size</th>
            <th>Contact Info</th>
            <th>Special Requests</th>
            <th>Table Number</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()) { ?>
        <tr>
            <td><?php echo $row['reservation_id']; ?></td>
            <td><?php echo $row['date']; ?></td>
            <td><?php echo $row['time']; ?></td>
            <td><?php echo $row['party_size']; ?></td>
            <td><?php echo $row['contact_info']; ?></td>
            <td><?php echo $row['special_requests']; ?></td>
            <td><?php echo $row['table_number']; ?></td>
        </tr>
        <?php } ?>
    </table>

</body>
</html>
