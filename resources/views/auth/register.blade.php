<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro | CMS Susurros Ancestrales</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <main class="login-page">
        <section class="login-card">
            <div class="login-header">
                <p class="login-badge">Susurros Ancestrales</p>
                <h1>Crear cuenta</h1>
                <p>Regístrese para acceder al dashboard del CMS.</p>
            </div>

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
                    <label for="name">Nombre</label>
                    <input
                        id="name"
                        type="text"
                        name="name"
                        value="{{ old('name') }}"
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
                        autocomplete="username"
                        required
                        maxlength="150"
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
                    <p class="field-hint">Mínimo 12 caracteres, mayúsculas, minúsculas, números y símbolos.</p>
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

                <button type="submit" class="button-primary">Registrarse</button>
            </form>

            <p class="auth-footer">
                ¿Ya tiene cuenta?
                <a href="{{ route('login') }}">Iniciar sesión</a>
                ·
                <a href="{{ route('home') }}">Inicio</a>
            </p>
        </section>
    </main>
</body>
</html>
