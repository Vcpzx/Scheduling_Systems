<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
  <title>Forgot Password — Campus Scheduler</title>
  <link rel="stylesheet" href="{{ asset('css/style.css') }}">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <script>
    (function() {
      var savedTheme = localStorage.getItem('cs_theme');
      var prefersDark = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
      if (savedTheme === 'dark' || (!savedTheme && prefersDark)) {
        document.documentElement.setAttribute('data-theme', 'dark');
        document.documentElement.classList.add('dark');
      } else {
        document.documentElement.setAttribute('data-theme', 'light');
        document.documentElement.classList.remove('dark');
      }
    })();
  </script>
</head>
<body class="cs-auth-page">

  <div style="position: fixed; top: 1rem; right: 1rem; z-index: 100;">
    <button type="button" class="cs-theme-toggle-btn cs-theme-toggle-trigger" style="width: auto; padding: 0.5rem 0.85rem;" title="Toggle Theme" aria-label="Toggle Theme">
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
      <span class="cs-theme-label">Theme</span>
    </button>
  </div>

  <div class="cs-auth-card">
    <div class="cs-auth-header">
      <div style="display: flex; justify-content: center; margin-bottom: 1rem;">
        <span class="cs-logo-badge" style="font-size: 1.25rem; padding: 0.6rem 0.9rem;">CS</span>
      </div>
      <h2>Reset Password</h2>
      <p>Enter your User ID or Email to verify your account</p>
    </div>

    @if($errors->has('identifier'))
      <div class="cs-alert cs-alert-danger">
        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
        <span>{{ $errors->first('identifier') }}</span>
      </div>
    @endif

    <form method="POST" action="{{ route('password.email') }}" class="cs-form-grid">
      @csrf
      <div class="cs-form-group">
        <label class="cs-form-label" for="identifier">User ID or Registered Email</label>
        <input class="cs-input" type="text" id="identifier" name="identifier" value="{{ old('identifier') }}" placeholder="e.g. T-101, S-201, or email@campus.edu" required autofocus>
        <span class="cs-form-help">Teachers and students can recover their password using their assigned ID or email.</span>
      </div>

      <button type="submit" class="cs-button" style="width: 100%; margin-top: 0.5rem;">
        Continue to Reset Password
      </button>
    </form>

    <div class="cs-auth-footer">
      Remembered your password? <a href="{{ route('login') }}" style="font-weight: 700;">Back to Sign In</a>
    </div>
  </div>

  <script>
    document.addEventListener('DOMContentLoaded', function () {
      const themeTrigger = document.querySelector('.cs-theme-toggle-trigger');
      if (themeTrigger) {
        themeTrigger.addEventListener('click', function (e) {
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
      }
    });
  </script>
</body>
</html>