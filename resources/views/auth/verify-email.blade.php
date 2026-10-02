<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirme su correo | CMS Susurros Ancestrales</title>
    <script>
        document.documentElement.setAttribute('data-theme', localStorage.getItem('colombia-theme') || (matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light'));
    </script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="login-page auth-page">
    <main class="login-card auth-card franja-tricolor">
        <header class="login-header auth-header">
            <span class="login-badge">Susurros Ancestrales</span>
            <h1>Verifique su correo</h1>
            <p>Enviamos un enlace de confirmación. No podrá entrar al CMS hasta completar este paso.</p>
        </header>

        @if (session('status'))
            <p class="alert alert-success" role="status">{{ session('status') }}</p>
        @endif

        <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <button type="submit" class="btn-colombia">Reenviar enlace</button>
        </form>

        <form method="POST" action="{{ route('logout') }}" style="margin-top:1rem">
            @csrf
            <button type="submit" class="button-secondary">Cerrar sesión</button>
        </form>
    </main>
</body>
</html>
