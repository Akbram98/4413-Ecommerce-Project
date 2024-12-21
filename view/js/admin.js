document.addEventListener('DOMContentLoaded', () => {
  const inventoryTable = document.getElementById('inventory');
  const updateForm = document.getElementById('update-form');
  const saveUpdateButton = document.getElementById('save-update');
  const cancelUpdateButton = document.getElementById('cancel-update');

  const isAdmin = sessionStorage.getItem('isAdmin');
    if (isAdmin !== 'true') {
        alert('Access Denied! Admins only.');
        window.location.href = 'index.html';
        return;
    }

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
        <tr id="inventory-row-${index}" data-item-id="${item.itemId}" data-item-img="${item.image}" data-item-brand="${item.brand}">
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

    //get admin username from session
    const adminUser = sessionStorage.getItem("username");

    //check if admin user is null
    if(adminUser === null){
      return;
    }

    //get the current selected item by index
    //get data attributes for id, image, brand
    const updateValue = document.getElementById(`inventory-row-${currentItemIndex}`);
    const item_Id = updateValue.getAttribute("data-item-id");
    const item_image = updateValue.getAttribute("data-item-img");
    const item_brand = updateValue.getAttribute("data-item-brand");

    //check if the id, brand, image is null
    if(item_Id === null || item_brand === null || item_image === null){
      return;
    }

    //request body
    const updateRequest = {
      itemid: item_Id,
      image: item_image,
      brand: item_brand,
      name: updatedItem.name,
      price: updatedItem.price,
      quantity: updatedItem.quantity,
      description: updatedItem.description,
      admin: adminUser


    };

    fetch('http://localhost/eecs4413/controller/authController/adminUpdateItem', {
      method: 'PUT',
      headers: {
        'Content-Type': 'application/json',
      },
      body: JSON.stringify(updateRequest)

    })
    .then(response => response.json())
    .then(data => {
      if(data.status == 'success'){
        console.log('Updated item:', updatedItem);
      }else{
        console.error('Error updating item:',data.message);
      }
    })
   

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
    //get the current row selected
    const deleteValue = document.getElementById(`inventory-row-${index}`);
      
    //get the username of the current user from session and check if null
      const adminUser = sessionStorage.getItem("username");
    if(adminUser===null){
      return;
    }

      //get the itemId from the attribute of the currently selected item
      //modified fetchInventory() to store item_Id
      //check if item_Id is null


      const item_Id = deleteValue.getAttribute("data-item-id");
      if(item_Id===null){
        return;
      }
    
    if (confirm('Are you sure you want to delete this item?')) {
      

      const deleteRequest = {
        item_id: item_Id,
        admin: adminUser
      };

      fetch('http://localhost/eecs4413/controller/authController/deleteItem',{
      method: 'DELETE',
      headers: {
        'Content-Type': 'application/json',
      },
      body: JSON.stringify(deleteRequest)
      })
      .then(response =>response.json())
      .then(data => {
        if(data.status == 'success'){
      //if success then fetches updated inventory
      console.log(`Item at index ${index} deleted`);
      fetchInventory();
        }else{
          console.error('Error deleting item:', data.message);
        }
})
      // Ideally, send a DELETE request to the backend to remove the item
    }
  };

  // Initial fetch
  fetchInventory();
});
