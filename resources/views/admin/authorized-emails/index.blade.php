@extends('layouts.cms')

@section('title', 'Correos autorizados')
@section('heading', 'Lista blanca')

@section('content')
<form method="POST" action="{{ route('admin.authorized-emails.store') }}" class="cms-form" novalidate>
    @csrf
    <label>Correo
        <input
            type="email"
            name="email"
            value="{{ old('email') }}"
            required
            maxlength="255"
            aria-invalid="{{ $errors->has('email') ? 'true' : 'false' }}"
            @error('email') aria-describedby="email-error" @enderror
        >
        @error('email')
            <span class="field-error" id="email-error" role="alert"><span aria-hidden="true">⚠</span> {{ $message }}</span>
        @enderror
    </label>
    <label>Motivo
        <input
            type="text"
            name="reason"
            value="{{ old('reason') }}"
            maxlength="255"
            aria-invalid="{{ $errors->has('reason') ? 'true' : 'false' }}"
            @error('reason') aria-describedby="reason-error" @enderror
        >
        @error('reason')
            <span class="field-error" id="reason-error" role="alert"><span aria-hidden="true">⚠</span> {{ $message }}</span>
        @enderror
    </label>
    <button type="submit" class="btn-colombia">Autorizar</button>
</form>

<table class="ds-table" style="margin-top:1.5rem">
    <thead>
        <tr>
            <th scope="col">Correo</th>
            <th scope="col">Motivo</th>
            <th scope="col">Estado</th>
            <th scope="col">Acciones</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($emails as $item)
            <tr>
                <td>{{ $item->email }}</td>
                <td>{{ $item->reason }}</td>
                <td>{{ $item->is_active ? 'Activo' : 'Inactivo' }}</td>
                <td>
                    <form method="POST" action="{{ route('admin.authorized-emails.toggle', $item) }}" style="display:inline">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="button-secondary">{{ $item->is_active ? 'Desactivar' : 'Activar' }}</button>
                    </form>
                    <form method="POST" action="{{ route('admin.authorized-emails.destroy', $item) }}" style="display:inline" onsubmit="return confirm('¿Quitar este correo de la lista blanca?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="button-danger">Eliminar</button>
                    </form>
                </td>
            </tr>
        @empty
            <tr><td colspan="4">No hay correos autorizados.</td></tr>
        @endforelse
    </tbody>
</table>
@endsection
