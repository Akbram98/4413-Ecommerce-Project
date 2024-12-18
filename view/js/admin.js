// admin.js

document.addEventListener('DOMContentLoaded', () => {
  // Access control for Admin page (in case someone types /admin.html to try to access this page for example)
  const isAdmin = sessionStorage.getItem('isAdmin');
  if (isAdmin !== 'true') {
    alert('Access Denied! Admins only.');
    window.location.href = 'index.html';
    return; // Prevent further execution
  }

  const inventoryTable = document.getElementById('inventory');

  // Inventory data
  const inventory = [
    { id: 1, name: "Laptop", stock: 10 },
    { id: 2, name: "T-Shirt", stock: 50 },
    { id: 3, name: "iPhone 14 Pro", stock: 5 }
  ];

  function renderInventory() {
    inventoryTable.innerHTML = inventory.map((item, index) => `
      <tr>
        <td>${item.name}</td>
        <td>${item.stock}</td>
        <td>
          <div class="stock-controls">
            <input type="number" id="stock-input-${index}" value="${item.stock}" min="0">
            <button onclick="updateStock(${index})">Update Stock</button>
            <button onclick="deleteItem(${index})">Delete Item</button>
          </div>
        </td>
      </tr>
    `).join('');
  }
  
  function updateStock(index) {
    const newStock = parseInt(document.getElementById(`stock-input-${index}`).value);
    if (!isNaN(newStock) && newStock >= 0) {
      inventory[index].stock = newStock;
      renderInventory(); // Re-render inventory with updated stock
    } else {
      alert("Please enter a valid number for stock.");
    }
  }
  
  function deleteItem(index) {
    if (confirm("Are you sure you want to delete this item?")) {
      inventory.splice(index, 1); // Remove the item from inventory
      renderInventory(); // Re-render inventory after deletion
    }
  }
  

  /*// Update stock
  window.updateStock = (index, delta) => {
    inventory[index].stock += delta;
    if (inventory[index].stock < 0) {
      inventory[index].stock = 0;
    }
    renderInventory();
  };*/

  // Initial render
  renderInventory();
});
