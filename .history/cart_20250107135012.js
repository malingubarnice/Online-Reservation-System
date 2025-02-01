// Array to store selected items
let selectedItems = [];

// Function to add an item to the order list
function addItemToOrder(item) {
    selectedItems.push(item);
    updateOrderList();
}

// Function to update the order list UI
function updateOrderList() {
    const orderListContainer = document.querySelector('.order-list');
    orderListContainer.innerHTML = ''; // Clear previous items

    selectedItems.forEach((item, index) => {
        const orderItemHTML = `
            <div class="order-item">
                <span>${item.name}</span>
                <span>KSh ${item.price.toLocaleString()}</span>
                <button onclick="removeItemFromOrder(${index})">Remove</button>
            </div>
        `;
        orderListContainer.innerHTML += orderItemHTML;
    });
}

// Function to remove an item from the order list
function removeItemFromOrder(index) {
    selectedItems.splice(index, 1);
    updateOrderList();
}

// Function to handle placing the order
document.querySelector('.place-order-btn').addEventListener('click', function () {
    if (selectedItems.length === 0) {
        alert('Your order is empty!');
        return;
    }

    // Update the cart section with selected items
    const cartItemsContainer = document.querySelector('.cart-items');
    cartItemsContainer.innerHTML = ''; // Clear previous cart items

    selectedItems.forEach(item => {
        const cartItemHTML = `
            <div class="cart-item">
                <span>${item.name}</span>
                <span>KSh ${item.price.toLocaleString()}</span>
            </div>
        `;
        cartItemsContainer.innerHTML += cartItemHTML;
    });

    alert('Your order has been placed and added to the cart!');

    // Clear the order list after placing the order
    selectedItems = [];
    updateOrderList();
});
