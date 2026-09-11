@extends('layouts.cms')

@section('title', $item->exists ? 'Editar ítem' : 'Nuevo ítem')
@section('heading', $item->exists ? 'Editar ítem de Juega' : 'Nuevo ítem de Juega')

@section('content')
<form method="POST" action="{{ $item->exists ? route('admin.play-items.update', $item) : route('admin.play-items.store') }}" enctype="multipart/form-data" class="cms-form">
    @csrf
    @if ($item->exists)
        @method('PUT')
    @endif

    <label>Título
        <input name="title" value="{{ old('title', $item->title) }}" required maxlength="150">
    </label>
    <label>Descripción corta
        <input name="description" value="{{ old('description', $item->description) }}" maxlength="500">
    </label>
    <label>URL del video
        <input type="url" name="video_url" value="{{ old('video_url', $item->video_url) }}">
    </label>
    <label>Imagen de fondo (9:16)
        <input type="file" name="image" accept="image/*" {{ $item->exists ? '' : 'required' }}>
    </label>
    @if ($item->exists && isset($media) && $media->url($item->image_path))
        <img class="preview" src="{{ $media->url($item->image_path) }}" alt="">
    @endif

    <button type="submit" class="btn-primary">Guardar</button>
    <a href="{{ route('admin.play-items.index') }}">Cancelar</a>
</form>
@endsection
