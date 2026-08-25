<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Set New Password — Campus Scheduler</title>
  <link rel="stylesheet" href="{{ asset('css/style.css') }}">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
</head>
<body class="cs-auth-page">

  <div class="cs-auth-card">
    <div class="cs-auth-header">
      <div style="display: flex; justify-content: center; margin-bottom: 1rem;">
        <span class="cs-logo-badge" style="font-size: 1.25rem; padding: 0.6rem 0.9rem;">CS</span>
      </div>
      <h2>Set New Password</h2>
      <p>Enter your account's new secure password</p>
    </div>

    @if(session('info'))
      <div class="cs-alert cs-alert-info">
        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="8"></line></svg>
        <span>{{ session('info') }}</span>
      </div>
    @endif

    @if($errors->any())
      <div class="cs-alert cs-alert-danger">
        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
        <span>{{ $errors->first() }}</span>
      </div>
    @endif

    <form method="POST" action="{{ route('password.update') }}" class="cs-form-grid">
      @csrf

      <div class="cs-form-group">
        <label class="cs-form-label" for="user_id">User ID</label>
        <input class="cs-input" type="text" id="user_id" name="user_id" value="{{ $userId ?? old('user_id') }}" placeholder="e.g. T-101, S-201" required {{ !empty($userId) ? 'readonly' : '' }}>
      </div>

      <div class="cs-form-group">
        <label class="cs-form-label" for="password">New Password</label>
        <div class="cs-password-wrapper">
          <input class="cs-input" type="password" id="password" name="password" minlength="6" placeholder="Enter at least 6 characters" required autofocus>
          <button type="button" class="cs-password-toggle" title="Toggle password visibility" aria-label="Show or hide password">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
          </button>
        </div>
      </div>

      <div class="cs-form-group">
        <label class="cs-form-label" for="password_confirmation">Confirm New Password</label>
        <div class="cs-password-wrapper">
          <input class="cs-input" type="password" id="password_confirmation" name="password_confirmation" minlength="6" placeholder="Repeat your new password" required>
          <button type="button" class="cs-password-toggle" title="Toggle password visibility" aria-label="Show or hide password">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
          </button>
        </div>
      </div>

      <button type="submit" class="cs-button cs-button-success" style="width: 100%; margin-top: 0.5rem;">
        Update Password & Sign In
      </button>
    </form>

    <div class="cs-auth-footer">
      <a href="{{ route('login') }}" style="font-weight: 700;">Back to Sign In</a>
    </div>
  </div>

  <script>
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

          button.innerHTML = isPassword
            ? `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path><line x1="1" y1="1" x2="23" y2="23"></line></svg>`
            : `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>`;
        });
      });
    });
  </script>
</body>
</html>