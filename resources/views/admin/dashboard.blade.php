@extends('layouts.cms')

@section('title', 'Dashboard')
@section('heading', 'Dashboard')

@section('content')
<section class="welcome-card ds-card franja-tricolor">
    <span class="eyebrow">Panel principal</span>
    <h2>Bienvenido, {{ auth()->user()->name }}</h2>
    <p>Administre el contenido de Susurros Ancestrales desde un entorno autenticado. Las rutas del CMS exigen sesión y autorización por rol.</p>
</section>

<section class="stats-grid">
    @can('manage-users')
        <a class="cms-stat franja-tricolor" href="{{ route('admin.users.index') }}">
            <span>Usuarios</span>
            <strong>{{ $userCount }}</strong>
            <small>Cuentas registradas</small>
        </a>
    @else
        <article class="cms-stat franja-tricolor">
            <span>Usuarios</span>
            <strong>{{ $userCount }}</strong>
            <small>Cuentas registradas</small>
        </article>
    @endcan

    @can('manage-content')
        <a class="cms-stat franja-tricolor" href="{{ route('admin.play-items.index') }}">
            <span>Juega</span>
            <strong>{{ $playCount }}</strong>
            <small>Características publicadas</small>
        </a>
        <a class="cms-stat franja-tricolor" href="{{ route('admin.sponsors.index') }}">
            <span>Explora</span>
            <strong>{{ $sponsorCount }}</strong>
            <small>Patrocinadores</small>
        </a>
        <a class="cms-stat franja-tricolor" href="{{ route('admin.puzzles.index') }}">
            <span>Puzzles</span>
            <strong>{{ $puzzleCount }}</strong>
            <small>Recursos multimedia</small>
        </a>
    @endcan
</section>

<section class="dashboard-grid">
    <article class="ds-card">
        <h2>Actividad reciente</h2>
        @forelse ($recentLogs as $log)
            <p class="audit-line">
                <strong>{{ $log->action }}</strong>
                · {{ $log->result }}
                · {{ $log->user?->email ?? 'sistema' }}
                · {{ $log->created_at?->format('d/m/Y H:i') }}
            </p>
        @empty
            <p>Aún no hay eventos registrados.</p>
        @endforelse
    </article>
    <article class="ds-card">
        <h2>Estado de seguridad</h2>
        <ul class="security-list">
            <li>Autenticación activa</li>
            <li>Sesión regenerada tras el login</li>
            <li>Rutas del CMS protegidas con <code>auth</code></li>
            <li>Autorización por Gates (<code>manage-users</code>, <code>manage-content</code>)</li>
            <li>Bitácora de acceso en <code>audit_logs</code></li>
        </ul>
    </article>
</section>
@endsection
