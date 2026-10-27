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
    <a href="#main-content" class="skip-link">Saltar al contenido principal</a>
    <main id="main-content" class="login-card auth-card franja-tricolor" tabindex="-1">
        <header class="login-header auth-header">
            <span class="login-badge">Susurros Ancestrales</span>
            <h1>Crear cuenta</h1>
            <p>Cree su cuenta para ver el sitio. El CMS lo activa un administrador.</p>
        </header>

            @if ($errors->any())
                <div class="alert alert-error" role="alert" aria-live="assertive">
                    <strong><span aria-hidden="true">⚠</span> No fue posible completar el registro.</strong>
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('register.store') }}" class="login-form" novalidate>
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
                        aria-invalid="{{ $errors->has('name') ? 'true' : 'false' }}"
                        @error('name') aria-describedby="name-error" @enderror
                    >
                    @error('name')
                        <p class="field-error" id="name-error" role="alert"><span aria-hidden="true">⚠</span> {{ $message }}</p>
                    @enderror
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
                        aria-invalid="{{ $errors->has('email') ? 'true' : 'false' }}"
                        @error('email') aria-describedby="email-error" @enderror
                    >
                    @error('email')
                        <p class="field-error" id="email-error" role="alert"><span aria-hidden="true">⚠</span> {{ $message }}</p>
                    @enderror
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
                            aria-describedby="password-hint{{ $errors->has('password') ? ' password-error' : '' }}"
                            aria-invalid="{{ $errors->has('password') ? 'true' : 'false' }}"
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
                    <p class="field-hint" id="password-hint">Mínimo 8 caracteres, con mayúsculas, minúsculas y un número.</p>
                    @error('password')
                        <p class="field-error" id="password-error" role="alert"><span aria-hidden="true">⚠</span> {{ $message }}</p>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="password_confirmation">Confirmar contraseña</label>
                    <input
                        id="password_confirmation"
                        type="password"
                        name="password_confirmation"
                        autocomplete="new-password"
                        required
                        aria-invalid="{{ $errors->has('password_confirmation') ? 'true' : 'false' }}"
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
