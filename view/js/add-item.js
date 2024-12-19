document.addEventListener('DOMContentLoaded', () => {
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
  });
  