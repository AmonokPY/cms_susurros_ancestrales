@extends('layouts.app')

@section('title', 'Inicio')

@section('content')
<div class="container">
    <h1>CMS Susurros Ancestrales</h1>
    <p>Portal construido con seguridad desde el diseño: autenticación, validación, políticas de acceso y CRUD de publicaciones.</p>

    <div class="hero-actions">
        @guest
            <a href="{{ route('login') }}" class="button-primary">Iniciar sesión</a>
            <a href="{{ route('register') }}" class="button-secondary">Crear cuenta</a>
        @else
            <a href="{{ route('dashboard') }}" class="button-primary">Ir al dashboard</a>
            <a href="{{ route('posts.index') }}" class="button-secondary">Publicaciones</a>
        @endguest
    </div>
</div>
@endsection
