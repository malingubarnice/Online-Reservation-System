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

// Search & Filter
$search = isset($_GET['search']) ? $_GET['search'] : "";
$from_date = isset($_GET['from_date']) ? $_GET['from_date'] : "";
$to_date = isset($_GET['to_date']) ? $_GET['to_date'] : "";

// Pagination
$limit = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Base query
$query = "SELECT room_id, check_in_date, check_out_date, guest_count, contact_info 
          FROM bookings WHERE 1";

// Apply search filter
if (!empty($search)) {
    $query .= " AND (room_id LIKE '%$search%' OR contact_info LIKE '%$search%')";
}

// Apply date filter
if (!empty($from_date) && !empty($to_date)) {
    $query .= " AND check_in_date BETWEEN '$from_date' AND '$to_date'";
}

// Order & Limit
$query .= " ORDER BY check_in_date DESC LIMIT $limit OFFSET $offset";
$result = mysqli_query($conn, $query);

// Get total records for pagination
$total_query = "SELECT COUNT(*) as total FROM bookings WHERE 1";
$total_result = mysqli_query($conn, $total_query);
$total_row = mysqli_fetch_assoc($total_result);
$total_pages = ceil($total_row['total'] / $limit);
?>

<form method="GET">
    <input type="text" name="search" placeholder="Search by Room ID or Contact" value="<?php echo $search; ?>">
    <input type="date" name="from_date" value="<?php echo $from_date; ?>">
    <input type="date" name="to_date" value="<?php echo $to_date; ?>">
    <button type="submit">Filter</button>
    <a href="export_bookings.php">Export to CSV</a>
</form>

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

<!-- Pagination -->
<?php for ($i = 1; $i <= $total_pages; $i++) { ?>
    <a href="?page=<?php echo $i; ?>&search=<?php echo $search; ?>&from_date=<?php echo $from_date; ?>&to_date=<?php echo $to_date; ?>">
        <?php echo $i; ?>
    </a>
<?php } ?>

<?php mysqli_close($conn); ?>
