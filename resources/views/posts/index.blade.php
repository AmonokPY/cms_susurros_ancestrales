@extends('layouts.app')

@section('title', 'Publicaciones')

@section('content')
<div class="container">
    <div class="page-header">
        <div>
            <h1>Publicaciones</h1>
            <p>CRUD protegido con autenticación, validación y autorización por propietario.</p>
        </div>
        <a href="{{ route('posts.create') }}" class="button-primary">Nueva publicación</a>
    </div>

    @forelse ($posts as $post)
        <article class="post-item">
            <h2><a href="{{ route('posts.show', $post) }}">{{ $post->title }}</a></h2>
            <p class="post-meta">
                Por {{ $post->user->name }} · {{ $post->created_at->format('d/m/Y H:i') }}
            </p>
            <p>{{ \Illuminate\Support\Str::limit($post->body, 180) }}</p>
        </article>
    @empty
        <p class="empty-state">Aún no hay publicaciones. Crea la primera.</p>
    @endforelse
</div>
@endsection
