document.addEventListener('DOMContentLoaded', () => {
    const isAdmin = sessionStorage.getItem('isAdmin');
    if (isAdmin !== 'true') {
      alert('Access Denied! Admins only.');
      window.location.href = 'index.html';
      return; // Prevent further execution
    }
  
    // Get the form and inputs
    const form = document.getElementById('add-item-form');
    const itemName = document.getElementById('item-name');
    const itemDescription = document.getElementById('item-description');
    const itemPrice = document.getElementById('item-price');
    const itemQuantity = document.getElementById('item-quantity');
    const itemImage = document.getElementById('item-image');
    const itemBrand = document.getElementById('item-brand');
  
    // Add submit event listener to the form
    form.addEventListener('submit', (event) => {
      event.preventDefault(); // Prevent the form from submitting normally
  
      // Get the form data
      const formData = new FormData();
      formData.append('admin', sessionStorage.getItem('username')); // Assuming username is stored in sessionStorage
      formData.append('name', itemName.value);
      formData.append('description', itemDescription.value);
      formData.append('price', itemPrice.value);
      formData.append('quantity', itemQuantity.value);
      formData.append('image', itemImage.value);
      formData.append('brand', itemBrand.value);

      console.log(itemImage.value);
  
      // Send AJAX request to add the new item
      fetch('http://localhost/eecs4413/controller/authController/adminAddItem', {
        method: 'POST',
        body: formData,
      })
      .then(response => response.json())
      .then(data => {
        if (data.status === 'success') {
          // Display success message
          alert('Product added successfully!');
          // Optionally, reset the form or redirect
          form.reset();
        } else {
          alert(data.message + " " + JSON.stringify(data.mydata) + " " + data);
        }
      })
      .catch(error => {
        console.error('Error with the AJAX request:', error);
        alert('An error occurred. Please try again later.');
      });
    });
  });
  