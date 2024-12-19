document.addEventListener('DOMContentLoaded', () => {
  // Access control for Admin page
  const isAdmin = sessionStorage.getItem('isAdmin');
  if (isAdmin !== 'true') {
    alert('Access Denied! Admins only.');
    window.location.href = 'index.html';
    return; // Prevent further execution
  }

  const navLinks = document.querySelectorAll('#navigation a');
  const currentPath = window.location.pathname.split('/').pop();

  navLinks.forEach(link => {
    const hrefPath = link.getAttribute('href').split('/').pop();
    if (hrefPath === currentPath) {
      link.classList.add('active');
    } else {
      link.classList.remove('active');
    }
  });

  const inventoryTable = document.getElementById('inventory');

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
        <tr>
          <td>
            <img src="${item.image}" alt="${item.name}" style="width: 50px; height: 50px;">
            <input type="file" id="image-input-${index}" accept="image/*">
          </td>
          <td>
            <input type="text" id="name-input-${index}" value="${item.name}">
          </td>
          <td>
            <textarea id="description-input-${index}" rows="2">${item.description}</textarea>
          </td>
          <td>
            <input type="number" id="stock-input-${index}" value="${item.quantity}" min="0">
          </td>
          <td>
            <input type="number" id="price-input-${index}" value="${item.price}" step="0.01" min="0">
          </td>
          <td>${item.date}</td>
          <td>
            <div class="actions">
              <button onclick="updateStock(${index})">Update</button>
              <button onclick="deleteItem(${index})">Delete</button>
            </div>
          </td>
        </tr>
      `)
      .join('');
  }

  // Update stock and other fields
  function updateStock(index) {
    const nameInput = document.getElementById(`name-input-${index}`);
    const descriptionInput = document.getElementById(`description-input-${index}`);
    const stockInput = document.getElementById(`stock-input-${index}`);
    const priceInput = document.getElementById(`price-input-${index}`);
    const imageInput = document.getElementById(`image-input-${index}`);

    const item = {
      name: nameInput.value,
      description: descriptionInput.value,
      quantity: parseInt(stockInput.value, 10),
      price: parseFloat(priceInput.value)
    };

    if (imageInput.files && imageInput.files[0]) {
      item.image = URL.createObjectURL(imageInput.files[0]);
    }

    console.log('Updated item:', item);
    // Here you could send a PUT/POST request to update the inventory in the backend
  }

  function deleteItem(index) {
    if (confirm('Are you sure you want to delete this item?')) {
      // Ideally, send a DELETE request to the backend to remove the item
      console.log(`Item at index ${index} deleted`);
    }
  }

  // Initial fetch
  fetchInventory();
});
