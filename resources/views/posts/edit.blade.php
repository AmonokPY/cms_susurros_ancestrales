@extends('layouts.app')

@section('title', 'Editar publicación')

@section('content')
<div class="container">
    <h1>Editar publicación</h1>
    <p>Solo el propietario puede actualizar este recurso (Policy + Gate).</p>

    <form method="POST" action="{{ route('posts.update', $post) }}" class="post-form">
        @csrf
        @method('PATCH')

        <div class="form-group">
            <label for="title">Título</label>
            <input id="title" name="title" value="{{ old('title', $post->title) }}" required maxlength="150">
            @error('title') <p class="field-error">{{ $message }}</p> @enderror
        </div>

        <div class="form-group">
            <label for="body">Contenido</label>
            <textarea id="body" name="body" required maxlength="10000">{{ old('body', $post->body) }}</textarea>
            @error('body') <p class="field-error">{{ $message }}</p> @enderror
        </div>

        <div class="form-actions">
            <button type="submit" class="btn-colombia">Actualizar</button>
            <a href="{{ route('posts.show', $post) }}" class="button-secondary">Cancelar</a>
        </div>
    </form>
</div>
@endsection
