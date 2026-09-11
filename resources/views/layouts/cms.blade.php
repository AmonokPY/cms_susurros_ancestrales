<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'CMS Susurranes')</title>
    <script>
        document.documentElement.setAttribute('data-theme', localStorage.getItem('colombia-theme') || (matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light'));
    </script>
    @vite(['resources/css/cms.css', 'resources/js/cms.js'])
    <script src="https://cdn.jsdelivr.net/npm/tinymce@6.8.4/tinymce.min.js" referrerpolicy="origin"></script>
</head>
<body class="cms-body">
    <aside class="cms-sidebar">
        <p class="cms-brand" style="color:#FCD116;font-family:var(--font-display);">CMS Susurranes</p>
        <nav>
            <a href="{{ route('dashboard') }}">Panel</a>
            <a href="{{ route('admin.settings.edit') }}">Inicio</a>
            <a href="{{ route('admin.play-items.index') }}">Juega</a>
            <a href="{{ route('admin.sponsors.index') }}">Explora</a>
            <a href="{{ route('admin.puzzles.index') }}">Puzzles</a>
            <a href="{{ route('posts.index') }}">Publicaciones</a>
            <a href="{{ route('home') }}" target="_blank">Ver sitio</a>
        </nav>
        <button type="button" class="theme-toggle" data-theme-toggle>Tema claro/oscuro</button>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit">Cerrar sesión</button>
        </form>
    </aside>
    <div class="cms-main">
        <header class="cms-top">
            <h1>@yield('heading', 'Panel')</h1>
            <span>{{ auth()->user()->name }}</span>
        </header>
        @if (session('status'))
            <p class="alert alert-success">{{ session('status') }}</p>
        @endif
        @if ($errors->any())
            <div class="alert alert-error">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        @yield('content')
    </div>
</body>
</html>
