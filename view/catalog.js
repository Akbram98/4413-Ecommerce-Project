document.addEventListener('DOMContentLoaded', () => {
    // Product list
    const products = [
      { id: 1, name: "Laptop", category: "electronics", price: 999.99, image: "assets/laptop.png" },
      { id: 2, name: "iPhone 14 Pro", category: "electronics", price: 749.99, image: "assets/iPhone-14.png" },
      { id: 3, name: "Sony WH 1000xm5", category: "electronics", price: 149.99, image: "assets/Sony WH 1000xm5.png" },
      { id: 4, name: "Black Shirt", category: "fashion", price: 24.99, image: "assets/Black Shirt.jpg" },
      { id: 5, name: "Washing Machine", category: "home", price: 499.99, image: "assets/Washing_Machine.png" },
    ];
  
    // Cart to track item quantities
    const cart = {};
  
    const productList = document.getElementById('product-list');
    const searchInput = document.getElementById('search');
    const filterSelect = document.getElementById('filter');
  
    // Function to render products
    const renderProducts = (filteredProducts) => {
      productList.innerHTML = '';
      filteredProducts.forEach((product) => {
        const productCard = document.createElement('div');
        productCard.className = 'product-card';
        productCard.innerHTML = `
          <img src="${product.image}" alt="${product.name}" class="product-image">
          <h3>${product.name}</h3>
          <p>Price: $${product.price.toFixed(2)}</p>
          <div id="cart-controls-${product.id}">
            <button class="add-to-cart" data-id="${product.id}">Add to Cart</button>
          </div>
        `;
        productList.appendChild(productCard);
      });
    };
  
    // Filter products based on search and category
    const filterProducts = () => {
      const searchQuery = searchInput.value.toLowerCase();
      const selectedCategory = filterSelect.value;
  
      const filteredProducts = products.filter((product) => {
        const matchesSearch = product.name.toLowerCase().includes(searchQuery);
        const matchesCategory = selectedCategory === 'all' || product.category === selectedCategory;
        return matchesSearch && matchesCategory;
      });
  
      renderProducts(filteredProducts);
    };
  
    // Update cart controls dynamically
    const updateCartControls = (productId) => {
      const cartControls = document.getElementById(`cart-controls-${productId}`);
      const quantity = cart[productId] || 0;
  
      if (quantity > 0) {
        cartControls.innerHTML = `
          <button class="decrease-quantity" data-id="${productId}">-</button>
          <span class="cart-quantity" data-id="${productId}">${quantity}</span>
          <button class="increase-quantity" data-id="${productId}">+</button>
        `;
      } else {
        cartControls.innerHTML = `
          <button class="add-to-cart" data-id="${productId}">Add to Cart</button>
        `;
      }
    };
  
    // Handle cart operations
    const handleCart = (productId, operation) => {
      if (operation === 'add') {
        cart[productId] = (cart[productId] || 0) + 1;
      } else if (operation === 'remove' && cart[productId]) {
        cart[productId] -= 1;
        if (cart[productId] === 0) {
          delete cart[productId];
        }
      }
  
      // Update cart controls for the specific product
      updateCartControls(productId);
    };
  
    // Event listeners
    searchInput.addEventListener('input', filterProducts);
    filterSelect.addEventListener('change', filterProducts);
  
    productList.addEventListener('click', (event) => {
      const target = event.target;
      const productId = parseInt(target.getAttribute('data-id'), 10);
  
      if (target.classList.contains('add-to-cart')) {
        handleCart(productId, 'add');
      } else if (target.classList.contains('increase-quantity')) {
        handleCart(productId, 'add');
      } else if (target.classList.contains('decrease-quantity')) {
        handleCart(productId, 'remove');
      }
    });
  
    // Initial render
    renderProducts(products);
  });
  