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

// Fetch bookings data
$query = "SELECT room_id, check_in_date, check_out_date, guest_count, contact_info FROM bookings ORDER BY check_in_date DESC";
$result = mysqli_query($conn, $query);
?>

<table border="1">
    <tr>
        <th>Room ID</th>
        <th>Check-in Date</th>
        <th>Check-out Date</th>
        <th>Guest Count</th>
        <th>Contact Info</th>
    </tr>
    <?php while ($row = mysqli_fetch_assoc($result)) { ?>
    <tr>
        <td><?php echo htmlspecialchars($row['room_id']); ?></td>
        <td><?php echo htmlspecialchars($row['check_in_date']); ?></td>
        <td><?php echo htmlspecialchars($row['check_out_date']); ?></td>
        <td><?php echo htmlspecialchars($row['guest_count']); ?></td>
        <td><?php echo htmlspecialchars($row['contact_info']); ?></td>
    </tr>
    <?php } ?>
</table>

<?php mysqli_close($conn); ?>
