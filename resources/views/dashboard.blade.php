<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | CMS Susurros Ancestrales</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <main class="dashboard">
        <header class="dashboard-header">
            <div>
                <p class="login-badge">CMS Susurros Ancestrales</p>
                <h1>Bienvenido, {{ auth()->user()->name }}</h1>
                <p>El acceso a esta página requiere autenticación.</p>
            </div>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="button-secondary">Cerrar sesión</button>
            </form>
        </header>

        <section class="dashboard-panel">
            <h2>Panel del CMS</h2>
            <p>Administra el contenido del sitio. Las publicaciones usan rutas RESTful, Form Requests y Policy.</p>

            <div class="form-actions">
                <a href="{{ route('posts.index') }}" class="button-primary">Ver publicaciones</a>
                <a href="{{ route('posts.create') }}" class="button-secondary">Nueva publicación</a>
                <a href="{{ route('home') }}">Volver al inicio</a>
            </div>
        </section>
    </main>
</body>
</html>
