export default function PHPImplementationGuide() {
  return (
    <div className="p-6 max-w-4xl mx-auto">
      <h1 className="text-2xl font-bold mb-4">PHP Implementation Guide</h1>

      <div className="space-y-6">
        <section>
          <h2 className="text-xl font-semibold mb-2">Step 1: Create the loading-screen.php file</h2>
          <pre className="bg-gray-100 p-4 rounded-md overflow-auto">
            {`<?php
// loading-screen.php
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
      <div class="chef-hat-container">
        <i class="fas fa-hat-chef"></i>
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
  }
</style>

<script>
  document.addEventListener('DOMContentLoaded', function() {
    const loadingScreen = document.getElementById('loading-screen');
    
    // Hide loading screen when page is loaded
    window.addEventListener('load', function() {
      setTimeout(function() {
        loadingScreen.classList.add('hidden');
      }, 2000);
    });
    
    // Show loading screen when navigating away
    document.addEventListener('click', function(e) {
      const target = e.target.closest('a');
      if (target && !target.getAttribute('href').startsWith('#') && !target.getAttribute('target')) {
        loadingScreen.classList.remove('hidden');
      }
    });
    
    // For form submissions
    document.addEventListener('submit', function() {
      loadingScreen.classList.remove('hidden');
    });
  });
</script>`}
          </pre>
        </section>

        <section>
          <h2 className="text-xl font-semibold mb-2">Step 2: Include in header.php</h2>
          <p className="mb-2">
            Add this at the beginning of your header.php file, right after the opening &lt;body&gt; tag:
          </p>
          <pre className="bg-gray-100 p-4 rounded-md overflow-auto">
            {`<?php
// header.php
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Your existing head content -->
</head>
<body>
    <?php include 'includes/loading-screen.php'; ?>
    
    <!-- Rest of your header content -->`}
          </pre>
        </section>

        <section>
          <h2 className="text-xl font-semibold mb-2">Step 3: Ensure Font Awesome is included</h2>
          <p className="mb-2">
            The loading screen uses Font Awesome icons. Make sure it's included in your head section:
          </p>
          <pre className="bg-gray-100 p-4 rounded-md overflow-auto">
            {`<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />`}
          </pre>
        </section>

        <section>
          <h2 className="text-xl font-semibold mb-2">Step 4: Test on all pages</h2>
          <p>
            Since the loading screen is included in the header.php file, it will automatically appear on all pages that
            include the header. Test navigation between different pages to ensure the loading screen appears correctly
            during transitions.
          </p>
        </section>
      </div>
    </div>
  )
}
