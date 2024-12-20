document.addEventListener('DOMContentLoaded', () => {
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
        <p>Price: $${parseFloat(product.price).toFixed(2)}</p>
        <p class="product-quantity">in stock: ${product.quantity}</p>
        <div id="cart-controls-${product.itemId}">
          <button class="add-to-cart" data-id="${product.itemId}">Add to Cart</button>
        </div>
      `;
      productList.appendChild(productCard);
    });
  };

  // Filter products based on search and category
  const filterProducts = (products) => {
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

  // Fetch product data from the API
  const fetchProducts = async () => {
    try {
      const response = await fetch('http://localhost/eecs4413/controller/authController/getItems');
      const data = await response.json();

      if (data.status === 'success') {
        const products = data.items.map(item => ({
          itemId: item.itemId,
          name: item.name,
          price: item.price,
          description: item.description,
          image: item.image,
          quantity: parseInt(item.quantity, 10), // Parse quantity as a number
        }));

        // Initial render
        renderProducts(products);

        // Filter products after fetching
        searchInput.addEventListener('input', () => filterProducts(products));
        filterSelect.addEventListener('change', () => filterProducts(products));
        
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
      } else {
        console.error('Error fetching products: ', data.message);
      }
    } catch (error) {
      console.error('Request failed: ', error);
    }
  };

  // Call the function to fetch products
  fetchProducts();
});
