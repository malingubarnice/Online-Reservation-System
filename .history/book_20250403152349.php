<?php
include 'backend.php'; // Ensure database connection is included
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book a Room</title>
    <link rel="stylesheet" href="styles.css"> <!-- Link to your CSS file -->
</head>
<body>

    <!-- Navigation -->
    <nav>
        <a href="index.php">Home</a>
        <a href="book.php">Book a Room</a>
        <a href="order.php">Order Food</a>
        <a href="contact.php">Contact Us</a>
    </nav>

    <!-- Rooms Section -->
    <section id="rooms">
        <h1>Book a Room</h1>
        <p>Explore our luxury rooms and make a booking today.</p>
        
        <div class="room-list">
            <?php
            // Fetch room details from the database
            $query = "SELECT * FROM rooms";
            $result = mysqli_query($conn, $query);

            while ($row = mysqli_fetch_assoc($result)) {
                echo '<div class="room-card">';
                echo '<h3>' . $row['name'] . '</h3>';
                echo '<p>Price: KSh ' . $row['price'] . ' per night</p>';
                echo '<button onclick="selectRoom(' . $row['id'] . ', \'' . $row['name'] . '\', ' . $row['price'] . ')">Book Now</button>';
                echo '</div>';
            }
            ?>
        </div>

        <!-- Booking Form (Initially Hidden) -->
        <div class="reservation-form">
            <form action="send_room.php" method="post">
                <!-- Hidden input for room ID -->
                <input type="hidden" id="room_id" name="room_id">
                
                <label for="room-selection">Room</label>
                <input type="text" id="room-selection" name="room_name" readonly>

                <label for="check-in-date">Check-in Date</label>
                <input type="date" id="check-in-date" name="check_in_date" required>

                <label for="check-out-date">Check-out Date</label>
                <input type="date" id="check-out-date" name="check_out_date" required>

                <label for="guest-count">Number of Guests</label>
                <input type="number" id="guest-count" name="guest_count" min="1" required>

                <label for="contact-info">Contact Information</label>
                <input type="text" id="contact-info" name="contact_info" placeholder="Enter your contact info" required>

                <p id="price-display"></p>
                <input type="hidden" id="price" name="price">

                <button type="submit" class="reserve-btn">Book Room</button>
            </form>
        </div>
    </section>

    <script>
        function selectRoom(id, name, price) {
            document.getElementById('room_id').value = id;
            document.getElementById('room-selection').value = name;
            document.getElementById('price').value = price;
            document.getElementById('price-display').innerText = "Total Price: KSh " + price;
        }
    </script>

</body>
</html>
