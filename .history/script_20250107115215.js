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







// Fetch room data when the page loads
