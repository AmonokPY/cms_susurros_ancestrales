<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', $settings->hero_title ?? 'Susurros Ancestrales')</title>
    <script>
        document.documentElement.setAttribute('data-theme', localStorage.getItem('colombia-theme') || (matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light'));
    </script>
    <script src="https://cdn.jsdelivr.net/npm/gsap@3.12.5/dist/gsap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/gsap@3.12.5/dist/Draggable.min.js"></script>
    @vite(['resources/css/public.css', 'resources/js/public.js'])
</head>
<body class="@yield('body_class', 'public-home')">
    <div class="stripe-flag"></div>
    <header class="site-header">
        <a class="logo" href="{{ route('home') }}">
            <span class="brand-mark"><span></span><span></span><span></span></span>
            {{ $settings->hero_title ?? 'Susurros Ancestrales' }}
        </a>
        <nav>
            <a href="{{ route('home') }}" class="{{ request()->routeIs('home') ? 'is-active' : '' }}">Inicio</a>
            <a href="{{ route('puzzles.index') }}" class="{{ request()->routeIs('puzzles.*') ? 'is-active' : '' }}">Puzzles</a>
            <a href="{{ route('contact') }}" class="{{ request()->routeIs('contact') ? 'is-active' : '' }}">Contacto</a>
            @auth
                @if (auth()->user()->canAccessCms())
                    <a href="{{ route('dashboard') }}">CMS</a>
                @endif
                <span class="nav-user">{{ auth()->user()->name }}</span>
                <form method="POST" action="{{ route('logout') }}" class="nav-logout">
                    @csrf
                    <button type="submit">Salir</button>
                </form>
            @else
                <a href="{{ route('login') }}">Ingresar</a>
            @endauth
            <button type="button" class="theme-toggle" data-theme-toggle aria-label="Cambiar tema">Tema</button>
        </nav>
    </header>

    @if (session('status'))
        <p class="alert alert-success" style="max-width:1100px;margin:1rem auto;">{{ session('status') }}</p>
    @endif
    @yield('content')

    <footer class="ds-footer site-footer">
        <div class="stripe-flag"></div>
        <p>&copy; {{ date('Y') }} {{ $settings->hero_title ?? 'Susurros Ancestrales' }} · Videojuego colombiano</p>
    </footer>
</body>
</html>
