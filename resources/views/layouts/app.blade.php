<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'CMS Susurros Ancestrales')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <header>
        <nav>
            <a href="{{ route('home') }}" class="brand">Susurros Ancestrales</a>
            <div class="nav-links">
                <a href="{{ route('home') }}">Inicio</a>
                <a href="{{ route('about') }}">Acerca</a>
                <a href="{{ route('contact') }}">Contacto</a>

                @auth
                    <a href="{{ route('dashboard') }}">Dashboard</a>
                    <a href="{{ route('posts.index') }}">Publicaciones</a>
                    <form method="POST" action="{{ route('logout') }}" class="nav-logout">
                        @csrf
                        <button type="submit">Cerrar sesión</button>
                    </form>
                @else
                    <a href="{{ route('login') }}">Ingresar</a>
                    <a href="{{ route('register') }}" class="nav-cta">Registrarse</a>
                @endauth
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
</body>
</html>
