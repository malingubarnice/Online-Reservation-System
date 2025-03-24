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
    <style>
        /* General Styling */
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
            color: #333;
            margin: 20px;
            padding: 20px;
        }

        /* Page Title */
        h2 {
            text-align: center;
            color: #2c3e50;
            margin-bottom: 20px;
        }

        /* Search Form */
        form {
            text-align: center;
            margin-bottom: 20px;
        }

        input[type="text"] {
            padding: 10px;
            width: 250px;
            border: 1px solid #aaa;
            border-radius: 5px;
            font-size: 14px;
        }

        button {
            background-color: #2c3e50;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        button:hover {
            background-color: #1a252f;
        }

        /* Reservation Group Titles */
        h3 {
            background-color: #34495e;
            color: white;
            padding: 10px;
            border-radius: 5px;
            text-align: center;
        }

        /* Table Styling */
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            background-color: white;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
        }

        th, td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: center;
        }

        th {
            background-color: #2c3e50;
            color: white;
        }

        /* Pagination */
        .pagination {
            text-align: center;
            margin-top: 20px;
        }

        .pagination a {
            display: inline-block;
            padding: 8px 12px;
            margin: 0 5px;
            text-decoration: none;
            color: white;
            background-color: #2c3e50;
            border-radius: 5px;
        }

        .pagination a:hover {
            background-color: #1a252f;
        }

        /* Export Button */
        .export-form {
            text-align: center;
            margin-top: 20px;
        }

        .export-form button {
            background-color: #27ae60;
        }

        .export-form button:hover {
            background-color: #1e8449;
        }
    </style>
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

        <table>
            <tr>
                <th>Reservation ID</th>
                <th>Time</th>
                <th>Party Size</th>
                <th>Contact Info</th>
                <th>Special Requests</th>
                <th>Table Number</th>
            </tr>

            <?php
            // Fetch reservations for this
