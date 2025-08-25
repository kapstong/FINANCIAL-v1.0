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
  <title>Accounts Receivable</title>
  <link rel="icon" type="image/png" href="logo2.png">
  <script src="https://cdn.tailwindcss.com"></script>

  <style>
    :root{
      --brand:#0f1c49; --brand-600:#0c173c; --brand-100:#e8ecf9;
      --ink:#000; --muted:#000; --ring:0 0 0 3px rgba(15,28,73,.15);
      --card:#ffffff; --border:#eef2f7;
    }
    
    /* Dark mode variables */
    html.dark {
      --ink: #e5e7eb;
      --muted: #9ca3af;
      --card: rgba(17,24,39,.92);
      --border: rgba(71,85,105,.55);
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
        radial-gradient(60% 60% at 100% 0%, #ffe7cc 0%, transparent 55%),
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
    .btn{ display:inline-flex; align-items:center; gap:.5rem; padding:.55rem .95rem; border-radius:.65rem; font-weight:600; color:var(--ink) }
    .btn-brand{ background:var(--brand); color:#fff } .btn-brand:hover{ background:var(--brand-600) }
    .btn-soft{ background:#fff; border:1px solid var(--border); color:var(--ink) } .btn-soft:hover{ background:#f8fafc }
    html.dark .btn-soft{ background:var(--card); border-color:var(--border); color:var(--ink); }
    html.dark .btn-soft:hover{ background:rgba(31,41,55,.92); }
    .top-chip{ height:3px; background:var(--brand) }
    .sidebar-transition{ transition:transform .28s ease }
    .overlay{ display:none } .overlay.active{ display:block; position:fixed; inset:0; background:rgba(0,0,0,.35); z-index:40 }

    .tab-pill{ padding:.4rem .8rem; border-radius:9999px; border:1px solid var(--border); font-weight:700; font-size:.9rem; color:var(--ink) }
    .tab-pill.active{ background:var(--brand); color:#fff; border-color:var(--brand) }
    .toast-card{ background:#fff; border:1px solid var(--border); border-radius:.75rem; padding:.6rem .9rem; box-shadow:0 10px 30px rgba(0,0,0,.08) }
    
    /* Dark mode specific styles */
    html.dark .btn{ color:var(--ink); }
    html.dark .toast-card{ background:var(--card); border-color:var(--border); color:var(--ink); }
    
    /* Dark mode text colors */
    html.dark .text-slate-700 {
      color: var(--muted);
    }
    
    /* Dark mode for tables */
    html.dark thead tr {
      background: rgba(31,41,55,.92);
    }
    
    html.dark tbody tr:hover {
      background: rgba(31,41,55,.92);
    }
    
    html.dark .bg-orange-50 {
      background: rgba(194,65,12,.2);
    }
    
    html.dark .text-orange-800 {
      color: #fb923c;
    }
    
    html.dark .text-red-600 {
      color: #f87171;
    }
    
    /* Dark mode for status badges */
    html.dark .bg-orange-100 {
      background: rgba(194,65,12,.3);
    }
    
    /* Dark mode for table borders */
    html.dark .border-t {
      border-color: var(--border);
    }
    
    /* Dark mode context bar */
    html.dark #contextBar {
      background: rgba(17,24,39,.8);
      border-color: var(--border);
    }
    
    /* Dark mode sidebar */
    html.dark #sidebar {
      background: var(--card);
      border-color: var(--border);
    }
    
    /* Enhanced Sidebar */
    #sidebar {
      background: rgba(255, 255, 255, 0.95) !important;
      backdrop-filter: blur(20px) !important;
      border-right: 1px solid rgba(0, 0, 0, 0.1) !important;
      box-shadow: 4px 0 20px rgba(0, 0, 0, 0.05) !important;
    }
    
    .sidebar-item {
      border-radius: 12px !important;
      margin: 2px 8px !important;
      transition: all 0.3s ease !important;
      border: 1px solid transparent !important;
      display: flex !important;
      align-items: center !important;
      gap: 0.6rem !important;
      width: 100% !important;
      padding: 0.5rem 0.75rem !important;
      color: var(--ink) !important;
    }
    
    .sidebar-item:hover {
      background: rgba(15, 28, 73, 0.08) !important;
      border-color: rgba(15, 28, 73, 0.1) !important;
      transform: translateX(4px) !important;
    }
    
    .sidebar-item.active {
      background: linear-gradient(135deg, rgba(15, 28, 73, 0.15) 0%, rgba(15, 28, 73, 0.1) 100%) !important;
      border-color: rgba(15, 28, 73, 0.2) !important;
      box-shadow: 0 2px 8px rgba(15, 28, 73, 0.1) !important;
      color: var(--brand) !important;
      font-weight: 700 !important;
    }
    
    /* Dark Mode Enhanced Sidebar */
    html.dark #sidebar {
      background: rgba(17, 24, 39, 0.95) !important;
      border-right-color: rgba(255, 255, 255, 0.1) !important;
      box-shadow: 4px 0 20px rgba(0, 0, 0, 0.3) !important;
    }
    
    html.dark .sidebar-item {
      color: var(--ink) !important;
    }
    
    html.dark .sidebar-item:hover {
      background: rgba(255, 255, 255, 0.08) !important;
      border-color: rgba(255, 255, 255, 0.15) !important;
    }
    
    html.dark .sidebar-item.active {
      background: linear-gradient(135deg, rgba(255, 255, 255, 0.15) 0%, rgba(255, 255, 255, 0.1) 100%) !important;
      border-color: rgba(255, 255, 255, 0.2) !important;
      color: var(--brand-100) !important;
    }
    
    /* Dark mode for enhanced filter sections */
    html.dark .bg-slate-50 {
      background: rgba(31,41,55,.92);
      border-color: var(--border);
    }
    
    /* Dark mode for filter inputs and selects */
    html.dark input[type="date"],
    html.dark select {
      background: var(--card);
      border-color: var(--border);
      color: var(--ink);
    }
    
    html.dark input[type="date"]:focus,
    html.dark select:focus {
      border-color: var(--brand);
      box-shadow: 0 0 0 3px rgba(15,28,73,.15);
    }
    
    /* Dark mode for enhanced table borders */
    html.dark .border-slate-200 {
      border-color: var(--border);
    }
    
    html.dark .border-slate-100 {
      border-color: var(--border);
    }
    
    /* Dark mode for table rows */
    html.dark tbody tr {
      background: rgba(31, 41, 55, 0.92) !important;
      color: var(--ink) !important;
    }
    
    html.dark tbody tr:hover {
      background: rgba(51, 65, 85, 0.92) !important;
    }
    
    html.dark thead tr {
      background: rgba(31, 41, 55, 0.92) !important;
      color: var(--ink) !important;
    }
    
    /* Dark mode for table text */
    html.dark .text-gray-900 {
      color: var(--ink) !important;
    }
    
    html.dark .text-gray-600 {
      color: var(--muted) !important;
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
    
    html.dark .bg-orange-50 {
      background: rgba(194,65,12,.2);
      border-color: rgba(251,146,60,.3);
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
    
    html.dark .text-orange-800 {
      color: #fb923c;
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
    
    html.dark .text-orange-600 {
      color: #f97316;
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
    /* Dark mode text color  */
    html.dark .text-slate-800 {
      color: var(--ink);
    }
    
    html.dark .text-slate-500 {
      color: var(--muted);
    }
    
    html.dark .text-slate-600 {
      color: var(--muted);
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
    .ar-card {
      transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    
    .ar-card:hover {
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
          <h2 class="text-2xl font-bold text-white mb-2">Loading Accounts Receivable</h2>
          <p class="text-slate-300 text-sm">Preparing your receivables data...</p>
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
        <span class="font-semibold text-slate-800">Accounts Receivable</span>
      </div>
      <nav id="contextTabs" class="flex flex-wrap gap-2">
        <a class="tab-pill" href="#Accounts%20Receivable/ar-open">Open Invoices</a>
        <a class="tab-pill" href="#Accounts%20Receivable/ar-pay">Payment History</a>
        <a class="tab-pill" href="#Accounts%20Receivable/ar-aging">Aging Report</a>
      </nav>
    </div>
  </div>

  <!-- LAYOUT -->
  <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 grid grid-cols-1 lg:grid-cols-[240px_1fr] gap-6 py-6">
    <div id="overlay" class="overlay"></div>

    <!-- SIDEBAR -->
    <aside id="sidebar" class="fixed lg:static left-0 top-14 lg:top-auto w-64 lg:w-full h-[calc(100vh-56px)] lg:h-auto bg-white border-r border-[var(--ring)] sidebar-transition -translate-x-full lg:translate-x-0 z-50 overflow-y-auto">
      <nav class="p-3 space-y-1">
        <div class="text-[11px] uppercase tracking-widest text-slate-500 px-2 pt-2 pb-1">Navigation</div>
        <a class="sidebar-item" href="index.php"><span>🏠</span><span>Dashboard</span></a>

        <a class="sidebar-item" href="General Ledger.php"><span>📘</span><span>General Ledger</span></a>
        <a class="sidebar-item active" href="Accounts Receivable.php"><span>💳</span><span>Accounts Receivable</span></a>
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

    /* --------- AR tabs + view --------- */
    const AR_TABS = [
      { id:'ar-open',  label:'Open Invoices' },
      { id:'ar-pay',   label:'Payment History' },
      { id:'ar-aging', label:'Aging Report' },
    ];

    function decodeHash(){
      const h=(location.hash||'#Accounts%20Receivable/ar-open').slice(1);
      const [mod, tab]=h.split('/');
      return { module: decodeURIComponent(mod||'Accounts Receivable'), tab: decodeURIComponent(tab||'ar-open') };
    }

    function markActiveTab(tabId){
      $$('#contextTabs .tab-pill').forEach(a=>{
        a.classList.toggle('active', a.getAttribute('href') === `#Accounts%20Receivable/${tabId}`);
      });
    }

    function renderView(tabId){
      const tab = AR_TABS.find(t=>t.id===tabId) || AR_TABS[0];
      const host = $('#contentHost');
      host.innerHTML = `
        <section class="card p-5">
          <div class="flex items-center justify-between">
            <div>
              <div class="text-xs uppercase tracking-wide text-[var(--muted)]">ACCOUNTS RECEIVABLE</div>
              <h2 class="text-xl font-bold">${tab.label}</h2>
            </div>
            <div class="flex gap-2">
              <button class="btn btn-soft">+ Add</button>
              <button class="btn btn-brand">Export</button>
            </div>
          </div>

          <div class="mt-6 space-y-6">
            <!-- Enhanced Filter Section -->
            <div class="bg-slate-50 border border-slate-200 rounded-lg p-4">
              <div class="flex flex-wrap gap-4 items-end">
                <div>
                  <label class="block text-sm font-medium text-slate-700 mb-2">Customer</label>
                  <select class="w-48 px-3 py-2 border border-slate-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="">All Customers</option>
                    <option value="customer1">Customer A</option>
                    <option value="customer2">Customer B</option>
                    <option value="customer3">Customer C</option>
                  </select>
                </div>
                <div>
                  <label class="block text-sm font-medium text-slate-700 mb-2">Status</label>
                  <select class="w-40 px-3 py-2 border border-slate-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="">All Status</option>
                    <option value="open">Open</option>
                    <option value="paid">Paid</option>
                    <option value="overdue">Overdue</option>
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
                    <th class="text-left px-4 py-3 font-semibold text-slate-700">Customer</th>
                    <th class="text-left px-4 py-3 font-semibold text-slate-700">Description</th>
                    <th class="text-right px-4 py-3 font-semibold text-slate-700">Amount</th>
                    <th class="text-center px-4 py-3 font-semibold text-slate-700">Status</th>
                    <th class="text-center px-4 py-3 font-semibold text-slate-700">Actions</th>
                  </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                  <tr class="hover:bg-slate-50 transition-colors">
                    <td class="px-4 py-3 text-slate-900">2025-08-01</td>
                    <td class="px-4 py-3 font-mono text-slate-900 font-medium">INV-0001</td>
                    <td class="px-4 py-3 text-slate-700">Sample Customer</td>
                    <td class="px-4 py-3 text-slate-700">Sample invoice for <b>${tab.label}</b></td>
                    <td class="px-4 py-3 text-right font-mono text-slate-900">₱1,200.00</td>
                    <td class="px-4 py-3 text-center">
                      <span class="px-2 py-1 bg-orange-100 text-orange-800 rounded-full text-xs font-medium">Open</span>
                    </td>
                    <td class="px-4 py-3 text-center">
                      <div class="flex items-center justify-center gap-2">
                        <button class="text-blue-600 hover:text-blue-800 text-sm font-medium px-2 py-1 rounded hover:bg-blue-50">View</button>
                        <button class="text-slate-600 hover:text-slate-800 text-sm font-medium px-2 py-1 rounded hover:bg-slate-50">Edit</button>
                        <button class="text-red-600 hover:text-red-800 text-sm font-medium px-2 py-1 rounded hover:bg-red-50">Delete</button>
                      </div>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
            
            <!-- Enhanced Summary Section -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
              <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                <div class="text-xs uppercase tracking-wide text-blue-600 font-semibold">Total Outstanding</div>
                <div class="text-2xl font-bold text-blue-800 mt-1">₱12,450.00</div>
              </div>
              <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                <div class="text-xs uppercase tracking-wide text-green-600 font-semibold">Total Paid</div>
                <div class="text-2xl font-bold text-green-800 mt-1">₱8,200.00</div>
              </div>
              <div class="bg-orange-50 border border-orange-200 rounded-lg p-4">
                <div class="text-xs uppercase tracking-wide text-orange-600 font-semibold">Overdue</div>
                <div class="text-2xl font-bold text-orange-800 mt-1">₱2,100.00</div>
              </div>
              <div class="bg-slate-50 border border-slate-200 rounded-lg p-4">
                <div class="text-xs uppercase tracking-wide text-slate-600 font-semibold">Total Invoices</div>
                <div class="text-2xl font-bold text-slate-800 mt-1">24</div>
              </div>
            </div>
            
            <div class="text-sm text-slate-500 text-center py-2">
              ${tab.label} • Last updated: ${new Date().toLocaleString()}
            </div>
          </div>
        </section>
      `;
    }

    /* --------- Hash change handling --------- */
    function handleHashChange(){
      const { tab } = decodeHash();
      markActiveTab(tab);
      renderView(tab);
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
      const cards = document.querySelectorAll('.ar-card, .card');
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

    /* --------- Initialize --------- */
    document.addEventListener('DOMContentLoaded', () => {
      // Start loading screen
      initLoadingScreen();
      
      // Set initial hash if none exists
      if(!location.hash){
        location.hash = '#Accounts%20Receivable/ar-open';
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
      
      // Simulate real-time notifications for Accounts Receivable
      simulateARNotifications();
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

    // Simulate Accounts Receivable-specific notifications
    function simulateARNotifications() {
      // Invoice notifications (every 2 minutes)
      setInterval(() => {
        if (Math.random() < 0.25) { // 25% chance
          const invoices = [
            'New invoice created: INV-' + Math.floor(Math.random() * 1000 + 100) + ' for ₱' + Math.floor(Math.random() * 50000 + 10000).toLocaleString('en-US'),
            'Invoice overdue: INV-' + Math.floor(Math.random() * 1000 + 100) + ' is ' + Math.floor(Math.random() * 30 + 1) + ' days past due',
            'Payment received: ₱' + Math.floor(Math.random() * 30000 + 5000).toLocaleString('en-US') + ' for invoice INV-' + Math.floor(Math.random() * 1000 + 100),
            'Customer credit limit reached: ₱' + Math.floor(Math.random() * 100000 + 50000).toLocaleString('en-US')
          ];
          const randomInvoice = invoices[Math.floor(Math.random() * invoices.length)];
          addNotificationToPanel('financial', randomInvoice);
        }
      }, 120000);

      // Collection notifications (every 3 minutes)
      setInterval(() => {
        if (Math.random() < 0.2) { // 20% chance
          const collections = [
            'Collection call scheduled: Customer A - Invoice INV-' + Math.floor(Math.random() * 1000 + 100),
            'Payment reminder sent: 3 invoices overdue for Customer B',
            'Collection report generated: ₱' + Math.floor(Math.random() * 200000 + 100000).toLocaleString('en-US') + ' outstanding',
            'Customer payment plan approved: 6-month installment for ₱' + Math.floor(Math.random() * 50000 + 20000).toLocaleString('en-US')
          ];
          const randomCollection = collections[Math.floor(Math.random() * collections.length)];
          addNotificationToPanel('system', randomCollection);
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
