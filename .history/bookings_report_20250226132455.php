<?php
include 'backend.php'; // Ensure database connection

$query = "SELECT room_id, check_in_date, check_out_date, guest_count, contact_info FROM bookings ORDER BY check_in_date DESC";
$result = mysqli_query($conn, $query);
?>
<table border="1">
    <tr>
        <th>Room ID</th>
        <th>Check-in Date</th>
        <th>Check-out Date</th>
        <th>Guest Count</th>
        <th>Contact Info</th>
    </tr>
    <?php while ($row = mysqli_fetch_assoc($result)) { ?>
    <tr>
        <td><?php echo $row['room_id']; ?></td>
        <td><?php echo $row['check_in_date']; ?></td>
        <td><?php echo $row['check_out_date']; ?></td>
        <td><?php echo $row['guest_count']; ?></td>
        <td><?php echo $row['contact_info']; ?></td>
    </tr>
    <?php } ?>
</table>
