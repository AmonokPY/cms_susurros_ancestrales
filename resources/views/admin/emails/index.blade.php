@extends('layouts.cms')

@section('title', 'Correos recibidos')
@section('heading', 'Correos recibidos')

@section('content')
<p>El CMS consulta el buzón por POP3/IMAP. Los mensajes se guardan una sola vez según <code>message_id</code>. El HTML se muestra como texto para evitar XSS.</p>

<form method="POST" action="{{ route('admin.emails.fetch') }}" style="margin-bottom:1rem">
    @csrf
    <button type="submit" class="btn-colombia">Consultar buzón ahora</button>
</form>

@if ($errors->has('mailbox'))
    <p class="alert alert-error">{{ $errors->first('mailbox') }}</p>
@endif

<table class="ds-table">
    <thead>
        <tr>
            <th>Estado</th>
            <th>De</th>
            <th>Asunto</th>
            <th>Fecha</th>
            <th></th>
        </tr>
    </thead>
    <tbody>
        @forelse ($emails as $email)
            <tr>
                <td>{{ $email->is_read ? 'Leído' : 'Nuevo' }}</td>
                <td>{{ $email->from_name ? $email->from_name.' <'.$email->from_email.'>' : $email->from_email }}</td>
                <td>{{ $email->subject }}</td>
                <td>{{ $email->received_at?->format('d/m/Y H:i') }}</td>
                <td>
                    <a href="{{ route('admin.emails.show', $email) }}">Ver</a>
                    @unless ($email->is_read)
                        <form method="POST" action="{{ route('admin.emails.mark-read', $email) }}" style="display:inline">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="button-secondary">Marcar leído</button>
                        </form>
                    @endunless
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="5">No hay correos guardados. Envíe un mensaje desde Contacto y pulse Consultar buzón.</td>
            </tr>
        @endforelse
    </tbody>
</table>
@endsection
