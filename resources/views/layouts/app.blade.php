
<!-- resources/views/layouts/app.blade.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'URL Shortener')</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; background: #f5f5f5; line-height: 1.6; }
        header { background: #333; color: white; padding: 1rem; }
        header a { color: white; text-decoration: none; margin-right: 1rem; }
        header form { display: inline; }
        header button { background: red; color: white; border: none; padding: 0.5rem 1rem; cursor: pointer; }
        .container { max-w-1200px; margin: 0 auto; padding: 2rem 1rem; }
        .card { background: white; padding: 1.5rem; margin: 1rem 0; border-radius: 4px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .btn { display: inline-block; padding: 0.75rem 1.5rem; background: #007bff; color: white; text-decoration: none; border-radius: 4px; border: none; cursor: pointer; }
        .btn:hover { background: #0056b3; }
        .btn-danger { background: #dc3545; }
        .btn-danger:hover { background: #c82333; }
        .form-group { margin-bottom: 1rem; }
        label { display: block; margin-bottom: 0.5rem; font-weight: bold; }
        input, textarea, select { width: 100%; padding: 0.75rem; border: 1px solid #ddd; border-radius: 4px; }
        .error { color: red; margin-top: 0.5rem; }
        .alert { padding: 1rem; margin-bottom: 1rem; border-radius: 4px; }
        .alert-success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .alert-danger { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 0.75rem; text-align: left; border-bottom: 1px solid #ddd; }
        th { background: #f0f0f0; font-weight: bold; }
        tr:hover { background: #f9f9f9; }
    </style>
</head>
<body>
    <header>
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <div>
                <a href="{{ route('dashboard') }}">URL Shortener</a>
                @auth
                    <a href="{{ route('dashboard') }}">Dashboard</a>
                    <a href="{{ route('short-urls.index') }}">Short URLs</a>
                    @if(auth()->user()->isAdmin())
                        <a href="{{ route('invitations.create') }}">Invite Users</a>
                    @endif
                @endauth
            </div>
            <div>
                @auth
                    <span>{{ auth()->user()->name }} ({{ auth()->user()->role->name }})</span>
                    <form action="{{ route('logout') }}" method="POST" style="display: inline;">
                        @csrf
                        <button type="submit" class="btn btn-danger" style="padding: 0.5rem 1rem;">Logout</button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="btn">Login</a>
                @endauth
            </div>
        </div>
    </header>

    <div class="container">
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        @yield('content')
    </div>
</body>
</html>