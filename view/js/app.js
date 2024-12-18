// app.js

document.addEventListener('DOMContentLoaded', () => {
    // Simulated authentication status
    let isLoggedIn = false;
  
    // Handle user login status
    function updateNavbar() {
      const loginLink = document.querySelector('#login-link');
      const logoutLink = document.querySelector('#logout-link');
  
      if (isLoggedIn) {
        loginLink.style.display = 'none';
        logoutLink.style.display = 'inline';
      } else {
        loginLink.style.display = 'inline';
        logoutLink.style.display = 'none';
      }
    }
  
    // Simulate login/logout actions
    document.querySelector('#login-link')?.addEventListener('click', () => {
      isLoggedIn = true;
      alert("Logged in successfully!");
      updateNavbar();
    });
  
    document.querySelector('#logout-link')?.addEventListener('click', () => {
      isLoggedIn = false;
      alert("Logged out successfully!");
      updateNavbar();
    });
  
    // Initial navbar setup
    updateNavbar();
  });
  