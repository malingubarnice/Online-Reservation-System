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
$query = "SELECT reservation_id, date, time, party_size, contact_info, special_requests, table_number FROM reservations";
if (!empty($search)) {
    $search = $conn->real_escape_string($search);
    $query .= " WHERE reservation_id LIKE '%$search%'";
}
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

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reservations Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 20px;
            text-align: center;
        }
        .container {
            width: 90%;
            margin: auto;
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            padding: 15px;
            text-align: center;
            border: 1px solid #ddd;
        }
        th {
            background: #6a1b9a;
            color: white;
            font-size: 16px;
        }
        tr:nth-child(even) {
            background: #f9f9f9;
        }
        tr:hover {
            background: #e1bee7;
        }
        .search-box {
            margin: 20px 0;
        }
        input[type="text"] {
            padding: 10px;
            width: 300px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        button {
            padding: 10px 15px;
            background: #6a1b9a;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        button:hover {
            background: #4a148c;
        }
        .pagination a {
            text-decoration: none;
            padding: 10px;
            margin: 5px;
            background: #6a1b9a;
            color: white;
            border-radius: 5px;
        }
        .pagination a:hover {
            background: #4a148c;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Reservations Report</h2>

    <!-- Search Form -->
    <form method="GET" class="search-box">
        <input type="text" name="search" placeholder="Enter Reservation ID (e.g., RES-20250306-123)" 
               value="<?php echo htmlspecialchars($search); ?>">
        <button type="submit">Search</button>
    </form>

    <!-- Table Displaying Reservations -->
    <table>
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
    <div class="pagination">
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
</div>

</body>
</html>
