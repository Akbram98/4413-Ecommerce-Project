document.addEventListener('DOMContentLoaded', () => {
    const cart = []; // Simulate an empty cart; populate this array for testing with data
    const cartEmptyMessage = document.getElementById('cart-empty-message');
    const cartItemsContainer = document.getElementById('cart-items');
    const cartTableBody = document.getElementById('cart-table-body');
    const cartTotalElement = document.getElementById('cart-total');
  
    // Function to calculate total price
    const calculateTotal = () => {
      return cart.reduce((total, item) => total + item.price * item.quantity, 0).toFixed(2);
    };
  
    // Function to render the cart
    const renderCart = () => {
      if (cart.length === 0) {
        cartEmptyMessage.style.display = 'block';
        cartItemsContainer.style.display = 'none';
        return;
      }
  
      cartEmptyMessage.style.display = 'none';
      cartItemsContainer.style.display = 'block';
  
      cartTableBody.innerHTML = '';
      cart.forEach((item) => {
        const row = document.createElement('tr');
        row.innerHTML = `
          <td>${item.name}</td>
          <td>$${item.price.toFixed(2)}</td>
          <td>${item.quantity}</td>
          <td>$${(item.price * item.quantity).toFixed(2)}</td>
          <td><button class="remove-item" data-id="${item.id}">Remove</button></td>
        `;
        cartTableBody.appendChild(row);
      });
  
      cartTotalElement.textContent = `Total: $${calculateTotal()}`;
    };
  
    // Handle removing items
    cartTableBody.addEventListener('click', (event) => {
      if (event.target.classList.contains('remove-item')) {
        const itemId = parseInt(event.target.getAttribute('data-id'), 10);
        const index = cart.findIndex((item) => item.id === itemId);
        if (index > -1) cart.splice(index, 1);
        renderCart();
      }
    });
  
    renderCart();
  });
  