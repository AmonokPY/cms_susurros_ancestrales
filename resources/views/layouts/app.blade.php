<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'CMS Susurros Ancestrales')</title>
    <script>
        document.documentElement.setAttribute('data-theme', localStorage.getItem('colombia-theme') || (matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light'));
    </script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <div class="stripe-flag"></div>
    <header class="ds-header franja-tricolor">
        <nav class="ds-nav">
            <a href="{{ route('home') }}" class="brand" style="color:#FCD116;display:flex;align-items:center;gap:10px;font-family:var(--font-display);font-weight:700;">
                <span class="brand-mark"><span></span><span></span><span></span></span>
                Susurros Ancestrales
            </a>
            <div class="nav-links">
                <a href="{{ route('home') }}">Inicio</a>
                <a href="{{ route('about') }}">Acerca</a>
                <a href="{{ route('contact') }}">Contacto</a>
                <a href="{{ route('puzzles.index') }}">Puzzles</a>
                @auth
                    <a href="{{ route('dashboard') }}">Dashboard</a>
                    <a href="{{ route('posts.index') }}">Publicaciones</a>
                    <form method="POST" action="{{ route('logout') }}" class="nav-logout">
                        @csrf
                        <button type="submit">Cerrar sesión</button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="btn-secondary btn-sm">Ingresar</a>
                    <a href="{{ route('register') }}" class="btn-colombia btn-sm">Registrarse</a>
                @endauth
                <button type="button" class="theme-toggle" data-theme-toggle aria-label="Cambiar tema">Claro / Oscuro</button>
            </div>
        </nav>
    </header>

    <main>
        @if (session('status'))
            <div class="container">
                <p class="alert alert-success" role="status">{{ session('status') }}</p>
            </div>
        @endif
        @yield('content')
    </main>

    <footer class="ds-footer">
        <div class="stripe-flag"></div>
        <p style="text-align:center;padding:1.5rem;">&copy; {{ date('Y') }} Susurros Ancestrales · Hecho en Colombia</p>
    </footer>
</body>
</html>
