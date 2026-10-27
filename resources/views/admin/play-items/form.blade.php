@extends('layouts.cms')

@section('title', $item->exists ? 'Editar ítem' : 'Nuevo ítem')
@section('heading', $item->exists ? 'Editar ítem de Juega' : 'Nuevo ítem de Juega')

@section('content')
<form method="POST" action="{{ $item->exists ? route('admin.play-items.update', $item) : route('admin.play-items.store') }}" enctype="multipart/form-data" class="cms-form" novalidate>
    @csrf
    @if ($item->exists)
        @method('PUT')
    @endif

    <label>Título
        <input
            name="title"
            value="{{ old('title', $item->title) }}"
            required
            maxlength="150"
            aria-invalid="{{ $errors->has('title') ? 'true' : 'false' }}"
            @error('title') aria-describedby="title-error" @enderror
        >
        @error('title')
            <span class="field-error" id="title-error" role="alert"><span aria-hidden="true">⚠</span> {{ $message }}</span>
        @enderror
    </label>
    <label>Descripción corta
        <input
            name="description"
            value="{{ old('description', $item->description) }}"
            maxlength="500"
            aria-invalid="{{ $errors->has('description') ? 'true' : 'false' }}"
            @error('description') aria-describedby="description-error" @enderror
        >
        @error('description')
            <span class="field-error" id="description-error" role="alert"><span aria-hidden="true">⚠</span> {{ $message }}</span>
        @enderror
    </label>
    <label>URL del video
        <input
            type="url"
            name="video_url"
            value="{{ old('video_url', $item->video_url) }}"
            aria-invalid="{{ $errors->has('video_url') ? 'true' : 'false' }}"
            @error('video_url') aria-describedby="video_url-error" @enderror
        >
        @error('video_url')
            <span class="field-error" id="video_url-error" role="alert"><span aria-hidden="true">⚠</span> {{ $message }}</span>
        @enderror
    </label>
    <label>Imagen de fondo (9:16)
        <input
            type="file"
            name="image"
            accept="image/*"
            {{ $item->exists ? '' : 'required' }}
            aria-invalid="{{ $errors->has('image') ? 'true' : 'false' }}"
            @error('image') aria-describedby="image-error" @enderror
        >
        @error('image')
            <span class="field-error" id="image-error" role="alert"><span aria-hidden="true">⚠</span> {{ $message }}</span>
        @enderror
    </label>
    @if ($item->exists && isset($media) && $media->url($item->image_path))
        <img class="preview" src="{{ $media->url($item->image_path) }}" alt="">
    @endif
    <label>Texto alternativo de la imagen (alt)
        <input
            name="image_alt"
            value="{{ old('image_alt', $item->title) }}"
            {{ $item->exists ? '' : 'required' }}
            maxlength="200"
            aria-describedby="image_alt-hint{{ $errors->has('image_alt') ? ' image_alt-error' : '' }}"
            aria-invalid="{{ $errors->has('image_alt') ? 'true' : 'false' }}"
        >
        <span class="field-hint" id="image_alt-hint">Obligatorio al subir una imagen informativa (ej. descripción visual de la tarjeta).</span>
        @error('image_alt')
            <span class="field-error" id="image_alt-error" role="alert"><span aria-hidden="true">⚠</span> {{ $message }}</span>
        @enderror
    </label>

    <button type="submit" class="btn-primary">Guardar</button>
    <a href="{{ route('admin.play-items.index') }}">Cancelar</a>
</form>
@endsection
