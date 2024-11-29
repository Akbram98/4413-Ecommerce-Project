// admin.js

document.addEventListener('DOMContentLoaded', () => {
    const inventoryTable = document.getElementById('inventory');
  
    // Simulated inventory data
    const inventory = [
      { id: 1, name: "Laptop", stock: 10 },
      { id: 2, name: "T-Shirt", stock: 50 }
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
  