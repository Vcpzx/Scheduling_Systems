<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>@yield('title', 'Campus Scheduler')</title>
  <link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>
<body class="cs-body">
@auth
  <header class="cs-navbar">
    <a href="{{ Auth::user()->isAdmin() ? route('admin.dashboard') : route('schedule.index') }}" class="cs-brand"><span class="cs-logo-badge">CS</span> Campus Scheduler</a>
    <nav class="cs-nav-links">
      <a href="{{ route('schedule.index') }}">Schedule</a>
      @if(Auth::user()->isAdmin()) <a href="{{ route('admin.dashboard') }}">Admin workspace</a> @endif
    </nav>
    <div class="cs-user-nav">
      <div class="cs-user-info"><strong>{{ Auth::user()->name }}</strong><small>{{ Auth::user()->user_id }} · {{ ucfirst(Auth::user()->role) }}</small></div>
      <form method="POST" action="{{ route('logout') }}">@csrf<button class="cs-button cs-button-danger" type="submit">Log out</button></form>
    </div>
  </header>
@endauth
  @if(session('success')) <div class="cs-toast cs-toast-success">{{ session('success') }}</div> @endif
  @if($errors->any()) <div class="cs-toast cs-toast-error">{{ $errors->first() }}</div> @endif
  <main class="cs-main">@yield('content')</main>
</body>
</html>