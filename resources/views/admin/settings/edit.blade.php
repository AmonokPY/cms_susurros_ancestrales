@extends('layouts.cms')

@section('title', 'Editar inicio')
@section('heading', 'Página de inicio')

@section('content')
<form method="POST" action="{{ route('admin.settings.update') }}" enctype="multipart/form-data" class="cms-form" novalidate>
    @csrf
    @method('PUT')

    <fieldset>
        <legend>Encabezado</legend>
        <label>Título principal
            <input
                name="hero_title"
                value="{{ old('hero_title', $settings->hero_title) }}"
                required
                maxlength="150"
                aria-invalid="{{ $errors->has('hero_title') ? 'true' : 'false' }}"
                @error('hero_title') aria-describedby="hero_title-error" @enderror
            >
            @error('hero_title')
                <span class="field-error" id="hero_title-error" role="alert"><span aria-hidden="true">⚠</span> {{ $message }}</span>
            @enderror
        </label>
        <label>Texto botón Run
            <input
                name="run_button_text"
                value="{{ old('run_button_text', $settings->run_button_text) }}"
                required
                aria-invalid="{{ $errors->has('run_button_text') ? 'true' : 'false' }}"
                @error('run_button_text') aria-describedby="run_button_text-error" @enderror
            >
            @error('run_button_text')
                <span class="field-error" id="run_button_text-error" role="alert"><span aria-hidden="true">⚠</span> {{ $message }}</span>
            @enderror
        </label>
        <label>URL del juego
            <input
                type="url"
                name="run_button_url"
                value="{{ old('run_button_url', $settings->run_button_url) }}"
                aria-invalid="{{ $errors->has('run_button_url') ? 'true' : 'false' }}"
                @error('run_button_url') aria-describedby="run_button_url-error" @enderror
            >
            @error('run_button_url')
                <span class="field-error" id="run_button_url-error" role="alert"><span aria-hidden="true">⚠</span> {{ $message }}</span>
            @enderror
        </label>
        <label>Texto botón Android
            <input
                name="android_button_text"
                value="{{ old('android_button_text', $settings->android_button_text) }}"
                required
                aria-invalid="{{ $errors->has('android_button_text') ? 'true' : 'false' }}"
                @error('android_button_text') aria-describedby="android_button_text-error" @enderror
            >
            @error('android_button_text')
                <span class="field-error" id="android_button_text-error" role="alert"><span aria-hidden="true">⚠</span> {{ $message }}</span>
            @enderror
        </label>
        <label>URL del APK
            <input
                type="url"
                name="android_apk_url"
                value="{{ old('android_apk_url', $settings->android_apk_url) }}"
                aria-invalid="{{ $errors->has('android_apk_url') ? 'true' : 'false' }}"
                @error('android_apk_url') aria-describedby="android_apk_url-error" @enderror
            >
            @error('android_apk_url')
                <span class="field-error" id="android_apk_url-error" role="alert"><span aria-hidden="true">⚠</span> {{ $message }}</span>
            @enderror
        </label>
    </fieldset>

    <fieldset>
        <legend>Quiénes somos</legend>
        <label>Título
            <input
                name="about_title"
                value="{{ old('about_title', $settings->about_title) }}"
                required
                aria-invalid="{{ $errors->has('about_title') ? 'true' : 'false' }}"
                @error('about_title') aria-describedby="about_title-error" @enderror
            >
            @error('about_title')
                <span class="field-error" id="about_title-error" role="alert"><span aria-hidden="true">⚠</span> {{ $message }}</span>
            @enderror
        </label>
        <label>Texto
            <textarea
                name="about_text"
                class="wysiwyg"
                aria-invalid="{{ $errors->has('about_text') ? 'true' : 'false' }}"
                @error('about_text') aria-describedby="about_text-error" @enderror
            >{{ old('about_text', $settings->about_text) }}</textarea>
            @error('about_text')
                <span class="field-error" id="about_text-error" role="alert"><span aria-hidden="true">⚠</span> {{ $message }}</span>
            @enderror
        </label>
        <label>Imagen lateral
            <input
                type="file"
                name="about_image"
                accept="image/*"
                aria-invalid="{{ $errors->has('about_image') ? 'true' : 'false' }}"
                @error('about_image') aria-describedby="about_image-error" @enderror
            >
            @error('about_image')
                <span class="field-error" id="about_image-error" role="alert"><span aria-hidden="true">⚠</span> {{ $message }}</span>
            @enderror
        </label>
        @if (!empty($media) && $media->url($settings->about_image_path))
            <img class="preview" src="{{ $media->url($settings->about_image_path) }}" alt="">
        @endif
        <label>Texto alternativo (obligatorio al subir imagen informativa)
            <input
                name="about_image_alt"
                value="{{ old('about_image_alt', $settings->about_image_alt) }}"
                maxlength="200"
                aria-describedby="about_image_alt-hint{{ $errors->has('about_image_alt') ? ' about_image_alt-error' : '' }}"
                aria-invalid="{{ $errors->has('about_image_alt') ? 'true' : 'false' }}"
            >
            <span class="field-hint" id="about_image_alt-hint">Describa brevemente el contenido de la imagen para lectores de pantalla.</span>
            @error('about_image_alt')
                <span class="field-error" id="about_image_alt-error" role="alert"><span aria-hidden="true">⚠</span> {{ $message }}</span>
            @enderror
        </label>
    </fieldset>

    <fieldset>
        <legend>Contáctanos</legend>
        <label>Título
            <input
                name="contact_title"
                value="{{ old('contact_title', $settings->contact_title) }}"
                required
                aria-invalid="{{ $errors->has('contact_title') ? 'true' : 'false' }}"
                @error('contact_title') aria-describedby="contact_title-error" @enderror
            >
            @error('contact_title')
                <span class="field-error" id="contact_title-error" role="alert"><span aria-hidden="true">⚠</span> {{ $message }}</span>
            @enderror
        </label>
        <label>Correo
            <input
                type="email"
                name="contact_email"
                value="{{ old('contact_email', $settings->contact_email) }}"
                aria-invalid="{{ $errors->has('contact_email') ? 'true' : 'false' }}"
                @error('contact_email') aria-describedby="contact_email-error" @enderror
            >
            @error('contact_email')
                <span class="field-error" id="contact_email-error" role="alert"><span aria-hidden="true">⚠</span> {{ $message }}</span>
            @enderror
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
                    <input name="extra_socials[{{ $i }}][name]" value="{{ $social['name'] ?? '' }}" placeholder="Nombre" aria-label="Nombre de la red social {{ $i + 1 }}">
                    <input type="url" name="extra_socials[{{ $i }}][url]" value="{{ $social['url'] ?? '' }}" placeholder="https://" aria-label="URL de la red social {{ $i + 1 }}">
                </div>
            @endforeach
        </div>
        <button type="button" id="add-social" class="btn-secondary">Agregar red</button>
    </fieldset>

    <button type="submit" class="btn-primary">Guardar</button>
</form>
@endsection
