// admin.js

document.addEventListener('DOMContentLoaded', () => {
  // Access control for Admin page (in case someone types /admin.html to try to access this page for example)
  const isAdmin = localStorage.getItem('isAdmin');
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

  // Render inventory
  function renderInventory() {
    inventoryTable.innerHTML = inventory.map((item, index) => `
      <tr>
        <td>${item.name}</td>
        <td>${item.stock}</td>
        <td>
          <button onclick="updateStock(${index}, 1)">Add Stock</button>
          <button onclick="updateStock(${index}, -1)">Remove Stock</button>
        </td>
      </tr>
    `).join('');
  }

  // Update stock
  window.updateStock = (index, delta) => {
    inventory[index].stock += delta;
    if (inventory[index].stock < 0) {
      inventory[index].stock = 0;
    }
    renderInventory();
  };

  // Initial render
  renderInventory();
});
