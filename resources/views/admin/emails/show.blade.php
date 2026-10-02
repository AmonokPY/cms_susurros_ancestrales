@extends('layouts.cms')

@section('title', $email->subject)
@section('heading', 'Mensaje recibido')

@section('content')
<p><a href="{{ route('admin.emails.index') }}">Volver a la bandeja</a></p>

<article class="ds-card">
    <p><strong>De:</strong> {{ $email->from_name }} &lt;{{ $email->from_email }}&gt;</p>
    <p><strong>Asunto:</strong> {{ $email->subject }}</p>
    <p><strong>Fecha:</strong> {{ $email->received_at?->format('d/m/Y H:i') }}</p>
    <p><strong>Adjuntos:</strong> {{ $email->has_attachments ? 'Sí (no se abren automáticamente)' : 'No' }}</p>
    <hr>
    <pre style="white-space:pre-wrap;font-family:inherit">{{ $email->body }}</pre>
</article>
@endsection
