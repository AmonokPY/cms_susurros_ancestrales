@extends('layouts.app')

@section('title', 'Nueva publicación')

@section('content')
<div class="container">
    <h1>Nueva publicación</h1>
    <p>El propietario se asigna desde el usuario autenticado (no desde el formulario).</p>

    <form method="POST" action="{{ route('posts.store') }}" class="post-form">
        @csrf

        <div class="form-group">
            <label for="title">Título</label>
            <input id="title" name="title" value="{{ old('title') }}" required maxlength="150">
            @error('title') <p class="field-error">{{ $message }}</p> @enderror
        </div>

        <div class="form-group">
            <label for="body">Contenido</label>
            <textarea id="body" name="body" required maxlength="10000">{{ old('body') }}</textarea>
            @error('body') <p class="field-error">{{ $message }}</p> @enderror
        </div>

        <div class="form-actions">
            <button type="submit" class="btn-colombia">Guardar</button>
            <a href="{{ route('posts.index') }}" class="button-secondary">Cancelar</a>
        </div>
    </form>
</div>
@endsection
