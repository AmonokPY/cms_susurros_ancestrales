@extends('layouts.app')

@section('title', $post->title)

@section('content')
<div class="container">
    <article class="post-detail ds-card franja-tricolor">
        <h1>{{ $post->title }}</h1>
        <p class="post-meta">
            Por {{ $post->user->name }} · {{ $post->created_at->format('d/m/Y H:i') }}
        </p>
        <div class="post-body">{{ $post->body }}</div>

        <div class="form-actions">
            <a href="{{ route('posts.index') }}" class="button-secondary">Volver al listado</a>

            @can('update', $post)
                <a href="{{ route('posts.edit', $post) }}" class="button-primary">Editar</a>
            @endcan

            @can('delete', $post)
                <form method="POST" action="{{ route('posts.destroy', $post) }}" onsubmit="return confirm('¿Eliminar esta publicación?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="button-danger">Eliminar</button>
                </form>
            @endcan
        </div>
    </article>
</div>
@endsection
