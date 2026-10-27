@extends('layouts.cms')

@section('title', $item->exists ? 'Editar puzzle' : 'Nuevo puzzle')
@section('heading', $item->exists ? 'Editar puzzle' : 'Nuevo puzzle')

@section('content')
<form method="POST" action="{{ $item->exists ? route('admin.puzzles.update', $item) : route('admin.puzzles.store') }}" enctype="multipart/form-data" class="cms-form" novalidate>
    @csrf
    @if ($item->exists)
        @method('PUT')
    @endif

    <fieldset>
        <legend>Tarjeta</legend>
        <label>Nombre
            <input
                name="name"
                value="{{ old('name', $item->name) }}"
                required
                maxlength="150"
                aria-invalid="{{ $errors->has('name') ? 'true' : 'false' }}"
                @error('name') aria-describedby="name-error" @enderror
            >
            @error('name')
                <span class="field-error" id="name-error" role="alert"><span aria-hidden="true">⚠</span> {{ $message }}</span>
            @enderror
        </label>
        <label>URL única (slug)
            <input
                name="slug"
                value="{{ old('slug', $item->slug) }}"
                pattern="[a-z0-9]+(?:-[a-z0-9]+)*"
                placeholder="se genera del nombre si lo dejas vacío"
                aria-invalid="{{ $errors->has('slug') ? 'true' : 'false' }}"
                @error('slug') aria-describedby="slug-error" @enderror
            >
            @error('slug')
                <span class="field-error" id="slug-error" role="alert"><span aria-hidden="true">⚠</span> {{ $message }}</span>
            @enderror
        </label>
        <label>Descripción corta
            <input
                name="short_description"
                value="{{ old('short_description', $item->short_description) }}"
                maxlength="500"
                aria-invalid="{{ $errors->has('short_description') ? 'true' : 'false' }}"
                @error('short_description') aria-describedby="short_description-error" @enderror
            >
            @error('short_description')
                <span class="field-error" id="short_description-error" role="alert"><span aria-hidden="true">⚠</span> {{ $message }}</span>
            @enderror
        </label>
        <label>Orden
            <input type="number" name="sort_order" value="{{ old('sort_order', $item->sort_order) }}" min="0">
        </label>
        <label>Imagen de portada
            <input
                type="file"
                name="cover_image"
                accept="image/*"
                {{ $item->exists ? '' : 'required' }}
                aria-invalid="{{ $errors->has('cover_image') ? 'true' : 'false' }}"
                @error('cover_image') aria-describedby="cover_image-error" @enderror
            >
            @error('cover_image')
                <span class="field-error" id="cover_image-error" role="alert"><span aria-hidden="true">⚠</span> {{ $message }}</span>
            @enderror
        </label>
        @if ($item->exists && isset($media) && $media->url($item->cover_image_path))
            <img class="preview" src="{{ $media->url($item->cover_image_path) }}" alt="">
        @endif
        <label>Texto alternativo de portada (alt)
            <input
                name="cover_image_alt"
                value="{{ old('cover_image_alt', $item->name) }}"
                {{ $item->exists ? '' : 'required' }}
                maxlength="200"
                aria-describedby="cover_image_alt-hint{{ $errors->has('cover_image_alt') ? ' cover_image_alt-error' : '' }}"
                aria-invalid="{{ $errors->has('cover_image_alt') ? 'true' : 'false' }}"
            >
            <span class="field-hint" id="cover_image_alt-hint">Obligatorio al subir la imagen de portada del puzzle.</span>
            @error('cover_image_alt')
                <span class="field-error" id="cover_image_alt-error" role="alert"><span aria-hidden="true">⚠</span> {{ $message }}</span>
            @enderror
        </label>
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
        <label>Audio (MP3/WAV)
            <input
                type="file"
                name="audio"
                accept="audio/mpeg,audio/wav,.mp3,.wav"
                aria-invalid="{{ $errors->has('audio') ? 'true' : 'false' }}"
                @error('audio') aria-describedby="audio-error" @enderror
            >
            @error('audio')
                <span class="field-error" id="audio-error" role="alert"><span aria-hidden="true">⚠</span> {{ $message }}</span>
            @enderror
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
            <input
                type="file"
                name="extra_image"
                accept="image/*"
                aria-invalid="{{ $errors->has('extra_image') ? 'true' : 'false' }}"
                @error('extra_image') aria-describedby="extra_image-error" @enderror
            >
            @error('extra_image')
                <span class="field-error" id="extra_image-error" role="alert"><span aria-hidden="true">⚠</span> {{ $message }}</span>
            @enderror
        </label>
        @if ($item->exists && isset($media) && $media->url($item->extra_image_path))
            <img class="preview" src="{{ $media->url($item->extra_image_path) }}" alt="">
        @endif
        <label>Texto alternativo de imagen adicional (alt)
            <input
                name="extra_image_alt"
                value="{{ old('extra_image_alt') }}"
                maxlength="200"
                aria-describedby="extra_image_alt-hint{{ $errors->has('extra_image_alt') ? ' extra_image_alt-error' : '' }}"
                aria-invalid="{{ $errors->has('extra_image_alt') ? 'true' : 'false' }}"
            >
            <span class="field-hint" id="extra_image_alt-hint">Obligatorio si adjunta una imagen adicional informativa.</span>
            @error('extra_image_alt')
                <span class="field-error" id="extra_image_alt-error" role="alert"><span aria-hidden="true">⚠</span> {{ $message }}</span>
            @enderror
        </label>
        <label>Texto del CTA
            <input name="cta_text" value="{{ old('cta_text', $item->cta_text) }}">
        </label>
        <label>Enlace o teléfono del CTA (https://, tel: o mailto:)
            <input
                name="cta_link"
                value="{{ old('cta_link', $item->cta_link) }}"
                placeholder="tel:3227486684"
                aria-invalid="{{ $errors->has('cta_link') ? 'true' : 'false' }}"
                @error('cta_link') aria-describedby="cta_link-error" @enderror
            >
            @error('cta_link')
                <span class="field-error" id="cta_link-error" role="alert"><span aria-hidden="true">⚠</span> {{ $message }}</span>
            @enderror
        </label>
    </fieldset>

    <button type="submit" class="btn-primary">Guardar</button>
    <a href="{{ route('admin.puzzles.index') }}">Cancelar</a>
</form>
@endsection
