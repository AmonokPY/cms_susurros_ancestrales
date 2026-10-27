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
    @vite(['resources/css/cms.css', 'resources/js/cms.js'])
    <script src="https://cdn.jsdelivr.net/npm/tinymce@6.8.4/tinymce.min.js" referrerpolicy="origin"></script>
</head>
<body class="cms-body cms-layout">
    <a href="#main-content" class="skip-link">Saltar al contenido principal</a>
    <aside class="cms-sidebar sidebar" aria-label="Barra lateral de administración">
        <p class="cms-brand" style="color:#FCD116;font-family:var(--font-display);">CMS Susurros Ancestrales</p>
        <nav aria-label="Menú principal">
            <a href="{{ route('dashboard') }}" @if(request()->routeIs('dashboard')) aria-current="page" @endif>Dashboard</a>
            @can('manage-users')
                <a href="{{ route('admin.users.index') }}" @if(request()->routeIs('admin.users.*')) aria-current="page" @endif>Usuarios</a>
                <a href="{{ route('admin.authorized-emails.index') }}" @if(request()->routeIs('admin.authorized-emails.*')) aria-current="page" @endif>Lista blanca</a>
            @endcan
            @can('manage-content')
                <a href="{{ route('admin.settings.edit') }}" @if(request()->routeIs('admin.settings.*')) aria-current="page" @endif>Inicio</a>
                <a href="{{ route('admin.play-items.index') }}" @if(request()->routeIs('admin.play-items.*')) aria-current="page" @endif>Juega</a>
                <a href="{{ route('admin.sponsors.index') }}" @if(request()->routeIs('admin.sponsors.*')) aria-current="page" @endif>Explora</a>
                <a href="{{ route('admin.emails.index') }}" @if(request()->routeIs('admin.emails.*')) aria-current="page" @endif>Correos</a>
            @endcan
            @can('manage-media')
                <a href="{{ route('admin.puzzles.index') }}" @if(request()->routeIs('admin.puzzles.*')) aria-current="page" @endif>Puzzles</a>
            @endcan
            <a href="{{ route('home') }}" target="_blank" rel="noopener noreferrer">Ver sitio <span class="sr-only">(abre en una nueva pestaña)</span></a>
        </nav>
        <button type="button" class="theme-toggle" data-theme-toggle aria-label="Cambiar entre tema claro y oscuro">Tema claro/oscuro</button>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit">Cerrar sesión</button>
        </form>
    </aside>
    <div class="cms-main">
        <header class="cms-top topbar">
            <div>
                <h1>@yield('heading', 'Panel')</h1>
                <span>Panel de administración</span>
            </div>
            <span>{{ auth()->user()->name }}</span>
        </header>
        <div id="cms-live-region" class="sr-only" aria-live="polite" aria-atomic="true"></div>
        @if (session('status'))
            <p class="alert alert-success" role="status" aria-live="polite">
                <span aria-hidden="true">✓</span> {{ session('status') }}
            </p>
        @endif
        @if ($errors->any())
            <div class="alert alert-error" role="alert" aria-live="assertive" id="cms-error-summary">
                <strong><span aria-hidden="true">⚠</span> Error de validación: revise los campos marcados.</strong>
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <main id="main-content" class="cms-content" tabindex="-1">
            @yield('content')
        </main>
    </div>
</body>
</html>
