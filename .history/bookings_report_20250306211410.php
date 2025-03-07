<?php
// Database connection
$conn = new mysqli("localhost", "root", "", "coppers_ivy");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Pagination setup
$limit = 10; 
$page = isset($_GET['page']) ? $_GET['page'] : 1;
$start = ($page - 1) * $limit;

// Search & Sorting
$search = isset($_GET['search']) ? $_GET['search'] : '';
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'check_in_date DESC';

$query = "SELECT booking_id, room_id, check_in_date, check_out_date, guest_count, contact_info, created_at 
          FROM bookings 
          WHERE contact_info LIKE '%$search%'
          ORDER BY $sort
          LIMIT $start, $limit";
$result = $conn->query($query);

// Get total rows for pagination
$totalQuery = "SELECT COUNT(*) AS total FROM bookings WHERE contact_info LIKE '%$search%'";
$totalResult = $conn->query($totalQuery);
$totalRow = $totalResult->fetch_assoc();
$totalPages = ceil($totalRow['total'] / $limit);
?>

<form method="GET">
    <input type="text" name="search" placeholder="Search by booking ID" value="<?php echo $search; ?>">
    <button type="submit">Search</button>
</form>

<table border="1">
    <tr>
        <th><a href="?sort=booking_id">Booking ID</a></th>
        <th><a href="?sort=room_id">Room ID</a></th>
        <th><a href="?sort=check_in_date">Check-in Date</a></th>
        <th><a href="?sort=check_out_date">Check-out Date</a></th>
        <th>Guest Count</th>
        <th><a href="?sort=contact_info">Contact Info</a></th>
        <th><a href="?sort=created_at">Created At</a></th>
    </tr>
    <?php while ($row = $result->fetch_assoc()) { ?>
    <tr>
        <td><?php echo htmlspecialchars($row['booking_id']); ?></td>
        <td><?php echo htmlspecialchars($row['room_id']); ?></td>
        <td><?php echo htmlspecialchars($row['check_in_date']); ?></td>
        <td><?php echo htmlspecialchars($row['check_out_date']); ?></td>
        <td><?php echo htmlspecialchars($row['guest_count']); ?></td>
        <td><?php echo htmlspecialchars($row['contact_info']); ?></td>
        <td><?php echo htmlspecialchars($row['created_at']); ?></td>
        
    </tr>
    <?php } ?>
</table>

<!-- Pagination -->
<?php for ($i = 1; $i <= $totalPages; $i++): ?>
    <a href="?page=<?php echo $i; ?>&search=<?php echo $search; ?>&sort=<?php echo $sort; ?>"><?php echo $i; ?></a>
<?php endfor; ?>

<!-- CSV Export -->
<form action="export_bookings.php" method="POST">
    <button type="submit">Export to CSV</button>
</form>