@extends('layouts.app')

@section('title', 'Acerca')

@section('content')
<div class="container">
    <article class="ds-card franja-tricolor">
    <h1>Acerca del proyecto</h1>
    <p>
        Este CMS aplica prácticas de desarrollo seguro con Laravel:
        CSRF, validación en servidor, hashing de contraseñas,
        regeneración de sesión y rate limiting.
    </p>
    </article>
</div>
@endsection
