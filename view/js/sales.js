document.addEventListener('DOMContentLoaded', function () {

    const isAdmin = sessionStorage.getItem('isAdmin');
    const username = sessionStorage.getItem('username'); // Fetching username from sessionStorage

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

    // Function to calculate total revenue and format it with commas
    function calculateTotalRevenue(salesData) {
        let totalRevenue = 0;
        
        // Calculate total revenue
        salesData.forEach(sale => {
            totalRevenue += parseFloat(sale.Payment.totalPrice);
        });
        
        // Format total revenue with commas and 2 decimal places
        const formattedRevenue = totalRevenue.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });

        // Update the total revenue display
        document.getElementById('total-revenue-amount').innerText = `$${formattedRevenue}`;
    }

    // Function to render sales data
    function renderSalesData(salesData) {
        const tableBody = document.getElementById('sales-data');
        tableBody.innerHTML = ''; // Clear previous rows
    
        salesData.forEach(sale => {
            sale.transactions.forEach(transaction => {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>${transaction.transId}</td>
                    <td>${transaction.userName}</td>
                    <td>${transaction.itemId}</td>
                    <td>$${parseFloat(transaction.itemPrice).toFixed(2)}</td>
                    <td>${transaction.quantity}</td>
                    <td>$${(parseFloat(transaction.itemPrice) * parseInt(transaction.quantity)).toFixed(2)}</td>
                    <td>${transaction.date}</td>
                `;
                tableBody.appendChild(row);
            });
        });
    }

    // Function to generate the charts
    function generateChart(salesData) {
        const itemCounts = {}; // Object to hold the quantity of each item
        const totalPrices = {}; // Object to hold the total price for each item
    
        // Loop through sales data and calculate quantities and total prices
        salesData.forEach(sale => {
            sale.transactions.forEach(transaction => {
                const itemId = transaction.itemId;
                const quantity = parseInt(transaction.quantity);
                const itemPrice = parseFloat(transaction.itemPrice);
                const totalPrice = itemPrice * quantity;
    
                // Accumulate quantity sold per item
                itemCounts[itemId] = (itemCounts[itemId] || 0) + quantity;
    
                // Accumulate total price per item
                totalPrices[itemId] = (totalPrices[itemId] || 0) + totalPrice;
            });
        });
    
        // Create the first chart (Number of Products Sold)
        const ctx1 = document.getElementById('sales-chart').getContext('2d');
        const chartData1 = {
            labels: Object.keys(itemCounts), // Item IDs or names (replace with actual item names if you have them)
            datasets: [{
                label: 'Number of Products Sold',
                data: Object.values(itemCounts),
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                borderColor: 'rgba(75, 192, 192, 1)',
                borderWidth: 1
            }]
        };
    
        new Chart(ctx1, {
            type: 'bar',
            data: chartData1,
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    
        // Create the second chart (Total Price per Item)
        const ctx2 = document.getElementById('total-price-chart').getContext('2d');
        const chartData2 = {
            labels: Object.keys(totalPrices), // Item IDs or names (replace with actual item names if you have them)
            datasets: [{
                label: 'Total Price of Products Sold',
                data: Object.values(totalPrices),
                backgroundColor: 'rgba(255, 159, 64, 0.2)',
                borderColor: 'rgba(255, 159, 64, 1)',
                borderWidth: 1
            }]
        };
    
        new Chart(ctx2, {
            type: 'bar',
            data: chartData2,
            options: {
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return '$' + value.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                            }
                        }
                    }
                }
            }
        });
    }

    // Sorting functionality
    document.getElementById('sort-by').addEventListener('change', function() {
        const sortBy = this.value;
    
        let sortedSalesData = [...salesData];
        if (sortBy === 'date') {
            sortedSalesData.sort((a, b) => new Date(a.Payment.date) - new Date(b.Payment.date));
        } else if (sortBy === 'userName') {
            sortedSalesData.sort((a, b) => a.transactions[0].userName.localeCompare(b.transactions[0].userName));
        } else if (sortBy === 'category') {
            sortedSalesData.sort((a, b) => a.transactions[0].itemId.localeCompare(b.transactions[0].itemId));
        }
    
        renderSalesData(sortedSalesData);
    });

    // Fetching sales data from the API
    fetch(`http://localhost/eecs4413/controller/authController/adminGetTransactions?admin=${username}`)
        .then(response => response.json())
        .then(data => {
            const salesData = data.sales; // Assuming the API returns an object with a "sales" array
            calculateTotalRevenue(salesData);
            renderSalesData(salesData);
            generateChart(salesData);
        })
        .catch(error => {
            console.error('Error fetching sales data:', error);
        });

});
