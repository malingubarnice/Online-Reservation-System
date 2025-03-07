<?php
// Database connection
$conn = new mysqli("localhost", "root", "", "coppers_ivy");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Pagination setup
$limit = 10; // Number of records per page
$page = isset($_GET['page']) ? $_GET['page'] : 1;
$start = ($page - 1) * $limit;

// Search & Sorting
$search = isset($_GET['search']) ? $_GET['search'] : '';
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'total_price DESC';

$query = "SELECT customer_email, items, customer_name, total_price 
          FROM orders 
          WHERE customer_name LIKE '%$search%' OR customer_email LIKE '%$search%'
          ORDER BY $sort
          LIMIT $start, $limit";
$result = $conn->query($query);

// Get total rows for pagination
$totalQuery = "SELECT COUNT(*) AS total FROM orders WHERE customer_name LIKE '%$search%' OR customer_email LIKE '%$search%'";
$totalResult = $conn->query($totalQuery);
$totalRow = $totalResult->fetch_assoc();
$totalPages = ceil($totalRow['total'] / $limit);
?>

<form method="GET">
    <input type="text" name="search" placeholder="Search by name or email" value="<?php echo $search; ?>">
    <button type="submit">Search</button>
</form>

<table border="1">
    <tr>
        <th><a href="?sort=customer_email">Customer Email</a></th>
        <th>Items</th>
        <th><a href="?sort=customer_name">Customer Name</a></th>
        <th><a href="?sort=total_price DESC">Total Price</a></th>
    </tr>
    <?php while ($row = $result->fetch_assoc()) { ?>
    <tr>
        <td><?php echo $row['customer_email']; ?></td>
        <td><?php echo $row['items']; ?></td>
        <td><?php echo $row['customer_name']; ?></td>
        <td><?php echo $row['total_price']; ?></td>
    </tr>
    <?php } ?>
</table>

<!-- Pagination -->
<?php for ($i = 1; $i <= $totalPages; $i++): ?>
    <a href="?page=<?php echo $i; ?>&search=<?php echo $search; ?>&sort=<?php echo $sort; ?>"><?php echo $i; ?></a>
<?php endfor; ?>

<!-- CSV Export -->
<form action="export_orders.php" method="POST">
    <button type="submit">Export to CSV</button>
</form>
