document.addEventListener('DOMContentLoaded', () => {
  const inventoryTable = document.getElementById('inventory');
  const updateForm = document.getElementById('update-form');
  const saveUpdateButton = document.getElementById('save-update');
  const cancelUpdateButton = document.getElementById('cancel-update');

  let currentItemIndex = null;

  // Fetch inventory data from REST API
  function fetchInventory() {
    fetch('http://localhost/eecs4413/controller/authController/getItems')
      .then(response => response.json())
      .then(data => {
        if (data.status === 'success') {
          renderInventory(data.items);
        } else {
          console.error('Error fetching inventory:', data);
        }
      })
      .catch(error => {
        console.error('Error with the GET request:', error);
      });
  }

  // Render inventory data in the table
  function renderInventory(items) {
    inventoryTable.innerHTML = items
      .map((item, index) => `
        <tr id="inventory-row-${index}">
          <td>
            <img src="${item.image}" alt="${item.name}" style="width: 50px; height: 50px;">
          </td>
          <td>${item.name}</td>
          <td>${item.description}</td>
          <td>${item.quantity}</td>
          <td>${item.price}</td>
          <td>${item.date}</td>
          <td>
            <div class="actions">
              <button onclick="showUpdateForm(${index}, '${item.name}', '${item.description}', ${item.quantity}, ${item.price}, '${item.image}')">Update</button>
              <button onclick="deleteItem(${index})">Delete</button>
            </div>
          </td>
        </tr>
      `)
      .join('');
  }

  // Show update form for the selected item
  window.showUpdateForm = (index, name, description, quantity, price, image) => {
    currentItemIndex = index;

    // Populate the form with the selected item's data
    document.getElementById('update-name').value = name;
    document.getElementById('update-description').value = description;
    document.getElementById('update-stock').value = quantity;
    document.getElementById('update-price').value = price;

    // Hide all rows except the one being updated
    Array.from(inventoryTable.children).forEach((row, i) => {
      row.style.display = i === index ? 'table-row' : 'none';
    });

    // Show the update form
    updateForm.style.display = 'block';
  };

  // Save updated item
  saveUpdateButton.addEventListener('click', () => {
    if (currentItemIndex === null) return;

    const updatedItem = {
      name: document.getElementById('update-name').value,
      description: document.getElementById('update-description').value,
      quantity: parseInt(document.getElementById('update-stock').value, 10),
      price: parseFloat(document.getElementById('update-price').value),
    };

    console.log('Updated item:', updatedItem);

    // Send updated data to the backend (PUT/POST request can be added here)

    // Reset the table and hide the form
    updateForm.style.display = 'none';
    fetchInventory();
  });

  // Cancel the update and restore the table rows
  cancelUpdateButton.addEventListener('click', () => {
    // Hide the update form
    updateForm.style.display = 'none';

    // Show all rows again
    Array.from(inventoryTable.children).forEach((row) => {
      row.style.display = 'table-row';
    });
  });

  // Delete an item
  window.deleteItem = (index) => {
    if (confirm('Are you sure you want to delete this item?')) {
      console.log(`Item at index ${index} deleted`);
      // Ideally, send a DELETE request to the backend to remove the item
    }
  };

  // Initial fetch
  fetchInventory();
});
