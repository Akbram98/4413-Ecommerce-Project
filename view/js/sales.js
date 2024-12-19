document.addEventListener('DOMContentLoaded', function () {

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

    const salesData = [
      {
        "Payment": {
          "transId": "1000086",
          "cardNum": "1234 5678 1234 5",
          "cvv": "123",
          "expiry": "12/25",
          "totalPrice": "100548.12",
          "processed": "1",
          "date": "2024-12-10 17:41:15"
        },
        "transactions": [
          {
            "transId": "1000086",
            "itemId": "10018",
            "userName": "user01",
            "quantity": "142",
            "date": "2024-12-10 17:41:15",
            "itemPrice": "699.99"
          },
          {
            "transId": "1000086",
            "itemId": "10027",
            "userName": "user01",
            "quantity": "46",
            "date": "2024-12-10 17:41:15",
            "itemPrice": "24.99"
          }
        ]
      },
      {
        "Payment": {
          "transId": "1000087",
          "cardNum": "3456 7890 3456 7",
          "cvv": "345",
          "expiry": "10/23",
          "totalPrice": "103620.94",
          "processed": "1",
          "date": "2024-12-10 17:41:15"
        },
        "transactions": [
          {
            "transId": "1000087",
            "itemId": "10007",
            "userName": "user03",
            "quantity": "105",
            "date": "2024-12-10 17:41:15",
            "itemPrice": "25"
          },
          {
            "transId": "1000087",
            "itemId": "10010",
            "userName": "user03",
            "quantity": "122",
            "date": "2024-12-10 17:41:15",
            "itemPrice": "199.99"
          },
          {
            "transId": "1000087",
            "itemId": "10019",
            "userName": "user03",
            "quantity": "117",
            "date": "2024-12-10 17:41:15",
            "itemPrice": "499.99"
          },
          {
            "transId": "1000087",
            "itemId": "10023",
            "userName": "user03",
            "quantity": "79",
            "date": "2024-12-10 17:41:15",
            "itemPrice": "139.99"
          },
          {
            "transId": "1000087",
            "itemId": "10024",
            "userName": "user03",
            "quantity": "88",
            "date": "2024-12-10 17:41:15",
            "itemPrice": "79.99"
          }
        ]
      }
    ];
  
    // Function to calculate total revenue and format it with commas
function calculateTotalRevenue() {
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
    function renderSalesData(sortedSalesData) {
      const tableBody = document.getElementById('sales-data');
      tableBody.innerHTML = ''; // Clear previous rows
  
      sortedSalesData.forEach(sale => {
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
function generateChart() {
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
        },
        plugins: {
          tooltip: {
            callbacks: {
              title: function(tooltipItem) {
                const itemId = tooltipItem[0].label;
                return `Item ID: ${itemId}`;
              },
              label: function(tooltipItem) {
                const itemId = tooltipItem.label;
                const itemQuantity = itemCounts[itemId];
                const itemTotalPrice = totalPrices[itemId];
                return `Quantity Sold: ${itemQuantity}, Total Price: $${itemTotalPrice.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
              }
            }
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
        backgroundColor: 'rgba(255, 159, 64, 0.2)',  // Different color
        borderColor: 'rgba(255, 159, 64, 1)',        // Different color
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
        },
        plugins: {
          tooltip: {
            callbacks: {
              title: function(tooltipItem) {
                const itemId = tooltipItem[0].label;
                return `Item ID: ${itemId}`;
              },
              label: function(tooltipItem) {
                const itemId = tooltipItem.label;
                const itemTotalPrice = totalPrices[itemId];
                return `Total Price: $${itemTotalPrice.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
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
  
    // Initialize page
    calculateTotalRevenue();
    renderSalesData(salesData);
    generateChart();
  });
  