// admin.js

document.addEventListener('DOMContentLoaded', () => {
  // Access control for Admin page (in case someone types /admin.html to try to access this page for example)
  const isAdmin = sessionStorage.getItem('isAdmin');
  if (isAdmin !== 'true') {
    alert('Access Denied! Admins only.');
    window.location.href = 'index.html';
    return; // Prevent further execution
  }

  const navLinks = document.querySelectorAll('#navigation a');
  const currentPath = window.location.pathname.split('/').pop(); // Get the current page file name
  
    navLinks.forEach(link => {
      const hrefPath = link.getAttribute('href').split('/').pop(); // Extract the file name from href
      if (hrefPath === currentPath) {
        link.classList.add('active'); // Add active class to the matching link
      } else {
        link.classList.remove('active'); // Remove active class from non-matching links
      }
    });

  const inventoryTable = document.getElementById('inventory');

 // Inventory data
const inventory = [
  { 
    id: 1, 
    name: "Laptop", 
    description: "A high-performance laptop.", 
    stock: 10, 
    price: 999.99, 
    image: "laptop.jpg", 
    date: "2024-12-01"
  },
  { 
    id: 2, 
    name: "T-Shirt", 
    description: "A stylish cotton t-shirt.", 
    stock: 50, 
    price: 19.99, 
    image: "tshirt.jpg", 
    date: "2024-12-05"
  },
  { 
    id: 3, 
    name: "iPhone 14 Pro", 
    description: "The latest smartphone with advanced features.", 
    stock: 5, 
    price: 1299.99, 
    image: "iphone.jpg", 
    date: "2024-12-10"
  }
];

function renderInventory() {
  inventoryTable.innerHTML = inventory
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
        <input type="number" id="stock-input-${index}" value="${item.stock}" min="0">
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
  
  const item = inventory[index];
  item.name = nameInput.value;
  item.description = descriptionInput.value;
  item.stock = parseInt(stockInput.value, 10);
  item.price = parseFloat(priceInput.value);

  if (imageInput.files && imageInput.files[0]) {
    item.image = URL.createObjectURL(imageInput.files[0]); // Update the image preview
  }

  renderInventory(); // Re-render the inventory to reflect changes
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
