<?php
// Database connection
$conn = new mysqli("localhost", "root", "", "coppers_ivy");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Search filter
$search = isset($_GET['search']) ? $_GET['search'] : '';

// Query to fetch bookings per check-in date with details
$query = "SELECT check_in_date, contact_info, guest_count, room_id 
          FROM bookings 
          WHERE contact_info LIKE '%$search%'
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
    <input type="text" name="search" placeholder="Search by contact info" value="<?php echo htmlspecialchars($search); ?>">
    <button type="submit">Search</button>
</form>

<!-- Table Displaying Bookings Per Date with Details -->
<table border="1">
    <tr>
        <th>Date</th>
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
                    <th>Contact Info</th>
                    <th>Guest Count</th>
                    <th>Room ID</th>
                </tr>
                <?php foreach ($details as $booking) { ?>
                <tr>
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
