<?php
// Database connection
$conn = new mysqli("localhost", "root", "", "coppers_ivy");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Pagination setup
$limit = 10;
$page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
$start = ($page - 1) * $limit;

// Search & Sorting
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'date DESC';

// Base SQL query
$query = "SELECT reservation_id, date, time, party_size, contact_info, special_requests, table_number 
          FROM reservations";

// Apply search if a reservation ID is entered
if (!empty($search)) {
    $search = $conn->real_escape_string($search);
    $query .= " WHERE reservation_id LIKE '%$search%'";
}

// Add sorting and limit for pagination
$query .= " ORDER BY $sort LIMIT $start, $limit";
$result = $conn->query($query);

// Get total rows for pagination
$totalQuery = "SELECT COUNT(*) AS total FROM reservations";
if (!empty($search)) {
    $totalQuery .= " WHERE reservation_id LIKE '%$search%'";
}
$totalResult = $conn->query($totalQuery);
$totalRow = $totalResult->fetch_assoc();
$totalPages = ceil($totalRow['total'] / $limit);
?>

<!-- Search Form (Now Accepting Reservation ID Format "RES-YYYYMMDD-XXX") -->
<form method="GET">
    <input type="text" name="search" placeholder="Enter Reservation ID (e.g., RES-20250306-123)" 
           value="<?php echo htmlspecialchars($search); ?>">
    <button type="submit">Search</button>
</form>

<!-- Table Displaying Reservations with Sorting -->
<table border="1">
    <tr>
        <th><a href="?sort=date">Date</a></th>
        <th><a href="?sort=time">Time</a></th>
        <th>Party Size</th>
        <th><a href="?sort=contact_info">Contact Info</a></th>
        <th>Special Requests</th>
        <th>Table Number</th>
        <th><a href="?sort=reservation_id">Reservation ID</a></th>
    </tr>
    <?php while ($row = $result->fetch_assoc()) { ?>
    <tr>
        <td><?php echo date('l, jS F Y', strtotime($row['date'])); ?></td>
        <td><?php echo date('h:i A', strtotime($row['time'])); ?></td>
        <td><?php echo htmlspecialchars($row['party_size']); ?></td>
        <td><?php echo htmlspecialchars($row['contact_info']); ?></td>
        <td><?php echo htmlspecialchars($row['special_requests']); ?></td>
        <td><?php echo htmlspecialchars($row['table_number']); ?></td>
        <td><?php echo htmlspecialchars($row['reservation_id']); ?></td>
    </tr>
    <?php } ?>
</table>

<!-- Pagination -->
<?php for ($i = 1; $i <= $totalPages; $i++): ?>
    <a href="?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>&sort=<?php echo urlencode($sort); ?>">
        <?php echo $i; ?>
    </a>
<?php endfor; ?>

<!-- CSV Export -->
<form action="export_reservations.php" method="POST">
    <button type="submit">Export to CSV</button>
</form>
