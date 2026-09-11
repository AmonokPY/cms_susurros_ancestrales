@extends('layouts.cms')

@section('title', 'Editar inicio')
@section('heading', 'Página de inicio')

@section('content')
<form method="POST" action="{{ route('admin.settings.update') }}" enctype="multipart/form-data" class="cms-form">
    @csrf
    @method('PUT')

    <fieldset>
        <legend>Encabezado</legend>
        <label>Título principal
            <input name="hero_title" value="{{ old('hero_title', $settings->hero_title) }}" required maxlength="150">
        </label>
        <label>Texto botón Run
            <input name="run_button_text" value="{{ old('run_button_text', $settings->run_button_text) }}" required>
        </label>
        <label>URL del juego
            <input type="url" name="run_button_url" value="{{ old('run_button_url', $settings->run_button_url) }}">
        </label>
        <label>Texto botón Android
            <input name="android_button_text" value="{{ old('android_button_text', $settings->android_button_text) }}" required>
        </label>
        <label>URL del APK
            <input type="url" name="android_apk_url" value="{{ old('android_apk_url', $settings->android_apk_url) }}">
        </label>
    </fieldset>

    <fieldset>
        <legend>Quiénes somos</legend>
        <label>Título
            <input name="about_title" value="{{ old('about_title', $settings->about_title) }}" required>
        </label>
        <label>Texto
            <textarea name="about_text" class="wysiwyg">{{ old('about_text', $settings->about_text) }}</textarea>
        </label>
        <label>Imagen lateral
            <input type="file" name="about_image" accept="image/*">
        </label>
        @if (!empty($media) && $media->url($settings->about_image_path))
            <img class="preview" src="{{ $media->url($settings->about_image_path) }}" alt="">
        @endif
        <label>Texto alternativo
            <input name="about_image_alt" value="{{ old('about_image_alt', $settings->about_image_alt) }}">
        </label>
    </fieldset>

    <fieldset>
        <legend>Contáctanos</legend>
        <label>Título
            <input name="contact_title" value="{{ old('contact_title', $settings->contact_title) }}" required>
        </label>
        <label>Correo
            <input type="email" name="contact_email" value="{{ old('contact_email', $settings->contact_email) }}">
        </label>
        <label>Teléfono
            <input name="contact_phone" value="{{ old('contact_phone', $settings->contact_phone) }}">
        </label>
        <label>Facebook
            <input type="url" name="facebook_url" value="{{ old('facebook_url', $settings->facebook_url) }}">
        </label>
        <label>Instagram
            <input type="url" name="instagram_url" value="{{ old('instagram_url', $settings->instagram_url) }}">
        </label>
        <label>Twitter/X
            <input type="url" name="twitter_url" value="{{ old('twitter_url', $settings->twitter_url) }}">
        </label>
        <label>YouTube
            <input type="url" name="youtube_url" value="{{ old('youtube_url', $settings->youtube_url) }}">
        </label>
        <label>Dirección
            <input name="address" value="{{ old('address', $settings->address) }}">
        </label>
        <label>Texto adicional
            <textarea name="contact_extra" class="wysiwyg">{{ old('contact_extra', $settings->contact_extra) }}</textarea>
        </label>

        <p>Otras redes</p>
        <div id="extra-socials">
            @foreach (old('extra_socials', $settings->extra_socials ?? []) as $i => $social)
                <div class="social-row">
                    <input name="extra_socials[{{ $i }}][name]" value="{{ $social['name'] ?? '' }}" placeholder="Nombre">
                    <input type="url" name="extra_socials[{{ $i }}][url]" value="{{ $social['url'] ?? '' }}" placeholder="https://">
                </div>
            @endforeach
        </div>
        <button type="button" id="add-social" class="btn-secondary">Agregar red</button>
    </fieldset>

    <button type="submit" class="btn-primary">Guardar</button>
</form>
@endsection
