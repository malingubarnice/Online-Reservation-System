<?php
// Connect to MySQL (using XAMPP's default settings)
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "coppers_ivy"; // Ensure this database exists

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Set content type to JSON
header('Content-Type: application/json');

// Handle POST request for different actions
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $action = $_POST['action'] ?? '';

    // Handle reservation creation
    if ($action === 'reserve') {
        $date = $_POST['date'];
        $time = $_POST['time'];
        $party_size = $_POST['party_size'];
        $contact_info = $_POST['contact_info'];
        $special_requests = $_POST['special_requests'];
        $selected_table = $_POST['selected_table'];

        // Simple validation (make sure all fields are filled)
        if (empty($date) || empty($time) || empty($party_size) || empty($contact_info) || empty($selected_table)) {
            echo json_encode(['status' => 'error', 'message' => 'All fields are required.']);
            exit;
        }

        // Check if the table is already reserved for the given date and time
        $sql_check = "SELECT * FROM reservations WHERE date = '$date' AND time = '$time' AND table_number = '$selected_table'";
        $result_check = $conn->query($sql_check);

        if ($result_check->num_rows > 0) {
            // If the table is already reserved, return an error
            echo json_encode(['status' => 'error', 'message' => 'This table is already reserved for the selected date and time, Please select another table.']);
            exit;
        }

        // Insert the reservation into the database
        $sql_insert = "INSERT INTO reservations (date, time, party_size, contact_info, special_requests, table_number) 
                       VALUES ('$date', '$time', '$party_size', '$contact_info', '$special_requests', '$selected_table')";

        if ($conn->query($sql_insert) === TRUE) {
            echo json_encode(['status' => 'success', 'message' => 'Reservation created successfully!']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Error: ' . $conn->error]);
        }
    }

    // Handle fetching menu items
    if ($action === 'get_menu') {
        // Fetch menu items from database
        $sql = "SELECT name, description, price, image_url FROM menu_items"; // Make sure your table name is correct
        $result = $conn->query($sql);

        // Check if any menu items exist
        if ($result->num_rows > 0) {
            $menuItems = [];
            while ($row = $result->fetch_assoc()) {
                $menuItems[] = $row;
            }
            echo json_encode($menuItems);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'No menu items found']);
        }
    }
}

$conn->close();
?> get_menu.php has the following code <?php
$conn = new mysqli("localhost", "root", "", "coppers_ivy");
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

$result = $conn->query("SELECT * FROM menu");
$menu = [];
while ($row = $result->fetch_assoc()) {
    $menu[] = $row;
}
echo json_encode($menu);
$conn->close();
?>
place_order.php has the following code <?php
$data = json_decode(file_get_contents('php://input'), true);
$conn = new mysqli("localhost", "root", "", "coppers_ivy");
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

$name = "Anonymous"; // Use customer name from login if available
$email = "test@example.com"; // Use customer email from login if available
$items = json_encode($data['order']);
$total_price = array_sum(array_column($data['order'], 'price'));

$stmt = $conn->prepare("INSERT INTO orders (customer_name, customer_email, items, total_price) VALUES (?, ?, ?, ?)");
$stmt->bind_param("sssd", $name, $email, $items, $total_price);

if ($stmt->execute()) {
    echo json_encode(['status' => 'success']);
} else {
    echo json_encode(['status' => 'error', 'message' => $conn->error]);
}
$conn->close();
?> and script.js has the following code document.addEventListener('DOMContentLoaded', () => {
    let cart = [];  // Array to store the cart items

    const tablesContainer = document.querySelector('.tables');
    let selectedTable = null;  // To store the selected table number

    // Dynamically create tables
    for (let i = 1; i <= 10; i++) {
        const table = document.createElement('div');
        table.classList.add('table');
        table.textContent = Table ${i};
        
        // Add click event to each table
        table.addEventListener('click', () => {
            if (selectedTable !== null) {
                // Remove selection from previously selected table
                document.querySelector(#table-${selectedTable}).classList.remove('selected');
            }
            
            // Mark the clicked table as selected
            selectedTable = i;
            table.classList.add('selected');
            
            // Optionally, update the reservation form with selected table
            document.getElementById('selected-table').value = Table ${i};
        });
        
        // Give each table a unique ID for easy access
        table.id = table-${i};
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
                menuItem.innerHTML = 
                    <img src="${item.image_url}" alt="${item.name}">
                    <h3>${item.name}</h3>
                    <p>${item.description}</p>
                    <p><strong>Price: KSh ${item.price}</strong></p>
                    <button class="add-to-order-btn" data-id="${item.id}" data-name="${item.name}" data-price="${item.price}">Add to Order</button>
                ;
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
            cartItemDiv.innerHTML = <p>${item.name} - KSh ${item.price.toFixed(2)}</p>;
            cartContainer.appendChild(cartItemDiv);
            total += item.price;
        });

        // Display total price
        const totalDiv = document.createElement('div');
        totalDiv.classList.add('total-price');
        totalDiv.innerHTML = <p>Total: KSh ${total.toFixed(2)}</p>;
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