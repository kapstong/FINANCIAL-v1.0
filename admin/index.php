<?php
require_once '../includes/auth.php';
$auth = new Auth();
$auth->requireAuth();

$user = $auth->getCurrentUser();

// Get real-time dashboard data
$dashboardData = $auth->getDashboardData();
$data = $dashboardData['data'];

// Format currency values
function formatCurrency($amount) {
    return '₱' . number_format($amount, 2);
}

// Get current month name
$currentMonthName = date('F Y');
?>
<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Finance Suite — Dashboard</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>

  <style>
    :root{
      --brand:#0f1c49;
      --brand-600:#0c173c;
      --brand-100:#e8ecf9;
      --ink:#000;
      --muted:#000;
      --ring:0 0 0 3px rgba(15,28,73,.15);
      --card-bg: rgba(255,255,255,.95);
      --card-border: rgba(226,232,240,.9);
    }
    
    /* Dark mode variables */
    html.dark {
      --ink: #e5e7eb;
      --muted: #9ca3af;
      --card-bg: rgba(17,24,39,.92);
      --card-border: rgba(71,85,105,.55);
    }

    body{ background:#fff; color:var(--ink); padding-top: 0; }
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

    .navbar { background: var(--brand); color:#fff; height: 3.5rem; }
    .navbar * { color:white !important; }
    .nav-input{
      background: rgba(255,255,255,.18);
      border: 1px solid rgba(255,255,255,.35);
      padding: .35rem .6rem; border-radius:.6rem;
    }
    .nav-input::placeholder{ color:#f1f5f9; }

    .card{ background:var(--card-bg); border-radius:14px; border:1px solid var(--card-border); box-shadow:0 6px 18px rgba(2,6,23,.04) }
    html.dark .card{ box-shadow:0 16px 48px rgba(0,0,0,.5); }
    .btn{ display:inline-flex; align-items:center; gap:.5rem; padding:.55rem .95rem; border-radius:.65rem; font-weight:600; color:#000 }
    .btn-brand{ background:var(--brand); color:#fff !important } .btn-brand:hover{ background:var(--brand-600) }
    .btn-soft{ background:#fff; border:1px solid var(--card-border); } .btn-soft:hover{ background:#f8fafc }
    .top-chip{ height:3px; background:var(--brand) }

    .sidebar-transition{ transition:transform .28s ease }
    .overlay{ display:none } .overlay.active{ display:block; position:fixed; inset:0; background:rgba(0,0,0,.35); z-index:40 }

    .sidebar-item{ display:flex; align-items:center; gap:.6rem; width:100%; padding:.5rem .75rem; border-radius:.6rem; color:var(--ink); }
    .sidebar-item:hover{ background:#f8fafc }
    html.dark .sidebar-item:hover{ background:rgba(31,41,55,.92); }
    .sidebar-item.active{ background:rgba(15,28,73,.06); color:var(--brand); font-weight:700 }

    .tab-pill{ padding:.4rem .8rem; border-radius:9999px; border:1px solid var(--card-border); font-weight:700; font-size:.9rem; color:var(--ink) }
    .tab-pill.active{ background:var(--brand); color:#fff; border-color:var(--brand) }

    .modal{ display:none; position:fixed; inset:0; z-index:60; }
    .modal.active{ display:flex }
    .modal-backdrop{ position:absolute; inset:0; background:rgba(0,0,0,.42) }
    .modal-panel{ position:relative; margin:auto; width:min(680px,92vw); }
    .toast-card{ background:#fff; border:1px solid var(--card-border); border-radius:.75rem; padding:.6rem .9rem; box-shadow:0 10px 30px rgba(0,0,0,.08); color:#000 }
    
    /* Dark mode specific styles */
    html.dark .btn{ color:var(--ink); }
    html.dark .btn-soft{ background:var(--card-bg); border-color:var(--card-border); color:var(--ink); }
    html.dark .btn-soft:hover{ background:rgba(31,41,55,.92); }
    html.dark .toast-card{ background:var(--card-bg); border-color:var(--card-border); color:var(--ink); }
    
    /* Dark mode context bar */
    html.dark #contextBar {
      background: rgba(17,24,39,.8);
      border-color: rgba(71,85,105,.55);
    }
    
    /* Dark mode sidebar */
    
         /* Dark mode for new dashboard elements */
     html.dark .text-slate-800 { color: var(--ink); }
     html.dark .text-slate-600 { color: var(--muted); }
     html.dark .text-slate-500 { color: var(--muted); }
     html.dark .border-slate-200 { border-color: var(--border); }
     html.dark .hover\:bg-slate-50:hover { background-color: rgba(31,41,55,.92); }
     html.dark .bg-slate-50 { background-color: rgba(31,41,55,.92); }
     html.dark .border-slate-100 { border-color: var(--border); }
     html.dark #sidebar {
       background: var(--card-bg);
       border-color: var(--card-border);
     }
     
     /* Dark mode text colors */
     html.dark .text-slate-500 {
       color: var(--muted);
     }
     
         /* Enhanced Navigation Bar Styles */
    .navbar {
      background: linear-gradient(135deg, var(--brand) 0%, var(--brand-600) 100%);
      box-shadow: 0 4px 20px rgba(15, 28, 73, 0.15);
      backdrop-filter: blur(10px);
      border-bottom: 1px solid rgba(255, 255, 255, 0.1);
      height: 3.5rem !important;
      min-height: 3.5rem !important;
      max-height: 3.5rem !important;
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
     
     /* Dark Mode Enhanced Styles */
     html.dark .navbar {
       background: linear-gradient(135deg, rgba(15, 28, 73, 0.95) 0%, rgba(12, 23, 60, 0.95) 100%);
       border-bottom-color: rgba(255, 255, 255, 0.1);
     }
     
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
     
     html.dark .text-slate-500 {
       color: rgba(255, 255, 255, 0.6);
     }
     
     /* Enhanced Brand Logo */
     .navbar a[href="index.php"] {
       transition: all 0.3s ease;
     }
     
     .navbar a[href="index.php"]:hover {
       transform: scale(1.05);
     }
     
     .navbar a[href="index.php"] span {
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

    /* Dark mode for notification panel */
    html.dark #notificationPanel {
      background: rgba(17,24,39,.95);
      border-color: rgba(71,85,105,.55);
      backdrop-filter: blur(20px);
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
    
    html.dark #notificationPanel .text-gray-500 {
      color: #9ca3af;
    }
    
    html.dark #notificationPanel .border-gray-100 {
      border-color: rgba(71,85,105,.3);
    }
    
    html.dark #notificationPanel .border-gray-200 {
      border-color: rgba(71,85,105,.55);
    }
    
    html.dark #notificationPanel .hover\:bg-gray-50:hover {
      background: rgba(55,65,81,.95);
    }
    
    html.dark #notificationPanel .text-blue-600 {
      color: #60a5fa;
    }
    
    html.dark #notificationPanel .hover\:text-blue-800:hover {
      color: #93c5fd;
    }
    
    /* Dark mode for notification badge */
    html.dark #notificationBadge {
      background: #ef4444;
      color: #ffffff;
      box-shadow: 0 0 0 2px rgba(17,24,39,.95);
    }
    
    /* Dark mode for notification bell button */
    html.dark #notificationBell {
      color: #ffffff;
    }
    
    html.dark #notificationBell:hover {
      background: rgba(255,255,255,.15);
    }
    
    /* Enhanced bell hover effects */
    #notificationBell {
      transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    
    #notificationBell:hover {
      transform: scale(1.1);
      background: rgba(255,255,255,.15);
    }
    
    #notificationBell:active {
      transform: scale(0.95);
    }
    
    /* Glow effect when there are notifications */
    #notificationBell.has-notifications {
      box-shadow: 0 0 15px rgba(239, 68, 68, 0.6);
    }
    
    html.dark #notificationBell.has-notifications {
      box-shadow: 0 0 15px rgba(239, 68, 68, 0.8);
    }
      
      /* Ensure proper spacing for sticky navigation */
      .sticky {
        position: sticky;
        top: 0;
      }
      
      /* Prevent content from being hidden behind sticky nav */
      main {
        position: relative;
        z-index: 10;
        margin-top: 0;
      }
      
      /* Ensure proper spacing for sticky navigation */
      .sticky {
        position: sticky;
        top: 0;
      }
      
      /* Add top margin to main content to prevent overlap */
      .mx-auto.max-w-7xl {
        margin-top: 0;
        padding-top: 0;
      }
      
             /* Ensure sidebar doesn't overlap with sticky nav */
       #sidebar {
         top: 3.5rem !important;
       }
       
       /* Scrollable main content area */
       main {
         scrollbar-width: thin;
         scrollbar-color: rgba(15, 28, 73, 0.3) transparent;
       }
       
       main::-webkit-scrollbar {
         width: 6px;
       }
       
       main::-webkit-scrollbar-track {
         background: transparent;
       }
       
       main::-webkit-scrollbar-thumb {
         background: rgba(15, 28, 73, 0.3);
         border-radius: 3px;
       }
       
       main::-webkit-scrollbar-thumb:hover {
         background: rgba(15, 28, 73, 0.5);
       }
       
               /* Dark mode scrollbar */
        html.dark main {
          scrollbar-color: rgba(255, 255, 255, 0.3) transparent;
        }
        
        html.dark main::-webkit-scrollbar-thumb {
          background: rgba(255, 255, 255, 0.3);
        }
        
        html.dark main::-webkit-scrollbar-thumb:hover {
          background: rgba(255, 255, 255, 0.5);
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
        .dashboard-card {
          transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        .dashboard-card:hover {
          transform: translateY(-4px);
          box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }
        
        /* Loading screen transitions */
        #globalLoader {
          transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        /* KPI value animations */
        .kpi-value {
          transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
        }

    /* Enhanced notification system styles */
    #notificationPanel {
      transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
      transform-origin: top right;
    }
    
    #notificationPanel.hidden {
      opacity: 0;
      transform: scale(0.95) translateY(-10px);
      pointer-events: none;
    }
    
    #notificationPanel:not(.hidden) {
      opacity: 1;
      transform: scale(1) translateY(0);
    }
    
    #notificationBadge {
      transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
      animation: pulse 2s infinite;
    }
    
    /* Enhanced badge animation */
    #notificationBadge:not(.hidden) {
      animation: badgeBounce 0.6s ease-out, pulse 2s infinite 0.6s;
    }
    
    @keyframes badgeBounce {
      0% { transform: scale(0) rotate(0deg); }
      50% { transform: scale(1.2) rotate(180deg); }
      100% { transform: scale(1) rotate(360deg); }
    }
    
    /* Bell shake animation when there are notifications */
    #notificationBell.has-notifications {
      animation: bellShake 0.5s ease-in-out;
    }
    
    /* Ripple effect for notification arrival */
    #notificationBell::after {
      content: '';
      position: absolute;
      top: 50%;
      left: 50%;
      width: 0;
      height: 0;
      border-radius: 50%;
      background: rgba(255, 255, 255, 0.3);
      transform: translate(-50%, -50%);
      transition: all 0.6s ease-out;
      pointer-events: none;
    }
    
    #notificationBell.has-notifications::after {
      width: 40px;
      height: 40px;
      opacity: 0;
    }
    
    @keyframes bellShake {
      0%, 100% { transform: rotate(0deg); }
      10%, 30%, 50%, 70%, 90% { transform: rotate(-8deg); }
      20%, 40%, 60%, 80% { transform: rotate(8deg); }
    }
    
    @keyframes pulse {
      0%, 100% { transform: scale(1); }
      50% { transform: scale(1.1); }
    }
    
    /* Toast notification animations */
    .toast-card {
      animation: slideInRight 0.3s ease-out;
      transition: all 0.3s ease;
    }
    
    .toast-card:hover {
      transform: translateX(-5px);
    }
    
    @keyframes slideInRight {
      from {
        transform: translateX(100%);
        opacity: 0;
      }
      to {
        transform: translateX(0);
        opacity: 1;
      }
    }
    
    /* Dark mode scrollbar for notification panel */
    html.dark #notificationList::-webkit-scrollbar {
      width: 6px;
    }
    
    html.dark #notificationList::-webkit-scrollbar-track {
      background: rgba(31,41,55,.5);
      border-radius: 3px;
    }
    
    html.dark #notificationList::-webkit-scrollbar-thumb {
      background: rgba(156,163,175,.5);
      border-radius: 3px;
    }
    
    html.dark #notificationList::-webkit-scrollbar-thumb:hover {
      background: rgba(156,163,175,.7);
    }
    
    /* Comprehensive Responsive Design - Mobile First */
    @media (max-width: 640px) {
      .container {
        padding-left: 1rem;
        padding-right: 1rem;
      }
      
      .navbar {
        height: 4rem;
        padding: 0 1rem;
      }
      
      .nav-input {
        width: 100%;
        max-width: none;
        font-size: 0.875rem;
      }
      
      .card {
        padding: 1rem;
        margin-bottom: 1rem;
        border-radius: 12px;
      }
      
      .grid {
        grid-template-columns: 1fr;
        gap: 1rem;
      }
      
      .stats-grid {
        grid-template-columns: 1fr;
        gap: 1rem;
      }
      
      .sidebar {
        width: 100%;
        transform: translateX(-100%);
        z-index: 50;
      }
      
      .sidebar.active {
        transform: translateX(0);
      }
      
      .overlay.active {
        display: block;
      }
      
      .btn {
        width: 100%;
        justify-content: center;
        padding: 0.75rem 1rem;
        min-height: 44px;
      }
      
      .tab-pill {
        padding: 0.5rem 1rem;
        font-size: 0.875rem;
        min-height: 44px;
      }
      
      .chart-container {
        height: 300px;
        margin: 0 -1rem;
      }
      
      .stats-card {
        padding: 1rem;
      }
      
      .table-responsive {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
        margin: 0 -1rem;
        padding: 0 1rem;
      }
      
      .table-responsive table {
        min-width: 600px;
      }
      
      .mobile-menu-toggle {
        display: block;
      }
      
      .desktop-only {
        display: none;
      }
      
      .mobile-only {
        display: block;
      }
      
      .notification-panel {
        width: calc(100vw - 2rem);
        right: 1rem;
        left: 1rem;
      }
      
      .modal-panel {
        width: calc(100vw - 2rem);
        margin: 1rem;
      }
      
      .navbar .nav-input {
        display: none;
      }
      
      .navbar .clockWrap {
        display: none;
      }
    }
    
    @media (min-width: 641px) and (max-width: 1024px) {
      .container {
        padding-left: 1.5rem;
        padding-right: 1.5rem;
      }
      
      .grid {
        grid-template-columns: repeat(2, 1fr);
        gap: 1.5rem;
      }
      
      .stats-grid {
        grid-template-columns: repeat(2, 1fr);
        gap: 1.5rem;
      }
      
      .chart-container {
        height: 400px;
      }
      
      .sidebar {
        width: 240px;
      }
      
      .mobile-menu-toggle {
        display: none;
      }
      
      .desktop-only {
        display: block;
      }
      
      .mobile-only {
        display: none;
      }
      
      .navbar .nav-input {
        width: 300px;
      }
    }
    
    @media (min-width: 1025px) {
      .container {
        padding-left: 2rem;
        padding-right: 2rem;
      }
      
      .grid {
        grid-template-columns: repeat(4, 1fr);
        gap: 2rem;
      }
      
      .stats-grid {
        grid-template-columns: repeat(4, 1fr);
        gap: 2rem;
      }
      
      .chart-container {
        height: 500px;
      }
      
      .sidebar {
        width: 280px;
      }
      
      .mobile-menu-toggle {
        display: none;
      }
      
      .desktop-only {
        display: block;
      }
      
      .mobile-only {
        display: none;
      }
      
      .navbar .nav-input {
        width: 400px;
      }
    }
    
    /* Mobile Navigation */
    .mobile-nav {
      display: none;
      position: fixed;
      bottom: 0;
      left: 0;
      right: 0;
      background: white;
      border-top: 1px solid #e5e7eb;
      z-index: 50;
      padding: 0.5rem;
      box-shadow: 0 -4px 20px rgba(0, 0, 0, 0.1);
    }
    
    html.dark .mobile-nav {
      background: #1f2937;
      border-top-color: #374151;
      box-shadow: 0 -4px 20px rgba(0, 0, 0, 0.3);
    }
    
    .mobile-nav.active {
      display: block;
    }
    
    .mobile-nav-grid {
      display: grid;
      grid-template-columns: repeat(5, 1fr);
      gap: 0.5rem;
    }
    
    .mobile-nav-item {
      display: flex;
      flex-direction: column;
      align-items: center;
      padding: 0.5rem;
      color: #6b7280;
      text-decoration: none;
      font-size: 0.75rem;
      transition: color 0.2s;
      border-radius: 8px;
    }
    
    .mobile-nav-item.active {
      color: var(--brand);
      background: rgba(15, 28, 73, 0.1);
    }
    
    .mobile-nav-item:hover {
      color: var(--brand);
    }
    
    html.dark .mobile-nav-item {
      color: #9ca3af;
    }
    
    html.dark .mobile-nav-item.active {
      color: #60a5fa;
      background: rgba(96, 165, 250, 0.1);
    }
    
    html.dark .mobile-nav-item:hover {
      color: #60a5fa;
    }
    
    /* Touch-friendly interactions */
    @media (hover: none) and (pointer: coarse) {
      .btn, .sidebar-item, .tab-pill {
        min-height: 44px;
      }
      
      .nav-input {
        min-height: 44px;
      }
      
      .card {
        border-radius: 12px;
      }
      
      .notification-bell {
        min-height: 44px;
        min-width: 44px;
      }
    }
    
    /* High contrast mode support */
    @media (prefers-contrast: high) {
      .card {
        border-width: 2px;
      }
      
      .btn {
        border-width: 2px;
      }
      
      .sidebar-item {
        border-width: 1px;
      }
    }
    
    /* Reduced motion support */
    @media (prefers-reduced-motion: reduce) {
      * {
        animation-duration: 0.01ms !important;
        animation-iteration-count: 1 !important;
        transition-duration: 0.01ms !important;
      }
    }
    
    /* Landscape mobile optimization */
    @media (max-width: 768px) and (orientation: landscape) {
      .navbar {
        height: 3.5rem;
      }
      
      .chart-container {
        height: 250px;
      }
      
      .mobile-nav {
        padding: 0.25rem;
      }
    }
    
    /* Large screen optimization */
    @media (min-width: 1440px) {
      .container {
        max-width: 1400px;
      }
      
      .grid {
        grid-template-columns: repeat(5, 1fr);
        gap: 2.5rem;
      }
      
      .stats-grid {
        grid-template-columns: repeat(5, 1fr);
        gap: 2.5rem;
      }
    }
    
    /* ===== COMPONENT-SPECIFIC RESPONSIVE FIXES ===== */
    
    /* Chart Container Responsive */
    .chart-container {
      position: relative;
      width: 100%;
      height: 300px;
    }
    
    @media (max-width: 640px) {
      .chart-container {
        height: 250px;
        margin: 0 -1rem;
      }
      
      .chart-container canvas {
        max-height: 250px;
      }
    }
    
    @media (min-width: 641px) and (max-width: 1024px) {
      .chart-container {
        height: 350px;
      }
    }
    
    @media (min-width: 1025px) {
      .chart-container {
        height: 400px;
      }
    }
    
    @media (min-width: 1440px) {
      .chart-container {
        height: 500px;
      }
    }
    
    /* Table Responsive Improvements */
    .table-responsive {
      overflow-x: auto;
      -webkit-overflow-scrolling: touch;
      border-radius: 8px;
      border: 1px solid var(--card-border);
    }
    
    .table-responsive table {
      width: 100%;
      border-collapse: collapse;
    }
    
    .table-responsive th,
    .table-responsive td {
      padding: 0.75rem;
      text-align: left;
      border-bottom: 1px solid var(--card-border);
      white-space: nowrap;
    }
    
    .table-responsive th {
      background: #f8fafc;
      font-weight: 600;
      font-size: 0.875rem;
      color: #374151;
    }
    
    html.dark .table-responsive th {
      background: #1f2937;
      color: #d1d5db;
    }
    
    @media (max-width: 640px) {
      .table-responsive {
        margin: 0 -1rem;
        border-radius: 0;
        border-left: none;
        border-right: none;
      }
      
      .table-responsive table {
        min-width: 600px;
        font-size: 0.875rem;
      }
      
      .table-responsive th,
      .table-responsive td {
        padding: 0.5rem 0.75rem;
      }
    }
    
    /* Form Elements Responsive */
    .form-grid {
      display: grid;
      gap: 1rem;
    }
    
    .form-input {
      width: 100%;
      padding: 0.75rem;
      border: 1px solid var(--card-border);
      border-radius: 8px;
      font-size: 0.875rem;
      transition: border-color 0.2s, box-shadow 0.2s;
    }
    
    .form-input:focus {
      outline: none;
      border-color: var(--brand);
      box-shadow: 0 0 0 3px rgba(15, 28, 73, 0.1);
    }
    
    html.dark .form-input {
      background: #1f2937;
      border-color: #374151;
      color: #f9fafb;
    }
    
    html.dark .form-input:focus {
      border-color: #60a5fa;
      box-shadow: 0 0 0 3px rgba(96, 165, 250, 0.1);
    }
    
    @media (max-width: 640px) {
      .form-grid {
        grid-template-columns: 1fr;
        gap: 0.75rem;
      }
      
      .form-input {
        font-size: 16px; /* Prevents zoom on iOS */
        padding: 0.875rem;
        min-height: 44px;
      }
    }
    
    @media (min-width: 641px) and (max-width: 1024px) {
      .form-grid {
        grid-template-columns: repeat(2, 1fr);
        gap: 1rem;
      }
    }
    
    @media (min-width: 1025px) {
      .form-grid {
        grid-template-columns: repeat(3, 1fr);
        gap: 1.5rem;
      }
    }
    
    /* Quick Actions Grid Responsive */
    .quick-actions-grid {
      display: grid;
      gap: 1rem;
    }
    
    @media (max-width: 640px) {
      .quick-actions-grid {
        grid-template-columns: 1fr;
        gap: 0.75rem;
      }
      
      .quick-actions-grid button {
        padding: 1rem;
        text-align: left;
        min-height: 80px;
      }
    }
    
    @media (min-width: 641px) and (max-width: 1024px) {
      .quick-actions-grid {
        grid-template-columns: repeat(2, 1fr);
        gap: 1rem;
      }
    }
    
    @media (min-width: 1025px) {
      .quick-actions-grid {
        grid-template-columns: repeat(4, 1fr);
        gap: 1.5rem;
      }
    }
    
    /* KPI Grid Responsive */
    #kpiGrid {
      display: grid;
      gap: 1.25rem;
    }
    
    @media (max-width: 640px) {
      #kpiGrid {
        grid-template-columns: 1fr;
        gap: 1rem;
      }
      
      #kpiGrid .card {
        padding: 1rem;
      }
      
      #kpiGrid .text-2xl {
        font-size: 1.5rem;
      }
    }
    
    @media (min-width: 641px) and (max-width: 1024px) {
      #kpiGrid {
        grid-template-columns: repeat(2, 1fr);
        gap: 1.25rem;
      }
    }
    
    @media (min-width: 1025px) {
      #kpiGrid {
        grid-template-columns: repeat(4, 1fr);
        gap: 1.5rem;
      }
    }
    
    /* Monthly Summary Grid Responsive */
    .monthly-summary-grid {
      display: grid;
      gap: 1.25rem;
    }
    
    @media (max-width: 640px) {
      .monthly-summary-grid {
        grid-template-columns: 1fr;
        gap: 1rem;
      }
    }
    
    @media (min-width: 641px) and (max-width: 1024px) {
      .monthly-summary-grid {
        grid-template-columns: repeat(3, 1fr);
        gap: 1.25rem;
      }
    }
    
    @media (min-width: 1025px) {
      .monthly-summary-grid {
        grid-template-columns: repeat(3, 1fr);
        gap: 1.5rem;
      }
    }
    
    /* Chart and Table Layout Responsive */
    .chart-table-layout {
      display: grid;
      gap: 1.5rem;
    }
    
    @media (max-width: 640px) {
      .chart-table-layout {
        grid-template-columns: 1fr;
        gap: 1rem;
      }
      
      .chart-table-layout .card {
        margin: 0 -1rem;
        border-radius: 0;
        border-left: none;
        border-right: none;
      }
    }
    
    @media (min-width: 641px) and (max-width: 1024px) {
      .chart-table-layout {
        grid-template-columns: 1fr;
        gap: 1.5rem;
      }
    }
    
    @media (min-width: 1025px) {
      .chart-table-layout {
        grid-template-columns: 1fr 2fr;
        gap: 2rem;
      }
    }
    
    /* Button Responsive Improvements */
    .btn {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      gap: 0.5rem;
      padding: 0.75rem 1.5rem;
      border-radius: 8px;
      font-weight: 600;
      font-size: 0.875rem;
      text-decoration: none;
      transition: all 0.2s;
      border: none;
      cursor: pointer;
      min-height: 44px;
    }
    
    @media (max-width: 640px) {
      .btn {
        width: 100%;
        padding: 0.875rem 1rem;
        font-size: 0.875rem;
        min-height: 48px;
      }
      
      .btn-group {
        display: flex;
        flex-direction: column;
        gap: 0.75rem;
      }
    }
    
    @media (min-width: 641px) {
      .btn {
        width: auto;
      }
      
      .btn-group {
        display: flex;
        gap: 0.75rem;
      }
    }
    
    /* Search Input Responsive */
    .nav-input {
      background: rgba(255, 255, 255, 0.18);
      border: 1px solid rgba(255, 255, 255, 0.35);
      padding: 0.5rem 1rem;
      border-radius: 8px;
      color: white;
      font-size: 0.875rem;
      transition: all 0.2s;
    }
    
    .nav-input::placeholder {
      color: rgba(255, 255, 255, 0.7);
    }
    
    .nav-input:focus {
      outline: none;
      background: rgba(255, 255, 255, 0.25);
      border-color: rgba(255, 255, 255, 0.5);
    }
    
    @media (max-width: 640px) {
      .nav-input {
        display: none;
      }
    }
    
    @media (min-width: 641px) and (max-width: 1024px) {
      .nav-input {
        width: 250px;
        font-size: 0.875rem;
      }
    }
    
    @media (min-width: 1025px) {
      .nav-input {
        width: 350px;
        font-size: 0.875rem;
      }
    }
    
    /* Context Bar Responsive */
    #contextBar {
      background: rgba(255, 255, 255, 0.9);
      backdrop-filter: blur(8px);
      border-bottom: 1px solid var(--card-border);
      padding: 0 1rem;
    }
    
    html.dark #contextBar {
      background: rgba(15, 23, 42, 0.9);
    }
    
    @media (max-width: 640px) {
      #contextBar {
        padding: 0 0.75rem;
      }
      
      #contextBar .mx-auto {
        padding: 0;
      }
      
      #contextTabs {
        flex-wrap: wrap;
        gap: 0.5rem;
      }
      
      .tab-pill {
        font-size: 0.75rem;
        padding: 0.4rem 0.8rem;
        min-height: 36px;
      }
    }
    
    /* Modal Responsive */
    .modal-panel {
      width: min(680px, 92vw);
      max-height: 90vh;
      overflow-y: auto;
    }
    
    @media (max-width: 640px) {
      .modal-panel {
        width: calc(100vw - 2rem);
        margin: 1rem;
        max-height: calc(100vh - 2rem);
      }
    }
    
    /* Toast Responsive */
    .toast-card {
      max-width: 400px;
      word-wrap: break-word;
    }
    
    @media (max-width: 640px) {
      .toast-card {
        max-width: calc(100vw - 2rem);
        margin: 0 1rem;
      }
    }
    
    /* Notification Panel Responsive */
    .notification-panel {
      width: 320px;
      max-height: 400px;
    }
    
    @media (max-width: 640px) {
      .notification-panel {
        width: calc(100vw - 2rem);
        right: 1rem;
        left: 1rem;
        max-height: calc(100vh - 8rem);
      }
    }
    
    /* Profile Menu Responsive */
    #profileMenu {
      width: 224px;
      max-height: 400px;
      overflow-y: auto;
    }
    
    @media (max-width: 640px) {
      #profileMenu {
        width: calc(100vw - 2rem);
        right: 1rem;
        max-height: calc(100vh - 8rem);
      }
    }
    
    /* Sidebar Responsive Improvements */
    .sidebar {
      transition: transform 0.3s ease;
    }
    
    @media (max-width: 768px) {
      .sidebar {
        position: fixed;
        left: 0;
        top: 3.5rem;
        width: 100%;
        height: calc(100vh - 3.5rem);
        transform: translateX(-100%);
        z-index: 50;
        background: white;
        border-right: none;
        border-bottom: 1px solid var(--card-border);
      }
      
      html.dark .sidebar {
        background: #1f2937;
        border-bottom-color: #374151;
      }
      
      .sidebar.active {
        transform: translateX(0);
      }
      
      .overlay.active {
        display: block;
        position: fixed;
        inset: 0;
        background: rgba(0, 0, 0, 0.35);
        z-index: 40;
      }
    }
    
    @media (min-width: 769px) {
      .sidebar {
        position: static;
        transform: none;
        width: 240px;
        height: auto;
      }
      
      .overlay {
        display: none !important;
      }
    }
    
    /* Mobile Navigation Responsive */
    .mobile-nav {
      display: none;
      position: fixed;
      bottom: 0;
      left: 0;
      right: 0;
      background: white;
      border-top: 1px solid #e5e7eb;
      z-index: 50;
      padding: 0.5rem;
      box-shadow: 0 -4px 20px rgba(0, 0, 0, 0.1);
    }
    
    html.dark .mobile-nav {
      background: #1f2937;
      border-top-color: #374151;
      box-shadow: 0 -4px 20px rgba(0, 0, 0, 0.3);
    }
    
    .mobile-nav.active {
      display: block;
    }
    
    .mobile-nav-grid {
      display: grid;
      grid-template-columns: repeat(5, 1fr);
      gap: 0.5rem;
    }
    
    .mobile-nav-item {
      display: flex;
      flex-direction: column;
      align-items: center;
      padding: 0.5rem;
      color: #6b7280;
      text-decoration: none;
      font-size: 0.75rem;
      transition: color 0.2s;
      border-radius: 8px;
      min-height: 60px;
      justify-content: center;
    }
    
    .mobile-nav-item.active {
      color: var(--brand);
      background: rgba(15, 28, 73, 0.1);
    }
    
    .mobile-nav-item:hover {
      color: var(--brand);
    }
    
    html.dark .mobile-nav-item {
      color: #9ca3af;
    }
    
    html.dark .mobile-nav-item.active {
      color: #60a5fa;
      background: rgba(96, 165, 250, 0.1);
    }
    
    html.dark .mobile-nav-item:hover {
      color: #60a5fa;
    }
    
    @media (max-width: 640px) {
      .mobile-nav {
        padding: 0.25rem;
      }
      
      .mobile-nav-item {
        font-size: 0.7rem;
        min-height: 56px;
        padding: 0.4rem;
      }
      
      .mobile-nav-item span:first-child {
        font-size: 1.25rem;
      }
    }
    
    @media (min-width: 641px) and (max-width: 768px) {
      .mobile-nav-item {
        font-size: 0.75rem;
        min-height: 60px;
      }
    }
    
    @media (min-width: 769px) {
      .mobile-nav {
        display: none !important;
      }
    }
    
    /* Print Styles */
    @media print {
      .navbar,
      .sidebar,
      .mobile-nav,
      .notification-panel,
      .modal,
      .toast,
      .overlay {
        display: none !important;
      }
      
      .card {
        border: 1px solid #000;
        box-shadow: none;
        break-inside: avoid;
      }
      
      .table-responsive {
        overflow: visible;
        border: none;
      }
      
      .table-responsive table {
        border-collapse: collapse;
      }
      
      .table-responsive th,
      .table-responsive td {
        border: 1px solid #000;
        padding: 0.5rem;
      }
      
      body {
        background: white !important;
        color: black !important;
      }
      
      .container {
        max-width: none;
        padding: 0;
      }
    }
    
    /* Fix for main content area to use full width */
    main {
      width: 100%;
      max-width: none;
    }
    
    #contentHost {
      width: 100%;
      max-width: none;
    }
    
    /* Ensure cards use full width */
    .card {
      width: 100%;
      max-width: none;
      margin: 0;
    }
    
    /* Fix filter grid layout */
    .filter-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
      gap: 1rem;
      width: 100%;
    }
    
    @media (max-width: 1023px) {
      .filter-grid {
        grid-template-columns: 1fr;
      }
    }
    
    /* Ensure table uses full width */
    .table-responsive {
      width: 100%;
      max-width: none;
    }
    
    .table-responsive table {
      width: 100%;
      max-width: none;
    }
    
    /* Fix summary grid to use full width */
    .summary-grid {
      width: 100%;
      max-width: none;
    }
    
    /* Ensure all form elements use full width */
    .filter-grid select,
    .filter-grid input {
      width: 100%;
      max-width: none;
    }
    
    /* Fix button layout in filters */
    .filter-grid .flex {
      grid-column: 1 / -1;
      display: flex;
      gap: 1rem;
    }
    
    .filter-grid .flex .btn {
      flex: 1;
    }
  </style>
</head>
<body class="min-h-screen text-[15px] bg-soft">

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
         <h2 class="text-2xl font-bold text-white mb-2">Loading Dashboard</h2>
         <p class="text-slate-300 text-sm">Preparing your financial overview...</p>
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

  <div id="toast" class="fixed top-4 right-4 z-[120] hidden"></div>

  <header class="sticky top-0 z-30 border-b border-[var(--ring)] navbar backdrop-blur">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 h-14 flex items-center gap-3">
             <button id="openSidebar" class="md:hidden p-2 rounded hover:bg-white/20 transition-all duration-300 hover:scale-105" aria-label="Open menu">
         <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h10"/></svg>
       </button>

      <a href="index.php" class="flex items-center gap-3">
        <img src="logo2.png" alt="ATIÉRA" class="h-8 w-auto sm:h-10" draggable="false">
        <span class="font-extrabold tracking-wide text-lg">ATIERA</span>
      </a>

      <div class="ml-auto flex items-center gap-2">
        <input id="searchInput" placeholder="Search modules, cards, rows…" class="nav-input text-sm w-72 outline-none"/>
      </div>

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
           <img src="../uploads/notif-bell.png" alt="Notifications" class="w-5 h-5 object-contain">
          <!-- Notification Badge -->
          <span id="notificationBadge" class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center hidden">0</span>
        </button>
        
        <!-- Notification Panel -->
        <div id="notificationPanel" class="hidden absolute right-0 mt-2 w-80 bg-white dark:bg-gray-900 rounded-lg shadow-xl border border-gray-200 dark:border-gray-700 overflow-hidden z-50">
          <div class="px-4 py-3 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800">
            <div class="flex items-center justify-between">
              <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Notifications</h3>
              <button onclick="clearAllNotifications()" class="text-xs text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 transition-colors">Clear All</button>
            </div>
          </div>
          <div id="notificationList" class="max-h-64 overflow-y-auto">
            <div class="p-4 text-center text-gray-500 dark:text-gray-400 text-sm">
              No new notifications
            </div>
          </div>
        </div>
      </div>

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

  <div id="contextBar" class="sticky top-14 z-20 hidden border-b border-[var(--ring)] bg-white/90 backdrop-blur">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 h-12 flex items-center justify-between">
      <div class="flex items-center gap-3">
        <span id="contextTitle" class="font-semibold"></span>
      </div>
      <nav id="contextTabs" class="flex flex-wrap gap-2"></nav>
    </div>
  </div>
<br><br>
  <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 grid grid-cols-1 md:grid-cols-[240px_1fr] gap-6 py-6" style="margin-top: 0;">
    <div id="overlay" class="overlay"></div>

         <aside id="sidebar" class="fixed md:static left-0 top-14 md:top-auto w-64 md:w-full h-[calc(100vh-56px)] md:h-auto bg-white border-r border-[var(--card-border)] sidebar-transition -translate-x-full md:translate-x-0 z-40 overflow-y-auto">
      <nav id="sidebarNav" class="p-3 space-y-1">
        <div class="text-[11px] uppercase tracking-widest text-slate-500 px-2 pt-2 pb-1">Navigation</div>

        <a class="sidebar-item active" href="index.php"><span>🏠</span><span>Dashboard</span></a>
        <a class="sidebar-item" href="General Ledger.php"><span>📘</span><span>General Ledger</span></a>
        <a class="sidebar-item" href="Accounts Receivable.php"><span>💳</span><span>Accounts Receivable</span></a>
        <a class="sidebar-item" href="Collections.php"><span>🧾</span><span>Collections</span></a>
        <a class="sidebar-item" href="Accounts Payable.php"><span>📄</span><span>Accounts Payable</span></a>
        <a class="sidebar-item" href="Disbursement.php"><span>💸</span><span>Disbursement</span></a>
        <a class="sidebar-item" href="Budget Management.php"><span>📊</span><span>Budget Management</span></a>
        <a class="sidebar-item" href="Reports.php"><span>📑</span><span>Reports</span></a>
      </nav>
    </aside>

         <main class="space-y-6 overflow-y-auto max-h-[calc(100vh-8rem)] pr-2">
      <section id="dashboardHome" class="space-y-6">
                 <!-- Secondary Navigation Bar -->
         <div class="mb-6">
           <div class="flex items-center justify-between">
             <h1 class="text-2xl font-bold text-slate-800">Financial Dashboard</h1>
           </div>
         </div>
         
         <!-- Main Content Header -->
         <div class="mb-6">
           <div class="flex items-center justify-between mb-4">
             <div>
               <div class="text-sm text-slate-500 mb-1">FINANCIAL DASHBOARD</div>
               <h2 class="text-3xl font-bold text-slate-800">Overview</h2>
             </div>
             <div class="btn-group">
                               <button id="refreshDashboard" class="btn btn-brand flex items-center gap-2 transition-all duration-300 hover:scale-105">
                 <svg id="refreshIcon" class="w-4 h-4 transition-transform duration-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                   <path stroke-linecap="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                 </svg>
                 <span id="refreshText">Refresh</span>
               </button>
               <button class="btn btn-soft flex items-center gap-2">
                 <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                   <path stroke-linecap="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2zm0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                 </svg>
                 Export
               </button>
             </div>
           </div>
         </div>
         
         <!-- Context Bar with Last Updated -->
         <div class="card p-4 mb-6">
           <div class="flex items-center justify-between">
             <div class="flex items-center gap-4">
               <span class="text-sm text-slate-500">Last updated: <span id="lastUpdated" class="font-semibold text-slate-700"><?php echo date('M d, Y H:i:s'); ?></span></span>
             </div>
             <div class="flex items-center gap-2">
               <span class="text-xs text-slate-400">Overview • Real-time data</span>
             </div>
           </div>
         </div>
        
        <!-- Quick Actions - Moved to top for better UX -->
        <div class="card p-6">
          <h3 class="text-lg font-semibold mb-4">Quick Actions</h3>
          <div class="quick-actions-grid">
            <button onclick="window.location.href='General Ledger.php'" class="p-4 border border-slate-200 rounded-lg hover:bg-slate-50 transition-colors text-left">
              <div class="text-blue-600 mb-2">
                <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
              </div>
              <div class="font-medium">New Journal Entry</div>
              <div class="text-sm text-slate-500">Record transactions</div>
            </button>
            
            <button onclick="window.location.href='Accounts Receivable.php'" class="p-4 border border-slate-200 rounded-lg hover:bg-slate-50 transition-colors text-left">
              <div class="text-green-600 mb-2">
                <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                </svg>
              </div>
              <div class="font-medium">Record Receivable</div>
              <div class="text-sm text-slate-500">Track money owed</div>
            </button>
            
            <button onclick="window.location.href='Accounts Payable.php'" class="p-4 border border-slate-200 rounded-lg hover:bg-slate-50 transition-colors text-left">
              <div class="text-red-600 mb-2">
                <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
              </div>
              <div class="font-medium">Record Payable</div>
              <div class="text-sm text-slate-500">Track money owed</div>
            </button>
            
            <button onclick="window.location.href='Reports.php'" class="p-4 border border-slate-200 rounded-lg hover:bg-slate-50 transition-colors text-left">
              <div class="text-purple-600 mb-2">
                <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
              </div>
              <div class="font-medium">Generate Reports</div>
              <div class="text-sm text-slate-500">Financial analysis</div>
            </button>
          </div>
        </div>
        
                 <div id="kpiGrid" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
           <div class="card p-4 relative overflow-hidden dashboard-card" data-title="cash">
             <div class="absolute inset-x-0 top-0 top-chip"></div>
             <div class="text-xs uppercase tracking-wide">Cash Balance</div>
             <div class="text-2xl font-extrabold mt-1" id="cashBalanceValue"><?php echo formatCurrency($data['cash_balance']); ?></div>
             <div class="text-xs text-slate-500">Current Balance</div>
             <button class="mt-3 btn btn-brand hover:scale-105 transition-transform duration-200" onclick="window.location.href='General Ledger.php'">View Ledger ▸</button>
           </div>
           <div class="card p-4 relative overflow-hidden dashboard-card" data-title="ar outstanding">
             <div class="absolute inset-x-0 top-0 top-chip"></div>
             <div class="text-xs uppercase tracking-wide">AR Outstanding</div>
             <div class="text-2xl font-extrabold mt-1" id="arOutstandingValue"><?php echo formatCurrency($data['ar_outstanding']); ?></div>
             <div class="text-xs text-slate-500">Accounts Receivable</div>
             <button class="mt-3 btn btn-brand hover:scale-105 transition-transform duration-200" onclick="window.location.href='Accounts Receivable.php'">View AR ▸</button>
           </div>
           <div class="card p-4 relative overflow-hidden dashboard-card" data-title="ap outstanding">
             <div class="absolute inset-x-0 top-0 top-chip"></div>
             <div class="text-xs uppercase tracking-wide">AP Outstanding</div>
             <div class="text-2xl font-extrabold mt-1" id="apOutstandingValue"><?php echo formatCurrency($data['ap_outstanding']); ?></div>
             <div class="text-xs text-slate-500">Accounts Payable</div>
             <button class="mt-3 btn btn-brand hover:scale-105 transition-transform duration-200" onclick="window.location.href='Accounts Payable.php'">View AP ▸</button>
           </div>
           <div class="card p-4 relative overflow-hidden dashboard-card" data-title="revenue mtd">
             <div class="absolute inset-x-0 top-0 top-chip"></div>
             <div class="text-xs uppercase tracking-wide">Revenue (MTD)</div>
             <div class="text-2xl font-extrabold mt-1" id="revenueMTDValue"><?php echo formatCurrency($data['revenue_mtd']); ?></div>
             <div class="text-xs text-slate-500"><?php echo $currentMonthName; ?></div>
             <button class="mt-3 btn btn-brand hover:scale-105 transition-transform duration-200" onclick="window.location.href='Reports.php'">View Reports ▸</button>
           </div>
         </div>

        <!-- Monthly Summary Cards -->
        <div class="monthly-summary-grid mb-5">
          <div class="card p-4 text-center">
            <div class="text-xs uppercase tracking-wide text-slate-500">Total Revenue</div>
            <div class="text-2xl font-bold text-green-600 mt-1"><?php echo formatCurrency($data['monthly_summary']['total_revenue']); ?></div>
            <div class="text-xs text-slate-500"><?php echo $currentMonthName; ?></div>
          </div>
          <div class="card p-4 text-center">
            <div class="text-xs uppercase tracking-wide text-slate-500">Total Expenses</div>
            <div class="text-2xl font-bold text-red-600 mt-1"><?php echo formatCurrency($data['monthly_summary']['total_expenses']); ?></div>
            <div class="text-xs text-slate-500"><?php echo $currentMonthName; ?></div>
          </div>
          <div class="card p-4 text-center">
            <div class="text-xs uppercase tracking-wide text-slate-500">Net Income</div>
            <div class="text-2xl font-bold <?php echo $data['monthly_summary']['net_income'] >= 0 ? 'text-green-600' : 'text-red-600'; ?> mt-1">
              <?php echo formatCurrency($data['monthly_summary']['net_income']); ?>
            </div>
            <div class="text-xs text-slate-500"><?php echo $currentMonthName; ?></div>
          </div>
        </div>

        <div class="chart-table-layout">
                     <div class="card p-0 lg:col-span-1 overflow-hidden">
             <div class="px-5 pt-4 pb-2 flex items-center justify-between">
               <h3 class="font-semibold">Sales Trend</h3>
               <div class="flex gap-1">
                 <button class="tab-pill active" data-period="7d">7D</button>
                 <button class="tab-pill" data-period="30d">30D</button>
                 <button class="tab-pill" data-period="90d">90D</button>
               </div>
             </div>
             <div class="p-5 pt-0 relative">
               <div id="chartLoader" class="absolute inset-0 flex items-center justify-center bg-white/80 backdrop-blur-sm z-10">
                 <div class="flex flex-col items-center gap-3">
                   <div class="w-8 h-8 border-4 border-blue-200 border-t-blue-500 rounded-full animate-spin"></div>
                   <p class="text-sm text-slate-600">Loading chart...</p>
                 </div>
               </div>
               <canvas id="salesChart" height="200"></canvas>
             </div>
           </div>

          <div class="card p-0 overflow-hidden">
            <div class="px-5 pt-4 pb-2 flex items-center justify-between">
              <h3 class="font-semibold">Recent Transactions</h3>
              <a href="#" class="text-sm text-blue-600 hover:underline">View All</a>
            </div>
            <div class="table-responsive">
              <table class="w-full text-sm">
                <thead class="bg-slate-50">
                  <tr>
                    <th class="text-left p-3 font-medium">Date</th>
                    <th class="text-left p-3 font-medium">Description</th>
                    <th class="text-left p-3 font-medium">Type</th>
                    <th class="text-right p-3 font-medium">Amount</th>
                  </tr>
                </thead>
                <tbody>
                  <?php if (!empty($data['recent_transactions'])): ?>
                    <?php foreach ($data['recent_transactions'] as $transaction): ?>
                      <tr class="border-b border-slate-100">
                        <td class="p-3"><?php echo date('Y-m-d', strtotime($transaction['date'])); ?></td>
                        <td class="p-3"><?php echo htmlspecialchars($transaction['description']); ?></td>
                        <td class="p-3">
                          <?php if ($transaction['type'] === 'Income'): ?>
                            <span class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-xs">Income</span>
                          <?php elseif ($transaction['type'] === 'Expense'): ?>
                            <span class="px-2 py-1 bg-red-100 text-red-800 rounded-full text-xs">Expense</span>
                          <?php else: ?>
                            <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded-full text-xs">Transaction</span>
                          <?php endif; ?>
                        </td>
                        <td class="p-3 text-right">
                          <?php 
                          $amount = $transaction['amount'];
                          $prefix = ($transaction['entry_type'] === 'debit' && $transaction['type'] === 'Expense') ? '-' : '';
                          echo $prefix . formatCurrency($amount); 
                          ?>
                        </td>
                      </tr>
                    <?php endforeach; ?>
                  <?php else: ?>
                    <tr>
                      <td colspan="4" class="p-3 text-center text-slate-500">No recent transactions found</td>
                    </tr>
                  <?php endif; ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </section>
    </main>
  </div>

  <script>
    const $ = (s, r=document)=>r.querySelector(s);
    
    // Dashboard data from PHP
    const dashboardData = {
      salesTrend: <?php echo json_encode($data['sales_trend']); ?>,
      currentMonth: '<?php echo $currentMonthName; ?>',
      cashBalance: <?php echo $data['cash_balance']; ?>,
      arOutstanding: <?php echo $data['ar_outstanding']; ?>,
      apOutstanding: <?php echo $data['ap_outstanding']; ?>,
      revenueMTD: <?php echo $data['revenue_mtd']; ?>
    };
    
         function updateClock() {
       const now = new Date();
       const dateStr = now.toLocaleDateString('en-US', { 
         weekday: 'short', 
         month: 'short', 
         day: 'numeric' 
       });
       const timeStr = now.toLocaleTimeString('en-US', { 
         hour12: false, 
         hour: '2-digit', 
         minute: '2-digit',
         second: '2-digit'
       });
       
       $('#liveDate').textContent = dateStr;
       $('#liveTime').textContent = timeStr;
       $('#liveDateMobile').textContent = dateStr;
       $('#liveTimeMobile').textContent = timeStr;
     }

         function initCharts() {
       const ctx = document.getElementById('salesChart').getContext('2d');
       const chartLoader = $('#chartLoader');
       
       // Process sales trend data
       const salesData = dashboardData.salesTrend;
       const labels = salesData.map(item => {
         const date = new Date(item.date);
         return date.toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
       }).reverse();
       const data = salesData.map(item => item.sales).reverse();
       
       // Simulate chart loading delay
       setTimeout(() => {
         new Chart(ctx, {
           type: 'line',
           data: {
             labels: labels.length > 0 ? labels : ['No Data'],
             datasets: [{
               label: 'Sales',
               data: data.length > 0 ? data : [0],
               borderColor: '#0f1c49',
               backgroundColor: 'rgba(15, 28, 73, 0.1)',
               tension: 0.4,
               fill: true
             }]
           },
           options: {
             responsive: true,
             maintainAspectRatio: false,
             plugins: { 
               legend: { display: false },
               tooltip: {
                 callbacks: {
                   label: function(context) {
                     return '₱' + context.parsed.y.toLocaleString('en-US', { minimumFractionDigits: 2 });
                   }
                 }
               }
             },
             scales: {
               y: { 
                 beginAtZero: true, 
                 grid: { display: false },
                 ticks: {
                   callback: function(value) {
                     return '₱' + value.toLocaleString('en-US');
                   }
                 }
               },
               x: { grid: { display: false } }
             }
           }
         });
         
         // Hide chart loader with fade out
         if (chartLoader) {
           chartLoader.style.opacity = '0';
           setTimeout(() => {
             chartLoader.style.display = 'none';
           }, 300);
         }
       }, 800); // 800ms delay for better UX
     }

    function initSidebar() {
      const sidebar = $('#sidebar');
      const overlay = $('#overlay');
      const openBtn = $('#openSidebar');
      
      openBtn.addEventListener('click', () => {
        sidebar.classList.remove('-translate-x-full');
        overlay.classList.add('active');
      });
      
      overlay.addEventListener('click', () => {
        sidebar.classList.add('-translate-x-full');
        overlay.classList.remove('active');
      });
    }

    function initProfileMenu() {
      const profileBtn = $('#profileBtn');
      const profileMenu = $('#profileMenu');
      
      profileBtn.addEventListener('click', () => {
        profileMenu.classList.toggle('hidden');
      });
      
      document.addEventListener('click', (e) => {
        if (!profileBtn.contains(e.target) && !profileMenu.contains(e.target)) {
          profileMenu.classList.add('hidden');
        }
      });
    }

    function initSearch() {
      const searchInput = $('#searchInput');
      const dashboardCards = document.querySelectorAll('.dashboard-card');
      
      searchInput.addEventListener('input', (e) => {
        const query = e.target.value.toLowerCase();
        
        dashboardCards.forEach(card => {
          const title = card.dataset.title.toLowerCase();
          if (title.includes(query)) {
            card.style.display = 'block';
          } else {
            card.style.display = 'none';
          }
        });
      });
    }

    // Dark mode functionality
    function initDarkMode() {
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
      function initDarkModeOnLoad() {
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
    initDarkModeOnLoad();
  }
  
     // Dashboard refresh functionality
   function initDashboardRefresh() {
     const refreshBtn = $('#refreshDashboard');
     const lastUpdated = $('#lastUpdated');
     const refreshIcon = $('#refreshIcon');
     const refreshText = $('#refreshText');
     
     if (refreshBtn) {
       refreshBtn.addEventListener('click', function() {
         // Show loading state
         this.disabled = true;
         refreshIcon.classList.add('animate-spin');
         refreshText.textContent = 'Refreshing...';
         
         // Simulate loading with progress
         let progress = 0;
         const progressInterval = setInterval(() => {
           progress += 10;
           if (progress >= 100) {
             clearInterval(progressInterval);
             // Refresh the page after loading animation
             setTimeout(() => {
               window.location.reload();
             }, 500);
           }
         }, 100);
       });
     }
     
     // Auto-refresh every 5 minutes
     setInterval(() => {
       if (lastUpdated) {
         const now = new Date();
         lastUpdated.textContent = now.toLocaleDateString('en-US', { 
           month: 'short', 
           day: 'numeric', 
           year: 'numeric' 
         }) + ' ' + now.toLocaleTimeString('en-US', { 
           hour: '2-digit', 
           minute: '2-digit', 
           second: '2-digit' 
         });
       }
     }, 5000);
   }

    // Debug profile image loading
    document.addEventListener('DOMContentLoaded', function() {
      const profileImg = document.querySelector('#profileBtn img');
      if (profileImg) {
        console.log('Profile image src:', profileImg.src);
        console.log('Profile image complete:', profileImg.complete);
        console.log('Profile image naturalWidth:', profileImg.naturalWidth);
        console.log('Profile image naturalHeight:', profileImg.naturalHeight);
        
        // Test if image loads
        profileImg.addEventListener('load', function() {
          console.log('Profile image loaded successfully');
        });
        
        profileImg.addEventListener('error', function() {
          console.log('Profile image failed to load');
        });
      }
    });

         // Dashboard tabs functionality
     function initDashboardTabs() {
       const tabButtons = document.querySelectorAll('[data-tab]');
       
       tabButtons.forEach(button => {
         button.addEventListener('click', () => {
           const targetTab = button.dataset.tab;
           
           // Update active tab button
           tabButtons.forEach(btn => btn.classList.remove('active'));
           button.classList.add('active');
           
           // Show corresponding content (for now, all content is visible)
           // You can add tab-specific content later if needed
           console.log('Switched to tab:', targetTab);
         });
       });
     }
     
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
                // Animate KPI values in
                animateKPIValues();
              }, 300);
            }, 200);
          }
          progressBar.style.width = progress + '%';
        }, 100);
      }
      
      // Animate KPI values with counting effect
      function animateKPIValues() {
        const kpiElements = [
          { element: $('#cashBalanceValue'), value: dashboardData.cashBalance },
          { element: $('#arOutstandingValue'), value: dashboardData.arOutstanding },
          { element: $('#apOutstandingValue'), value: dashboardData.apOutstanding },
          { element: $('#revenueMTDValue'), value: dashboardData.revenueMTD }
        ];
        
        kpiElements.forEach((kpi, index) => {
          setTimeout(() => {
            if (kpi.element) {
              kpi.element.style.opacity = '0';
              kpi.element.style.transform = 'translateY(10px)';
              
              // Animate counting up
              let current = 0;
              const target = kpi.value;
              const increment = target / 30; // 30 steps
              const countInterval = setInterval(() => {
                current += increment;
                if (current >= target) {
                  current = target;
                  clearInterval(countInterval);
                  
                  // Show final value with animation
                  kpi.element.style.opacity = '1';
                  kpi.element.style.transform = 'translateY(0)';
                  kpi.element.style.transition = 'all 0.5s ease-out';
                }
                kpi.element.textContent = '₱' + Math.floor(current).toLocaleString('en-US');
              }, 20);
            }
          }, index * 200); // Stagger the animations
        });
      }
      
      document.addEventListener('DOMContentLoaded', () => {
        // Start loading screen
        initLoadingScreen();
        
        updateClock();
        setInterval(updateClock, 1000);
        initCharts();
        initSidebar();
        initProfileMenu();
        initSearch();
        initDarkMode();
        initDashboardTabs();
        initDashboardRefresh();
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
          icon: '💰',
          color: 'green',
          title: 'Financial Update',
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

      // Refresh notification styles when theme changes
      function refreshNotificationStyles() {
        // Force re-render of notification list to apply new theme classes
        if (notifications.length > 0) {
          updateNotificationList();
        }
      }

      // Initialize notification system
      function initNotificationSystem() {
        // Load existing notifications
        loadNotifications();
        
        // Simulate real-time notifications for dashboard
        simulateDashboardNotifications();
        
        // Listen for theme changes
        const observer = new MutationObserver((mutations) => {
          mutations.forEach((mutation) => {
            if (mutation.type === 'attributes' && mutation.attributeName === 'class') {
              refreshNotificationStyles();
            }
          });
        });
        
        // Observe the html element for class changes (dark mode toggle)
        observer.observe(document.documentElement, {
          attributes: true,
          attributeFilter: ['class']
        });
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
        
        // Also show toast notification
        showToast(message, type === 'system' ? 'info' : type === 'financial' ? 'success' : 'info', config.duration);
      }

      // Update notification badge
      function updateNotificationBadge() {
        const badge = $('#notificationBadge');
        const bell = $('#notificationBell');
        
        if (notificationCount > 0) {
          badge.textContent = notificationCount > 99 ? '99+' : notificationCount;
          badge.classList.remove('hidden');
          
          // Add bell shake animation
          bell.classList.add('has-notifications');
          // Remove the class after animation to allow it to repeat
          setTimeout(() => {
            bell.classList.remove('has-notifications');
          }, 500);
        } else {
          badge.classList.add('hidden');
          bell.classList.remove('has-notifications');
        }
      }

      // Update notification list
      function updateNotificationList() {
        const list = $('#notificationList');
        if (notifications.length === 0) {
          list.innerHTML = '<div class="p-4 text-center text-gray-500 dark:text-gray-400 text-sm">No new notifications</div>';
          return;
        }

        list.innerHTML = notifications.map(notification => `
          <div class="p-3 border-b border-gray-100 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">
            <div class="flex items-start gap-3">
              <span class="text-lg">${notification.icon}</span>
              <div class="flex-1 min-w-0">
                <p class="text-sm font-medium text-gray-900 dark:text-gray-100">${notificationTypes[notification.type].title}</p>
                <p class="text-sm text-gray-600 dark:text-gray-300 mt-1">${notification.message}</p>
                <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">${notification.time}</p>
              </div>
              <button onclick="removeNotification(${notification.id})" class="text-gray-400 dark:text-gray-500 hover:text-gray-600 dark:hover:text-gray-300 transition-colors">
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

      // Simulate dashboard-specific notifications
      function simulateDashboardNotifications() {
        // Financial updates (every 2 minutes)
        setInterval(() => {
          if (Math.random() < 0.2) { // 20% chance
            const updates = [
              'Cash balance updated: ₱' + Math.floor(Math.random() * 100000 + 50000).toLocaleString('en-US'),
              'New AR transaction: Invoice #' + Math.floor(Math.random() * 1000 + 100) + ' for ₱' + Math.floor(Math.random() * 50000 + 10000).toLocaleString('en-US'),
              'AP payment processed: ₱' + Math.floor(Math.random() * 30000 + 5000).toLocaleString('en-US') + ' to vendor',
              'Revenue milestone: Monthly target reached at ' + Math.floor(Math.random() * 20 + 80) + '%'
            ];
            const randomUpdate = updates[Math.floor(Math.random() * updates.length)];
            addNotificationToPanel('financial', randomUpdate);
          }
        }, 120000);

        // System alerts (every 3 minutes)
        setInterval(() => {
          if (Math.random() < 0.15) { // 15% chance
            const alerts = [
              'Dashboard data refreshed successfully',
              'New user login detected',
              'System performance: All modules running optimally',
              'Database connection: Stable and responsive'
            ];
            const randomAlert = alerts[Math.floor(Math.random() * alerts.length)];
            addNotificationToPanel('system', randomAlert);
          }
        }, 180000);
      }

      // Show toast notification with theme support
      function showToast(message, type = 'info', duration = 5000) {
        const toast = $('#toast');
        const toastId = 'toast-' + Date.now();
        
        const typeConfig = {
          info: { icon: 'ℹ️', bg: 'bg-blue-500', text: 'text-blue-500' },
          success: { icon: '✅', bg: 'bg-green-500', text: 'text-green-500' },
          warning: { icon: '⚠️', bg: 'bg-yellow-500', text: 'text-yellow-500' },
          error: { icon: '❌', bg: 'bg-red-500', text: 'text-red-500' }
        };
        
        const config = typeConfig[type] || typeConfig.info;
        
        const toastHTML = `
          <div id="${toastId}" class="toast-card mb-3 border-l-4 ${config.bg} border-l-current dark:bg-gray-800 dark:border-gray-600">
            <div class="flex items-center gap-3">
              <span class="text-lg">${config.icon}</span>
              <p class="text-sm font-medium text-gray-900 dark:text-gray-100">${message}</p>
              <button onclick="dismissToast('${toastId}')" class="ml-auto text-gray-400 dark:text-gray-500 hover:text-gray-600 dark:hover:text-gray-300">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
              </button>
            </div>
          </div>
        `;
        
        toast.insertAdjacentHTML('beforeend', toastHTML);
        toast.classList.remove('hidden');
        
        // Auto-dismiss after duration
        setTimeout(() => {
          dismissToast(toastId);
        }, duration);
      }
      
      // Dismiss specific toast
      function dismissToast(toastId) {
        const toastElement = document.getElementById(toastId);
        if (toastElement) {
          toastElement.remove();
          
          // Hide toast container if no more toasts
          const toast = $('#toast');
          if (toast.children.length === 0) {
            toast.classList.add('hidden');
          }
        }
      }
      
      // Clear all toasts
      function clearAllToasts() {
        const toast = $('#toast');
        toast.innerHTML = '';
        toast.classList.add('hidden');
      }

      // Close notification panel when clicking outside
      document.addEventListener('click', function(e) {
        const panel = $('#notificationPanel');
        const bell = $('#notificationBell');
        
        if (panel && !panel.contains(e.target) && !bell.contains(e.target)) {
          panel.classList.add('hidden');
        }
      });
      
      // Mobile navigation functionality
      function initMobileNav() {
        const mobileNav = $('#mobileNav');
        const currentPath = window.location.pathname;
        
        // Highlight current page
        const currentItem = mobileNav.querySelector(`[href="${currentPath}"]`);
        if (currentItem) {
          currentItem.classList.add('active');
        }
        
        // Show mobile nav on mobile devices
        if (window.innerWidth <= 768) {
          mobileNav.classList.add('active');
        }
        
        // Handle window resize
        window.addEventListener('resize', () => {
          if (window.innerWidth <= 768) {
            mobileNav.classList.add('active');
          } else {
            mobileNav.classList.remove('active');
          }
        });
      }
      
      // Initialize mobile navigation
      document.addEventListener('DOMContentLoaded', () => {
        initMobileNav();
      });
  </script>

  <!-- Mobile Navigation Bar -->
  <nav id="mobileNav" class="mobile-nav md:hidden">
    <div class="mobile-nav-grid">
      <a href="index.php" class="mobile-nav-item">
        <span class="text-xl">🏠</span>
        <span>Dashboard</span>
      </a>
      <a href="General Ledger.php" class="mobile-nav-item">
        <span class="text-xl">📘</span>
        <span>GL</span>
      </a>
      <a href="Accounts Receivable.php" class="mobile-nav-item">
        <span class="text-xl">💳</span>
        <span>AR</span>
      </a>
      <a href="Budget Management.php" class="mobile-nav-item">
        <span class="text-xl">📊</span>
        <span>Budget</span>
      </a>
      <a href="Reports.php" class="mobile-nav-item">
        <span class="text-xl">📑</span>
        <span>Reports</span>
      </a>
    </div>
  </nav>

</body>
</html>
