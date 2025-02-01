document.addEventListener('DOMContentLoaded', () => {
    let cartItems = []; // Array to store selected items
    let selectedTable = null;

    // Dynamically create tables
    const tablesContainer = document.querySelector('.tables');
    for (let i = 1; i <= 10; i++) {
        const table = document.createElement('div');
        table.classList.add('table');
        table.id = `table-${i}`;
        table.textContent = `Table ${i}`;
        table.addEventListener('click', () => {
            if (selectedTable) document.querySelector(`#table-${selectedTable}`).classList.remove('selected');
            selectedTable = i;
            table.classList.add('selected');
            document.getElementById('selected-table').value = `Table ${i}`;
        });
        tablesContainer.appendChild(table);
    }

    // Handle reservation submission
    document.querySelector('.reserve-btn').addEventListener('click', () => {
        const date = document.getElementById('date').value;
        const time = document.getElementById('time').value;
        const partySize = document.getElementById('party-size').value;
        const contactInfo = document.getElementById('contact-info').value;

        if (!selectedTable) return alert('Please select a table before proceeding!');
        if (!date || !time || !partySize || !contactInfo) return alert('Fill in all required fields!');

        fetch('backend.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: new URLSearchParams({ action: 'reserve', date, time, party_size: partySize, contact_info: contactInfo, table: selectedTable })
        })
        .then(response => response.json())
        .then(data => alert(data.message))
        .catch(error => console.error('Reservation error:', error));
    });

    // Fetch and display menu items
    fetch('get_menu.php')
        .then(response => response.json())
        .then(data => {
            const menuContainer = document.querySelector('.menu-items');
            data.forEach(item => {
                const menuItem = document.createElement('div');
                menuItem.classList.add('menu-item');
                menuItem.innerHTML = `
                    <img src="${item.image_url}" alt="${item.name}">
                    <h3>${item.name}</h3>
                    <p>${item.description}</p>
                    <p><strong>Price: KSh ${item.price}</strong></p>
                    <button onclick="addToOrder('${item.name}', ${item.price})">Add to Order</button>
                `;
                menuContainer.appendChild(menuItem);
            });
        })
        .catch(error => console.error('Menu fetch error:', error));

    // Display cart items when 'Place Order' is clicked
    document.querySelector('.place-order-btn').addEventListener('click', () => {
        const cartSection = document.querySelector('.cart-items');
        cartSection.innerHTML = '';

        if (cartItems.length === 0) {
            cartSection.innerHTML = '<p>Your cart is empty.</p>';
            return;
        }

        let totalPrice = 0;
        cartItems.forEach(item => {
            const itemElement = document.createElement('div');
            itemElement.className = 'cart-item';
            itemElement.innerHTML = `<p>${item.name} - KSh ${item.price.toLocaleString()}</p>`;
            cartSection.appendChild(itemElement);
            totalPrice += item.price;
        });

        const totalElement = document.createElement('div');
        totalElement.className = 'cart-total';
        totalElement.innerHTML = `<strong>Total: KSh ${totalPrice.toLocaleString()}</strong>`;
        cartSection.appendChild(totalElement);

        // Reset the cart
        cartItems = [];
    });
});

// Function to add items to the cart
function addToOrder(itemName, price) {
    cartItems.push({ name: itemName, price });
    alert(`${itemName} has been added to your order.`);
}








// Fetch room data when the page loads
document.addEventListener('DOMContentLoaded', function () {
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

// Function to display rooms dynamically
function displayRooms(rooms) {
    const roomListContainer = document.querySelector('.room-list');

    rooms.forEach(room => {
      const roomListHTML = `
        <div class="room-card">
          <img src="assets/${room.image_url}" alt="${room.room_name}">
          <h2>${room.room_name}</h2>
          <p>Type: ${room.room_type}</p>
          <p>${room.description}</p>
          <p>Price: KSh ${room.price.toLocaleString()}</p>
          <button onclick="selectRoom(${room.id}, '${room.room_name}', ${room.price})">Book Now</button>
        </div>
      `;
      roomListContainer.innerHTML += roomListHTML;
    });
}

// Handle room selection for booking
function selectRoom(roomId, roomName, price) {
    console.log(`Room selected: ${roomName} (ID: ${roomId})`);

    // Pre-fill the booking form with room details
    document.getElementById('room-selection').value = roomName;
    document.getElementById('room_id').value = roomId;  // Set room_id in hidden input
    document.getElementById('price').textContent = `KSh ${price.toLocaleString()}`;
    document.querySelector('.booking-form').style.display = 'block';  // Show the booking form
}

// Handle form submission for booking
document.getElementById('room-booking-form').addEventListener('submit', function (event) {
    event.preventDefault(); // Prevent default form submission

    // Collect form data
    const roomId = document.getElementById('room_id').value;
    const checkInDate = document.getElementById('check-in-date').value;
    const checkOutDate = document.getElementById('check-out-date').value;
    const guestCount = document.getElementById('guest-count').value;
    const contactInfo = document.getElementById('contact-info').value;

    // Debugging: Log the contact info to verify
    console.log("Contact Info:", contactInfo);  // This should log the contact info entered by the user

    // Debug: log the form data to verify
    console.log('Form Data:', {
        roomId,
        checkInDate,
        checkOutDate,
        guestCount,
        contactInfo
    });

    

    // Check if the check-in date is today or later
    const today = new Date();
    const checkIn = new Date(checkInDate);
    const checkOut = new Date(checkOutDate);

    // Reset the time part to 00:00:00 for comparison
    today.setHours(0, 0, 0, 0);
    checkIn.setHours(0, 0, 0, 0); // Set check-in date to 00:00:00 to match date comparison
    checkOut.setHours(0, 0, 0, 0); // Set check-out date to 00:00:00 for consistency

    // Date Validation: Ensure check-in is not in the past
    if (checkIn < today) {
        alert("Check-in date cannot be in the past.");
        return;
    }

    // Check-out date must be after check-in date
    if (checkIn >= checkOut) {
        alert("Check-out date must be after check-in date.");
        return;
    }

    // Send the booking data to the backend (book_room.php)
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
            alert('Booking confirmed!');
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => console.error('Error:', error));
});








