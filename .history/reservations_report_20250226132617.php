<?php
$query = "SELECT date, time, party_size, contact_info, special_requests, table_number FROM reservations ORDER BY date DESC";
$result = mysqli_query($conn, $query);
?>
<table border="1">
    <tr>
        <th>Date</th>
        <th>Time</th>
        <th>Party Size</th>
        <th>Contact Info</th>
        <th>Special Requests</th>
        <th>Table Number</th>
    </tr>
    <?php while ($row = mysqli_fetch_assoc($result)) { ?>
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
