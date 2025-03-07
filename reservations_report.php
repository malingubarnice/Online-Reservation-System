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

// Fetch reservations grouped by date
$groupedQuery = "SELECT date, COUNT(*) AS total FROM reservations GROUP BY date ORDER BY date DESC";
$groupedResult = $conn->query($groupedQuery);
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

    <!-- Search Form -->
    <form method="GET">
        <input type="text" name="search" placeholder="Enter Reservation ID (e.g., RES-20250306-123)" 
               value="<?php echo htmlspecialchars($search); ?>">
        <button type="submit">Search</button>
    </form>

    <!-- Display Grouped Reservations -->
    <?php while ($group = $groupedResult->fetch_assoc()) { 
        $date = $group['date'];
        $formattedDate = date('l, jS F Y', strtotime($date));
        $totalBookings = $group['total'];
    ?>
        <h3><?php echo $formattedDate; ?> - Total Reservations: <?php echo $totalBookings; ?></h3>

        <table border="1">
            <tr>
                <th>Reservation ID</th>
                <th>Time</th>
                <th>Party Size</th>
                <th>Contact Info</th>
                <th>Special Requests</th>
                <th>Table Number</th>
            </tr>

            <?php
            // Fetch reservations for this date
            $detailsQuery = "SELECT * FROM reservations WHERE date = '$date' ORDER BY time ASC";
            $detailsResult = $conn->query($detailsQuery);

            while ($row = $detailsResult->fetch_assoc()) { ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['reservation_id']); ?></td>
                    <td><?php echo date('h:i A', strtotime($row['time'])); ?></td>
                    <td><?php echo htmlspecialchars($row['party_size']); ?></td>
                    <td><?php echo htmlspecialchars($row['contact_info']); ?></td>
                    <td><?php echo htmlspecialchars($row['special_requests']); ?></td>
                    <td><?php echo htmlspecialchars($row['table_number']); ?></td>
                </tr>
            <?php } ?>
        </table>
    <?php } ?>

    <!-- Pagination -->
    <div>
        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
            <a href="?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>&sort=<?php echo urlencode($sort); ?>">
                <?php echo $i; ?>
            </a>
        <?php endfor; ?>
    </div>

    <!-- CSV Export -->
    <form action="export_reservations.php" method="POST">
        <button type="submit">Export to CSV</button>
    </form>

</body>
</html>
