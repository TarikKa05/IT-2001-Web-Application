function setTheme(theme) {
    // Remove all theme classes from the body
    document.body.classList.remove('light', 'dark', 'blue');
    // Add the selected theme class to the body
    document.body.classList.add(theme);
    // Save the user's choice in localStorage
    localStorage.setItem('selectedTheme', theme);
  }
  
  // Load the saved theme when the page loads
  document.addEventListener('DOMContentLoaded', () => {
    const savedTheme = localStorage.getItem('selectedTheme') || 'light';
    setTheme(savedTheme);
  });