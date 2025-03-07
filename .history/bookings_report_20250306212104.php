<?php
// Database connection
$conn = new mysqli("localhost", "root", "", "coppers_ivy");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Search filter
$search = isset($_GET['search']) ? $_GET['search'] : '';

// Query to count bookings per check-in date
$query = "SELECT check_in_date, COUNT(*) AS total_bookings
          FROM bookings 
          WHERE contact_info LIKE '%$search%'
          GROUP BY check_in_date
          ORDER BY check_in_date DESC";
$result = $conn->query($query);
?>

<!-- Search Form -->
<form method="GET">
    <input type="text" name="search" placeholder="Search by contact info" value="<?php echo $search; ?>">
    <button type="submit">Search</button>
</form>

<!-- Table Displaying Bookings Per Date -->
<table border="1">
    <tr>
        <th>Date</th>
        <th>Total Bookings</th>
    </tr>
    <?php while ($row = $result->fetch_assoc()) { ?>
    <tr>
        <td><?php echo date('l, jS F Y', strtotime($row['check_in_date'])); ?></td>
        <td><?php echo htmlspecialchars($row['total_bookings']); ?></td>
    </tr>
    <?php } ?>
</table>

<!-- CSV Export -->
<form action="export_bookings.php" method="POST">
    <button type="submit">Export to CSV</button>
</form>
