<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Campus Scheduler - Login</title>
  <style>
    * { box-sizing: border-box; }
    body { margin: 0; padding: 1rem; font-family: "Trebuchet MS", "Segoe UI", sans-serif; background: linear-gradient(135deg, #f8fafc 0%, #f3f7f1 100%); display: flex; align-items: center; justify-content: center; min-height: 100vh; }
    .card { width: min(100%, 380px); padding: clamp(1.25rem, 5vw, 2rem); background: #ffffff; border: 1px solid #e2e8f0; border-radius: 8px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); }
    h2 { margin-top: 0; text-align: center; color: #1e293b; }
    p { text-align: center; color: #64748b; font-size: 0.9rem; margin-bottom: 1.5rem; }
    .group { display: flex; flex-direction: column; gap: 0.4rem; margin-bottom: 1rem; }
    label { font-size: 0.85rem; font-weight: 600; color: #334155; }
    input { width: 100%; min-height: 2.75rem; padding: 0.6rem; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 0.9rem; }
    button { width: 100%; padding: 0.75rem; background: #2563eb; color: white; border: none; border-radius: 6px; font-weight: 600; cursor: pointer; margin-top: 0.5rem; }
    button:hover { background: #1d4ed8; }
    .error { padding: 0.75rem; background: #fee2e2; color: #991b1b; border-radius: 6px; font-size: 0.85rem; margin-bottom: 1rem; }
  </style>
</head>
<body>
  <div class="card">
    <h2>Campus Scheduler</h2>
    <p>Sign in to your account</p>

    @if($errors->has('auth'))
      <div class="error">{{ $errors->first('auth') }}</div>
    @endif
    @if(session('success'))
      <div style="padding: .75rem; background: #dcfce7; color: #166534; border-radius: 6px; font-size: .85rem; margin-bottom: 1rem;">{{ session('success') }}</div>
    @endif

    <form method="POST" action="{{ route('login') }}">
      @csrf
      <div class="group">
        <label>User ID</label>
        <input type="text" name="user_id" placeholder="Enter Your ID" required>
      </div>

      <div class="group">
        <label>Password</label>
        <input type="password" name="password" placeholder="Enter Your Password" required>
      </div>

      <button type="submit">Sign In</button>
      <!-- Add this below <button type="submit">Sign In</button> -->
<p style="text-align: center; margin-top: 1.5rem; font-size: 0.85rem; color: #64748b;">
  Don't have an account? <a href="{{ route('register') }}" style="color: #2563eb; text-decoration: none; font-weight: 600;">Create Account</a>
</p>
    </form>
  </div>
</body>
</html>