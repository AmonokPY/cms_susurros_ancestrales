@extends('layouts.public')

@section('title', 'Contacto')
@section('body_class', 'public-home')

@section('content')
<section class="contact" id="formulario-contacto">
    <h2>{{ $settings->contact_title ?? 'Contacto' }}</h2>
    <p>Escríbanos. El mensaje se envía por SMTP al buzón del CMS; un administrador lo revisa en la bandeja recibida.</p>

    @if ($errors->any())
        <div class="alert alert-error" role="alert">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('contact.store') }}" class="contact-form">
        @csrf

        <div class="form-group">
            <label for="name">Nombre</label>
            <input id="name" type="text" name="name" value="{{ old('name') }}" required maxlength="100" autocomplete="name">
        </div>

        <div class="form-group">
            <label for="email">Correo</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required maxlength="255" autocomplete="email">
        </div>

        <div class="form-group">
            <label for="message">Mensaje</label>
            <textarea id="message" name="message" rows="8" required maxlength="5000">{{ old('message') }}</textarea>
        </div>

        <button type="submit" class="btn-colombia">Enviar mensaje</button>
    </form>
</section>
@endsection
