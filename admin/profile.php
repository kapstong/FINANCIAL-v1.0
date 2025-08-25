<?php
require_once '../includes/auth.php';
$auth = new Auth();
$auth->requireAuth();

$user = $auth->getCurrentUser();

// Handle form submissions
$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Handle profile image upload
    if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = '../uploads/';
        $fileExtension = strtolower(pathinfo($_FILES['profile_image']['name'], PATHINFO_EXTENSION));
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];
        
        if (in_array($fileExtension, $allowedExtensions)) {
            $fileName = 'profile_' . $user['id'] . '_' . time() . '.' . $fileExtension;
            $uploadPath = $uploadDir . $fileName;
            
            if (move_uploaded_file($_FILES['profile_image']['tmp_name'], $uploadPath)) {
                $result = $auth->updateProfileImage($user['id'], $fileName);
                if ($result['success']) {
                    $message = 'Profile image updated successfully!';
                    $messageType = 'success';
                    // Refresh user data
                    $user = $auth->getCurrentUser();
                } else {
                    $message = $result['message'];
                    $messageType = 'error';
                }
            } else {
                $message = 'Failed to upload image';
                $messageType = 'error';
            }
        } else {
            $message = 'Invalid file type. Only JPG, PNG, and GIF are allowed.';
            $messageType = 'error';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Profile</title>
  <link rel="icon" type="image/png" href="logo2.png">
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.12/cropper.min.css">
  <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.12/cropper.min.js"></script>

  <style>
         :root{
       --brand:#0f1c49; --brand-600:#0c173c; --brand-100:#e8ecf9;
       --ink:#000; --muted:#000; --ring:0 0 0 3px rgba(15,28,73,.15);
       --card:#ffffff; --border:#eef2f7;
       --card-bg: rgba(255,255,255,.95);
       --card-border: rgba(226,232,240,.9);
     }
    
         /* Dark mode variables */
     html.dark {
       --ink: #e5e7eb;
       --muted: #9ca3af;
       --card: rgba(17,24,39,.92);
       --border: rgba(71,85,105,.55);
       --card-bg: rgba(17,24,39,.92);
       --card-border: rgba(71,85,105,.55);
     }
    .navbar{ background:var(--brand); color:#fff; height: 3.5rem; }
    .navbar *{ color:#fff !important; }
    .nav-input{
      background:rgba(255,255,255,.18); border:1px solid rgba(255,255,255,.35);
      padding:.35rem .6rem; border-radius:.6rem; color:#fff !important;
    }
    .nav-input::placeholder{ color:#f1f5f9; }
    
    /* Enhanced Navigation Bar Styles */
    .navbar {
      background: linear-gradient(135deg, var(--brand) 0%, var(--brand-600) 100%);
      box-shadow: 0 4px 20px rgba(15, 28, 73, 0.15);
      backdrop-filter: blur(10px);
      border-bottom: 1px solid rgba(255, 255, 255, 0.1);
      height: 3.5rem !important;
    }
    
    .navbar .nav-input {
      background: rgba(255, 255, 255, 0.15);
      border: 1px solid rgba(255, 255, 255, 0.25);
      backdrop-filter: blur(10px);
      transition: all 0.3s ease;
    }
    
    .navbar .nav-input:focus {
      background: rgba(255, 255, 255, 0.25);
      border-color: rgba(255, 255, 255, 0.4);
      box-shadow: 0 0 0 3px rgba(255, 255, 255, 0.1);
    }
    
    .navbar .nav-input::placeholder {
      color: rgba(255, 255, 255, 0.7);
    }
    
    /* Enhanced Profile Button */
    #profileBtn {
      background: rgba(255, 255, 255, 0.1);
      border: 1px solid rgba(255, 255, 255, 0.2);
      backdrop-filter: blur(10px);
      transition: all 0.3s ease;
    }
    
    #profileBtn:hover {
      background: rgba(255, 255, 255, 0.2);
      border-color: rgba(255, 255, 255, 0.3);
      transform: translateY(-1px);
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    }
    
    /* Enhanced Dark Mode Toggle */
    #headerDarkModeToggle {
      background: rgba(255, 255, 255, 0.1);
      border: 1px solid rgba(255, 255, 255, 0.2);
      backdrop-filter: blur(10px);
      transition: all 0.3s ease;
    }
    
    #headerDarkModeToggle:hover {
      background: rgba(255, 255, 255, 0.2);
      border-color: rgba(255, 255, 255, 0.3);
      transform: translateY(-1px);
    }
    
    /* Enhanced Clock Wrap */
    #clockWrap {
      background: rgba(255, 255, 255, 0.1);
      border: 1px solid rgba(255, 255, 255, 0.2);
      border-radius: 8px;
      padding: 4px 8px;
      backdrop-filter: blur(10px);
    }
    
    #liveTime {
      background: rgba(255, 255, 255, 0.15);
      border: 1px solid rgba(255, 255, 255, 0.25);
      transition: all 0.3s ease;
    }
    
    #liveTime:hover {
      background: rgba(255, 255, 255, 0.25);
      border-color: rgba(255, 255, 255, 0.35);
    }
    
    /* Enhanced Brand Logo */
    .navbar a[href="dashboard.php"] {
      transition: all 0.3s ease;
    }
    
    .navbar a[href="dashboard.php"]:hover {
      transform: scale(1.05);
    }
    
    .navbar a[href="dashboard.php"] span {
      background: linear-gradient(135deg, #ffffff 0%, #e2e8f0 100%);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      background-clip: text;
      text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }
    
    /* Enhanced Search Input */
    .navbar .nav-input {
      font-weight: 500;
      letter-spacing: 0.025em;
    }
    
    /* Smooth Transitions for All Interactive Elements */
    .navbar button,
    .navbar input,
    .navbar a {
      transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    
    /* Enhanced Mobile Responsiveness */
    @media (max-width: 768px) {
      .navbar .nav-input {
        width: 200px;
      }
      
      #clockWrap {
        display: none;
      }
    }
    
    /* Dark Mode Enhanced Styles */
    html.dark .navbar {
      background: linear-gradient(135deg, rgba(15, 28, 73, 0.95) 0%, rgba(12, 23, 60, 0.95) 100%);
      border-bottom-color: rgba(255, 255, 255, 0.1);
    }
         body{ background:#fff; color: var(--ink); }
     html.dark body{ 
       background: linear-gradient(140deg, rgba(7,12,38,1) 50%, rgba(11,21,56,1) 50%);
       color: var(--ink);
     }
     
     .bg-soft{
       background:
         radial-gradient(70% 70% at 0% 0%, var(--brand-100) 0%, transparent 60%),
         radial-gradient(60% 60% at 100% 0%, #eef2ff 0%, transparent 55%),
         linear-gradient(#fff,#fff);
     }
     html.dark .bg-soft{
       background:
         radial-gradient(70% 60% at 8% 10%, rgba(212,175,55,.08) 0, transparent 60%),
         radial-gradient(40% 40% at 100% 0%, rgba(212,175,55,.12) 0, transparent 40%),
         linear-gradient(140deg, rgba(7,12,38,1) 50%, rgba(11,21,56,1) 50%);
     }
    
         .card{ background:var(--card); border-radius:14px; border:1px solid var(--border); box-shadow:0 6px 18px rgba(2,6,23,.04) }
     html.dark .card{ box-shadow:0 16px 48px rgba(0,0,0,.5); }
    .btn{ display:inline-flex; align-items:center; gap:.5rem; padding:.55rem .95rem; border-radius:.65rem; font-weight:600 }
    .btn-brand{ background:var(--brand); color:#fff } .btn-brand:hover{ background:var(--brand-600) }
    .btn-soft{ background:#fff; border:1px solid var(--border); color:var(--ink) } .btn-soft:hover{ background:#f8fafc }
    html.dark .btn-soft{ background:var(--card); border-color:var(--border); color:var(--ink); }
    html.dark .btn-soft:hover{ background:rgba(31,41,55,.92); }
         .sidebar-transition{ transition:transform .28s ease }
          .overlay{ display:none } .overlay.active{ display:block; position:fixed; inset:0; background:rgba(0,0,0,.35); z-index:40 }
    .sidebar-item{ display:flex; align-items:center; gap:.6rem; width:100%; padding:.5rem .75rem; border-radius:.6rem; color:var(--ink) }
    .sidebar-item:hover{ background:#f8fafc }
    html.dark .sidebar-item:hover{ background:rgba(31,41,55,.92); }
    .sidebar-item:hover{ background:#f8fafc }
    .sidebar-item.active{ background:rgba(15,28,73,.06); color:var(--brand); font-weight:700 }
    .tab-pill{ padding:.4rem .8rem; border-radius:9999px; border:1px solid var(--border); font-weight:700; font-size:.9rem; color:var(--ink) }
    .tab-pill.active{ background:var(--brand); color:#fff; border-color:var(--brand) }
    .toast-card{ background:#fff; border:1px solid var(--border); border-radius:.75rem; padding:.6rem .9rem; box-shadow:0 10px 30px rgba(0,0,0,.08) }
    .form-input{ width:100%; padding:.5rem .75rem; border:1px solid #d1d5db; border-radius:.5rem; outline:none; transition:border-color .2s }
    .form-input:focus{ border-color:var(--brand); box-shadow:0 0 0 3px rgba(15,28,73,.1) }
    .stat-card{ background:linear-gradient(135deg, var(--brand) 0%, var(--brand-600) 100%); color:#fff; padding:1.5rem; border-radius:1rem }
    .alert{ padding:.75rem 1rem; border-radius:.5rem; margin-bottom:1rem }
    .alert-success{ background:#d1fae5; border:1px solid #a7f3d0; color:#065f46 }
    .alert-error{ background:#fee2e2; border:1px solid #fecaca; color:#991b1b }
    
         /* Profile image styling */
     .profile-image-container{ position:relative; display:inline-block }
     .profile-image-container img{ 
       width:80px; height:80px; 
       border-radius:50%; 
       object-fit:cover; 
       object-position:center;
       border:2px solid #e5e7eb;
       transition:all 0.2s ease;
     }
     .profile-image-container img:hover{ 
       transform:scale(1.05); 
       box-shadow:0 4px 12px rgba(0,0,0,0.15);
     }
     
     /* Modal styling */
     .modal-overlay {
       backdrop-filter: blur(4px);
     }
     
     /* Dark mode specific styles */
     html.dark .card{ box-shadow:0 16px 48px rgba(0,0,0,.5); }
     html.dark .btn-soft{ background:rgba(17,24,39,.92); border-color:rgba(71,85,105,.55); color:var(--ink); }
     html.dark .btn-soft:hover{ background:rgba(31,41,55,.92); }
     html.dark .sidebar-item:hover{ background:rgba(31,41,55,.92); }
     html.dark .sidebar-item{ color:var(--ink); }
     html.dark .tab-pill{ color:var(--ink); }
     html.dark .form-input{ background:#0b1220; border-color:#243041; color:var(--ink); }
     html.dark .alert-success{ background:#3f1b1b; border-color:#7f1d1d; color:#fecaca }
     html.dark .alert-error{ background:#1e1b4b; border-color:#3730a3; color:#c7d2fe }
     html.dark .profile-image-container img:hover{ box-shadow:0 4px 12px rgba(255,255,255,0.15); }
     html.dark .modal-overlay{ backdrop-filter: blur(8px); }
     
     /* Dark mode context bar */
     html.dark #contextBar {
       background: rgba(17,24,39,.8);
       border-color: rgba(71,85,105,.55);
     }
     
     /* Dark mode for new dashboard elements */
     html.dark .text-slate-800 { color: var(--ink); }
     html.dark .text-slate-600 { color: var(--muted); }
     html.dark .text-slate-500 { color: var(--muted); }
     html.dark .border-slate-200 { border-color: var(--border); }
     html.dark .hover\:bg-slate-50:hover { background-color: rgba(31,41,55,.92); }
     html.dark .bg-slate-50 { background-color: rgba(31,41,55,.92); }
     html.dark .border-slate-100 { border-color: var(--border); }
     
         /* Dark mode sidebar */
    html.dark #sidebar {
      background: var(--card);
      border-color: var(--border);
    }
    
    /* Enhanced Sidebar */
    #sidebar {
      background: rgba(255, 255, 255, 0.95);
      backdrop-filter: blur(20px);
      border-right: 1px solid rgba(0, 0, 0, 0.1);
      box-shadow: 4px 0 20px rgba(0, 0, 0, 0.05);
    }
    
    .sidebar-item {
      border-radius: 12px;
      margin: 2px 8px;
      transition: all 0.3s ease;
      border: 1px solid transparent;
    }
    
    .sidebar-item:hover {
      background: rgba(15, 28, 73, 0.08);
      border-color: rgba(15, 28, 73, 0.1);
      transform: translateX(4px);
    }
    
    .sidebar-item.active {
      background: linear-gradient(135deg, rgba(15, 28, 73, 0.15) 0%, rgba(15, 28, 73, 0.1) 100%);
      border-color: rgba(15, 28, 73, 0.2);
      box-shadow: 0 2px 8px rgba(15, 28, 73, 0.1);
    }
    
    /* Dark Mode Enhanced Sidebar */
    html.dark #sidebar {
      background: rgba(17, 24, 39, 0.95);
      border-right-color: rgba(255, 255, 255, 0.1);
      box-shadow: 4px 0 20px rgba(0, 0, 0, 0.3);
    }
    
    html.dark .sidebar-item {
      color: var(--ink);
    }
    
    html.dark .sidebar-item:hover {
      background: rgba(255, 255, 255, 0.08);
      border-color: rgba(255, 255, 255, 0.15);
    }
    
    html.dark .sidebar-item.active {
      background: linear-gradient(135deg, rgba(255, 255, 255, 0.15) 0%, rgba(255, 255, 255, 0.1) 100%);
      border-color: rgba(255, 255, 255, 0.2);
      color: var(--brand-100);
    }
     
     /* Dark mode text colors */
     html.dark .text-slate-800 {
       color: var(--ink);
     }
     
     html.dark .text-gray-500 {
       color: var(--muted);
     }
     
     html.dark .text-gray-600 {
       color: var(--muted);
     }
     
     html.dark .text-gray-900 {
       color: var(--ink);
     }
     
     /* Loading animations */
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
     .profile-card {
       transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
     }
     
     .profile-card:hover {
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
       content: '';
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
     }
    /* Dark mode for notification panel */
    html.dark #notificationPanel {
      background: rgba(17,24,39,.95);
      border-color: rgba(71,85,105,.55);
    }
    
    html.dark #notificationPanel .bg-gray-50 {
      background: rgba(31,41,55,.95);
      border-color: rgba(71,85,105,.55);
    }
    
    html.dark #notificationPanel .text-gray-900 {
      color: #e5e7eb;
    }
    
    html.dark #notificationPanel .text-gray-600 {
      color: #9ca3af;
    }
    
    html.dark #notificationPanel .text-gray-400 {
      color: #6b7280;
    }
    
    html.dark #notificationPanel .border-gray-100 {
      border-color: rgba(71,85,105,.3);
    }
    
    html.dark #notificationPanel .hover\:bg-gray-50:hover {
      background: rgba(55,65,81,.95);
    }
   </style>
</head>
  <body class="min-h-screen text-[15px] text-[var(--ink)] bg-soft">

    <!-- Enhanced Loading Screen -->
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
          <h2 class="text-2xl font-bold text-white mb-2">Loading Profile</h2>
          <p class="text-slate-300 text-sm">Preparing your profile data...</p>
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
    </div>



  <!-- TOAST -->
  <div id="toast" class="fixed top-4 right-4 z-[120] hidden"></div>

  <!-- HEADER -->
  <header class="sticky top-0 z-50 border-b border-[var(--ring)] navbar backdrop-blur">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 h-14 flex items-center gap-3">
             <button id="openSidebar" class="md:hidden p-2 rounded hover:bg-white/20 transition-all duration-300 hover:scale-105" aria-label="Open menu">
         <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h10"/></svg>
       </button>

      <!-- Brand -->
      <a href="dashboard.php" class="flex items-center gap-3">
        <img src="logo2.png" alt="ATIÉRA" class="h-8 w-auto sm:h-10" draggable="false">
        <span class="font-extrabold tracking-wide text-lg">ATIERA</span>
      </a>

      <!-- Search (global) -->
      <div class="ml-auto flex items-center gap-2">
        <input id="searchInput" placeholder="Search modules, cards, rows…" class="nav-input text-sm w-72 outline-none"/>
      </div>

            <!-- Live date/time -->
      <div id="clockWrap" class="hidden md:flex items-center gap-2 mr-1 select-none">
        <span id="liveDate" class="text-sm"></span>
         <button id="liveTime" class="text-sm font-mono px-2 py-0.5 rounded border border-white/30 bg-white/10"
                title="Click to toggle 12/24-hour time"></button>
      </div>

      <!-- Dark Mode Toggle -->
      <button id="headerDarkModeToggle" class="p-2 rounded hover:bg-white/10 text-white" title="Toggle dark mode">
        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
        </svg>
      </button>
      <!-- Notification Bell -->
      <div class="relative">
        <button id="notificationBell" class="p-2 rounded hover:bg-white/10 text-white relative" title="Notifications" onclick="toggleNotificationPanel()">
          <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-width="2" d="M15 17h5l-5 5v-5z"/>
          </svg>
          <!-- Notification Badge -->
          <span id="notificationBadge" class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center hidden">0</span>
        </button>
        
        <!-- Notification Panel -->
        <div id="notificationPanel" class="hidden absolute right-0 mt-2 w-80 bg-white rounded-lg shadow-xl border border-gray-200 overflow-hidden z-50">
          <div class="px-4 py-3 border-b border-gray-200 bg-gray-50">
            <div class="flex items-center justify-between">
              <h3 class="text-sm font-semibold text-gray-900">Notifications</h3>
              <button onclick="clearAllNotifications()" class="text-xs text-blue-600 hover:text-blue-800">Clear All</button>
            </div>
          </div>
          <div id="notificationList" class="max-h-64 overflow-y-auto">
            <div class="p-4 text-center text-gray-500 text-sm">
              No new notifications
            </div>
          </div>
        </div>
      </div>

      <!-- Profile -->
      <div class="relative">
        <button id="profileBtn" class="p-2 rounded hover:bg-white/10 flex items-center gap-2" title="Account">
          <img src="../uploads/<?php echo htmlspecialchars($user['profile_image'] ?? 'admindefault.png'); ?>" 
               alt="Profile" class="w-8 h-8 rounded-full object-cover border border-white/30">
        </button>
        <div id="profileMenu" class="hidden absolute right-0 mt-2 w-56 bg-black rounded-lg shadow-xl border border-[var(--card-border)] overflow-hidden text-[var(--ink)]">
          <div class="px-4 py-2 text-xs text-slate-500 border-b border-[var(--card-border)] md:hidden">
            <span id="liveDateMobile"></span> • <span id="liveTimeMobile" class="font-mono"></span>
          </div>
          <div class="px-4 py-2 border-b border-[var(--card-border)]">
            <div class="text-sm font-medium"><?php echo htmlspecialchars($user['username']); ?></div>
            <div class="text-xs text-slate-500"><?php echo htmlspecialchars($user['role_name']); ?></div>
          </div>
          <a href="settings.php" class="block px-4 py-2 text-black hover:bg-slate-900">Settings</a>
          <a href="profile.php" class="block px-4 py-2 text-black hover:bg-slate-900">Profile</a>
          <a href="logout.php" class="block px-4 py-2 text-black hover:bg-slate-900">Logout</a>
        </div>
      </div>
    </div>
  </header>

  <!-- NAVBAR SUB-MODULE TABS -->
  <div id="contextBar" class="sticky top-14 z-40 border-b border-[var(--ring)] bg-white/80 backdrop-blur">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 h-12 flex items-center justify-between">
      <div class="flex items-center gap-3">
        <span class="font-semibold text-slate-800">Profile</span>
      </div>
      <nav id="contextTabs" class="flex flex-wrap gap-2">
        <a class="tab-pill active" href="#profile/overview">Overview</a>
        <a class="tab-pill" href="#profile/activity">Activity</a>
        <a class="tab-pill" href="#profile/edit">Edit Profile</a>
      </nav>
    </div>
  </div>

  <!-- LAYOUT -->
  <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 grid grid-cols-1 md:grid-cols-[240px_1fr] gap-6 py-6">
    <div id="overlay" class="overlay"></div>

    <!-- SIDEBAR -->
    <aside id="sidebar" class="fixed md:static left-0 top-14 md:top-auto w-64 md:w-full h-[calc(100vh-56px)] md:h-auto bg-white border-r border-[var(--ring)] sidebar-transition -translate-x-full md:translate-x-0 z-50 overflow-y-auto">
      <nav class="p-3 space-y-1">
        <div class="text-[11px] uppercase tracking-widest text-slate-500 px-2 pt-2 pb-1">Navigation</div>
        <a class="sidebar-item" href="dashboard.php"><span>🏠</span><span>Dashboard</span></a>

        <a class="sidebar-item" href="General Ledger.php"><span>📘</span><span>General Ledger</span></a>
        <a class="sidebar-item" href="Accounts Receivable.php"><span>💳</span><span>Accounts Receivable</span></a>
        <a class="sidebar-item" href="Collections.php"><span>🧾</span><span>Collections</span></a>
        <a class="sidebar-item" href="Accounts Payable.php"><span>📄</span><span>Accounts Payable</span></a>
        <a class="sidebar-item" href="Disbursement.php"><span>💸</span><span>Disbursement</span></a>
        <a class="sidebar-item" href="Budget Management.php"><span>📊</span><span>Budget Management</span></a>
        <a class="sidebar-item" href="Reports.php"><span>📑</span><span>Reports</span></a>
      </nav>
    </aside>

    <!-- MAIN -->
    <main class="space-y-6">
      <?php if ($message): ?>
        <div class="alert alert-<?php echo $messageType; ?>">
          <?php echo htmlspecialchars($message); ?>
        </div>
      <?php endif; ?>
      
      <section id="contentHost" class="space-y-6">
        <!-- Profile Overview -->
        <div id="profile-overview" class="space-y-6">
          <!-- Profile Header -->
          <div class="card p-6">
            <div class="flex flex-col md:flex-row items-start md:items-center gap-6">
              <div class="relative">
                <img src="../uploads/<?php echo htmlspecialchars($user['profile_image'] ?? 'admindefault.png'); ?>" 
                     alt="Profile" class="w-32 h-32 rounded-full object-cover border-4 border-white shadow-lg">
                <div class="absolute -bottom-2 -right-2 w-8 h-8 bg-green-500 rounded-full border-4 border-white flex items-center justify-center">
                  <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                  </svg>
                </div>
              </div>
              
              <div class="flex-1">
                <h1 class="text-3xl font-bold text-gray-900"><?php echo htmlspecialchars($user['username']); ?></h1>
                <p class="text-lg text-gray-600"><?php echo htmlspecialchars($user['role_name']); ?></p>
                <p class="text-gray-500">Member since <?php echo date('F Y'); ?></p>
                
                <div class="flex gap-3 mt-4">
                  <a href="#profile/edit" class="btn btn-brand">Edit Profile</a>
                  <a href="settings.php" class="btn btn-soft">Settings</a>
                </div>
              </div>
            </div>
          </div>

          <!-- Stats Grid -->
          <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="stat-card">
              <div class="flex items-center justify-between">
                <div>
                  <p class="text-sm opacity-90">Total Logins</p>
                  <p class="text-2xl font-bold">127</p>
                </div>
                <div class="text-4xl opacity-20">🔐</div>
              </div>
            </div>
            
            <div class="stat-card">
              <div class="flex items-center justify-between">
                <div>
                  <p class="text-sm opacity-90">Last Login</p>
                  <p class="text-2xl font-bold">Today</p>
                </div>
                <div class="text-4xl opacity-20">⏰</div>
              </div>
            </div>
            
            <div class="stat-card">
              <div class="flex items-center justify-between">
                <div>
                  <p class="text-sm opacity-90">Session Time</p>
                  <p class="text-2xl font-bold">2h 15m</p>
                </div>
                <div class="text-4xl opacity-20">⏱️</div>
              </div>
            </div>
          </div>

          <!-- Quick Info -->
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="card p-6">
              <h3 class="text-lg font-semibold mb-4">Account Information</h3>
              <div class="space-y-3">
                <div class="flex justify-between">
                  <span class="text-gray-600">Username:</span>
                  <span class="font-medium"><?php echo htmlspecialchars($user['username']); ?></span>
                </div>
                <div class="flex justify-between">
                  <span class="text-gray-600">Role:</span>
                  <span class="font-medium"><?php echo htmlspecialchars($user['role_name']); ?></span>
                </div>
                <div class="flex justify-between">
                  <span class="text-gray-600">Status:</span>
                  <span class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-xs">Active</span>
                </div>
                <div class="flex justify-between">
                  <span class="text-gray-600">Last Updated:</span>
                  <span class="font-medium"><?php echo date('M d, Y'); ?></span>
                </div>
              </div>
            </div>
            
            <div class="card p-6">
              <h3 class="text-lg font-semibold mb-4">Recent Activity</h3>
              <div class="space-y-3">
                <div class="flex items-center gap-3">
                  <div class="w-2 h-2 bg-green-500 rounded-full"></div>
                  <span class="text-sm">Logged in successfully</span>
                  <span class="text-xs text-gray-500 ml-auto">2 min ago</span>
                </div>
                <div class="flex items-center gap-3">
                  <div class="w-2 h-2 bg-blue-500 rounded-full"></div>
                  <span class="text-sm">Viewed Dashboard</span>
                  <span class="text-xs text-gray-500 ml-auto">5 min ago</span>
                </div>
                <div class="flex items-center gap-3">
                  <div class="w-2 h-2 bg-purple-500 rounded-full"></div>
                  <span class="text-sm">Updated profile settings</span>
                  <span class="text-xs text-gray-500 ml-auto">1 hour ago</span>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Activity History -->
        <div id="profile-activity" class="hidden space-y-6">
          <div class="card p-6">
            <h3 class="text-lg font-semibold mb-4">Activity History</h3>
            <div class="space-y-4">
              <div class="border-l-4 border-blue-500 pl-4 py-2">
                <div class="flex items-center justify-between">
                  <div>
                    <p class="font-medium">System Login</p>
                    <p class="text-sm text-gray-600">Successfully logged into the system</p>
                  </div>
                  <span class="text-xs text-gray-500"><?php echo date('M d, Y H:i'); ?></span>
                </div>
              </div>
              
              <div class="border-l-4 border-green-500 pl-4 py-2">
                <div class="flex items-center justify-between">
                  <div>
                    <p class="font-medium">Profile Update</p>
                    <p class="text-sm text-gray-600">Updated profile information</p>
                  </div>
                  <span class="text-xs text-gray-500"><?php echo date('M d, Y H:i', strtotime('-1 hour')); ?></span>
                </div>
              </div>
              
              <div class="border-l-4 border-purple-500 pl-4 py-2">
                <div class="flex items-center justify-between">
                  <div>
                    <p class="font-medium">Password Change</p>
                    <p class="text-sm text-gray-600">Changed account password</p>
                  </div>
                  <span class="text-xs text-gray-500"><?php echo date('M d, Y H:i', strtotime('-2 days')); ?></span>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Edit Profile -->
        <div id="profile-edit" class="hidden space-y-6">
          <div class="card p-6">
            <h3 class="text-lg font-semibold mb-4">Edit Profile</h3>
            <form method="POST" enctype="multipart/form-data" class="space-y-6">
              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                 <div>
                   <label class="block text-sm font-medium mb-2">Profile Image</label>
                   <div class="flex items-center gap-4">
                                                         <div class="profile-image-container">
                    <img id="profilePreview" src="../uploads/<?php echo htmlspecialchars($user['profile_image'] ?? 'admindefault.png'); ?>" 
                         alt="Profile" class="cursor-pointer" onclick="openImagePreview(this.src)">
                  </div>
                     <div>
                       <input type="file" name="profile_image" accept="image/*" class="form-input" id="profileImageInput">
                       <p class="text-xs text-gray-500 mt-1">Recommended: 200x200px, JPG/PNG</p>
                     </div>
                   </div>
                 </div>
                
                <div>
                  <label class="block text-sm font-medium mb-2">Username</label>
                  <input type="text" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" 
                         class="form-input" readonly>
                  <p class="text-xs text-gray-500 mt-1">Username cannot be changed</p>
                </div>
              </div>
              
              <div class="flex gap-3">
                <button type="submit" class="btn btn-brand">Save Changes</button>
                <button type="button" class="btn btn-soft">Cancel</button>
              </div>
            </form>
          </div>
        </div>
      </section>
    </main>
  </div>

  <!-- Image Preview Modal -->
  <div id="imagePreviewModal" class="fixed inset-0 z-[100] hidden items-center justify-center bg-black/80">
    <div class="relative max-w-4xl max-h-[90vh] p-4">
      <button onclick="closeImagePreview()" class="absolute top-2 right-2 z-10 w-8 h-8 bg-white/20 hover:bg-white/30 rounded-full flex items-center justify-center text-white text-xl font-bold transition-colors">
        ×
      </button>
      <img id="modalImage" src="" alt="Full Preview" class="max-w-full max-h-full object-contain rounded-lg shadow-2xl">
    </div>
  </div>

  <script>
    const $=(s,r=document)=>r.querySelector(s), $$=(s,r=document)=>Array.from(r.querySelectorAll(s));

    /* loader */
    const Loader=(()=>{const el=$('#globalLoader');let on=false,t0=0;const MIN=350;
      function show(){if(on) return; on=true;t0=performance.now();el.classList.remove('hidden');el.classList.add('flex');}
      function hide(){if(!on) return;const d=Math.max(0,MIN-(performance.now()-t0));setTimeout(()=>{el.classList.add('hidden');el.classList.remove('flex');on=false;},d);}
      async function wrap(job){show();try{return typeof job==='function'?await job():await job;}finally{hide();}}
      return{show,hide,wrap};
    })();

    /* header interactions */
    const overlay=$('#overlay'), sidebar=$('#sidebar');
    $('#openSidebar')?.addEventListener('click', ()=>{sidebar.classList.remove('-translate-x-full'); overlay.classList.add('active');});
    overlay?.addEventListener('click', ()=>{sidebar.classList.add('-translate-x-full'); overlay.classList.remove('active');});
    const pBtn=$('#profileBtn'), pMenu=$('#profileMenu');
    pBtn?.addEventListener('click', ()=>pMenu.classList.toggle('hidden'));
    document.addEventListener('click', (e)=>{ if(pBtn && pMenu && !pBtn.contains(e.target) && !pMenu.contains(e.target)) pMenu.classList.add('hidden'); });
    $('#darkModeToggle')?.addEventListener('click', ()=>{ document.documentElement.classList.toggle('dark'); document.body.classList.toggle('bg-soft'); });

    /* live clock */
    (function(){
      const t=$('#liveTime'), d=$('#liveDate'), tm=$('#liveTimeMobile'), dm=$('#liveTimeMobile'); let is24=localStorage.getItem('fmt24')==='1';
      const fD=n=>new Intl.DateTimeFormat(undefined,{year:'numeric',month:'short',day:'2-digit',weekday:'short'}).format(n);
      const fT=n=>new Intl.DateTimeFormat(undefined,{hour:'2-digit',minute:'2-digit',second:'2-digit',hour12:!is24}).format(n);
      function tick(){const n=new Date(); if(d) d.textContent=fD(n); if(t) t.textContent=fT(n); if(dm) dm.textContent=fD(n); if(tm) tm.textContent=fT(n);}
      t?.addEventListener('click',()=>{is24=!is24; localStorage.setItem('fmt24',is24?'1':'0'); tick();});
      tick(); setInterval(tick,1000); $('#clockWrap')?.classList.remove('hidden');
    })();

    /* tab navigation */
    const tabs = $$('#contextTabs .tab-pill');
    tabs.forEach(tab => {
      tab.addEventListener('click', (e) => {
        e.preventDefault();
        tabs.forEach(t => t.classList.remove('active'));
        tab.classList.add('active');
        
        const target = tab.getAttribute('href').split('/')[1];
        showSection(target);
      });
    });

    function showSection(sectionName) {
      const sections = ['overview', 'activity', 'edit'];
      sections.forEach(section => {
        const element = document.getElementById('profile-' + section);
        if (element) {
          element.style.display = section === sectionName ? 'block' : 'none';
        }
      });
    }

    // Show overview section by default
    showSection('overview');

    // Profile image preview functionality
    const profileImageInput = document.getElementById('profileImageInput');
    const profilePreview = document.getElementById('profilePreview');

    if (profileImageInput && profilePreview) {
      profileImageInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
          // Validate file type
          const validTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
          if (!validTypes.includes(file.type)) {
            alert('Please select a valid image file (JPG, PNG, or GIF)');
            this.value = '';
            return;
          }

          // Create preview
          const reader = new FileReader();
          reader.onload = function(e) {
            profilePreview.src = e.target.result;
            profilePreview.style.display = 'block';
            
            // Add a subtle animation effect
            profilePreview.style.transform = 'scale(1.1)';
            setTimeout(() => {
              profilePreview.style.transform = 'scale(1)';
            }, 200);
          };
          reader.readAsDataURL(file);
                 }
       });
     }

     // Image preview modal functions
     function openImagePreview(imageSrc) {
       const modal = document.getElementById('imagePreviewModal');
       const modalImage = document.getElementById('modalImage');
       modalImage.src = imageSrc;
       modal.classList.remove('hidden');
       modal.classList.add('flex');
     }

     function closeImagePreview() {
       const modal = document.getElementById('imagePreviewModal');
       modal.classList.add('hidden');
       modal.classList.remove('flex');
     }

           // Close modal when clicking outside
      document.getElementById('imagePreviewModal').addEventListener('click', function(e) {
        if (e.target === this) {
          closeImagePreview();
        }
      });

      // Dark mode functionality
      const headerDarkModeToggle = document.getElementById('headerDarkModeToggle');
      
      // Toggle dark mode
      function toggleDarkMode() {
        const root = document.documentElement;
        const isDark = root.classList.toggle('dark');
        
        // Save preference to localStorage
        localStorage.setItem('darkMode', isDark ? 'enabled' : 'disabled');
        
        // Update toggle icon
        updateHeaderToggleIcon(isDark);
      }
      
      // Update header toggle icon
      function updateHeaderToggleIcon(isDark) {
        if (headerDarkModeToggle) {
          const svg = headerDarkModeToggle.querySelector('svg');
          if (svg) {
            if (isDark) {
              // Sun icon for dark mode
              svg.innerHTML = '<path stroke-linecap="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>';
            } else {
              // Moon icon for light mode
              svg.innerHTML = '<path stroke-linecap="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>';
            }
          }
        }
      }
      
      // Initialize dark mode on page load
      function initDarkMode() {
        const savedMode = localStorage.getItem('darkMode');
        const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
        
        // Use saved preference or system preference
        if (savedMode === 'enabled' || (!savedMode && prefersDark)) {
          document.documentElement.classList.add('dark');
          updateHeaderToggleIcon(true);
        } else {
          updateHeaderToggleIcon(false);
        }
      }
      
      // Event listeners
      if (headerDarkModeToggle) {
        headerDarkModeToggle.addEventListener('click', toggleDarkMode);
      }
      
      // Initialize on page load
      initDarkMode();

      // Initialize loading screen
      function initLoadingScreen() {
        const loader = $('#globalLoader');
        const progressBar = $('#loadingProgress');
        
        // Simulate loading progress
        let progress = 0;
        const progressInterval = setInterval(() => {
          progress += Math.random() * 15 + 5; // Random progress between 5-20
          if (progress >= 100) {
            progress = 100;
            clearInterval(progressInterval);
            
            // Hide loader with fade out effect
            setTimeout(() => {
              loader.style.opacity = '0';
              loader.style.transform = 'scale(0.95)';
              setTimeout(() => {
                loader.style.display = 'none';
                // Animate content in
                animateContentIn();
              }, 300);
            }, 200);
          }
          progressBar.style.width = progress + '%';
        }, 100);
      }
      
      // Animate content in
      function animateContentIn() {
        const cards = document.querySelectorAll('.profile-card, .card');
        cards.forEach((card, index) => {
          setTimeout(() => {
            card.style.opacity = '0';
            card.style.transform = 'translateY(20px)';
            card.style.transition = 'all 0.5s ease-out';
            
            setTimeout(() => {
              card.style.opacity = '1';
              card.style.transform = 'translateY(0)';
            }, 100);
          }, index * 100);
        });
      }
      
      // Add loading state to buttons
      function initButtonLoading() {
        const buttons = document.querySelectorAll('button[onclick*="add"], button[onclick*="delete"], button[onclick*="edit"], button[onclick*="save"], button[onclick*="submit"]');
        buttons.forEach(button => {
          button.addEventListener('click', function() {
            if (!this.classList.contains('btn-loading')) {
              const originalText = this.textContent;
              this.classList.add('btn-loading');
              this.textContent = 'Processing...';
              
              // Reset after 2 seconds (or you can reset after actual operation)
              setTimeout(() => {
                this.classList.remove('btn-loading');
                this.textContent = originalText;
              }, 2000);
            }
          });
        });
      }

      // Initialize loading screen on page load
      document.addEventListener('DOMContentLoaded', () => {
        initLoadingScreen();
        initButtonLoading();
        // Initialize notification system after a short delay to avoid conflicts
        setTimeout(() => {
          initNotificationSystem();
        }, 100);
      });

      // ===== NOTIFICATION SYSTEM =====
      
      // Notification types and their configurations
      const notificationTypes = {
        system: {
          icon: '🔔',
          color: 'blue',
          title: 'System Alert',
          duration: 5000
        },
        financial: {
          icon: '📊',
          color: 'green',
          title: 'Financial Report',
          duration: 6000
        },
        security: {
          icon: '🔒',
          color: 'red',
          title: 'Security Alert',
          duration: 8000
        },
        email: {
          icon: '📧',
          color: 'purple',
          title: 'Email Notification',
          duration: 4000
        }
      };

      let notificationCount = 0;
      let notifications = [];

      // Initialize notification system
      function initNotificationSystem() {
        // Load existing notifications
        loadNotifications();
        
        // Simulate real-time notifications for Profile
        simulateProfileNotifications();
      }

      // Toggle notification panel
      function toggleNotificationPanel() {
        const panel = $('#notificationPanel');
        panel.classList.toggle('hidden');
      }

      // Add notification to panel
      function addNotificationToPanel(type, message) {
        const config = notificationTypes[type];
        if (!config) return;

        notificationCount++;
        updateNotificationBadge();

        const notification = {
          id: Date.now(),
          type: type,
          message: message,
          time: new Date().toLocaleTimeString(),
          icon: config.icon
        };

        notifications.unshift(notification);
        updateNotificationList();

        // Store in localStorage
        localStorage.setItem('notifications', JSON.stringify(notifications));
      }

      // Update notification badge
      function updateNotificationBadge() {
        const badge = $('#notificationBadge');
        if (notificationCount > 0) {
          badge.textContent = notificationCount > 99 ? '99+' : notificationCount;
          badge.classList.remove('hidden');
        } else {
          badge.classList.add('hidden');
        }
      }

      // Update notification list
      function updateNotificationList() {
        const list = $('#notificationList');
        if (notifications.length === 0) {
          list.innerHTML = '<div class="p-4 text-center text-gray-500 text-sm">No new notifications</div>';
          return;
        }

        list.innerHTML = notifications.map(notification => `
          <div class="p-3 border-b border-gray-100 hover:bg-gray-50 transition-colors">
            <div class="flex items-start gap-3">
              <span class="text-lg">${notification.icon}</span>
              <div class="flex-1 min-w-0">
                <p class="text-sm font-medium text-gray-900">${notificationTypes[notification.type].title}</p>
                <p class="text-sm text-gray-600 mt-1">${notification.message}</p>
                <p class="text-xs text-gray-400 mt-1">${notification.time}</p>
              </div>
              <button onclick="removeNotification(${notification.id})" class="text-gray-400 hover:text-gray-600">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
              </button>
            </div>
          </div>
        `).join('');
      }

      // Remove individual notification
      function removeNotification(id) {
        notifications = notifications.filter(n => n.id !== id);
        notificationCount = Math.max(0, notificationCount - 1);
        updateNotificationBadge();
        updateNotificationList();
        localStorage.setItem('notifications', JSON.stringify(notifications));
      }

      // Clear all notifications
      function clearAllNotifications() {
        notifications = [];
        notificationCount = 0;
        updateNotificationBadge();
        updateNotificationList();
        localStorage.setItem('notifications', JSON.stringify(notifications));
      }

      // Load notifications from localStorage
      function loadNotifications() {
        const saved = localStorage.getItem('notifications');
        if (saved) {
          notifications = JSON.parse(saved);
          notificationCount = notifications.length;
          updateNotificationBadge();
          updateNotificationList();
        }
      }

      // Simulate Profile-specific notifications
      function simulateProfileNotifications() {
        // Profile notifications (every 3 minutes)
        setInterval(() => {
          if (Math.random() < 0.2) { // 20% chance
            const messages = [
              'Profile updated successfully',
              'New login detected from different device',
              'Security settings modified',
              'Profile picture changed'
            ];
            const randomMessage = messages[Math.floor(Math.random() * messages.length)];
            addNotificationToPanel('system', randomMessage);
          }
        }, 180000);
      }

      // Close notification panel when clicking outside
      document.addEventListener('click', function(e) {
        const panel = $('#notificationPanel');
        const bell = $('#notificationBell');
        
        if (panel && !panel.contains(e.target) && !bell.contains(e.target)) {
          panel.classList.add('hidden');
        }
      });
    </script>

</body>
</html>
