document.addEventListener('DOMContentLoaded', function() {
    // Example JSON Data (You should replace it with an API call)
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

    const clientsData = [
        {
          userName: "mikdol",
          firstName: "Mikalle",
          lastName: "McGregor",
          address: "456 Myers street, Toronto, Ontario, L3Y 2K8",
          cardInfo: "Card Number: 3238 2438 8382 3333, CVV: 477, Expiry: 08/34",
          lastLogon: "2024-12-07 03:59:49",
          profile: {
            street: "456 Myers street",
            city: "Toronto",
            province: "Ontario",
            postal: "L3Y 2K8",
            cardNum: "3238 2438 8382 3333",
            cvv: "477",
            expiry: "08/34"
          }
        },
        {
          userName: "user01",
          firstName: "Alice",
          lastName: "Smith",
          address: "123 Oak St, Toronto, Ontario, M5A 1A1",
          cardInfo: "Card Number: 1234 5678 1234 5678, CVV: 123, Expiry: 12/25",
          lastLogon: "2024-11-20 14:23:10",
          profile: {
            street: "123 Oak St",
            city: "Toronto",
            province: "Ontario",
            postal: "M5A 1A1",
            cardNum: "1234 5678 1234 5678",
            cvv: "123",
            expiry: "12/25"
          }
        },
        {
          userName: "user03",
          firstName: "Charlie",
          lastName: "Brown",
          address: "456 Oak Rd, Hamilton, Ontario, L5N 2T3",
          cardInfo: "Card Number: 3456 7890 3456 7890, CVV: 345, Expiry: 10/23",
          lastLogon: "2024-11-25 08:14:55",
          profile: {
            street: "456 Oak Rd",
            city: "Hamilton",
            province: "Ontario",
            postal: "L5N 2T3",
            cardNum: "3456 7890 3456 7890",
            cvv: "345",
            expiry: "10/23"
          }
        }
      ];
      
  
    // Function to Render Clients Table
function renderClients(clients) {
    const tableBody = document.getElementById('clients-data');
    tableBody.innerHTML = ''; // Clear the table
    
    clients.forEach(client => {
      const row = document.createElement('tr');
      row.innerHTML = `
        <td>${client.userName}</td>
        <td>${client.firstName} ${client.lastName}</td>
        <td>${client.address}</td>
        <td>${client.cardInfo}</td>
        <td>${client.lastLogon}</td>
        <td>
         <div class="actions">
          <button class="update-btn" data-user="${client.userName}">Update</button>
          <button class="delete-btn" data-user="${client.userName}">Delete</button>
         </div>
        </td>
      `;
      tableBody.appendChild(row);
    });
  
    // Add event listeners for the buttons
    document.querySelectorAll('.update-btn').forEach(button => {
      button.addEventListener('click', handleUpdateClick);
    });
  
    document.querySelectorAll('.delete-btn').forEach(button => {
      button.addEventListener('click', handleDeleteClick);
    });
  }
  
  // Handle Update Button Click
  function handleUpdateClick(e) {
    const userName = e.target.getAttribute('data-user');
    const client = clientsData.find(client => client.userName === userName);
  
    // Remove all clients except the one being updated
    const filteredClients = clientsData.filter(client => client.userName === userName);
    renderClients(filteredClients); // Re-render with only the selected client
  
    // Populate the update form with user data
    document.getElementById('update-street').value = client.profile.street;
    document.getElementById('update-city').value = client.profile.city;
    document.getElementById('update-province').value = client.profile.province;
    document.getElementById('update-postal').value = client.profile.postal;
    document.getElementById('update-card-num').value = client.profile.cardNum;
    document.getElementById('update-cvv').value = client.profile.cvv;
    document.getElementById('update-expiry').value = client.profile.expiry;
  
    // Show the update form
    document.getElementById('update-form').style.display = 'block';
  }
  
  // Handle Delete Button Click
  function handleDeleteClick(e) {
    const userName = e.target.getAttribute('data-user');
    const index = clientsData.findIndex(client => client.userName === userName);
    if (index !== -1) {
      clientsData.splice(index, 1);
      renderClients(clientsData); // Re-render after deletion
    }
  }
  
  // Save the updates (this can be a POST request to an API)
  document.getElementById('save-update').addEventListener('click', () => {
    const updatedProfile = {
      street: document.getElementById('update-street').value,
      city: document.getElementById('update-city').value,
      province: document.getElementById('update-province').value,
      postal: document.getElementById('update-postal').value,
      cardNum: document.getElementById('update-card-num').value,
      cvv: document.getElementById('update-cvv').value,
      expiry: document.getElementById('update-expiry').value
    };
  
    // Apply the update to the client's data
    const clientIndex = clientsData.findIndex(client => client.userName === 'mikdol');
    if (clientIndex !== -1) {
      clientsData[clientIndex].profile = updatedProfile;
      renderClients(clientsData); // Re-render after update
    }
  
    // Hide the update form
    document.getElementById('update-form').style.display = 'none';
  });
  
  // Initialize the page by rendering the clients
  renderClients(clientsData);
  
  });
  