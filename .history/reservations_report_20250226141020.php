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
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'date DESC';

$query = "SELECT date, time, party_size, contact_info, special_requests, table_number 
          FROM reservations 
          WHERE contact_info LIKE '%$search%'
          ORDER BY $sort
          LIMIT $start, $limit";
$result = $conn->query($query);

// Get total rows for pagination
$totalQuery = "SELECT COUNT(*) AS total FROM reservations WHERE contact_info LIKE '%$search%'";
$totalResult = $conn->query($totalQuery);
$totalRow = $totalResult->fetch_assoc();
$totalPages = ceil($totalRow['total'] / $limit);
?>

<form method="GET">
    <input type="text" name="search" placeholder="Search by contact info" value="<?php echo $search; ?>">
    <button type="submit">Search</button>
</form>

<table border="1">
    <tr>
        <th><a href="?sort=date">Date</a></th>
        <th><a href="?sort=time">Time</a></th>
        <th>Party Size</th>
        <th><a href="?sort=contact_info">Contact Info</a></th>
        <th>Special Requests</th>
        <th>Table Number</th>
    </tr>
    <?php while ($row = $result->fetch_assoc()) { ?>
    <tr>
        <td><?php echo $row['date']; ?></td>
        <td><?php echo $row['time']; ?></td>
        <td><?php echo $row['party_size']; ?></td>
        <td><?php echo $row['contact_info']; ?></td>
        <td><?php echo $row['special_requests']; ?></td>
        <td><?php echo $row['table_number']; ?></td>
    </tr>
    <?php } ?>
</table>

<!-- Pagination -->
<?php for ($i = 1; $i <= $totalPages; $i++): ?>
    <a href="?page=<?php echo $i; ?>&search=<?php echo $search; ?>&sort=<?php echo $sort; ?>"><?php echo $i; ?></a>
<?php endfor; ?>

<!-- CSV Export -->
<form action="export_reservations.php" method="POST">
    <button type="submit">Export to CSV</button>
</form>
