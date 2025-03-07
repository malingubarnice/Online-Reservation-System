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

// Fetch reservations data
$query = "SELECT date, time, party_size, contact_info, special_requests, table_number FROM reservations ORDER BY date DESC";
$result = mysqli_query($conn, $query);
?>

<table border="1">
    <tr>
        <th>Date</th>
        <th>Time</th>
        <th>Party Size</th>
        <th>Contact Info</th>
        <th>Special Requests</th>
        <th>Table Number</th>
    </tr>
    <?php while ($row = mysqli_fetch_assoc($result)) { ?>
    <tr>
        <td><?php echo htmlspecialchars($row['date']); ?></td>
        <td><?php echo htmlspecialchars($row['time']); ?></td>
        <td><?php echo htmlspecialchars($row['party_size']); ?></td>
        <td><?php echo htmlspecialchars($row['contact_info']); ?></td>
        <td><?php echo htmlspecialchars($row['special_requests']); ?></td>
        <td><?php echo htmlspecialchars($row['table_number']); ?></td>
    </tr>
    <?php } ?>
</table>

<?php mysqli_close($conn); ?>
