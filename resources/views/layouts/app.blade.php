<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Campus Scheduler</title>
  <link rel="stylesheet" href="{{ asset('css/style.css') }}">
  <style>
    body { margin: 0; font-family: system-ui, -apple-system, sans-serif; background-color: #f8fafc; }
    .cs-navbar { background: #ffffff; border-bottom: 1px solid #e2e8f0; padding: 0.75rem 2rem; display: flex; justify-content: space-between; align-items: center; }
    .cs-brand { font-size: 1.2rem; font-weight: 700; color: #1e293b; text-decoration: none; display: flex; align-items: center; gap: 0.5rem; }
    .cs-logo-badge { background: #2563eb; color: white; padding: 0.3rem 0.6rem; border-radius: 6px; font-size: 0.9rem; }
    .cs-user-nav { display: flex; align-items: center; gap: 1.5rem; }
    .cs-user-info { text-align: right; }
    .cs-user-name { font-size: 0.9rem; font-weight: 600; color: #1e293b; }
    .cs-user-role { font-size: 0.75rem; color: #64748b; }
    .cs-logout-btn { background: #ef4444; color: white; border: none; padding: 0.45rem 0.9rem; border-radius: 6px; font-size: 0.85rem; font-weight: 600; cursor: pointer; }
    .cs-logout-btn:hover { background: #dc2626; }
  </style>
</head>
<body>
  @auth
  <nav class="cs-navbar">
    <a href="{{ route('schedule.index') }}" class="cs-brand">
      <span class="cs-logo-badge">CS</span> Campus Scheduler
    </a>

    <div class="cs-user-nav">
      <div class="cs-user-info">
        <div class="cs-user-name">{{ Auth::user()->name }}</div>
        <div class="cs-user-role">{{ Auth::user()->user_id }} · {{ ucfirst(Auth::user()->role) }}</div>
      </div>

      <form method="POST" action="{{ route('logout') }}" style="margin: 0;">
        @csrf
        <button type="submit" class="cs-logout-btn">Logout</button>
      </form>
    </div>
  </nav>
  @endauth

  <main>
    @yield('content')
  </main>
</body>
</html>