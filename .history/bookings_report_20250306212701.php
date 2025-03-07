<?php
// Database connection
$conn = new mysqli("localhost", "root", "", "coppers_ivy");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Search filter for Booking ID
$search = isset($_GET['search']) ? $_GET['search'] : '';

// Query to fetch all booking details
$query = "SELECT id, check_in_date, check_out_date, contact_info, guest_count, room_id 
          FROM bookings 
          WHERE id LIKE '%$search%'
          ORDER BY check_in_date DESC";
$result = $conn->query($query);

// Store bookings grouped by check-in date
$bookings = [];
while ($row = $result->fetch_assoc()) {
    $bookings[$row['check_in_date']][] = $row;
}
?>

<!-- Search Form -->
<form method="GET">
    <input type="text" name="search" placeholder="Search by Booking ID" value="<?php echo htmlspecialchars($search); ?>">
    <button type="submit">Search</button>
</form>

<!-- Table Displaying Bookings Per Date with All Details -->
<table border="1">
    <tr>
        <th>Check-in Date</th>
        <th>Total Bookings</th>
        <th>Details</th>
    </tr>
    <?php foreach ($bookings as $date => $details) { ?>
    <tr>
        <td><?php echo date('l, jS F Y', strtotime($date)); ?></td>
        <td><?php echo count($details); ?></td>
        <td>
            <table border="1">
                <tr>
                    <th>Booking ID</th>
                    <th>Check-in Date</th>
                    <th>Check-out Date</th>
                    <th>Contact Info</th>
                    <th>Guest Count</th>
                    <th>Room ID</th>
                </tr>
                <?php foreach ($details as $booking) { ?>
                <tr>
                    <td><?php echo htmlspecialchars($booking['id']); ?></td>
                    <td><?php echo date('Y-m-d', strtotime($booking['check_in_date'])); ?></td>
                    <td><?php echo date('Y-m-d', strtotime($booking['check_out_date'])); ?></td>
                    <td><?php echo htmlspecialchars($booking['contact_info']); ?></td>
                    <td><?php echo htmlspecialchars($booking['guest_count']); ?></td>
                    <td><?php echo htmlspecialchars($booking['room_id']); ?></td>
                </tr>
                <?php } ?>
            </table>
        </td>
    </tr>
    <?php } ?>
</table>

<!-- CSV Export -->
<form action="export_bookings.php" method="POST">
    <button type="submit">Export to CSV</button>
</form>
