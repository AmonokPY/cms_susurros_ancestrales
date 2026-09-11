<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar sesión | CMS Susurros Ancestrales</title>
    <script>
        document.documentElement.setAttribute('data-theme', localStorage.getItem('colombia-theme') || (matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light'));
    </script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <main class="login-page">
        <section class="login-card franja-tricolor">
            <div class="login-header">
                <p class="login-badge">Susurros Ancestrales</p>
                <h1>Iniciar sesión</h1>
                <p>Acceda al CMS con sus credenciales.</p>
            </div>

            @if ($errors->any())
                <div class="alert alert-error" role="alert">
                    <strong>No fue posible iniciar sesión.</strong>
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('login.store') }}" class="login-form">
                @csrf

                <div class="form-group">
                    <label for="email">Correo electrónico</label>
                    <input
                        id="email"
                        type="email"
                        name="email"
                        value="{{ old('email') }}"
                        autocomplete="username"
                        required
                        autofocus
                    >
                </div>

                <div class="form-group">
                    <label for="password">Contraseña</label>
                    <div class="password-wrapper">
                        <input
                            id="password"
                            type="password"
                            name="password"
                            autocomplete="current-password"
                            required
                        >
                        <button
                            id="togglePassword"
                            type="button"
                            class="password-toggle"
                            aria-label="Mostrar contraseña"
                        >
                            Mostrar
                        </button>
                    </div>
                </div>

                <label class="remember">
                    <input type="checkbox" name="remember" value="1">
                    Recordarme
                </label>

                <button type="submit" class="btn-colombia">Ingresar</button>
            </form>

            <p class="auth-footer">
                ¿No tiene cuenta?
                <a href="{{ route('register') }}">Registrarse</a>
                ·
                <a href="{{ route('home') }}">Inicio</a>
            </p>
        </section>
    </main>
</body>
</html>
