@extends('layouts.cms')

@section('title', $item->exists ? 'Editar puzzle' : 'Nuevo puzzle')
@section('heading', $item->exists ? 'Editar puzzle' : 'Nuevo puzzle')

@section('content')
<form method="POST" action="{{ $item->exists ? route('admin.puzzles.update', $item) : route('admin.puzzles.store') }}" enctype="multipart/form-data" class="cms-form">
    @csrf
    @if ($item->exists)
        @method('PUT')
    @endif

    <fieldset>
        <legend>Tarjeta</legend>
        <label>Nombre
            <input name="name" value="{{ old('name', $item->name) }}" required maxlength="150">
        </label>
        <label>URL única (slug)
            <input name="slug" value="{{ old('slug', $item->slug) }}" pattern="[a-z0-9]+(?:-[a-z0-9]+)*" placeholder="se genera del nombre si lo dejas vacío">
        </label>
        <label>Descripción corta
            <input name="short_description" value="{{ old('short_description', $item->short_description) }}" maxlength="500">
        </label>
        <label>Orden
            <input type="number" name="sort_order" value="{{ old('sort_order', $item->sort_order) }}" min="0">
        </label>
        <label>Imagen de portada
            <input type="file" name="cover_image" accept="image/*" {{ $item->exists ? '' : 'required' }}>
        </label>
        @if ($item->exists && isset($media) && $media->url($item->cover_image_path))
            <img class="preview" src="{{ $media->url($item->cover_image_path) }}" alt="">
        @endif
    </fieldset>

    <fieldset>
        <legend>Modal de detalles</legend>
        <label>Título completo
            <input name="full_title" value="{{ old('full_title', $item->full_title) }}" maxlength="200">
        </label>
        <label>Texto descriptivo
            <textarea name="description" class="wysiwyg">{{ old('description', $item->description) }}</textarea>
        </label>
        <label>URL de video
            <input type="url" name="video_url" value="{{ old('video_url', $item->video_url) }}">
        </label>
        <label>Audio (MP3/WAV)
            <input type="file" name="audio" accept="audio/mpeg,audio/wav,.mp3,.wav">
        </label>
        <label>Dirección
            <input name="address" value="{{ old('address', $item->address) }}">
        </label>
        <label>Coordenadas
            <input name="coordinates" value="{{ old('coordinates', $item->coordinates) }}">
        </label>
        <label>Enlace de Google Maps
            <input type="url" name="maps_url" value="{{ old('maps_url', $item->maps_url) }}">
        </label>
        <label>Beneficios
            <textarea name="benefits" class="wysiwyg">{{ old('benefits', $item->benefits) }}</textarea>
        </label>
        <label>Imagen adicional
            <input type="file" name="extra_image" accept="image/*">
        </label>
        @if ($item->exists && isset($media) && $media->url($item->extra_image_path))
            <img class="preview" src="{{ $media->url($item->extra_image_path) }}" alt="">
        @endif
        <label>Texto del CTA
            <input name="cta_text" value="{{ old('cta_text', $item->cta_text) }}">
        </label>
        <label>Enlace o teléfono del CTA (https://, tel: o mailto:)
            <input name="cta_link" value="{{ old('cta_link', $item->cta_link) }}" placeholder="tel:3249935042">
        </label>
    </fieldset>

    <button type="submit" class="btn-primary">Guardar</button>
    <a href="{{ route('admin.puzzles.index') }}">Cancelar</a>
</form>
@endsection
