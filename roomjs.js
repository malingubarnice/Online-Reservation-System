document.addEventListener('DOMContentLoaded', function () {
    // Fetch room data and display rooms dynamically
    fetch('get_room.php')
      .then(response => response.json())
      .then(data => {
        if (Array.isArray(data)) {
          displayRooms(data);
        } else {
          console.error('Error fetching rooms:', data.message);
        }
      })
      .catch(error => console.error('Error fetching room data:', error));
});

// Function to display room cards
function displayRooms(rooms) {
    const roomListContainer = document.querySelector('.room-list');
    roomListContainer.innerHTML = ""; // Clear previous content

    rooms.forEach(room => {
        const roomHTML = `
            <div class="room-card">
                <img src="${room.image_url}" alt="${room.room_name}" style="width: 100%; height: 200px; object-fit: cover;">
                <h2>${room.room_name}</h2>
                <p>Type: ${room.room_type}</p>
                <p>Capacity: ${room.capacity} guests</p>
                <p>${room.description}</p>
                <p>Price: KSh ${room.price_per_night.toLocaleString()}</p>
                <button onclick="selectRoom(${room.room_id}, '${room.room_name}', ${room.price_per_night})">Book Now</button>
            </div>
        `;
        roomListContainer.innerHTML += roomHTML;
    });
}

// Function to select a room for booking
function selectRoom(roomId, roomName, price) {
    console.log(`Room selected: ${roomName} (ID: ${roomId})`);

    document.getElementById('room-selection').value = roomName;
    document.getElementById('room_id').value = roomId;
    document.getElementById('price').textContent = `KSh ${price.toLocaleString()}`;
    document.querySelector('.reservation-form').style.display = 'block'; // Show the booking form
}

// Handle form submission for room booking
document.getElementById('room-booking-form').addEventListener('submit', function (event) {
    event.preventDefault(); // Prevent the form from reloading the page

    const roomId = document.getElementById('room_id').value;
    const checkInDate = document.getElementById('check-in-date').value;
    const checkOutDate = document.getElementById('check-out-date').value;
    const guestCount = document.getElementById('guest-count').value;
    const contactInfo = document.getElementById('contact-info').value;

    const today = new Date();
    const checkIn = new Date(checkInDate);
    const checkOut = new Date(checkOutDate);

    today.setHours(0, 0, 0, 0);
    checkIn.setHours(0, 0, 0, 0);
    checkOut.setHours(0, 0, 0, 0);

    // Check for invalid dates
    if (checkIn < today) {
        alert("Check-in date cannot be in the past.");
        return;
    }

    if (checkIn >= checkOut) {
        alert("Check-out date must be after check-in date.");
        return;
    }

    // Submit booking data via fetch
    fetch('book_room.php', {
        method: 'POST',
        body: JSON.stringify({
            room_id: roomId,
            check_in_date: checkInDate,
            check_out_date: checkOutDate,
            guest_count: guestCount,
            contact_info: contactInfo
        }),
        headers: {
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            alert('Booking confirmed! Email sent.');
            // Optionally, redirect to a confirmation page or reload
            window.location.href = "confirmation.php"; 
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => console.error('Error:', error));
});
