<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>@yield('title', 'Campus Scheduler — Academic & Room Management')</title>
  <link rel="stylesheet" href="{{ asset('css/style.css') }}">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
</head>
<body>
@auth
  <header class="cs-navbar">
    <a href="{{ Auth::user()->isAdmin() ? route('admin.dashboard') : (Auth::user()->isTeacher() ? route('teacher.dashboard') : route('student.dashboard')) }}" class="cs-brand">
      <span class="cs-logo-badge">CS</span>
      <span>Campus Scheduler</span>
    </a>

    <nav class="cs-nav-links">
      @if(Auth::user()->isTeacher())
        <a href="{{ route('teacher.dashboard', ['tab' => 'schedule']) }}" class="cs-nav-item {{ request()->routeIs('teacher.dashboard') && (request('tab', 'schedule') == 'schedule') ? 'active' : '' }}">
          <span>Schedule</span>
        </a>
        <a href="{{ route('teacher.dashboard', ['tab' => 'semesters']) }}" class="cs-nav-item {{ request('tab') == 'semesters' ? 'active' : '' }}">
          <span>Semesters & Classes</span>
        </a>
        <a href="{{ route('teacher.dashboard', ['tab' => 'room-requests']) }}" class="cs-nav-item {{ request('tab') == 'room-requests' ? 'active' : '' }}">
          <span>Room Requests</span>
        </a>
        <a href="{{ route('teacher.dashboard', ['tab' => 'notifications']) }}" class="cs-nav-item {{ request('tab') == 'notifications' ? 'active' : '' }}">
          <span>Notifications</span>
        </a>
      @elseif(Auth::user()->isStudent())
        <a href="{{ route('student.dashboard', ['tab' => 'schedule']) }}" class="cs-nav-item {{ request()->routeIs('student.dashboard') && (request('tab', 'schedule') == 'schedule') ? 'active' : '' }}">
          <span>My Schedule</span>
        </a>
        <a href="{{ route('student.dashboard', ['tab' => 'registration']) }}" class="cs-nav-item {{ request('tab') == 'registration' ? 'active' : '' }}">
          <span>Subject Registration</span>
        </a>
        <a href="{{ route('student.dashboard', ['tab' => 'notifications']) }}" class="cs-nav-item {{ request('tab') == 'notifications' ? 'active' : '' }}">
          <span>Notifications</span>
        </a>
        
  </a>
      @endif

      <a href="{{ route('schedule.index') }}" class="cs-nav-item {{ request()->routeIs('schedule.index') ? 'active' : '' }}">
        <span>Schedules</span>
      </a>

      @if(Auth::user()->isAdmin())
        <a href="{{ route('admin.dashboard') }}" class="cs-nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
          <span>⚙️ Admin Workspace</span>
        </a>
      @endif
    </nav>

    <div class="cs-user-nav">
      <div class="cs-user-pill">
        <div class="cs-user-avatar">
          {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
        </div>
        <div class="cs-user-details">
          <span class="cs-user-name">{{ Auth::user()->name }}</span>
          <span class="cs-user-role">{{ Auth::user()->user_id }} · {{ ucfirst(Auth::user()->role) }}{{ Auth::user()->section ? ' ('.Auth::user()->section.')' : '' }}</span>
        </div>
      </div>
      <form method="POST" action="{{ route('logout') }}" style="display:inline;">
        @csrf
        <button class="cs-button cs-button-secondary cs-button-sm" type="submit" title="Sign Out">Sign out</button>
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
    // Universal Password Show/Hide Toggle
    document.addEventListener('DOMContentLoaded', function () {
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
            : `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>`;
        });
      });

      // Auto-hide toast notifications after 5 seconds
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