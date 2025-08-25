<?php
require_once '../includes/auth.php';
$auth = new Auth();
$auth->requireAuth();

$user = $auth->getCurrentUser();
?>
<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>General Ledger</title>
  <link rel="icon" type="image/png" href="logo2.png">
  <script src="https://cdn.tailwindcss.com"></script>

  <style>
    :root{
      --brand:#0f1c49; --brand-600:#0c173c; --brand-100:#e8ecf9;
      --ink:#000; --muted:#000; --ring:0 0 0 3px rgba(15,28,73,.15);
      --card-bg: rgba(255,255,255,.95); --card-border: rgba(226,232,240,.9);
      --border: rgba(226,232,240,.9);

      /* Watermark tuning */
      --wm-opacity:.08;   /* lighter=.06 / darker=.12 */
      --wm-max-w:900px;   /* absolute width cap */
      --wm-max-h:70vh;    /* vertical cap */
      --wm-scale:0.65;    /* ~65% of table width */
    }
    
    /* Dark mode variables */
    html.dark {
      --ink: #e5e7eb;
      --muted: #9ca3af;
      --card-bg: rgba(17,24,39,.92);
      --card-border: rgba(71,85,105,.55);
      --border: rgba(71,85,105,.55);
    }

    body{ background:#fff; color:var(--ink); }
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

    /* Header / Navbar */
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
    .ledger-card {
      transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    
    .ledger-card:hover {
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
    
    /* Dark Mode Enhanced Styles */
    html.dark .navbar {
      background: linear-gradient(135deg, rgba(15, 28, 73, 0.95) 0%, rgba(12, 23, 60, 0.95) 100%);
      border-bottom-color: rgba(255, 255, 255, 0.1);
    }

    /* Cards / Buttons / Tabs */
    .card{ background:var(--card-bg); border-radius:14px; border:1px solid var(--card-border); box-shadow:0 6px 18px rgba(2,6,23,.04) }
    html.dark .card{ box-shadow:0 16px 48px rgba(0,0,0,.5); }
    .btn{ display:inline-flex; align-items:center; gap:.5rem; padding:.55rem .95rem; border-radius:.65rem; font-weight:600; color:var(--ink) }
    .btn-brand{ background:var(--brand); color:#fff !important } .btn-brand:hover{ background:var(--brand-600) }
    .btn-soft{ background:#fff; border:1px solid var(--card-border) } .btn-soft:hover{ background:#f8fafc }
    html.dark .btn-soft{ background:var(--card-bg); border-color:var(--card-border); color:var(--ink); }
    html.dark .btn-soft:hover{ background:rgba(31,41,55,.92); }
    .tab-pill{ padding:.4rem .8rem; border-radius:9999px; border:1px solid var(--card-border); font-weight:700; font-size:.9rem; color:var(--ink) }
    .tab-pill.active{ background:var(--brand); color:#fff; border-color:var(--brand) }

    /* Sidebar */
    .sidebar-transition{ transition:transform .28s ease }
    .overlay{ display:none } .overlay.active{ display:block; position:fixed; inset:0; background:rgba(0,0,0,.35); z-index:40 }
    .sidebar-item{ display:flex; align-items:center; gap:.6rem; width:100%; padding:.5rem .75rem; border-radius:.6rem; color:var(--ink) }
    .sidebar-item:hover{ background:#f8fafc }
    html.dark .sidebar-item:hover{ background:rgba(31,41,55,.92); }
    .sidebar-item.active{ background:rgba(15,28,73,.06); color:var(--brand); font-weight:700 }

    /* Table */
    th,td{ white-space:nowrap; }
    thead tr{ background:#f8fafc; }
    tbody tr:hover{ background:#f8fafc; }
    html.dark thead tr{ background:rgba(31,41,55,.92); }
    html.dark tbody tr:hover{ background:rgba(31,41,55,.92); }
    .empty-state{ border:2px dashed var(--card-border); border-radius:12px; padding:20px; text-align:center; color:var(--muted); }

    /* ===== PRINT (header + watermark centered on active table) ===== */
    .print-only{ display:none; }
    @media print{
      header,#contextBar,aside,.no-print,#toast{ display:none !important; }
      body{ background:#fff; -webkit-print-color-adjust:exact; print-color-adjust:exact; }
      .card{ box-shadow:none; border:0; }
      .print-only{ display:block !important; }
      thead{ display:table-header-group; }
      @page{ margin:16mm 14mm; }

      /* Print title block */
      .print-header{ border-bottom:1px solid #e5e7eb; margin-bottom:12px; padding-bottom:8px; position:relative; z-index:2; }
      .print-header .title{ font-size:18px; font-weight:800; }
      .print-header .sub{ font-size:12px; color:#334155; }
      .logo-line{ display:flex; align-items:center; gap:12px; }
      .logo-line img{ height:38px; width:auto; }

      /* Active table stays above watermark */
      .table-wrap, .table-wrap table{ position:relative; z-index:1; }

      /* Watermark centered in the table area */
      .table-wrap #printWM{
        display:block !important;
        position:absolute; inset:0; z-index:0; pointer-events:none;
        display:flex; align-items:center; justify-content:center;
      }
      .table-wrap #printWM img{
        width:clamp(260px, 50%, var(--wm-max-w));
        max-height:var(--wm-max-h);
        height:auto;
        opacity:var(--wm-opacity);
        filter:grayscale(12%);
      }
    }
    
    /* Dark mode specific styles */
    html.dark .btn{ color:var(--ink); }
    
    /* Dark mode context bar */
    html.dark #contextBar {
      background: rgba(17,24,39,.92);
      border-color: var(--border);
    }
    
         /* Dark mode sidebar */
     html.dark #sidebar {
       background: var(--card-bg);
       border-color: var(--card-border);
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
    
    html.dark .text-slate-500 {
      color: var(--muted);
    }
    
    /* Dark mode for tables */
    html.dark thead tr {
      background: rgba(31,41,55,.92);
    }
    
    html.dark tbody tr:hover {
      background: rgba(31,41,55,.92);
    }
    
    html.dark .empty-state {
      border-color: var(--border);
      color: var(--muted);
    }
    
    /* Dark mode for table headers */
    html.dark .bg-slate-50 {
      background: rgba(31,41,55,.92);
    }
    
    /* Dark mode for form inputs */
    html.dark .form-input {
      background: #0b1220;
      border-color: #243041;
      color: var(--ink);
    }
    
    /* Dark mode for enhanced filter sections */
    html.dark .bg-slate-50 {
      background: rgba(31,41,55,.92);
      border-color: var(--card-border);
    }
    
    /* Dark mode for filter inputs and selects */
    html.dark input[type="date"],
    html.dark select {
      background: var(--card-bg);
      border-color: var(--card-border);
      color: var(--ink);
    }
    
    html.dark input[type="date"]:focus,
    html.dark select:focus {
      border-color: var(--brand);
      box-shadow: 0 0 0 3px rgba(15,28,73,.15);
    }
    
    /* Dark mode for enhanced table borders */
    html.dark .border-slate-200 {
      border-color: var(--card-border);
    }
    
    html.dark .border-slate-100 {
      border-color: var(--card-border);
    }
    
    /* Dark mode for summary cards */
    html.dark .bg-blue-50 {
      background: rgba(30,58,138,.2);
      border-color: rgba(59,130,246,.3);
    }
    
    html.dark .bg-green-50 {
      background: rgba(5,122,85,.2);
      border-color: rgba(16,185,129,.3);
    }
    
    html.dark .bg-slate-50 {
      background: rgba(51,65,85,.2);
      border-color: rgba(100,116,139,.3);
    }
    
    /* Dark mode for summary card text */
    html.dark .text-blue-800 {
      color: #93c5fd;
    }
    
    html.dark .text-green-800 {
      color: #6ee7b7;
    }
    
    html.dark .text-slate-800 {
      color: #e2e8f0;
    }
    
    html.dark .text-blue-600 {
      color: #60a5fa;
    }
    
    html.dark .text-green-600 {
      color: #34d399;
    }
    
    html.dark .text-slate-600 {
      color: #94a3b8;
    }
    
    /* Dark mode for action buttons */
    html.dark .hover\:bg-blue-50:hover {
      background-color: rgba(30,58,138,.2);
    }
    
    html.dark .hover\:bg-slate-50:hover {
      background-color: rgba(51,65,85,.2);
    }
    
    html.dark .hover\:bg-red-50:hover {
      background-color: rgba(239,68,68,.2);
    }
    
    /* Dark mode for labels */
    html.dark .text-slate-700 {
      color: var(--muted);
    }
    
    /* Dark mode for status text */
    html.dark .text-slate-500 {
      color: var(--muted);
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
      
      .navbar .nav-input {
        display: none;
      }
      
      .navbar .clockWrap {
        display: none;
      }
      
      #contextBar {
        padding: 0 1rem;
      }
      
      #contextTabs {
        flex-wrap: wrap;
        gap: 0.5rem;
      }
      
      .tab-pill {
        font-size: 0.75rem;
        padding: 0.4rem 0.8rem;
      }
      
      .form-grid {
        grid-template-columns: 1fr;
        gap: 1rem;
      }
      
      .form-input {
        font-size: 16px; /* Prevents zoom on iOS */
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
      
      .form-grid {
        grid-template-columns: repeat(2, 1fr);
        gap: 1.5rem;
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
      
      .form-grid {
        grid-template-columns: repeat(3, 1fr);
        gap: 2rem;
      }
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
      
      .form-input {
        min-height: 44px;
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
    }
    
    /* ===== COMPONENT-SPECIFIC RESPONSIVE FIXES ===== */
    
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
    
    /* Sidebar Responsive Improvements */
    .sidebar {
      transition: transform 0.3s ease;
    }
    
    @media (max-width: 1023px) {
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
      
      /* Ensure main content takes full width on mobile/tablet */
      main {
        width: 100% !important;
        grid-column: 1 / -1;
      }
    }
    
    @media (min-width: 1024px) {
      .sidebar {
        position: static;
        transform: none;
        width: 240px;
        height: auto;
      }
      
      .overlay {
        display: none !important;
      }
      
      /* Ensure main content takes remaining width on desktop */
      main {
        width: 100%;
        grid-column: 2;
      }
    }
    
    /* Print Styles */
    @media print {
      .navbar,
      .sidebar,
      .notification-panel,
      .modal,
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
    
    /* Dark mode scrollbar for notifications */
    html.dark #notificationPanel::-webkit-scrollbar {
      width: 6px;
    }
    
    html.dark #notificationPanel::-webkit-scrollbar-track {
      background: #374151;
    }
    
    html.dark #notificationPanel::-webkit-scrollbar-thumb {
      background: #6b7280;
    }
    
    html.dark #notificationPanel::-webkit-scrollbar-thumb:hover {
      background: #9ca3af;
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
          <h2 class="text-2xl font-bold text-white mb-2">Loading General Ledger</h2>
          <p class="text-slate-300 text-sm">Preparing your financial records...</p>
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

  <div id="globalLoader" class="fixed inset-0 z-[100] hidden items-center justify-center bg-slate-900/20">
    <div class="flex flex-col items-center gap-3">
      <svg class="animate-spin h-10 w-10" viewBox="0 0 24 24" style="color:var(--brand)">
        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
      </svg>
      <p class="text-sm">Loading…</p>
    </div>
  </div>

  <div id="toast" class="fixed top-4 right-4 z-[120] hidden"></div>

  <!-- HEADER -->
  <header class="sticky top-0 z-50 border-b border-[var(--ring)] navbar backdrop-blur">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 h-14 flex items-center gap-3">
             <button id="openSidebar" class="md:hidden p-2 rounded hover:bg-white/20 transition-all duration-300 hover:scale-105" aria-label="Open menu">
         <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h10"/></svg>
       </button>

      <a href="index.php" class="flex items-center gap-3">
        <img src="logo2.png" alt="ATIÉRA" class="h-8 w-auto sm:h-10" draggable="false">
        <span class="font-extrabold tracking-wide text-lg">ATIERA</span>
      </a>

      <div class="ml-auto flex items-center gap-2">
        <input id="globalSearch" placeholder="Search modules, cards, rows…" class="nav-input text-sm w-72 outline-none"/>
        <button id="clearSearch" class="hidden px-2 py-1 text-xs bg-white/20 rounded hover:bg-white/30">Clear</button>
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
        <span class="font-semibold text-slate-800">General Ledger</span>
      </div>
      <nav id="contextTabs" class="flex flex-wrap gap-2">
        <a class="tab-pill" href="#General%20Ledger/gl-je">Journal Entries</a>
        <a class="tab-pill" href="#General%20Ledger/gl-tb">Trial Balance</a>
        <a class="tab-pill" href="#General%20Ledger/gl-ledger">Ledger Report</a>
      </nav>
    </div>
  </div>

  <!-- PRINT HEADER (print-only) -->
  <div id="printHeader" class="print-only px-2">  
    <div class="print-header">
      <div class="logo-line">
        <img src="logo.png" alt="ATIERA logo">
        <div> 
          <div class="title" id="printTitle">ATIERA — General Ledger — Journal Entries</div>
          <div class="sub">Prepared <span id="printWhen"></span> • <span id="printContext">All records</span></div>
        </div>
      </div>
    </div>
  </div>

  <!-- PRINT WATERMARK (logo only; moved to active table on print) -->
  <div id="printWM" class="print-only" aria-hidden="true">
    <img src="logo.png" alt="Logo watermark">
  </div>

  <!-- LAYOUT -->
  <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 grid grid-cols-1 lg:grid-cols-[240px_1fr] gap-6 py-6">
    <div id="overlay" class="overlay"></div>

    <!-- SIDEBAR -->
    <aside id="sidebar" class="fixed lg:static left-0 top-14 lg:top-auto w-64 lg:w-full h-[calc(100vh-56px)] lg:h-auto bg-white border-r border-[var(--ring)] sidebar-transition -translate-x-full lg:translate-x-0 z-50 overflow-y-auto">
      <nav class="p-3 space-y-1">
        <div class="text-[11px] uppercase tracking-widest text-slate-500 px-2 pt-2 pb-1">Navigation</div>
        <a class="sidebar-item" href="index.php"><span>🏠</span><span>Dashboard</span></a>

        <a class="sidebar-item active" href="General Ledger.php"><span>📘</span><span>General Ledger</span></a>
        <a class="sidebar-item" href="Accounts Receivable.php"><span>💳</span><span>Accounts Receivable</span></a>
        <a class="sidebar-item" href="Collections.php"><span>🧾</span><span>Collections</span></a>
        <a class="sidebar-item" href="Accounts Payable.php"><span>📄</span><span>Accounts Payable</span></a>
        <a class="sidebar-item" href="Disbursement.php"><span>💸</span><span>Disbursement</span></a>
        <a class="sidebar-item" href="Budget Management.php"><span>📊</span><span>Budget Management</span></a>
        <a class="sidebar-item" href="Reports.php"><span>📑</span><span>Reports</span></a>
      </nav>
    </aside>

    <!-- MAIN -->
    <main class="w-full space-y-6">
      <section id="contentHost" class="space-y-6 w-full"></section>
    </main>
  </div>

  <script>
    /* ===== Tiny helpers ===== */
    const $=(s,r=document)=>r.querySelector(s), $$=(s,r=document)=>Array.from(r.querySelectorAll(s));
    const fmtMoney = (n,curr='PHP') => Number(n).toLocaleString(undefined,{style:'currency',currency:curr});

    /* ===== Sample DATA per tab ===== */
    const DATA = {
      'gl-je': [
        {date:'2025-08-12', ref:'#0005', desc:'Supplies expense — housekeeping', debit:310.00, credit:0},
        {date:'2025-08-09', ref:'#0004', desc:'Utilities expense — electric bill', debit:920.35, credit:0},
        {date:'2025-08-05', ref:'#0003', desc:'F&B revenue — breakfast buffet', debit:0, credit:1330.00},
        {date:'2025-08-03', ref:'#0002', desc:'Room revenue — walk-in guests', debit:0, credit:2450.50},
        {date:'2025-08-01', ref:'#0001', desc:'Opening balance capitalization', debit:1000.00, credit:0},
      ],
      'gl-tb': [
        {account:'Cash', debit:54000, credit:0},
        {account:'Accounts Receivable', debit:12000, credit:0},
        {account:'Accounts Payable', debit:0, credit:8000},
        {account:'Revenue', debit:0, credit:3880.50},
        {account:'Utilities Expense', debit:920.35, credit:0},
        {account:'Supplies Expense', debit:310.00, credit:0},
      ],
      'gl-ledger': [
        {date:'2025-08-01', ref:'LED-001', desc:'Cash — debits/credits rollup', debit:54000.00, credit:0, balance:54000.00},
        {date:'2025-08-01', ref:'LED-002', desc:'AR — guest folios', debit:12000.00, credit:0, balance:66000.00},
        {date:'2025-08-01', ref:'LED-003', desc:'AP — suppliers', debit:0, credit:8000.00, balance:58000.00},
      ]
    };

    /* ===== Loader & Toast (optional UX) ===== */
    const Loader=(()=>{const el=$('#globalLoader');let on=false,t0=0;const MIN=250;
      function show(){if(on) return; on=true;t0=performance.now();el.classList.remove('hidden');el.classList.add('flex');}
      function hide(){if(!on) return;const d=Math.max(0,MIN-(performance.now()-t0));setTimeout(()=>{el.classList.add('hidden');el.classList.remove('flex');on=false;},d);}
      async function wrap(job){show();try{return typeof job==='function'?await job():await job;}finally{hide();}}
      return{show,hide,wrap};
    })();
    const Toast=(msg,ms=1500)=>{const t=$('#toast'); t.innerHTML=`<div class="toast-card bg-white border border-[var(--card-border)] rounded-md px-3 py-2 shadow">${msg}</div>`; t.classList.remove('hidden'); setTimeout(()=>t.classList.add('hidden'), ms);};

    /* ===== Sidebar, profile, theme, clock ===== */
    const overlay=$('#overlay'), sidebar=$('#sidebar');
    $('#openSidebar')?.addEventListener('click', ()=>{sidebar.classList.remove('-translate-x-full'); overlay.classList.add('active');});
    overlay?.addEventListener('click', ()=>{sidebar.classList.add('-translate-x-full'); overlay.classList.remove('active');});
    const pBtn=$('#profileBtn'), pMenu=$('#profileMenu');
    pBtn?.addEventListener('click', (e)=>{ e.stopPropagation(); pMenu.classList.toggle('hidden');});
    document.addEventListener('click', (e)=>{ if(pBtn && pMenu && !pBtn.contains(e.target) && !pMenu.contains(e.target)) pMenu.classList.add('hidden'); });
    $('#darkModeToggle')?.addEventListener('click', ()=>{ document.documentElement.classList.toggle('dark'); document.body.classList.toggle('bg-soft'); });
    (function clock(){
      const t=$('#liveTime'), d=$('#liveDate'), tm=$('#liveTimeMobile'), dm=$('#liveDateMobile'); let is24=localStorage.getItem('fmt24')==='1';
      const fD=n=>new Intl.DateTimeFormat(undefined,{year:'numeric',month:'short',day:'2-digit',weekday:'short'}).format(n);
      const fT=n=>new Intl.DateTimeFormat(undefined,{hour:'2-digit',minute:'2-digit',second:'2-digit',hour12:!is24}).format(n);
      function tick(){const n=new Date(); if(d) d.textContent=fD(n); if(t) t.textContent=fT(n); if(dm) dm.textContent=fD(n); if(tm) tm.textContent=fT(n);}
      t?.addEventListener('click',()=>{is24=!is24; localStorage.setItem('fmt24',is24?'1':'0'); tick();});
      tick(); setInterval(tick,1000); $('#clockWrap')?.classList.remove('hidden');
    })();

    /* ===== Tabs (hash-based like your original) ===== */
    const GL_TABS = [
      { id:'gl-je', label:'Journal Entries' },
      { id:'gl-tb', label:'Trial Balance' },
      { id:'gl-ledger', label:'Ledger Report' },
    ];

    function decodeHash(){
      const h=(location.hash||'#General%20Ledger/gl-je').slice(1);
      const [mod, tab]=h.split('/');
      return { module: decodeURIComponent(mod||'General Ledger'), tab: decodeURIComponent(tab||'gl-je') };
    }

    function markActiveTab(tabId){
      $$('#contextTabs .tab-pill').forEach(a=>{
        a.classList.toggle('active', a.getAttribute('href') === `#General%20Ledger/${tabId}`);
      });
    }

    /* ===== Search state ===== */
    let STATE = { q:'', from:'', to:'', currency:'PHP' };
    const searchEl=$('#globalSearch'), clearBtn=$('#clearSearch');
    function setSearch(q){
      STATE.q=q; searchEl.value=q; clearBtn.classList.toggle('hidden', !q);
      if(q) { Toast(`Searching for "${q}"`); } else { Toast('Search cleared'); }
    }
    searchEl?.addEventListener('input', (e)=>setSearch(e.target.value));
    clearBtn?.addEventListener('click', ()=>setSearch(''));

    /* ===== Content loading ===== */
    function loadContent(tabId){
      const data = DATA[tabId];
      if(!data) return;

      const content = $('#contentHost');
      let html = '';

      if(tabId === 'gl-je'){
        html = `
          <section class="card p-5">
            <div class="flex items-center justify-between">
              <div>
                <div class="text-xs uppercase tracking-wide text-[var(--muted)]">GENERAL LEDGER</div>
                <h2 class="text-xl font-bold">Journal Entries</h2>
              </div>
              <div class="flex gap-2">
                <button class="btn btn-soft">Export CSV</button>
                <button class="btn btn-brand" onclick="printTable('gl-je')">Print</button>
              </div>
            </div>

            <div class="mt-6 space-y-6">
              <!-- Enhanced Filter Section -->
              <div class="bg-slate-50 border border-slate-200 rounded-lg p-4">
                <div class="flex flex-wrap gap-4 items-end">
                  <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">From Date</label>
                    <input type="date" class="w-40 px-3 py-2 border border-slate-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                  </div>
                  <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">To Date</label>
                    <input type="date" class="w-40 px-3 py-2 border border-slate-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                  </div>
                  <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Account Type</label>
                    <select class="w-40 px-3 py-2 border border-slate-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                      <option value="">All Accounts</option>
                      <option value="asset">Assets</option>
                      <option value="liability">Liabilities</option>
                      <option value="equity">Equity</option>
                      <option value="revenue">Revenue</option>
                      <option value="expense">Expenses</option>
                    </select>
                  </div>
                  <button class="btn btn-brand px-6">Apply Filters</button>
                  <button class="btn btn-soft px-6">Reset</button>
                </div>
              </div>
              
              <!-- Enhanced Table Section -->
              <div class="overflow-x-auto border border-slate-200 rounded-lg">
                <table class="min-w-full text-sm">
                  <thead class="bg-slate-50 border-b border-slate-200">
                    <tr>
                      <th class="text-left px-4 py-3 font-semibold text-slate-700">Date</th>
                      <th class="text-left px-4 py-3 font-semibold text-slate-700">Reference</th>
                      <th class="text-left px-4 py-3 font-semibold text-slate-700">Description</th>
                      <th class="text-right px-4 py-3 font-semibold text-slate-700">Debit</th>
                      <th class="text-right px-4 py-3 font-semibold text-slate-700">Credit</th>
                      <th class="text-center px-4 py-3 font-semibold text-slate-700">Actions</th>
                    </tr>
                  </thead>
                  <tbody class="divide-y divide-slate-100">
                    ${data.map(row => `
                      <tr class="hover:bg-slate-50 transition-colors">
                        <td class="px-4 py-3 text-slate-900">${row.date}</td>
                        <td class="px-4 py-3 font-mono text-slate-900 font-medium">${row.ref}</td>
                        <td class="px-4 py-3 text-slate-700">${row.desc}</td>
                        <td class="px-4 py-3 text-right font-mono text-slate-900">${row.debit > 0 ? fmtMoney(row.debit) : '-'}</td>
                        <td class="px-4 py-3 text-right font-mono text-slate-900">${row.credit > 0 ? fmtMoney(row.credit) : '-'}</td>
                        <td class="px-4 py-3 text-center">
                          <div class="flex items-center justify-center gap-2">
                            <button class="text-blue-600 hover:text-blue-800 text-sm font-medium px-2 py-1 rounded hover:bg-blue-50">View</button>
                            <button class="text-slate-600 hover:text-slate-800 text-sm font-medium px-2 py-1 rounded hover:bg-slate-50">Edit</button>
                            <button class="text-red-600 hover:text-red-800 text-sm font-medium px-2 py-1 rounded hover:bg-red-50">Delete</button>
                          </div>
                        </td>
                      </tr>
                    `).join('')}
                  </tbody>
                </table>
              </div>
              
              <!-- Enhanced Summary Section -->
              <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                  <div class="text-xs uppercase tracking-wide text-blue-600 font-semibold">Total Debits</div>
                  <div class="text-2xl font-bold text-blue-800 mt-1">₱${data.reduce((sum, row) => sum + row.debit, 0).toLocaleString()}</div>
                </div>
                <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                  <div class="text-xs uppercase tracking-wide text-green-600 font-semibold">Total Credits</div>
                  <div class="text-2xl font-bold text-green-800 mt-1">₱${data.reduce((sum, row) => sum + row.credit, 0).toLocaleString()}</div>
                </div>
                <div class="bg-slate-50 border border-slate-200 rounded-lg p-4">
                  <div class="text-xs uppercase tracking-wide text-slate-600 font-semibold">Total Entries</div>
                  <div class="text-2xl font-bold text-slate-800 mt-1">${data.length}</div>
                </div>
              </div>
              
              <div class="text-sm text-slate-500 text-center py-2">
                Showing ${data.length} journal entries • Last updated: ${new Date().toLocaleString()}
              </div>
            </div>
          </section>
        `;
      } else if(tabId === 'gl-tb'){
        html = `
          <section class="card p-5">
            <div class="flex items-center justify-between">
              <div>
                <div class="text-xs uppercase tracking-wide text-[var(--muted)]">GENERAL LEDGER</div>
                <h2 class="text-xl font-bold">Trial Balance</h2>
              </div>
              <div class="flex gap-2">
                <button class="btn btn-soft">Export CSV</button>
                <button class="btn btn-brand" onclick="printTable('gl-tb')">Print</button>
              </div>
            </div>

            <div class="mt-6 space-y-6">
              <!-- Enhanced Filter Section -->
              <div class="bg-slate-50 border border-slate-200 rounded-lg p-4">
                <div class="flex flex-wrap gap-4 items-end">
                  <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">As of Date</label>
                    <input type="date" class="w-40 px-3 py-2 border border-slate-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                  </div>
                  <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Account Category</label>
                    <select class="w-40 px-3 py-2 border border-slate-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                      <option value="">All Categories</option>
                      <option value="asset">Assets</option>
                      <option value="liability">Liabilities</option>
                      <option value="equity">Equity</option>
                      <option value="revenue">Revenue</option>
                      <option value="expense">Expenses</option>
                    </select>
                  </div>
                  <button class="btn btn-brand px-6">Generate Report</button>
                  <button class="btn btn-soft px-6">Reset</button>
                </div>
              </div>
              
              <!-- Enhanced Table Section -->
              <div class="overflow-x-auto border border-slate-200 rounded-lg">
                <table class="min-w-full text-sm">
                  <thead class="bg-slate-50 border-b border-slate-200">
                    <tr>
                      <th class="text-left px-4 py-3 font-semibold text-slate-700">Account</th>
                      <th class="text-right px-4 py-3 font-semibold text-slate-700">Debit Balance</th>
                      <th class="text-right px-4 py-3 font-semibold text-slate-700">Credit Balance</th>
                      <th class="text-center px-4 py-3 font-semibold text-slate-700">Actions</th>
                    </tr>
                  </thead>
                  <tbody class="divide-y divide-slate-100">
                    ${data.map(row => `
                      <tr class="hover:bg-slate-50 transition-colors">
                        <td class="px-4 py-3 text-slate-900 font-medium">${row.account}</td>
                        <td class="px-4 py-3 text-right font-mono text-slate-900">${row.debit > 0 ? fmtMoney(row.debit) : '-'}</td>
                        <td class="px-4 py-3 text-right font-mono text-slate-900">${row.credit > 0 ? fmtMoney(row.credit) : '-'}</td>
                        <td class="px-4 py-3 text-center">
                          <button class="text-blue-600 hover:text-blue-800 text-sm font-medium px-2 py-1 rounded hover:bg-blue-50">View Details</button>
                        </td>
                      </tr>
                    `).join('')}
                  </tbody>
                </table>
              </div>
              
              <!-- Enhanced Summary Section -->
              <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                  <div class="text-xs uppercase tracking-wide text-blue-600 font-semibold">Total Debits</div>
                  <div class="text-2xl font-bold text-blue-800 mt-1">₱${data.reduce((sum, row) => sum + row.debit, 0).toLocaleString()}</div>
                </div>
                <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                  <div class="text-xs uppercase tracking-wide text-green-600 font-semibold">Total Credits</div>
                  <div class="text-2xl font-bold text-green-800 mt-1">₱${data.reduce((sum, row) => sum + row.credit, 0).toLocaleString()}</div>
                </div>
                <div class="bg-slate-50 border border-slate-200 rounded-lg p-4">
                  <div class="text-xs uppercase tracking-wide text-slate-600 font-semibold">Total Accounts</div>
                  <div class="text-2xl font-bold text-slate-800 mt-1">${data.length}</div>
                </div>
              </div>
              
              <div class="text-sm text-slate-500 text-center py-2">
                Trial Balance as of ${new Date().toLocaleDateString()} • ${data.length} accounts • Last updated: ${new Date().toLocaleString()}
              </div>
            </div>
          </section>
        `;
              } else if(tabId === 'gl-ledger'){
        html = `
          <section class="card p-5">
            <div class="flex items-center justify-between">
              <div>
                <div class="text-xs uppercase tracking-wide text-[var(--muted)]">GENERAL LEDGER</div>
                <h2 class="text-xl font-bold">Ledger Report</h2>
              </div>
              <div class="flex gap-2">
                <button class="btn btn-soft">Export CSV</button>
                <button class="btn btn-brand" onclick="printTable('gl-ledger')">Print</button>
              </div>
            </div>

            <div class="mt-6 space-y-6">
              <!-- Enhanced Filter Section -->
              <div class="bg-slate-50 border border-slate-200 rounded-lg p-4">
                <div class="flex flex-wrap gap-4 items-end">
                  <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Account</label>
                    <select class="w-48 px-3 py-2 border border-slate-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                      <option value="">Select Account</option>
                      <option value="cash">Cash</option>
                      <option value="accounts-receivable">Accounts Receivable</option>
                      <option value="accounts-payable">Accounts Payable</option>
                      <option value="revenue">Revenue</option>
                      <option value="expenses">Expenses</option>
                    </select>
                  </div>
                  <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">From Date</label>
                    <input type="date" class="w-40 px-3 py-2 border border-slate-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                  </div>
                  <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">To Date</label>
                    <input type="date" class="w-40 px-3 py-2 border border-slate-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                  </div>
                  <button class="btn btn-brand px-6">Generate Ledger</button>
                  <button class="btn btn-soft px-6">Reset</button>
                </div>
              </div>
              
              <!-- Enhanced Table Section -->
              <div class="overflow-x-auto border border-slate-200 rounded-lg">
                <table class="min-w-full text-sm">
                  <thead class="bg-slate-50 border-b border-slate-200">
                    <tr>
                      <th class="text-left px-4 py-3 font-semibold text-slate-700">Date</th>
                      <th class="text-left px-4 py-3 font-semibold text-slate-700">Reference</th>
                      <th class="text-left px-4 py-3 font-semibold text-slate-700">Description</th>
                      <th class="text-right px-4 py-3 font-semibold text-slate-700">Debit</th>
                      <th class="text-right px-4 py-3 font-semibold text-slate-700">Credit</th>
                      <th class="text-right px-4 py-3 font-semibold text-slate-700">Balance</th>
                      <th class="text-center px-4 py-3 font-semibold text-slate-700">Actions</th>
                    </tr>
                  </thead>
                  <tbody class="divide-y divide-slate-100">
                    ${data.map(row => `
                      <tr class="hover:bg-slate-50 transition-colors">
                        <td class="px-4 py-3 text-slate-900">${row.date}</td>
                        <td class="px-4 py-3 font-mono text-slate-900 font-medium">${row.ref}</td>
                        <td class="px-4 py-3 text-slate-700">${row.desc}</td>
                        <td class="px-4 py-3 text-right font-mono text-slate-900">${row.debit > 0 ? fmtMoney(row.debit) : '-'}</td>
                        <td class="px-4 py-3 text-right font-mono text-slate-900">${row.credit > 0 ? fmtMoney(row.credit) : '-'}</td>
                        <td class="px-4 py-3 text-right font-mono text-slate-900 font-semibold">${fmtMoney(row.balance)}</td>
                        <td class="px-4 py-3 text-center">
                          <button class="text-blue-600 hover:text-blue-800 text-sm font-medium px-2 py-1 rounded hover:bg-blue-50">View</button>
                        </td>
                      </tr>
                    `).join('')}
                  </tbody>
                </table>
              </div>
              
              <!-- Enhanced Summary Section -->
              <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                  <div class="text-xs uppercase tracking-wide text-blue-600 font-semibold">Total Debits</div>
                  <div class="text-2xl font-bold text-blue-800 mt-1">₱${data.reduce((sum, row) => sum + row.debit, 0).toFixed(2)}</div>
                </div>
                <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                  <div class="text-xs uppercase tracking-wide text-green-600 font-semibold">Total Credits</div>
                  <div class="text-2xl font-bold text-green-800 mt-1">₱${data.reduce((sum, row) => sum + row.credit, 0).toFixed(2)}</div>
                </div>
                <div class="bg-slate-50 border border-slate-200 rounded-lg p-4">
                  <div class="text-xs uppercase tracking-wide text-slate-600 font-semibold">Final Balance</div>
                  <div class="text-2xl font-bold text-slate-800 mt-1">₱${data[data.length - 1]?.balance.toFixed(2) || '0.00'}</div>
                </div>
              </div>
              
              <div class="text-sm text-slate-500 text-center py-2">
                Ledger Report • ${data.length} transactions • Last updated: ${new Date().toLocaleString()}
              </div>
            </div>
          </section>
        `;
      }

      content.innerHTML = html;
      updatePrintInfo(tabId);
    }

    function updatePrintInfo(tabId){
      const tab = GL_TABS.find(t => t.id === tabId);
      if(tab){
        $('#printTitle').textContent = `ATIERA — General Ledger — ${tab.label}`;
        $('#printWhen').textContent = new Date().toLocaleDateString();
        $('#printContext').textContent = 'All records';
      }
    }

    function printTable(tabId){
      window.print();
    }

    /* ===== Hash change handling ===== */
    function handleHashChange(){
      const { tab } = decodeHash();
      markActiveTab(tab);
      loadContent(tab);
    }

    window.addEventListener('hashchange', handleHashChange);
    window.addEventListener('load', handleHashChange);

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
      const cards = document.querySelectorAll('.ledger-card, .card');
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
      const buttons = document.querySelectorAll('button[onclick*="addEntry"], button[onclick*="deleteEntry"], button[onclick*="editEntry"], button[onclick*="saveEntry"]');
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
    
    /* ===== Initialize ===== */
    document.addEventListener('DOMContentLoaded', () => {
      // Start loading screen
      initLoadingScreen();
      
      // Set initial hash if none exists
      if(!location.hash){
        location.hash = '#General%20Ledger/gl-je';
      }
      
      // Initialize dark mode
      initDarkMode();
      initButtonLoading();
    });

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
      
      // Simulate real-time notifications for General Ledger
      simulateGeneralLedgerNotifications();
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

    // Simulate General Ledger-specific notifications
    function simulateGeneralLedgerNotifications() {
      // Journal entry notifications (every 2 minutes)
      setInterval(() => {
        if (Math.random() < 0.25) { // 25% chance
          const entries = [
            'New journal entry posted: Supplies expense for ₱' + Math.floor(Math.random() * 1000 + 100).toLocaleString('en-US'),
            'Journal entry updated: Revenue recognition for ₱' + Math.floor(Math.random() * 5000 + 1000).toLocaleString('en-US'),
            'Journal entry deleted: Duplicate transaction removed',
            'Journal entry approved: Manager review completed'
          ];
          const randomEntry = entries[Math.floor(Math.random() * entries.length)];
          addNotificationToPanel('financial', randomEntry);
        }
      }, 120000);

      // Trial balance notifications (every 3 minutes)
      setInterval(() => {
        if (Math.random() < 0.2) { // 20% chance
          const balances = [
            'Trial balance generated: All accounts balanced',
            'Trial balance discrepancy detected: Investigating variance',
            'Trial balance exported: PDF report ready for download',
            'Trial balance reconciled: Month-end closing complete'
          ];
          const randomBalance = balances[Math.floor(Math.random() * balances.length)];
          addNotificationToPanel('system', randomBalance);
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

    // Initialize notification system on page load
    document.addEventListener('DOMContentLoaded', () => {
      // Initialize notification system after a short delay to avoid conflicts
      setTimeout(() => {
        initNotificationSystem();
      }, 100);
    });
  </script>

</body>
</html>
