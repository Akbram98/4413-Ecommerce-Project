// checkout.js

document.addEventListener('DOMContentLoaded', () => {
    const checkoutForm = document.querySelector('form');
  
    checkoutForm.addEventListener('submit', (e) => {
      e.preventDefault();
  
      // Simulated payment process
      const isPaymentSuccessful = Math.random() > 0.33; // 67% chance of success
  
      if (isPaymentSuccessful) {
        alert("Payment successful! Order placed.");
        // Clear cart and redirect (example)
        window.location.href = "index.html";
      } else {
        alert("Payment failed. Please try again.");
      }
    });
  });
  