<?php
// Database connection
$conn = new mysqli("localhost", "root", "", "coppers_ivy");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Search filter for Booking ID (allowing alphanumeric input)
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

// SQL Query: Fetch all details, filter by exact Booking ID if provided
$query = "SELECT booking_id, check_in_date, check_out_date, contact_info, guest_count, room_id, created_at FROM bookings";

if (!empty($search)) {
    $search = $conn->real_escape_string($search);
    $query .= " WHERE booking_id LIKE '%$search%'"; // Allows partial search
}

$query .= " ORDER BY check_in_date DESC"; // Order by check-in date
total_count_query = "SELECT COUNT(*) as total FROM bookings";
$total_count_result = $conn->query($total_count_query);
$total_count_row = $total_count_result->fetch_assoc();
$total_bookings = $total_count_row['total'];

$result = $conn->query($query);

// Store bookings grouped by check-in date
$bookings = [];
while ($row = $result->fetch_assoc()) {
    $bookings[$row['check_in_date']][] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Search</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 10px; text-align: left; }
        th { background-color: #f4f4f4; }
        form { margin-bottom: 20px; }
        input, button { padding: 10px; margin: 5px; }
        button { background-color: #007bff; color: white; border: none; cursor: pointer; }
        button:hover { background-color: #0056b3; }
    </style>
</head>
<body>
    <h2>Search Bookings</h2>
    <p>Total Bookings: <?php echo $total_bookings; ?></p>
    <form method="GET">
        <input type="text" name="search" placeholder="Enter Booking ID (e.g., BKG-89543)" 
               value="<?php echo htmlspecialchars($search); ?>">
        <button type="submit">Search</button>
    </form>
    
    <?php if (count($bookings) > 0) { ?>
        <table>
            <tr>
                <th>Check-in Date</th>
                <th>Total Bookings</th>
                <th>Details</th>
            </tr>
            <?php foreach ($bookings as $date => $details) { ?>
            <tr>
                <td><?php echo date('l, jS F Y', strtotime($date)); ?></td>
                <td><?php echo count($details); ?></td>
                <td>
                    <table>
                        <tr>
                            <th>Booking ID</th>
                            <th>Check-in Date</th>
                            <th>Check-out Date</th>
                            <th>Contact Info</th>
                            <th>Guest Count</th>
                            <th>Room ID</th>
                            <th>Created At</th>
                        </tr>
                        <?php foreach ($details as $booking) { ?>
                        <tr>
                            <td><?php echo htmlspecialchars($booking['booking_id']); ?></td>
                            <td><?php echo date('Y-m-d', strtotime($booking['check_in_date'])); ?></td>
                            <td><?php echo date('Y-m-d', strtotime($booking['check_out_date'])); ?></td>
                            <td><?php echo htmlspecialchars($booking['contact_info']); ?></td>
                            <td><?php echo htmlspecialchars($booking['guest_count']); ?></td>
                            <td><?php echo htmlspecialchars($booking['room_id']); ?></td>
                            <td><?php echo date('l, jS F Y h:i A', strtotime($booking['created_at'])); ?></td>
                        </tr>
                        <?php } ?>
                    </table>
                </td>
            </tr>
            <?php } ?>
        </table>
    <?php } else { ?>
        <p>No bookings found.</p>
    <?php } ?>
</body>
</html>
