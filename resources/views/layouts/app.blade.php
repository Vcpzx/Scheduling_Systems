<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
  <title>@yield('title', 'Campus Scheduler — Academic & Room Management')</title>
  <link rel="stylesheet" href="{{ asset('css/style.css') }}">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <script>
    (function() {
      // Immediate Theme Pre-render Check
      var savedTheme = localStorage.getItem('cs_theme');
      var prefersDark = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
      if (savedTheme === 'dark' || (!savedTheme && prefersDark)) {
        document.documentElement.setAttribute('data-theme', 'dark');
        document.documentElement.classList.add('dark');
      } else {
        document.documentElement.setAttribute('data-theme', 'light');
        document.documentElement.classList.remove('dark');
      }

      // Immediate Sidebar Pre-render Check
      if (localStorage.getItem('cs_sidebar_collapsed') === 'true') {
        document.documentElement.classList.add('sidebar-collapsed');
      }
    })();
  </script>
</head>
<body>
@auth
  <!-- Mobile Topbar (< 1024px) -->
  <div class="cs-mobile-topbar">
    <div class="cs-mobile-left">
      <button type="button" class="cs-mobile-menu-btn" id="csMobileMenuBtn" aria-label="Open Navigation Menu">
        <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <line x1="3" y1="12" x2="21" y2="12"></line>
          <line x1="3" y1="6" x2="21" y2="6"></line>
          <line x1="3" y1="18" x2="21" y2="18"></line>
        </svg>
      </button>
      <a href="{{ Auth::user()->isAdmin() ? route('schedule.index') : (Auth::user()->isTeacher() ? route('teacher.dashboard') : route('student.dashboard')) }}" class="cs-mobile-brand">
        <span class="cs-logo-badge">CS</span>
        <span>Campus Scheduler</span>
      </a>
    </div>
    <div class="cs-mobile-actions">
      <button type="button" class="cs-mobile-theme-btn cs-theme-toggle-trigger" title="Toggle Theme" aria-label="Toggle Theme">
        <span class="cs-theme-icon-sun">
          <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="12" cy="12" r="5"></circle>
            <line x1="12" y1="1" x2="12" y2="3"></line>
            <line x1="12" y1="21" x2="12" y2="23"></line>
            <line x1="4.22" y1="4.22" x2="5.64" y2="5.64"></line>
            <line x1="18.36" y1="18.36" x2="19.78" y2="19.78"></line>
            <line x1="1" y1="12" x2="3" y2="12"></line>
            <line x1="21" y1="12" x2="23" y2="12"></line>
            <line x1="4.22" y1="19.78" x2="5.64" y2="18.36"></line>
            <line x1="18.36" y1="5.64" x2="19.78" y2="4.22"></line>
          </svg>
        </span>
        <span class="cs-theme-icon-moon">
          <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"></path>
          </svg>
        </span>
      </button>
    </div>
  </div>

  <!-- Off-Canvas Sidebar Backdrop for Mobile -->
  <div class="cs-sidebar-backdrop" id="csSidebarBackdrop"></div>

  <!-- Vertical Collapsible Sidebar -->
  <header class="cs-navbar">
    <div class="cs-sidebar-header">
      <a href="{{ Auth::user()->isAdmin() ? route('schedule.index') : (Auth::user()->isTeacher() ? route('teacher.dashboard') : route('student.dashboard')) }}" class="cs-brand" title="Campus Scheduler">
        <span class="cs-logo-badge">CS</span>
        <span class="cs-brand-text">Campus Scheduler</span>
      </a>
      <button type="button" class="cs-sidebar-toggle" id="csSidebarToggle" title="Collapse / Expand Sidebar" aria-label="Toggle Sidebar">
        <svg class="cs-toggle-icon" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <polyline points="15 18 9 12 15 6"></polyline>
        </svg>
      </button>
    </div>

    <nav class="cs-nav-links">
      @if(Auth::user()->isTeacher())
        <a href="{{ route('teacher.dashboard', ['tab' => 'schedule']) }}" class="cs-nav-item {{ request()->routeIs('teacher.dashboard') && (request('tab', 'schedule') == 'schedule') ? 'active' : '' }}" title="Subjects">
          <span class="cs-nav-icon">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path></svg>
          </span>
          <span class="cs-nav-label">Subjects</span>
        </a>
        <a href="{{ route('teacher.dashboard', ['tab' => 'semesters']) }}" class="cs-nav-item {{ request('tab') == 'semesters' ? 'active' : '' }}" title="Semesters & Classes">
          <span class="cs-nav-icon">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
          </span>
          <span class="cs-nav-label">Semesters & Classes</span>
        </a>
        <a href="{{ route('teacher.dashboard', ['tab' => 'room-requests']) }}" class="cs-nav-item {{ request('tab') == 'room-requests' ? 'active' : '' }}" title="Room Requests">
          <span class="cs-nav-icon">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path><polyline points="9 22 9 12 15 12 15 22"></polyline></svg>
          </span>
          <span class="cs-nav-label">Room Requests</span>
        </a>
        <a href="{{ route('teacher.dashboard', ['tab' => 'notifications']) }}" class="cs-nav-item {{ request('tab') == 'notifications' ? 'active' : '' }}" title="Notifications">
          <span class="cs-nav-icon">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path><path d="M13.73 21a2 2 0 0 1-3.46 0"></path></svg>
          </span>
          <span class="cs-nav-label">Notifications</span>
        </a>
      @elseif(Auth::user()->isStudent())
        <a href="{{ route('student.dashboard', ['tab' => 'schedule']) }}" class="cs-nav-item {{ request()->routeIs('student.dashboard') && (request('tab', 'schedule') == 'schedule') ? 'active' : '' }}" title="My Schedule">
          <span class="cs-nav-icon">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
          </span>
          <span class="cs-nav-label">My Schedule</span>
        </a>
        <a href="{{ route('student.dashboard', ['tab' => 'registration']) }}" class="cs-nav-item {{ request('tab') == 'registration' ? 'active' : '' }}" title="Subject Registration">
          <span class="cs-nav-icon">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"></path><rect x="8" y="2" width="8" height="4" rx="1" ry="1"></rect><path d="M9 14l2 2 4-4"></path></svg>
          </span>
          <span class="cs-nav-label">Subject Registration</span>
        </a>
        <a href="{{ route('student.dashboard', ['tab' => 'notifications']) }}" class="cs-nav-item {{ request('tab') == 'notifications' ? 'active' : '' }}" title="Notifications">
          <span class="cs-nav-icon">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path><path d="M13.73 21a2 2 0 0 1-3.46 0"></path></svg>
          </span>
          <span class="cs-nav-label">Notifications</span>
        </a>
      @endif

      <a href="{{ route('schedule.index') }}" class="cs-nav-item {{ request()->routeIs('schedule.index') ? 'active' : '' }}" title="Schedules">
        <span class="cs-nav-icon">
          <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
        </span>
        <span class="cs-nav-label">Schedules</span>
      </a>

      @if(Auth::user()->isAdmin())
        <a href="{{ route('admin.dashboard') }}" class="cs-nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" title="Admin Workspace">
          <span class="cs-nav-icon">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3"></circle><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path></svg>
          </span>
          <span class="cs-nav-label">Admin Workspace</span>
        </a>
      @endif
    </nav>

    <div class="cs-user-nav">
      <!-- Theme Mode Toggle -->
      <button type="button" class="cs-theme-toggle-btn cs-theme-toggle-trigger" id="csThemeToggle" title="Toggle Light / Dark Theme" aria-label="Toggle Theme">
        <span class="cs-theme-icon-sun">
          <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="12" cy="12" r="5"></circle>
            <line x1="12" y1="1" x2="12" y2="3"></line>
            <line x1="12" y1="21" x2="12" y2="23"></line>
            <line x1="4.22" y1="4.22" x2="5.64" y2="5.64"></line>
            <line x1="18.36" y1="18.36" x2="19.78" y2="19.78"></line>
            <line x1="1" y1="12" x2="3" y2="12"></line>
            <line x1="21" y1="12" x2="23" y2="12"></line>
            <line x1="4.22" y1="19.78" x2="5.64" y2="18.36"></line>
            <line x1="18.36" y1="5.64" x2="19.78" y2="4.22"></line>
          </svg>
        </span>
        <span class="cs-theme-icon-moon">
          <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"></path>
          </svg>
        </span>
        <span class="cs-theme-label">Theme Mode</span>
      </button>

      <div class="cs-user-pill" title="{{ Auth::user()->name }} ({{ Auth::user()->user_id }})">
        <div class="cs-user-avatar">
          {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
        </div>
        <div class="cs-user-details">
          <span class="cs-user-name">{{ Auth::user()->name }}</span>
          <span class="cs-user-role">{{ Auth::user()->user_id }} · {{ ucfirst(Auth::user()->role) }}{{ Auth::user()->section ? ' ('.Auth::user()->section.')' : '' }}</span>
        </div>
      </div>

      <form method="POST" action="{{ route('logout') }}" class="cs-logout-form">
        @csrf
        <button class="cs-button cs-button-secondary cs-button-sm cs-logout-btn" type="submit" title="Sign Out">
          <svg class="cs-logout-icon" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
            <polyline points="16 17 21 12 16 7"></polyline>
            <line x1="21" y1="12" x2="9" y2="12"></line>
          </svg>
          <span class="cs-logout-text">Sign out</span>
        </button>
      </form>
    </div>
  </header>
@endauth

  <!-- Global Toasts -->
  <div class="cs-toast-container">
    @if(session('success'))
      <div class="cs-toast cs-alert-success">{{ session('success') }}</div>
    @endif
    @if(session('info'))
      <div class="cs-toast cs-alert-info">{{ session('info') }}</div>
    @endif
    @if(session('error'))
      <div class="cs-toast cs-alert-danger">{{ session('error') }}</div>
    @endif
    @if($errors->any())
      <div class="cs-toast cs-alert-danger">{{ $errors->first() }}</div>
    @endif
  </div>

  <main class="cs-main">
    @yield('content')
  </main>

  <script>
    document.addEventListener('DOMContentLoaded', function () {
      // 1. Theme Toggle Functionality (Dark / Light Mode)
      const themeTriggers = document.querySelectorAll('.cs-theme-toggle-trigger');
      themeTriggers.forEach(btn => {
        btn.addEventListener('click', function (e) {
          e.preventDefault();
          const currentTheme = document.documentElement.getAttribute('data-theme') || 'light';
          const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
          
          document.documentElement.setAttribute('data-theme', newTheme);
          if (newTheme === 'dark') {
            document.documentElement.classList.add('dark');
          } else {
            document.documentElement.classList.remove('dark');
          }
          localStorage.setItem('cs_theme', newTheme);
        });
      });

      // 2. Desktop Sidebar Collapse & State Persistence
      const sidebarToggle = document.getElementById('csSidebarToggle');
      const navbar = document.querySelector('.cs-navbar');

      if (navbar) {
        if (localStorage.getItem('cs_sidebar_collapsed') === 'true') {
          navbar.classList.add('collapsed');
          document.documentElement.classList.add('sidebar-collapsed');
        }

        if (sidebarToggle) {
          sidebarToggle.addEventListener('click', function () {
            navbar.classList.toggle('collapsed');
            const isCollapsed = navbar.classList.contains('collapsed');
            localStorage.setItem('cs_sidebar_collapsed', isCollapsed ? 'true' : 'false');
            if (isCollapsed) {
              document.documentElement.classList.add('sidebar-collapsed');
            } else {
              document.documentElement.classList.remove('sidebar-collapsed');
            }
          });
        }
      }

      // 3. Mobile Off-Canvas Drawer Navigation
      const mobileMenuBtn = document.getElementById('csMobileMenuBtn');
      const backdrop = document.getElementById('csSidebarBackdrop');

      if (mobileMenuBtn && navbar && backdrop) {
        mobileMenuBtn.addEventListener('click', function () {
          navbar.classList.add('mobile-open');
          backdrop.classList.add('active');
        });

        backdrop.addEventListener('click', function () {
          navbar.classList.remove('mobile-open');
          backdrop.classList.remove('active');
        });

        // Close drawer when a nav link is clicked on mobile
        const navLinks = navbar.querySelectorAll('.cs-nav-item');
        navLinks.forEach(link => {
          link.addEventListener('click', function () {
            if (window.innerWidth <= 1024) {
              navbar.classList.remove('mobile-open');
              backdrop.classList.remove('active');
            }
          });
        });
      }

      // 4. Universal Password Show/Hide Toggle
      const toggleButtons = document.querySelectorAll('.cs-password-toggle');
      toggleButtons.forEach(button => {
        button.addEventListener('click', function (e) {
          e.preventDefault();
          const wrapper = button.closest('.cs-password-wrapper');
          if (!wrapper) return;
          const input = wrapper.querySelector('input');
          if (!input) return;

          const isPassword = input.getAttribute('type') === 'password';
          input.setAttribute('type', isPassword ? 'text' : 'password');

          // Toggle Eye Icon
          button.innerHTML = isPassword
            ? `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path><line x1="1" y1="1" x2="23" y2="23"></line></svg>`
            : `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>`;
        });
      });

      // 5. Auto-hide toast notifications after 5 seconds
      setTimeout(() => {
        const toasts = document.querySelectorAll('.cs-toast');
        toasts.forEach(toast => {
          toast.style.transition = 'opacity 0.5s ease';
          toast.style.opacity = '0';
          setTimeout(() => toast.remove(), 500);
        });
      }, 5000);
    });
  </script>
</body>
</html>