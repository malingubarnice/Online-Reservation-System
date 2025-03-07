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

// Fetch orders data
$query = "SELECT customer_email, items, customer_name, total_price FROM orders ORDER BY total_price DESC";
$result = mysqli_query($conn, $query);
?>

<table border="1">
    <tr>
        <th>Customer Email</th>
        <th>Items</th>
        <th>Customer Name</th>
        <th>Total Price</th>
    </tr>
    <?php while ($row = mysqli_fetch_assoc($result)) { ?>
    <tr>
        <td><?php echo htmlspecialchars($row['customer_email']); ?></td>
        <td><?php echo htmlspecialchars($row['items']); ?></td>
        <td><?php echo htmlspecialchars($row['customer_name']); ?></td>
        <td><?php echo htmlspecialchars($row['total_price']); ?></td>
    </tr>
    <?php } ?>
</table>

<?php mysqli_close($conn); ?>
