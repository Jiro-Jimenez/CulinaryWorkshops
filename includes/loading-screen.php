
    <?php
// loading-screen.php - Place this in the includes directory
?>
<div id="loading-screen" class="loading-screen">
  <div class="loading-content">
    <div class="loading-animation">
      <div class="utensils-container">
        <i class="fas fa-utensils"></i>
      </div>
      <div class="clock-container">
        <i class="far fa-clock"></i>
      </div>
    </div>
    <h1>Culinary Workshop</h1>
    <p>Preparing your culinary experience...</p>
    <div class="loading-bar-container">
      <div class="loading-bar"></div>
    </div>
  </div>
</div>

<style>
  .loading-screen {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: white;
    z-index: 9999;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    transition: opacity 0.5s ease;
  }
  
  .loading-content {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    transform: scale(0.9);
    opacity: 0;
    animation: fadeIn 0.5s forwards;
  }
  
  @keyframes fadeIn {
    to {
      transform: scale(1);
      opacity: 1;
    }
  }
  
  .loading-animation {
    position: relative;
    width: 100px;
    height: 100px;
    margin-bottom: 2rem;
  }
  
  .utensils-container {
    position: absolute;
    inset: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    animation: spin 3s linear infinite;
  }
  
  .utensils-container i {
    font-size: 3rem;
    color: #f59e0b;
  }
  
  .clock-container {
    position: absolute;
    inset: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    animation: spin-reverse 3s linear infinite;
    animation-delay: 0.2s;
  }
  
  .clock-container i {
    font-size: 4rem;
    color: #b45309;
    opacity: 0.3;
  }
  
  .chef-hat-container {
    position: relative;
    z-index: 10;
    display: flex;
    align-items: center;
    justify-content: center;
    height: 100%;
  }
  
  .chef-hat-container i {
    font-size: 5rem;
    color: #d97706;
  }
  
  @keyframes spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
  }
  
  @keyframes spin-reverse {
    from { transform: rotate(0deg); }
    to { transform: rotate(-360deg); }
  }
  
  .loading-screen h1 {
    font-size: 1.875rem;
    font-weight: bold;
    color: #92400e;
    margin-bottom: 0.5rem;
  }
  
  .loading-screen p {
    color: #d97706;
    margin-bottom: 1.5rem;
  }
  
  .loading-bar-container {
    width: 16rem;
    height: 0.5rem;
    background-color: #e5e7eb;
    border-radius: 9999px;
    overflow: hidden;
  }
  
  .loading-bar {
    height: 100%;
    background-color: #f59e0b;
    border-radius: 9999px;
    width: 0%;
    animation: loading 2s ease-in-out forwards;
  }
  
  @keyframes loading {
    to { width: 100%; }
  }
  
  .loading-screen.hidden {
    opacity: 0;
    pointer-events: none;
    /* Important: Add this to completely remove it from the page flow when hidden */
    display: none;
  }
  
  /* Fix for the tiny scrollbar issue */
  html, body {
    overflow: auto !important;
    height: auto !important;
  }
</style>

<script>
  // Wait for DOM to be ready
  document.addEventListener('DOMContentLoaded', function() {
    const loadingScreen = document.getElementById('loading-screen');
    
    if (!loadingScreen) {
      console.error('Loading screen element not found');
      return;
    }
    
    // Function to hide the loading screen
    function hideLoadingScreen() {
      loadingScreen.classList.add('hidden');
      // Re-enable scrolling
      document.body.style.overflow = 'auto';
    }
    
    // Hide loading screen after a delay
    setTimeout(function() {
      hideLoadingScreen();
    }, 2000);
    
    // Show loading screen when clicking on links
    document.addEventListener('click', function(e) {
      const target = e.target.closest('a');
      if (target && 
          target.getAttribute('href') && 
          !target.getAttribute('href').startsWith('#') && 
          !target.getAttribute('target') &&
          !target.getAttribute('href').includes('javascript:')) {
        
        // Only show loading screen for actual navigation
        loadingScreen.classList.remove('hidden');
        // This ensures the page actually navigates
        setTimeout(function() {
          window.location.href = target.href;
        }, 100);
      }
    });
    
    // For form submissions
    const forms = document.querySelectorAll('form');
    forms.forEach(function(form) {
      form.addEventListener('submit', function() {
        loadingScreen.classList.remove('hidden');
      });
    });
  });
</script>
