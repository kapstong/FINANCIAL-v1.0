<?php
// Script to add loading effects to all admin modules

$modules = [
    'admin/General Ledger.php',
    'admin/Accounts Receivable.php',
    'admin/Collections.php',
    'admin/Accounts Payable.php',
    'admin/Disbursement.php',
    'admin/Budget Management.php',
    'admin/Reports.php',
    'admin/settings.php',
    'admin/profile.php'
];

$loadingScreen = '    <!-- Enhanced Loading Screen -->
    <div id="globalLoader" class="fixed inset-0 z-[100] flex items-center justify-center bg-gradient-to-br from-slate-900/95 to-slate-800/95 backdrop-blur-sm">
      <div class="flex flex-col items-center gap-6">
        <!-- Main Spinner -->
        <div class="relative">
          <!-- Outer Ring -->
          <div class="w-20 h-20 border-4 border-slate-600/30 rounded-full animate-pulse"></div>
          <!-- Rotating Ring -->
          <div class="absolute inset-0 w-20 h-20 border-4 border-transparent border-t-blue-500 rounded-full animate-spin"></div>
          <!-- Inner Ring -->
          <div class="absolute inset-2 w-16 h-16 border-4 border-transparent border-t-indigo-400 rounded-full animate-spin" style="animation-direction: reverse; animation-duration: 1.5s;"></div>
          <!-- Center Dot -->
          <div class="absolute inset-6 w-8 h-8 bg-gradient-to-r from-blue-500 to-indigo-500 rounded-full animate-pulse"></div>
        </div>
        
        <!-- Loading Text -->
        <div class="text-center">
          <h2 class="text-2xl font-bold text-white mb-2">Loading Module</h2>
          <p class="text-slate-300 text-sm">Preparing your data...</p>
        </div>
        
        <!-- Progress Bar -->
        <div class="w-64 bg-slate-700 rounded-full h-2">
          <div id="loadingProgress" class="bg-gradient-to-r from-blue-500 to-indigo-500 h-2 rounded-full transition-all duration-500 ease-out" style="width: 0%"></div>
        </div>
        
        <!-- Loading Dots -->
        <div class="flex space-x-2">
          <div class="w-2 h-2 bg-blue-400 rounded-full animate-bounce" style="animation-delay: 0ms;"></div>
          <div class="w-2 h-2 bg-indigo-400 rounded-full animate-bounce" style="animation-delay: 150ms;"></div>
          <div class="w-2 h-2 bg-purple-400 rounded-full animate-bounce" style="animation-delay: 300ms;"></div>
        </div>
      </div>
    </div>';

$loadingCSS = '    /* Loading animations */
    @keyframes shimmer {
      0% { background-position: -200px 0; }
      100% { background-position: calc(200px + 100%) 0; }
    }
    
    .shimmer {
      background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
      background-size: 200px 100%;
      animation: shimmer 1.5s infinite;
    }
    
    html.dark .shimmer {
      background: linear-gradient(90deg, #374151 25%, #4b5563 50%, #374151 75%);
      background-size: 200px 100%;
    }
    
    /* Card hover effects */
    .module-card {
      transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    
    .module-card:hover {
      transform: translateY(-2px);
      box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 5px 10px -5px rgba(0, 0, 0, 0.04);
    }
    
    /* Loading screen transitions */
    #globalLoader {
      transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    
    /* Button loading states */
    .btn-loading {
      position: relative;
      pointer-events: none;
    }
    
    .btn-loading::after {
      content: \'\';
      position: absolute;
      width: 16px;
      height: 16px;
      top: 50%;
      left: 50%;
      margin-left: -8px;
      margin-top: -8px;
      border: 2px solid transparent;
      border-top: 2px solid currentColor;
      border-radius: 50%;
      animation: spin 1s linear infinite;
    }
    
    @keyframes spin {
      0% { transform: rotate(0deg); }
      100% { transform: rotate(360deg); }
    }';

$loadingJS = '      // Initialize loading screen
      function initLoadingScreen() {
        const loader = $(\'#globalLoader\');
        const progressBar = $(\'#loadingProgress\');
        
        // Simulate loading progress
        let progress = 0;
        const progressInterval = setInterval(() => {
          progress += Math.random() * 15 + 5; // Random progress between 5-20
          if (progress >= 100) {
            progress = 100;
            clearInterval(progressInterval);
            
            // Hide loader with fade out effect
            setTimeout(() => {
              loader.style.opacity = \'0\';
              loader.style.transform = \'scale(0.95)\';
              setTimeout(() => {
                loader.style.display = \'none\';
                // Animate content in
                animateContentIn();
              }, 300);
            }, 200);
          }
          progressBar.style.width = progress + \'%\';
        }, 100);
      }
      
      // Animate content in
      function animateContentIn() {
        const cards = document.querySelectorAll(\'.module-card, .card\');
        cards.forEach((card, index) => {
          setTimeout(() => {
            card.style.opacity = \'0\';
            card.style.transform = \'translateY(20px)\';
            card.style.transition = \'all 0.5s ease-out\';
            
            setTimeout(() => {
              card.style.opacity = \'1\';
              card.style.transform = \'translateY(0)\';
            }, 100);
          }, index * 100);
        });
      }
      
      // Add loading state to buttons
      function initButtonLoading() {
        const buttons = document.querySelectorAll(\'button[onclick*="add"], button[onclick*="delete"], button[onclick*="edit"], button[onclick*="save"], button[onclick*="submit"]\');
        buttons.forEach(button => {
          button.addEventListener(\'click\', function() {
            if (!this.classList.contains(\'btn-loading\')) {
              const originalText = this.textContent;
              this.classList.add(\'btn-loading\');
              this.textContent = \'Processing...\';
              
              // Reset after 2 seconds (or you can reset after actual operation)
              setTimeout(() => {
                this.classList.remove(\'btn-loading\');
                this.textContent = originalText;
              }, 2000);
            }
          });
        });
      }';

foreach ($modules as $module) {
    if (file_exists($module)) {
        echo "Processing: $module\n";
        
        $content = file_get_contents($module);
        
        // Add loading screen after body tag
        $content = preg_replace('/<body[^>]*>/', '$0' . "\n" . $loadingScreen, $content);
        
        // Add loading CSS before closing style tag
        $content = preg_replace('/<\/style>/', $loadingCSS . "\n  </style>", $content);
        
        // Add loading JavaScript before DOMContentLoaded
        $content = preg_replace('/document\.addEventListener\(\'DOMContentLoaded\'/', $loadingJS . "\n      " . '$0', $content);
        
        // Update DOMContentLoaded to include loading functions
        $content = preg_replace('/document\.addEventListener\(\'DOMContentLoaded\', \(\) => \{/', '$0' . "\n        // Start loading screen\n        initLoadingScreen();", $content);
        $content = preg_replace('/initDarkMode\(\);/', '$0' . "\n        initButtonLoading();", $content);
        
        file_put_contents($module, $content);
        echo "Updated: $module\n";
    } else {
        echo "File not found: $module\n";
    }
}

echo "Loading effects added to all modules!\n";
?>
