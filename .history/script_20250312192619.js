document.addEventListener('DOMContentLoaded', () => {
    let cart = [];  // Array to store the cart items

    const tablesContainer = document.querySelector('.tables');
    let selectedTable = null;  // To store the selected table number

    // Dynamically create tables
    for (let i = 1; i <= 10; i++) {
        const table = document.createElement('div');
        table.classList.add('table');
        table.textContent = `Table ${i}`;  // Use backticks for template literals
        
        // Add click event to each table
        table.addEventListener('click', () => {
            if (selectedTable !== null) {
                // Remove selection from previously selected table
                document.querySelector(`#table-${selectedTable}`).classList.remove('selected');
            }
            
            // Mark the clicked table as selected
            selectedTable = i;
            table.classList.add('selected');
            
            // Optionally, update the reservation form with selected table
            document.getElementById('selected-table').value = `Table ${i}`;
        });
        
        // Give each table a unique ID for easy access
        table.id = `table-${i}`;
        tablesContainer.appendChild(table);
    }

    // Add reservation interaction
    const reserveBtn = document.querySelector('.reserve-btn');
    reserveBtn.addEventListener('click', () => {
        const date = document.getElementById('date').value;
        const time = document.getElementById('time').value;
        const partySize = document.getElementById('party-size').value;
        const contactInfo = document.getElementById('contact-info').value;
        const specialRequests = document.getElementById('special-requests').value;
        const selectedTableElement = document.getElementById('selected-table').value;

        // If no table is selected, show an alert
        if (!selectedTableElement) {
            alert('Please select a table before proceeding with the reservation!');
            return;
        }

        // Validate reservation data (simple check)
        if (!date || !time || !partySize || !contactInfo) {
            alert('Please fill in all required fields!');
            return;
        }

        // Send reservation data to backend
        fetch('backend.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: new URLSearchParams({
                action: 'reserve',
                date: date,
                time: time,
                party_size: partySize,
                contact_info: contactInfo,
                special_requests: specialRequests,
                selected_table: selectedTableElement
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                alert(data.message);
            } else {
                alert(data.message);
            }
        })
        .catch(error => {
            console.error('Error making reservation:', error);
        });
    });

    // Fetch menu items from the backend
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
                    <button class="add-to-order-btn" data-id="${item.id}" data-name="${item.name}" data-price="${item.price}">Add to Order</button>
                `;
                menuContainer.appendChild(menuItem);
            });

            // Add event listener for 'Add to Order' buttons
            const addToOrderBtns = document.querySelectorAll('.add-to-order-btn');
            addToOrderBtns.forEach(button => {
                button.addEventListener('click', (event) => {
                    const id = event.target.getAttribute('data-id');
                    const name = event.target.getAttribute('data-name');
                    const price = parseFloat(event.target.getAttribute('data-price'));

                    // Add to cart
                    cart.push({ id, name, price });

                    // Update cart display
                    updateCartDisplay();
                });
            });
        })
        .catch(error => {
            console.error('Error fetching menu items:', error);
        });

    // Function to update the cart display
    function updateCartDisplay() {
        const cartContainer = document.querySelector('.order-list');
        cartContainer.innerHTML = ''; // Clear the current cart display
        let total = 0;

        // Display each item in the cart
        cart.forEach(item => {
            const cartItemDiv = document.createElement('div');
            cartItemDiv.classList.add('cart-item');
            cartItemDiv.innerHTML = `<p>${item.name} - KSh ${item.price.toFixed(2)}</p>`;
            cartContainer.appendChild(cartItemDiv);
            total += item.price;
        });

        // Display total price
        const totalDiv = document.createElement('div');
        totalDiv.classList.add('total-price');
        totalDiv.innerHTML = `<p>Total: KSh ${total.toFixed(2)}</p>`;
        cartContainer.appendChild(totalDiv);

        // Enable/Disable the "Place Order" button based on the cart
        togglePlaceOrderButton();
    }

    // Function to enable/disable the "Place Order" button based on the cart
    function togglePlaceOrderButton() {
        const placeOrderButton = document.querySelector('.place-order-btn');
        if (cart.length === 0) {
            placeOrderButton.disabled = true;
            placeOrderButton.textContent = 'Please add items to your order';
        } else {
            placeOrderButton.disabled = false;
            placeOrderButton.textContent = 'Place Order';
        }
    }

    // Handle the "Place Order" button click
    document.querySelector('.place-order-btn').addEventListener('click', function() {
        if (cart.length === 0) {
            alert('Please add items to your order before placing it.');
        } else {
            // Send the order to the backend
            fetch('place_order.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ order: cart })
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    alert('Order placed successfully!');
                    cart = []; // Clear the cart after successful order
                    updateCartDisplay(); // Update cart display
                } else {
                    alert('Error placing order: ' + data.message);
                }
            })
            .catch(error => console.error('Error placing order:', error));
        }
    });
});






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

function selectRoom(roomId, roomName, price) {
    console.log(`Room selected: ${roomName} (ID: ${roomId})`);

    document.getElementById('room-selection').value = roomName;
    document.getElementById('room_id').value = roomId;
    document.getElementById('price').textContent = `KSh ${price.toLocaleString()}`;
    document.querySelector('.booking-form').style.display = 'block';
}

document.getElementById('room-booking-form').addEventListener('submit', function (event) {
    event.preventDefault();

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

    if (checkIn < today) {
        alert("Check-in date cannot be in the past.");
        return;
    }

    if (checkIn >= checkOut) {
        alert("Check-out date must be after check-in date.");
        return;
    }

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
            location.reload();
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => console.error('Error:', error));
});
