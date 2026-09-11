@extends('layouts.cms')

@section('title', $item->exists ? 'Editar patrocinador' : 'Nuevo patrocinador')
@section('heading', $item->exists ? 'Editar patrocinador' : 'Nuevo patrocinador')

@section('content')
<form method="POST" action="{{ $item->exists ? route('admin.sponsors.update', $item) : route('admin.sponsors.store') }}" enctype="multipart/form-data" class="cms-form">
    @csrf
    @if ($item->exists)
        @method('PUT')
    @endif

    <label>Nombre
        <input name="name" value="{{ old('name', $item->name) }}" required maxlength="150">
    </label>
    <label>Información / beneficio
        <input name="description" value="{{ old('description', $item->description) }}" maxlength="500">
    </label>
    <label>Sitio web
        <input type="url" name="website_url" value="{{ old('website_url', $item->website_url) }}">
    </label>
    <label>Teléfono
        <input name="phone" value="{{ old('phone', $item->phone) }}">
    </label>
    <label>Correo
        <input type="email" name="email" value="{{ old('email', $item->email) }}">
    </label>
    <label>Logo / imagen
        <input type="file" name="image" accept="image/*" {{ $item->exists ? '' : 'required' }}>
    </label>
    @if ($item->exists && isset($media) && $media->url($item->image_path))
        <img class="preview" src="{{ $media->url($item->image_path) }}" alt="">
    @endif

    <button type="submit" class="btn-primary">Guardar</button>
    <a href="{{ route('admin.sponsors.index') }}">Cancelar</a>
</form>
@endsection
