<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Campus Scheduler - Register</title>
  <style>
    body { margin: 0; font-family: system-ui, -apple-system, sans-serif; background: #f8fafc; display: flex; align-items: center; justify-content: center; min-height: 100vh; }
    .card { width: 100%; max-width: 380px; padding: 2rem; background: #ffffff; border: 1px solid #e2e8f0; border-radius: 8px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); }
    h2 { margin-top: 0; text-align: center; color: #1e293b; }
    p { text-align: center; color: #64748b; font-size: 0.9rem; margin-bottom: 1.5rem; }
    .group { display: flex; flex-direction: column; gap: 0.4rem; margin-bottom: 1rem; }
    label { font-size: 0.85rem; font-weight: 600; color: #334155; }
    input, select { padding: 0.6rem; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 0.9rem; }
    button { width: 100%; padding: 0.75rem; background: #2563eb; color: white; border: none; border-radius: 6px; font-weight: 600; cursor: pointer; margin-top: 0.5rem; }
    button:hover { background: #1d4ed8; }
    .error { padding: 0.75rem; background: #fee2e2; color: #991b1b; border-radius: 6px; font-size: 0.85rem; margin-bottom: 1rem; }
  </style>
</head>
<body>
  <div class="card">
    <h2>Create Account</h2>
    <p>Sign up for a new account</p>

    @if($errors->any())
      <div class="error">{{ $errors->first() }}</div>
    @endif

    <form method="POST" action="{{ route('register') }}">
      @csrf
      <div class="group">
        <label>User ID</label>
        <input type="text" name="user_id" placeholder="e.g. SEC-002" required>
      </div>

      <div class="group">
        <label>Full Name</label>
        <input type="text" name="name" placeholder="Jane Doe" required>
      </div>

      <div class="group">
        <label>Role</label>
        <select name="role" required>
          <option value="student">Student</option>
          <option value="teacher">Teacher</option>
          <option value="secretary">Secretary</option>
          <option value="admin">Admin</option>
        </select>
      </div>

      <div class="group">
        <label>Password</label>
        <input type="password" name="password" required>
      </div>

      <button type="submit">Register & Sign In</button>
    </form>

    <p style="text-align: center; margin-top: 1.5rem; font-size: 0.85rem; color: #64748b;">
      Already have an account? <a href="{{ route('login') }}" style="color: #2563eb; text-decoration: none; font-weight: 600;">Sign In</a>
    </p>
  </div>
</body>
</html>