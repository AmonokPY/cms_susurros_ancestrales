<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro | CMS Susurros Ancestrales</title>
    <script>
        document.documentElement.setAttribute('data-theme', localStorage.getItem('colombia-theme') || (matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light'));
    </script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="login-page auth-page">
    <main class="login-card auth-card franja-tricolor">
        <header class="login-header auth-header">
            <span class="login-badge">Susurros Ancestrales</span>
            <h1>Crear cuenta</h1>
            <p>Cree su cuenta para ver el sitio. El CMS lo activa un administrador.</p>
        </header>

            @if ($errors->any())
                <div class="alert alert-error" role="alert">
                    <strong>No fue posible completar el registro.</strong>
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('register.store') }}" class="login-form">
                @csrf

                <div class="form-group">
                    <label for="name">Nombre completo</label>
                    <input
                        id="name"
                        type="text"
                        name="name"
                        value="{{ old('name') }}"
                        autocomplete="name"
                        required
                        maxlength="100"
                        autofocus
                    >
                </div>

                <div class="form-group">
                    <label for="email">Correo electrónico</label>
                    <input
                        id="email"
                        type="email"
                        name="email"
                        value="{{ old('email') }}"
                        autocomplete="email"
                        required
                        maxlength="255"
                    >
                </div>

                <div class="form-group">
                    <label for="password">Contraseña</label>
                    <div class="password-wrapper">
                        <input
                            id="password"
                            type="password"
                            name="password"
                            autocomplete="new-password"
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
                    <p class="field-hint">Mínimo 8 caracteres, con mayúsculas, minúsculas y un número.</p>
                </div>

                <div class="form-group">
                    <label for="password_confirmation">Confirmar contraseña</label>
                    <input
                        id="password_confirmation"
                        type="password"
                        name="password_confirmation"
                        autocomplete="new-password"
                        required
                    >
                </div>

                <button type="submit" class="btn-colombia">Crear cuenta</button>
            </form>

            <p class="auth-footer">
                ¿Ya tiene una cuenta?
                <a href="{{ route('login') }}">Iniciar sesión</a>
                ·
                <a href="{{ route('home') }}">Inicio</a>
            </p>
    </main>
</body>
</html>
