@extends('layouts.public')

@section('title', 'Contacto')
@section('body_class', 'public-home')

@section('content')
<section class="contact" id="formulario-contacto">
    <h2>{{ $settings->contact_title ?? 'Contacto' }}</h2>
    <p>Escríbanos. El mensaje se envía por SMTP al buzón del CMS; un administrador lo revisa en la bandeja recibida.</p>

    @if ($errors->any())
        <div class="alert alert-error" role="alert" aria-live="assertive">
            <strong><span aria-hidden="true">⚠</span> Revise los errores en el formulario:</strong>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('contact.store') }}" class="contact-form" novalidate>
        @csrf

        <div class="form-group">
            <label for="name">Nombre</label>
            <input
                id="name"
                type="text"
                name="name"
                value="{{ old('name') }}"
                required
                maxlength="100"
                autocomplete="name"
                aria-invalid="{{ $errors->has('name') ? 'true' : 'false' }}"
                @error('name') aria-describedby="contact-name-error" @enderror
            >
            @error('name')
                <p class="field-error" id="contact-name-error" role="alert"><span aria-hidden="true">⚠</span> {{ $message }}</p>
            @enderror
        </div>

        <div class="form-group">
            <label for="email">Correo</label>
            <input
                id="email"
                type="email"
                name="email"
                value="{{ old('email') }}"
                required
                maxlength="255"
                autocomplete="email"
                aria-invalid="{{ $errors->has('email') ? 'true' : 'false' }}"
                @error('email') aria-describedby="contact-email-error" @enderror
            >
            @error('email')
                <p class="field-error" id="contact-email-error" role="alert"><span aria-hidden="true">⚠</span> {{ $message }}</p>
            @enderror
        </div>

        <div class="form-group">
            <label for="message">Mensaje</label>
            <textarea
                id="message"
                name="message"
                rows="8"
                required
                maxlength="5000"
                aria-invalid="{{ $errors->has('message') ? 'true' : 'false' }}"
                @error('message') aria-describedby="contact-message-error" @enderror
            >{{ old('message') }}</textarea>
            @error('message')
                <p class="field-error" id="contact-message-error" role="alert"><span aria-hidden="true">⚠</span> {{ $message }}</p>
            @enderror
        </div>

        <button type="submit" class="btn-colombia">Enviar mensaje</button>
    </form>
</section>
@endsection
