<?php
// Database connection
$conn = new mysqli("localhost", "root", "", "coppers_ivy");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Pagination setup
$limit = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$start = ($page - 1) * $limit;

// Search & Sorting
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'created_at DESC';

// Base SQL query
$query = "SELECT customer_email, items, customer_name, total_price, created_at 
          FROM orders";

// Apply search filter
if (!empty($search)) {
    $search = $conn->real_escape_string($search);
    $query .= " WHERE customer_name LIKE '%$search%' OR customer_email LIKE '%$search%'";
}

// Apply sorting and limit
$query .= " ORDER BY $sort LIMIT $start, $limit";
$result = $conn->query($query);

// Get total rows for pagination
$totalQuery = "SELECT COUNT(*) AS total FROM orders";
if (!empty($search)) {
    $totalQuery .= " WHERE customer_name LIKE '%$search%' OR customer_email LIKE '%$search%'";
}
$totalResult = $conn->query($totalQuery);
$totalRow = $totalResult->fetch_assoc();
$totalPages = ceil($totalRow['total'] / $limit);

// Group orders by date
$ordersByDate = [];
while ($row = $result->fetch_assoc()) {
    $orderDate = date('l, jS F Y', strtotime($row['created_at']));
    if (!isset($ordersByDate[$orderDate])) {
        $ordersByDate[$orderDate] = [];
    }
    $ordersByDate[$orderDate][] = $row;
}
?>

<!-- Search Form -->
<form method="GET">
    <input type="text" name="search" placeholder="Search by name or email" value="<?php echo htmlspecialchars($search); ?>">
    <button type="submit">Search</button>
</form>

<!-- Orders Display -->
<?php foreach ($ordersByDate as $date => $orders): ?>
    <h2><?php echo $date; ?> - <?php echo count($orders); ?> Order(s)</h2>
    <table border="1">
        <tr>
            <th>Customer Email</th>
            <th>Items</th>
            <th>Customer Name</th>
            <th>Total Price</th>
            <th>Created At</th>
        </tr>
        <?php foreach ($orders as $order): ?>
        <tr>
            <td><?php echo htmlspecialchars($order['customer_email']); ?></td>
            <td><?php echo htmlspecialchars($order['items']); ?></td>
            <td><?php echo htmlspecialchars($order['customer_name']); ?></td>
            <td><?php echo number_format($order['total_price'], 2); ?></td>
            <td><?php echo date('l, jS F Y h:i A', strtotime($order['created_at'])); ?></td>
        </tr>
        <?php endforeach; ?>
    </table>
<?php endforeach; ?>

<!-- Pagination -->
<?php for ($i = 1; $i <= $totalPages; $i++): ?>
    <a href="?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>&sort=<?php echo urlencode($sort); ?>">
        <?php echo $i; ?>
    </a>
<?php endfor; ?>

<!-- CSV Export -->
<form action="export_orders.php" method="POST">
    <button type="submit">Export to CSV</button>
</form>
