@extends('layouts.app')

@section('title', 'Contacto')

@section('content')
<div class="container">
    <div class="ds-card franja-tricolor">
    <h1>Contacto</h1>
    <p>Formulario protegido con CSRF y validado en el servidor.</p>

    <form method="POST" action="{{ route('contact.send') }}" class="contact-form">
        @csrf

        <div class="form-group">
            <label for="name">Nombre</label>
            <input id="name" name="name" value="{{ old('name') }}" required maxlength="100">
            @error('name') <p class="field-error">{{ $message }}</p> @enderror
        </div>

        <div class="form-group">
            <label for="email">Correo</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required maxlength="150">
            @error('email') <p class="field-error">{{ $message }}</p> @enderror
        </div>

        <div class="form-group">
            <label for="message">Mensaje</label>
            <textarea id="message" name="message" required maxlength="2000">{{ old('message') }}</textarea>
            @error('message') <p class="field-error">{{ $message }}</p> @enderror
        </div>

        <button type="submit" class="btn-colombia">Enviar</button>
    </form>
    </div>
</div>
@endsection
