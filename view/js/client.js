document.addEventListener('DOMContentLoaded', function () {
    const isAdmin = sessionStorage.getItem('isAdmin');
    if (isAdmin !== 'true') {
        alert('Access Denied! Admins only.');
        window.location.href = 'index.html';
        return;
    }

    const username = sessionStorage.getItem('username');
    const url = `http://localhost/eecs4413/controller/authController/getCustomers?admin=${username}`;
    let clientsData = []; // This will store fetched client data

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

    // Fetch Clients Data
    fetch(url)
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                clientsData = data.users; // Store the fetched data
                renderClients(clientsData);
            } else {
                alert('Failed to fetch customer data.');
            }
        })
        .catch(error => console.error('Error fetching data:', error));

    // Function to Render Clients Table
    function renderClients(clients) {
        const tableBody = document.getElementById('clients-data');
        tableBody.innerHTML = ''; // Clear the table

        clients.forEach(client => {
            const row = document.createElement('tr');
            row.dataset.user = client.userName; // Associate row with the user
            row.innerHTML = `
                <td>${client.userName}</td>
                <td>${client.profile.firstName} ${client.profile.lastName}</td>
                <td>${client.profile.street}, ${client.profile.city}, ${client.profile.province}, ${client.profile.postal}</td>
                <td>Card Number: ${client.profile.cardNum}, CVV: ${client.profile.cvv}, Expiry: ${client.profile.expiry}</td>
                <td>${client.lastLogon || 'N/A'}</td>
                <td>
                    <div class="actions">
                        <button class="update-btn" data-user="${client.userName}">Update</button>
                        <button class="delete-btn" data-user="${client.userName}">Delete</button>
                    </div>
                </td>
            `;
            tableBody.appendChild(row);
        });

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

        if (client) {
            // Populate the update form
            document.getElementById('update-street').value = client.profile.street || '';
            document.getElementById('update-city').value = client.profile.city || '';
            document.getElementById('update-province').value = client.profile.province || '';
            document.getElementById('update-postal').value = client.profile.postal || '';
            document.getElementById('update-card-num').value = client.profile.cardNum || '';
            document.getElementById('update-cvv').value = client.profile.cvv || '';
            document.getElementById('update-expiry').value = client.profile.expiry || '';

            // Store the current username being updated
            document.getElementById('update-form').dataset.user = userName;

            // Hide all rows except the selected one
            document.querySelectorAll('#clients-data tr').forEach(row => {
                if (row.dataset.user !== userName) {
                    row.style.display = 'none';
                }
            });

            // Show the update form below the row
            const row = document.querySelector(`#clients-data tr[data-user="${userName}"]`);
            row.insertAdjacentHTML('afterend', `
                <tr id="update-row">
                    <td colspan="6">
                        <div id="update-form">
                            <label>Street: <input type="text" id="update-street"></label>
                            <label>City: <input type="text" id="update-city"></label>
                            <label>Province: <input type="text" id="update-province"></label>
                            <label>Postal: <input type="text" id="update-postal"></label>
                            <label>Card Number: <input type="text" id="update-card-num"></label>
                            <label>CVV: <input type="text" id="update-cvv"></label>
                            <label>Expiry: <input type="text" id="update-expiry"></label>
                            <button id="save-update">Save</button>
                            <button id="cancel-update">Cancel</button>
                        </div>
                    </td>
                </tr>
            `);

            // Attach event listeners to save and cancel buttons
            document.getElementById('save-update').addEventListener('click', saveUpdate);
            document.getElementById('cancel-update').addEventListener('click', cancelUpdate);
        }
    }

    // Handle Save Updates
    function saveUpdate() {
        const userName = document.getElementById('update-form').dataset.user;
        const updatedProfile = {
            street: document.getElementById('update-street').value,
            city: document.getElementById('update-city').value,
            province: document.getElementById('update-province').value,
            postal: document.getElementById('update-postal').value,
            cardNum: document.getElementById('update-card-num').value,
            cvv: document.getElementById('update-cvv').value,
            expiry: document.getElementById('update-expiry').value
        };

        // Update the client data locally
        const clientIndex = clientsData.findIndex(client => client.userName === userName);
        if (clientIndex !== -1) {
            clientsData[clientIndex].profile = { ...clientsData[clientIndex].profile, ...updatedProfile };
            renderClients(clientsData);
        }

        // Remove the update row
        document.getElementById('update-row').remove();
    }

    // Handle Cancel Update
    function cancelUpdate() {
        // Remove the update row and show all rows
        document.getElementById('update-row').remove();
        document.querySelectorAll('#clients-data tr').forEach(row => {
            row.style.display = '';
        });
    }

    // Handle Delete Button Click
    function handleDeleteClick(e) {
        const userName = e.target.getAttribute('data-user');
        clientsData = clientsData.filter(client => client.userName !== userName);
        renderClients(clientsData);
    }
});
