document.addEventListener('DOMContentLoaded', () => {
    const registerForm = document.getElementById('register-form');
  
    registerForm.addEventListener('submit', (event) => {
      event.preventDefault(); // Prevent the form from submitting immediately
  
      const password = document.getElementById('password').value;
      const confirmPassword = document.getElementById('confirm-password').value;
  
      // Check if passwords match
      if (password !== confirmPassword) {
        alert('Passwords do not match! Please try again.');
        return; // Stop form submission
      }
  
      // If passwords match, send registration data
      const formData = new FormData(registerForm);
  
      console.log('Sending data to server:', Object.fromEntries(formData.entries()));
      fetch('controller/authController.php', {
        method: 'POST',
        body: formData,
      })
        .then(response => response.json())
        .then(data => {
          if (data.status === 'success') {
            alert('Registration successful! Redirecting to sign-in page.');
            window.location.href = 'signin.html';
          } else {
            alert(`Error: ${data.message}`);
          }
        })
        .catch(error => {
          console.error('Error:', error);
          alert('An unexpected error occurred.');
        });
    });
  });
  