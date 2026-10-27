@extends('layouts.cms')

@section('title', $item->exists ? 'Editar patrocinador' : 'Nuevo patrocinador')
@section('heading', $item->exists ? 'Editar patrocinador' : 'Nuevo patrocinador')

@section('content')
<form method="POST" action="{{ $item->exists ? route('admin.sponsors.update', $item) : route('admin.sponsors.store') }}" enctype="multipart/form-data" class="cms-form" novalidate>
    @csrf
    @if ($item->exists)
        @method('PUT')
    @endif

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
    <label>Información / beneficio
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
    <label>Sitio web
        <input
            type="url"
            name="website_url"
            value="{{ old('website_url', $item->website_url) }}"
            aria-invalid="{{ $errors->has('website_url') ? 'true' : 'false' }}"
            @error('website_url') aria-describedby="website_url-error" @enderror
        >
        @error('website_url')
            <span class="field-error" id="website_url-error" role="alert"><span aria-hidden="true">⚠</span> {{ $message }}</span>
        @enderror
    </label>
    <label>Teléfono
        <input name="phone" value="{{ old('phone', $item->phone) }}">
    </label>
    <label>Correo
        <input
            type="email"
            name="email"
            value="{{ old('email', $item->email) }}"
            aria-invalid="{{ $errors->has('email') ? 'true' : 'false' }}"
            @error('email') aria-describedby="email-error" @enderror
        >
        @error('email')
            <span class="field-error" id="email-error" role="alert"><span aria-hidden="true">⚠</span> {{ $message }}</span>
        @enderror
    </label>
    <label>Logo / imagen
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
    <label>Texto alternativo del logo / imagen (alt)
        <input
            name="image_alt"
            value="{{ old('image_alt', $item->name) }}"
            {{ $item->exists ? '' : 'required' }}
            maxlength="200"
            aria-describedby="sponsor_image_alt-hint{{ $errors->has('image_alt') ? ' sponsor_image_alt-error' : '' }}"
            aria-invalid="{{ $errors->has('image_alt') ? 'true' : 'false' }}"
        >
        <span class="field-hint" id="sponsor_image_alt-hint">Obligatorio al subir el logo o imagen del patrocinador.</span>
        @error('image_alt')
            <span class="field-error" id="sponsor_image_alt-error" role="alert"><span aria-hidden="true">⚠</span> {{ $message }}</span>
        @enderror
    </label>

    <button type="submit" class="btn-primary">Guardar</button>
    <a href="{{ route('admin.sponsors.index') }}">Cancelar</a>
</form>
@endsection
