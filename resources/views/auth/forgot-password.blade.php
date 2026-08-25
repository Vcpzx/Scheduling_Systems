<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Forgot Password — Campus Scheduler</title>
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

</body>
</html>