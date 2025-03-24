<?php
// Database connection
$conn = new mysqli("localhost", "root", "", "coppers_ivy");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Search filter for Reservation ID
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

// SQL Query: Fetch all details, filter by exact Reservation ID if provided
$query = "SELECT reservation_id, date, time, party_size, contact_info, special_requests, table_number FROM reservations";

if (!empty($search)) {
    $search = $conn->real_escape_string($search);
    $query .= " WHERE reservation_id LIKE '%$search%'";
}

$query .= " ORDER BY date DESC"; // Order by date
$result = $conn->query($query);

// Store reservations grouped by date
$reservations = [];
while ($row = $result->fetch_assoc()) {
    $reservations[$row['date']][] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reservation Search</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 10px; text-align: left; }
        th { background-color: #f4f4f4; }
        form { margin-bottom: 20px; }
        input, button { padding: 10px; margin: 5px; }
        button { background-color:rgb(25, 167, 79); color: white; border: none; cursor: pointer; }
        button:hover { background-color:rgb(25, 167, 79); }
    </style>
</head>
<body>
    <h2>Search Reservations</h2>
    <form method="GET">
        <input type="text" name="search" placeholder="Enter Reservation ID (e.g., RES-20250306-123)" 
               value="<?php echo htmlspecialchars($search); ?>">
        <button type="submit">Search</button>
    </form>
    
    <?php if (count($reservations) > 0) { ?>
        <table>
            <tr>
                <th>Date</th>
                <th>Total Reservations</th>
                <th>Details</th>
            </tr>
            <?php foreach ($reservations as $date => $details) { ?>
            <tr>
                <td><?php echo date('l, jS F Y', strtotime($date)); ?></td>
                <td><?php echo count($details); ?></td>
                <td>
                    <table>
                        <tr>
                            <th>Reservation ID</th>
                            <th>Time</th>
                            <th>Party Size</th>
                            <th>Contact Info</th>
                            <th>Special Requests</th>
                            <th>Table Number</th>
                        </tr>
                        <?php foreach ($details as $reservation) { ?>
                        <tr>
                            <td><?php echo htmlspecialchars($reservation['reservation_id']); ?></td>
                            <td><?php echo date('h:i A', strtotime($reservation['time'])); ?></td>
                            <td><?php echo htmlspecialchars($reservation['party_size']); ?></td>
                            <td><?php echo htmlspecialchars($reservation['contact_info']); ?></td>
                            <td><?php echo htmlspecialchars($reservation['special_requests']); ?></td>
                            <td><?php echo htmlspecialchars($reservation['table_number']); ?></td>
                        </tr>
                        <?php } ?>
                    </table>
                </td>
            </tr>
            <?php } ?>
        </table>
    <?php } else { ?>
        <p>No reservations found.</p>
    <?php } ?>

    <!-- CSV Export -->
    <form action="export_reservations.php" method="POST">
        <button type="submit">Export to CSV</button>
    </form>
</body>
</html>