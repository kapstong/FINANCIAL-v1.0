<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Database connection
$host = 'localhost';
$dbname = 'atiera';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// Get user data
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();
?>

<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Reports</title>
  <link rel="icon" type="image/png" href="logo2.png">
  <script src="https://cdn.tailwindcss.com"></script>

  <style>
    :root{
      --brand:#0f1c49; --brand-600:#0c173c; --brand-100:#e8ecf9;
      --ink:#000; --muted:#000; --ring:0 0 0 3px rgba(15,28,73,.15);
      --card-bg: rgba(255,255,255,.95); --card-border: rgba(226,232,240,.9);
    }

    body{ background:#fff; color:var(--ink); }
    .bg-soft{
      background:
        radial-gradient(70% 70% at 0% 0%, var(--brand-100) 0%, transparent 60%),
        radial-gradient(60% 60% at 100% 0%, #eef2ff 0%, transparent 55%),
        linear-gradient(#fff,#fff);
    }

    /* Header / Navbar */
    .navbar{ background:var(--brand); color:#fff; }
    .navbar *{ color:#fff !important; }
    .nav-input{
      background:rgba(255,255,255,.18); border:1px solid rgba(255,255,255,.35);
      padding:.35rem .6rem; border-radius:.6rem; color:#fff !important;
    }
    .nav-input::placeholder{ color:#f1f5f9; }

    /* Cards / Buttons / Tabs */
    .card{ background:var(--card-bg); border-radius:14px; border:1px solid var(--card-border); box-shadow:0 6px 18px rgba(2,6,23,.04) }
    .btn{ display:inline-flex; align-items:center; gap:.5rem; padding:.55rem .95rem; border-radius:.65rem; font-weight:600; color:#000 }
    .btn-brand{ background:var(--brand); color:#fff !important } .btn-brand:hover{ background:var(--brand-600) }
    .btn-soft{ background:#fff; border:1px solid var(--card-border) } .btn-soft:hover{ background:#f8fafc }
    .tab-pill{ padding:.4rem .8rem; border-radius:9999px; border:1px solid var(--card-border); font-weight:700; font-size:.9rem; color:#000 }
    .tab-pill.active{ background:var(--brand); color:#fff; border-color:var(--brand) }

    /* Sidebar */
    .sidebar-transition{ transition:transform .28s ease }
    .overlay{ display:none } .overlay.active{ display:block; position:fixed; inset:0; background:rgba(0,0,0,.35); z-index:40 }
    .sidebar-item{ display:flex; align-items:center; gap:.6rem; width:100%; padding:.5rem .75rem; border-radius:.6rem; color:#000 }
    .sidebar-item:hover{ background:#f8fafc }
    .sidebar-item.active{ background:rgba(15,28,73,.06); color:var(--brand); font-weight:700 }

    /* Table */
    th,td{ white-space:nowrap; }
    thead tr{ background:#f8fafc; }
    tbody tr:hover{ background:#f8fafc; }
    .empty-state{ border:2px dashed var(--card-border); border-radius:12px; padding:20px; text-align:center; color:#475569; }

    .toast-card{ background:#fff; border:1px solid var(--card-border); border-radius:.75rem; padding:.6rem .9rem; box-shadow:0 10px 30px rgba(0,0,0,.08) }
    
    /* Modal */
    .modal{ display:none; position:fixed; inset:0; z-index:60; }
    .modal.active{ display:flex }
    .modal-backdrop{ position:absolute; inset:0; background:rgba(0,0,0,.42) }
    .modal-panel{ position:relative; margin:auto; width:min(680px,92vw); outline: none; }
    
    /* Utilities */
    .sr-only{ position:absolute; width:1px; height:1px; padding:0; margin:-1px; overflow:hidden; clip:rect(0,0,0,0); white-space:nowrap; border:0; }
    
    /* Dark mode styles */
    html.dark {
      --brand: #3b82f6;
      --brand-600: #2563eb;
      --brand-100: #dbeafe;
      --ink: #f8fafc;
      --muted: #94a3b8;
      --card-bg: rgba(15,23,42,.95);
      --card-border: rgba(51,65,85,.55);
    }
    
    html.dark body { background: #0f172a; }
    html.dark .bg-soft {
      background:
        radial-gradient(70% 70% at 0% 0%, rgba(59,130,246,.08) 0%, transparent 60%),
        radial-gradient(60% 60% at 100% 0%, rgba(59,130,246,.12) 0%, transparent 55%),
        linear-gradient(#0f172a,#0f172a);
    }
    
    html.dark .navbar { background: #1e293b; }
    html.dark .card { background: var(--card-bg); border-color: var(--card-border); }
    html.dark .btn-soft { background: var(--card-bg); border-color: var(--card-border); color: var(--ink); }
    html.dark .btn-soft:hover { background: rgba(51,65,85,.92); }
    html.dark .sidebar-item { color: var(--ink); }
    html.dark .sidebar-item:hover { background: rgba(51,65,85,.92); }
    html.dark .tab-pill { color: var(--ink); border-color: var(--card-border); }
    html.dark thead tr { background: rgba(51,65,85,.92); }
    html.dark tbody tr:hover { background: rgba(51,65,85,.92); }
         html.dark .nav-input { background: rgba(255,255,255,.15); border-color: rgba(255,255,255,.25); }
     html.dark .nav-input::placeholder { color: #cbd5e1; }
     
     /* Notification System Styles */
     #notificationPanel {
       transition: all 0.3s ease-in-out;
       transform-origin: top right;
     }
     
     #notificationPanel:not(.hidden) {
       animation: slideInDown 0.3s ease-out;
     }
     
     #notificationBadge {
       animation: pulse 2s infinite;
     }
     
     #notificationBadge.has-notifications {
       animation: badgeBounce 0.6s ease-in-out;
     }
     
     #notificationBell.has-notifications {
       animation: bellShake 0.5s ease-in-out;
     }
     
     /* Notification Panel Dark Mode Support */
     #notificationPanel {
       background: white;
       border-color: #e5e7eb;
     }
     
     html.dark #notificationPanel {
       background: #1f2937;
       border-color: #374151;
     }
     
     #notificationPanel .bg-gray-50 {
       background-color: #f9fafb;
     }
     
     html.dark #notificationPanel .bg-gray-50 {
       background-color: #374151;
     }
     
     #notificationPanel .text-gray-900 {
       color: #111827;
     }
     
     html.dark #notificationPanel .text-gray-900 {
       color: #f9fafc;
     }
     
     #notificationPanel .text-gray-600 {
       color: #4b5563;
     }
     
     html.dark #notificationPanel .text-gray-600 {
       color: #d1d5db;
     }
     
     #notificationPanel .text-gray-400 {
       color: #9ca3af;
     }
     
     html.dark #notificationPanel .text-gray-400 {
       color: #9ca3af;
     }
     
     #notificationPanel .text-gray-500 {
       color: #6b7280;
     }
     
     html.dark #notificationPanel .text-gray-500 {
       color: #9ca3af;
     }
     
     #notificationPanel .border-gray-100 {
       border-color: #f3f4f6;
     }
     
     html.dark #notificationPanel .border-gray-100 {
       border-color: #374151;
     }
     
     #notificationPanel .border-gray-200 {
       border-color: #e5e7eb;
     }
     
     html.dark #notificationPanel .border-gray-200 {
       border-color: #4b5563;
     }
     
     #notificationPanel .hover\:bg-gray-50:hover {
       background-color: #f9fafb;
     }
     
     html.dark #notificationPanel .hover\:bg-gray-50:hover {
       background-color: #4b5563;
     }
     
     #notificationPanel .text-blue-600 {
       color: #2563eb;
     }
     
     html.dark #notificationPanel .text-blue-600 {
       color: #60a5fa;
     }
     
     #notificationPanel .hover\:text-blue-800:hover {
       color: #1e40af;
     }
     
     html.dark #notificationPanel .hover\:text-blue-800:hover {
       color: #93c5fd;
     }
     
     /* Notification Badge */
     #notificationBadge {
       background-color: #ef4444;
       color: white;
     }
     
     /* Notification Bell */
     #notificationBell {
       position: relative;
     }
     
     #notificationBell::after {
       content: '';
       position: absolute;
       top: 50%;
       left: 50%;
       width: 100%;
       height: 100%;
       background: rgba(255, 255, 255, 0.1);
       border-radius: 50%;
       transform: translate(-50%, -50%) scale(0);
       transition: transform 0.3s ease;
     }
     
     #notificationBell:active::after {
       transform: translate(-50%, -50%) scale(1);
     }
     
     /* Toast Animations */
     .toast-card {
       animation: slideInRight 0.3s ease-out;
     }
     
     /* Keyframe Animations */
     @keyframes slideInDown {
       from {
         opacity: 0;
         transform: translateY(-10px) scale(0.95);
       }
       to {
         opacity: 1;
         transform: translateY(0) scale(1);
       }
     }
     
     @keyframes pulse {
       0%, 100% {
         opacity: 1;
       }
       50% {
         opacity: 0.5;
       }
     }
     
     @keyframes badgeBounce {
       0%, 20%, 53%, 80%, 100% {
         transform: translate3d(0, 0, 0);
       }
       40%, 43% {
         transform: translate3d(0, -8px, 0);
       }
       70% {
         transform: translate3d(0, -4px, 0);
       }
       90% {
         transform: translate3d(0, -2px, 0);
       }
     }
     
     @keyframes bellShake {
       0%, 100% {
         transform: rotate(0deg);
       }
       25% {
         transform: rotate(5deg);
       }
       75% {
         transform: rotate(-5deg);
       }
     }
     
     @keyframes slideInRight {
       from {
         opacity: 0;
         transform: translateX(100%);
       }
       to {
         opacity: 1;
         transform: translateX(0);
       }
     }
     
     /* Additional Dark Mode Styles for Complete UI */
     html.dark .sidebar-item.active {
       background: rgba(59,130,246,.15);
       color: #60a5fa;
     }
     
     html.dark #contextBar {
       background: rgba(15,23,42,.95);
       border-color: rgba(51,65,85,.55);
     }
     
     html.dark #contextBar .text-slate-800 {
       color: #f1f5f9;
     }
     
     html.dark .tab-pill.active {
       background: #3b82f6;
       color: white;
       border-color: #3b82f6;
     }
     
     html.dark .tab-pill:hover {
       background: rgba(51,65,85,.92);
     }
     
     html.dark .card {
       background: rgba(15,23,42,.95);
       border-color: rgba(51,65,85,.55);
     }
     
     html.dark .btn-soft {
       background: rgba(15,23,42,.95);
       border-color: rgba(51,65,85,.55);
       color: #f1f5f9;
     }
     
     html.dark .btn-soft:hover {
       background: rgba(51,65,85,.92);
     }
     
     html.dark .text-slate-600 {
       color: #cbd5e1;
     }
     
     html.dark .text-slate-800 {
       color: #f1f5f9;
     }
     
     html.dark .border-slate-200 {
       border-color: rgba(51,65,85,.55);
     }
     
     html.dark .bg-white {
       background-color: rgba(15,23,42,.95);
     }
     
     html.dark .bg-white\/80 {
       background-color: rgba(15,23,42,.8);
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
       
       .modal-panel {
         width: calc(100vw - 2rem);
         margin: 1rem;
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
<body class="min-h-screen text-[15px] text-[var(--ink)] bg-soft">
  <a href="#contentHost" class="sr-only focus:not-sr-only focus:absolute focus:top-2 focus:left-2 focus:bg-white focus:border focus:px-3 focus:py-1 rounded">Skip to content</a>

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
        <h2 class="text-2xl font-bold text-white mb-2">Loading Reports</h2>
        <p class="text-slate-300 text-sm">Preparing your reports overview...</p>
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
      <button id="openSidebar" class="md:hidden p-2 rounded hover:bg-white/10" aria-label="Open menu">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h10"/></svg>
      </button>

      <!-- Brand -->
      <a href="index.php" class="flex items-center gap-3">
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
          <a href="settings.php" class="block px-4 py-2 text-black hover:bg-slate-900">Settings</a>
          <a href="profile.php" class="block px-4 py-2 text-black hover:bg-slate-900">Profile</a>
          <a href="#" class="block px-4 py-2 text-black hover:bg-slate-900">My Messages</a>
          <a href="#" class="block px-4 py-2 text-black hover:bg-slate-900">Lock Screen</a>
          <a href="logout.php" class="block px-4 py-2 text-black hover:bg-slate-900">Logout</a>
        </div>
      </div>
    </div>
  </header>

  <!-- NAVBAR SUB-MODULE TABS -->
  <div id="contextBar" class="sticky top-14 z-40 border-b border-[var(--ring)] bg-white/80 backdrop-blur">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 h-12 flex items-center justify-between">
      <div class="flex items-center gap-3">
        <span class="font-semibold text-slate-800">Reports</span>
      </div>
      <nav id="contextTabs" class="flex flex-wrap gap-2" role="tablist" aria-label="Report submodules">
        <!-- href format: #Module/tabId -->
        <a class="tab-pill" role="tab" aria-selected="false" href="#Reports/rpt-fs">Financial Statements</a>
        <a class="tab-pill" role="tab" aria-selected="false" href="#Reports/rpt-custom">Custom Reports</a>
        <a class="tab-pill" role="tab" aria-selected="false" href="#Reports/rpt-schedule">Schedules</a>
      </nav>
    </div>
  </div>

  <!-- LAYOUT -->
  <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 grid grid-cols-1 lg:grid-cols-[240px_1fr] gap-6 py-6">
    <div id="overlay" class="overlay" aria-hidden="true"></div>

    <!-- SIDEBAR -->
    <aside id="sidebar" class="fixed lg:static left-0 top-14 lg:top-auto w-64 lg:w-full h-[calc(100vh-56px)] lg:h-auto bg-white border-r border-[var(--ring)] sidebar-transition -translate-x-full lg:translate-x-0 z-50 overflow-y-auto" aria-label="Sidebar">
      <nav class="p-3 space-y-1">
        <div class="text-[11px] uppercase tracking-widest text-slate-500 px-2 pt-2 pb-1">Navigation</div>
        <a class="sidebar-item" href="index.php"><span>🏠</span><span>Dashboard</span></a>
        <a class="sidebar-item" href="General Ledger.php"><span>📘</span><span>General Ledger</span></a>
        <a class="sidebar-item" href="Accounts Receivable.php"><span>💳</span><span>Accounts Receivable</span></a>
        <a class="sidebar-item" href="Collections.php"><span>🧾</span><span>Collections</span></a>
        <a class="sidebar-item" href="Accounts Payable.php"><span>📄</span><span>Accounts Payable</span></a>
        <a class="sidebar-item" href="Disbursement.php"><span>💸</span><span>Disbursement</span></a>
        <a class="sidebar-item" href="Budget Management.php"><span>📊</span><span>Budget Management</span></a>
        <a class="sidebar-item active" href="Reports.php"><span>📑</span><span>Reports</span></a>
      </nav>
    </aside>

    <!-- MAIN -->
    <main class="w-full space-y-6">
      <section id="contentHost" class="space-y-6 w-full" tabindex="-1"></section>
    </main>
  </div>

  <!-- MODAL ROOT (REUSABLE) -->
  <div id="modalRoot" class="modal" aria-hidden="true">
    <div class="modal-backdrop" data-close></div>
    <div class="modal-panel" role="dialog" aria-modal="true" aria-labelledby="modalTitle">
      <div class="card p-0 overflow-hidden">
        <div class="px-5 py-3 flex items-center justify-between border-b border-[var(--ring)]">
          <h3 id="modalTitle" class="font-bold">Modal</h3>
          <button class="px-2 py-1 rounded hover:bg-orange-50" data-close aria-label="Close">&times;</button>
        </div>
        <div id="modalBody" class="p-5 text-sm"></div>
        <div class="px-5 py-3 border-t border-[var(--ring)] flex items-center end gap-2">
          <button class="btn btn-soft" data-close>Close</button>
          <button id="modalPrimary" class="btn btn-brand">Save</button>
        </div>
      </div>
    </div>
  </div>

  <!-- MODAL TEMPLATES -->
  <template id="tpl-generate">
    <form id="formGenerate" class="grid grid-cols-1 md:grid-cols-2 gap-3">
      <label class="text-sm">Report Type
        <select class="w-full mt-1 border rounded px-2 py-1">
          <option>Income Statement</option>
          <option>Balance Sheet</option>
          <option>Cash Flow</option>
          <option>Trial Balance</option>
        </select>
      </label>
      <label class="text-sm">Period
        <input required type="month" class="w-full mt-1 border rounded px-2 py-1"/>
      </label>
      <label class="text-sm">Format
        <select class="w-full mt-1 border rounded px-2 py-1">
          <option>PDF</option>
          <option>Excel</option>
          <option>CSV</option>
        </select>
      </label>
      <label class="text-sm md:col-span-2">Notes
        <input placeholder="Optional" class="w-full mt-1 border rounded px-2 py-1"/>
      </label>
    </form>
  </template>

  <template id="tpl-share">
    <form id="formShare" class="grid gap-3">
      <label class="text-sm">Recipients
        <input required type="email" multiple placeholder="email@company.com" class="w-full mt-1 border rounded px-2 py-1"/>
      </label>
      <label class="text-sm">Message
        <textarea rows="3" class="w-full mt-1 border rounded px-2 py-1" placeholder="Optional message"></textarea>
      </label>
    </form>
  </template>

  <template id="tpl-schedule">
    <form id="formSchedule" class="grid grid-cols-1 md:grid-cols-2 gap-3">
      <label class="text-sm">Report
        <select class="w-full mt-1 border rounded px-2 py-1">
          <option>Income Statement</option>
          <option>Balance Sheet</option>
          <option>Cash Flow</option>
        </select>
      </label>
      <label class="text-sm">Frequency
        <select class="w-full mt-1 border rounded px-2 py-1">
          <option>Daily</option>
          <option selected>Monthly</option>
          <option>Quarterly</option>
        </select>
      </label>
      <label class="text-sm">Start Date
        <input type="date" class="w-full mt-1 border rounded px-2 py-1"/>
      </label>
      <label class="text-sm">Send To
        <input placeholder="email@company.com" class="w-full mt-1 border rounded px-2 py-1"/>
      </label>
    </form>
  </template>

  <script>
  "use strict";
  // Helpers
  const $=(s,r=document)=>r.querySelector(s);
  const $$=(s,r=document)=>Array.from(r.querySelectorAll(s));

  /* ===== Loader ===== */
  const Loader=(()=>{const el=$('#globalLoader');let on=false,t0=0;const MIN=350;
    function show(){ if(on) return; on=true; t0=performance.now(); el.classList.remove('hidden'); el.classList.add('flex'); }
    function hide(){ if(!on) return; const d=Math.max(0,MIN-(performance.now()-t0)); setTimeout(()=>{ el.classList.add('hidden'); el.classList.remove('flex'); on=false; }, d); }
    async function wrap(job){ show(); try{ return typeof job==='function'?await job():await job; } finally{ hide(); } }
    return {show,hide,wrap};
  })();

  /* ===== Toast ===== */
  function toast(msg,type='info'){
    const t=$('#toast');
    const color = type==='error' ? 'text-red-700 border-red-200 bg-red-50'
                : type==='success' ? 'text-green-700 border-green-200 bg-green-50'
                : 'text-slate-700';
    t.innerHTML = `<div class="toast-card ${color}">${msg}</div>`;
    t.classList.remove('hidden');
    clearTimeout(t._timer);
    t._timer = setTimeout(()=>t.classList.add('hidden'), 1800);
  }

  /* ===== Modal (focus trap + ESC) ===== */
  const modalRoot = $('#modalRoot'), mBody = $('#modalBody'), mTitle = $('#modalTitle'), mPrimary = $('#modalPrimary');
  let modalOkHandler = null, lastFocus = null;

  function focusables(root){
    return $$('button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])', root)
      .filter(el=>!el.hasAttribute('disabled') && !el.getAttribute('aria-hidden'));
  }

  function openModal({title, bodyHTML, primaryText='Save', onPrimary=null}){
    lastFocus = document.activeElement;
    mTitle.textContent = title || 'Modal';
    mBody.innerHTML = bodyHTML || '';
    mPrimary.textContent = primaryText;
    modalOkHandler = onPrimary;
    modalRoot.classList.add('active');
    modalRoot.setAttribute('aria-hidden','false');
    document.body.style.overflow = 'hidden';
    const f = focusables(modalRoot)[0]; if(f) f.focus();
  }
  function closeModal(){
    modalRoot.classList.remove('active');
    modalRoot.setAttribute('aria-hidden','true');
    modalOkHandler=null;
    document.body.style.overflow = '';
    if(lastFocus) lastFocus.focus();
  }
  modalRoot.addEventListener('click', (e)=>{ if(e.target===modalRoot || e.target.hasAttribute('data-close')) closeModal(); });
  document.addEventListener('keydown', (e)=>{
    if(!modalRoot.classList.contains('active')) return;
    if(e.key==='Escape'){ e.preventDefault(); closeModal(); }
    if(e.key==='Tab'){
      const fs = focusables(modalRoot);
      if(fs.length===0) return;
      const first=fs[0], last=fs[fs.length-1];
      if(e.shiftKey && document.activeElement===first){ e.preventDefault(); last.focus(); }
      else if(!e.shiftKey && document.activeElement===last){ e.preventDefault(); first.focus(); }
    }
  });
  mPrimary.addEventListener('click', async ()=>{
    if(typeof modalOkHandler==='function'){
      try{ await Loader.wrap(modalOkHandler); toast('Saved','success'); }
      catch(err){ console.error(err); toast('Action failed','error'); return; }
    }
    closeModal();
  });

  // Buttons that open modals
  document.addEventListener('click', (e)=>{
    const btn = e.target.closest('[data-open]');
    if(!btn) return;
    e.preventDefault();
    const key = btn.getAttribute('data-open');
    if(key==='generate'){
      openModal({ title:'Generate Report', bodyHTML: $('#tpl-generate').innerHTML, primaryText:'Generate',
        onPrimary: async ()=> new Promise(r=>setTimeout(r,500))
      });
    }else if(key==='share'){
      openModal({ title:'Share Report', bodyHTML: $('#tpl-share').innerHTML, primaryText:'Send',
        onPrimary: async ()=> new Promise(r=>setTimeout(r,500))
      });
    }else if(key==='schedule'){
      openModal({ title:'Schedule Report', bodyHTML: $('#tpl-schedule').innerHTML, primaryText:'Schedule',
        onPrimary: async ()=> new Promise(r=>setTimeout(r,600))
      });
    }
  });

  /* ===== Header interactions ===== */
  const overlay=$('#overlay'), sidebar=$('#sidebar');
  $('#openSidebar')?.addEventListener('click', ()=>{ sidebar.classList.remove('-translate-x-full'); overlay.classList.add('active'); });
  overlay?.addEventListener('click', ()=>{ sidebar.classList.add('-translate-x-full'); overlay.classList.remove('active'); });
  const pBtn=$('#profileBtn'), pMenu=$('#profileMenu');
  pBtn?.addEventListener('click', ()=>{ const open = pMenu.classList.toggle('hidden'); pBtn.setAttribute('aria-expanded', String(!open)); });
  document.addEventListener('click', (e)=>{ if(pBtn && pMenu && !pBtn.contains(e.target) && !pMenu.contains(e.target)) { pMenu.classList.add('hidden'); pBtn.setAttribute('aria-expanded','false'); } });

  /* ===== Live clock ===== */
  (function(){
    const t=$('#liveTime'), d=$('#liveDate'), tm=$('#liveTimeMobile'), dm=$('#liveDateMobile'); let is24=localStorage.getItem('fmt24')==='1';
    const fD=n=>new Intl.DateTimeFormat(undefined,{year:'numeric',month:'short',day:'2-digit',weekday:'short'}).format(n);
    const fT=n=>new Intl.DateTimeFormat(undefined,{hour:'2-digit',minute:'2-digit',second:'2-digit',hour12:!is24}).format(n);
    function tick(){const n=new Date(); if(d) d.textContent=fD(n); if(t) t.textContent=fT(n); if(dm) dm.textContent=fD(n); if(tm) tm.textContent=fT(n);}
    t?.addEventListener('click',()=>{is24=!is24; localStorage.setItem('fmt24',is24?'1':'0'); tick();});
    tick(); setInterval(tick,1000); $('#clockWrap')?.classList.remove('hidden');
  })();

  /* ===== Tabs + view (Reports) ===== */
  const MOD_NAME = 'Reports';
  const RPT_TABS = [
    { id:'rpt-fs',       label:'Financial Statements' },
    { id:'rpt-custom',   label:'Custom Reports' },
    { id:'rpt-schedule', label:'Schedules' },
  ];

  function decodeHash(){
    const def = `#${encodeURIComponent(MOD_NAME)}/rpt-fs`;
    const h=(location.hash||def).slice(1);
    const [mod, tab]=h.split('/');
    return { module: decodeURIComponent(mod||MOD_NAME), tab: decodeURIComponent(tab||'rpt-fs') };
  }

  function markActiveTab(tabId){
    $$('#contextTabs .tab-pill').forEach(a=>{
      const active = a.getAttribute('href') === `#${encodeURIComponent(MOD_NAME)}/${tabId}`;
      a.classList.toggle('active', active);
      a.setAttribute('aria-selected', String(active));
      if(active) a.focus({preventScroll:true});
    });
  }

  // Keyboard nav for tabs
  $('#contextTabs')?.addEventListener('keydown', (e)=>{
    if(e.key!=='ArrowRight' && e.key!=='ArrowLeft') return;
    const tabs = $$('#contextTabs .tab-pill');
    const i = tabs.findIndex(t => t.classList.contains('active'));
    let j = i;
    if(e.key==='ArrowRight') j = (i+1) % tabs.length;
    if(e.key==='ArrowLeft')  j = (i-1+tabs.length) % tabs.length;
    tabs[j].click();
    e.preventDefault();
  });

  function renderView(tabId){
    const tab = RPT_TABS.find(t=>t.id===tabId) || RPT_TABS[0];
    const host = $('#contentHost');

    // Right-side actions per tab
    let rightButtons = `
      <button class="btn btn-brand" data-open="generate">Generate</button>
      <button class="btn btn-soft" data-open="share">Share</button>`;
    if(tab.id === 'rpt-schedule'){
      rightButtons = `<button class="btn btn-brand" data-open="schedule">+ New Schedule</button>`;
    }

    // Sample table rows vary a bit per tab
    let headCols = '';
    let bodyRow = '';
    if(tab.id==='rpt-fs'){
      headCols = `
        <th class="text-left px-3 py-2">Report</th>
        <th class="text-left px-3 py-2">Period</th>
        <th class="text-left px-3 py-2">Prepared</th>
        <th class="text-left px-3 py-2">Actions</th>`;
      bodyRow = `
        <tr class="border-t">
          <td class="px-3 py-2">Income Statement</td>
          <td class="px-3 py-2">2025-08</td>
          <td class="px-3 py-2">Just now</td>
          <td class="px-3 py-2">
            <a href="#" class="text-[var(--brand)] font-semibold hover:underline" data-open="share">Share</a>
            <a href="#" class="ml-3 text-slate-600 hover:underline" onclick="event.preventDefault(); toast('Downloaded PDF','success')">Download</a>
          </td>
        </tr>`;
    } else if(tab.id==='rpt-custom'){
      headCols = `
        <th class="text-left px-3 py-2">Name</th>
        <th class="text-left px-3 py-2">Owner</th>
        <th class="text-left px-3 py-2">Last Run</th>
        <th class="text-left px-3 py-2">Actions</th>`;
      bodyRow = `
        <tr class="border-t">
          <td class="px-3 py-2">AR Aging (by Region)</td>
          <td class="px-3 py-2">Finance</td>
          <td class="px-3 py-2">2 days ago</td>
          <td class="px-3 py-2">
            <a href="#" class="text-[var(--brand)] font-semibold hover:underline" data-open="generate">Run</a>
            <a href="#" class="ml-3 text-slate-600 hover:underline" data-open="share">Share</a>
          </td>
        </tr>`;
    } else { // rpt-schedule
      headCols = `
        <th class="text-left px-3 py-2">Report</th>
        <th class="text-left px-3 py-2">Frequency</th>
        <th class="text-left px-3 py-2">Next Run</th>
        <th class="text-left px-3 py-2">Recipients</th>`;
      bodyRow = `
        <tr class="border-t">
          <td class="px-3 py-2">Balance Sheet</td>
          <td class="px-3 py-2">Monthly</td>
          <td class="px-3 py-2">1st of next month</td>
          <td class="px-3 py-2">finance@company.com</td>
        </tr>`;
    }

    host.innerHTML = `
      <section class="card p-5">
        <div class="flex items-center justify-between">
          <div>
            <div class="text-xs uppercase tracking-wide text-[var(--muted)]">${MOD_NAME.toUpperCase()}</div>
            <h1 class="text-xl font-bold">${tab.label}</h1>
          </div>
          <div class="flex gap-2">${rightButtons}</div>
        </div>

        <div class="mt-4 border-t border-[var(--ring)] pt-4">
          <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
              <thead class="bg-orange-50">
                <tr>${headCols}</tr>
              </thead>
              <tbody id="gridBody">
                ${bodyRow}
              </tbody>
            </table>
          </div>

          <div class="mt-5 grid grid-cols-1 md:grid-cols-2 gap-5">
            <div class="card p-4">
              <div class="font-semibold mb-2">Insight</div>
              <p class="text-sm text-slate-600">
                ${tab.id==='rpt-fs' ? 'Latest statements are ready to download and share.'
                : tab.id==='rpt-custom' ? 'Design ad-hoc views and run them on demand.'
                : 'Keep stakeholders informed with automated delivery.'}
              </p>
            </div>
            <div class="card p-4">
              <div class="font-semibold mb-2">Notes</div>
              <p class="text-sm text-slate-600">All actions use the global loader and modals for smooth UX.</p>
            </div>
          </div>
        </div>
      </section>
    `;
  }

  async function route(){
    const {module, tab} = decodeHash();
    if(module !== MOD_NAME){ location.hash = `#${encodeURIComponent(MOD_NAME)}/rpt-fs`; return; }
    localStorage.setItem('reports_active_tab', tab);
    await Loader.wrap(new Promise(r=>setTimeout(r,180)));
    markActiveTab(tab);
    renderView(tab);
    $('#contentHost')?.focus({preventScroll:true});
  }

  // Tab keyboard click support and hash router
  window.addEventListener('hashchange', route);
  window.addEventListener('DOMContentLoaded', ()=>{
    if(!location.hash){
      const last = localStorage.getItem('reports_active_tab') || 'rpt-fs';
      location.hash = `#${encodeURIComponent(MOD_NAME)}/${last}`;
    }
    route();
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
    
    // Initialize on load
    initDarkModeOnLoad();
  }

  // Initialize dark mode
  initDarkMode();

  // Initialize loading screen
  function initLoadingScreen() {
    const loader = $('#globalLoader');
    const progressBar = $('#loadingProgress');
    
    // Simulate loading progress
    let progress = 0;
    const progressInterval = setInterval(() => {
      progress += Math.random() * 15 + 5; // Random progress between 5-20 (same as Accounts Payable)
      if (progress >= 100) {
        progress = 100;
        clearInterval(progressInterval);
        
        // Hide loader with fade out effect (same as Accounts Payable)
        setTimeout(() => {
          loader.style.opacity = '0';
          loader.style.transform = 'scale(0.95)';
          setTimeout(() => {
            loader.style.display = 'none';
          }, 300);
        }, 200);
      }
      progressBar.style.width = progress + '%';
    }, 100); // Progress updates every 100ms (same as Accounts Payable)
  }

  // Initialize loading screen on page load
  document.addEventListener('DOMContentLoaded', () => {
    // Start loading screen immediately (same as Accounts Payable)
    initLoadingScreen();
  });

  // Notification system
  let notifications = [];
  let notificationCounter = 0;

  function toggleNotificationPanel() {
    const panel = document.getElementById('notificationPanel');
    panel.classList.toggle('hidden');
  }

  function clearAllNotifications() {
    notifications = [];
    notificationCounter = 0;
    updateNotificationList();
    updateNotificationBadge();
  }

  function addNotificationToPanel(type, message, timestamp = new Date()) {
    const notification = {
      id: ++notificationCounter,
      type: type,
      message: message,
      timestamp: timestamp,
      read: false
    };
    
    notifications.unshift(notification);
    updateNotificationList();
    updateNotificationBadge();
    showToast(`${type}: ${message}`, 'info');
  }

  function updateNotificationList() {
    const list = document.getElementById('notificationList');
    if (notifications.length === 0) {
      list.innerHTML = '<div class="p-4 text-center text-gray-500 text-sm dark:text-gray-400">No new notifications</div>';
      return;
    }

    list.innerHTML = notifications.map(notification => `
      <div class="p-3 border-b border-gray-100 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">
        <div class="flex items-start justify-between">
          <div class="flex-1">
            <div class="flex items-center gap-2 mb-1">
              <span class="text-lg">${getNotificationIcon(notification.type)}</span>
              <span class="text-sm font-medium text-gray-900 dark:text-gray-100">${notification.type}</span>
            </div>
            <p class="text-sm text-gray-600 dark:text-gray-300 mb-2">${notification.message}</p>
            <span class="text-xs text-gray-500 dark:text-gray-500">${formatTimestamp(notification.timestamp)}</span>
          </div>
          <button onclick="markAsRead(${notification.id})" class="text-gray-400 dark:text-gray-500 hover:text-gray-600 dark:hover:text-gray-300 text-sm">
            ${notification.read ? '✓' : '○'}
          </button>
        </div>
      </div>
    `).join('');
  }

  function updateNotificationBadge() {
    const badge = document.getElementById('notificationBadge');
    const unreadCount = notifications.filter(n => !n.read).length;
    
    if (unreadCount > 0) {
      badge.textContent = unreadCount;
      badge.classList.remove('hidden');
      document.getElementById('notificationBell').classList.add('has-notifications');
    } else {
      badge.classList.add('hidden');
      document.getElementById('notificationBell').classList.remove('has-notifications');
    }
  }

  function markAsRead(id) {
    const notification = notifications.find(n => n.id === id);
    if (notification) {
      notification.read = true;
      updateNotificationList();
      updateNotificationBadge();
    }
  }

  function getNotificationIcon(type) {
    const icons = {
      'System': '🔔',
      'Financial': '💰',
      'Alert': '⚠️',
      'Success': '✅',
      'Info': 'ℹ️'
    };
    return icons[type] || '🔔';
  }

  function formatTimestamp(timestamp) {
    const now = new Date();
    const diff = now - timestamp;
    const minutes = Math.floor(diff / 60000);
    const hours = Math.floor(diff / 3600000);
    const days = Math.floor(diff / 86400000);

    if (minutes < 1) return 'Just now';
    if (minutes < 60) return `${minutes}m ago`;
    if (hours < 24) return `${hours}h ago`;
    return `${days}d ago`;
  }

  function showToast(message, type = 'info') {
    const toast = document.getElementById('toast');
    const colors = {
      'info': 'text-blue-700 border-blue-200 bg-blue-50 dark:bg-blue-900/20 dark:border-blue-800 dark:text-blue-300',
      'success': 'text-green-700 border-green-200 bg-green-50 dark:bg-green-900/20 dark:border-green-800 dark:text-green-300',
      'warning': 'text-yellow-700 border-yellow-200 bg-yellow-50 dark:bg-yellow-900/20 dark:border-yellow-800 dark:text-yellow-300',
      'error': 'text-red-700 border-red-200 bg-red-50 dark:bg-red-900/20 dark:border-red-800 dark:text-red-300'
    };
    
    toast.innerHTML = `<div class="toast-card ${colors[type]} slideInRight">${message}</div>`;
    toast.classList.remove('hidden');
    
    clearTimeout(toast._timer);
    toast._timer = setTimeout(() => {
      toast.classList.add('hidden');
    }, 3000);
  }

  function refreshNotificationStyles() {
    // Force re-render of notification list to apply current theme
    updateNotificationList();
  }

  function initNotificationSystem() {
    // Add some sample notifications
    setTimeout(() => {
      addNotificationToPanel('System', 'Reports module loaded successfully');
    }, 1000);
    
    setTimeout(() => {
      addNotificationToPanel('Financial', 'Monthly reports are ready for review');
    }, 3000);

    // Listen for theme changes
    const observer = new MutationObserver((mutations) => {
      mutations.forEach((mutation) => {
        if (mutation.type === 'attributes' && mutation.attributeName === 'class') {
          refreshNotificationStyles();
        }
      });
    });

    observer.observe(document.documentElement, {
      attributes: true,
      attributeFilter: ['class']
    });
  }

  // Initialize notification system
  document.addEventListener('DOMContentLoaded', () => {
    initNotificationSystem();
  });
  </script>
</body>
</html>
