@extends('layouts.cms')

@section('title', 'Usuarios')
@section('heading', 'Usuarios')

@section('content')
<p>Quien se registra solo ve el sitio. Usted decide si lo deja como visitante o lo convierte en editor del CMS.</p>

<table class="ds-table">
    <thead>
        <tr>
            <th>Nombre</th>
            <th>Correo</th>
            <th>Tipo</th>
            <th></th>
        </tr>
    </thead>
    <tbody>
        @foreach ($users as $user)
            <tr>
                <td>{{ $user->name }}</td>
                <td>{{ $user->email }}</td>
                <td>{{ $user->canAccessCms() ? 'Editor (CMS)' : 'Solo ver la página' }}</td>
                <td>
                    @if ($user->is(auth()->user()))
                        <span>Su cuenta</span>
                    @else
                        <form method="POST" action="{{ route('admin.users.toggle-cms', $user) }}" style="display:inline">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="{{ $user->cms_access ? 'button-secondary' : 'btn-colombia' }}">
                                {{ $user->cms_access ? 'Dejar solo ver la página' : 'Hacer editor' }}
                            </button>
                        </form>
                        <form method="POST" action="{{ route('admin.users.destroy', $user) }}" style="display:inline" onsubmit="return confirm('¿Eliminar esta cuenta?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="button-danger">Eliminar</button>
                        </form>
                    @endif
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
@endsection
