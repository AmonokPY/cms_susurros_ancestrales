@extends('layouts.cms')

@section('title', 'Explora')
@section('heading', 'Patrocinadores')

@section('content')
<p><a class="btn-primary" href="{{ route('admin.sponsors.create') }}">Agregar patrocinador</a></p>
<p class="field-hint">Puede arrastrar cada tarjeta o enfocar el botón ☰ y usar las flechas Arriba/Abajo del teclado para reordenar.</p>
<ul class="reorder-list" data-reorder="{{ route('admin.sponsors.reorder') }}">
    @forelse ($items as $item)
        <li draggable="true" data-id="{{ $item->id }}">
            <span class="handle" tabindex="0" role="button" aria-label="Reordenar {{ $item->name }} (use las flechas arriba y abajo)">☰</span>
            @if ($media->url($item->image_path))
                <img src="{{ $media->url($item->image_path) }}" alt="">
            @endif
            <div>
                <strong>{{ $item->name }}</strong>
                <p>{{ $item->description }}</p>
            </div>
            <a href="{{ route('admin.sponsors.edit', $item) }}">Editar</a>
            <form method="POST" action="{{ route('admin.sponsors.destroy', $item) }}" onsubmit="return confirm('¿Eliminar este patrocinador?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="button-danger btn-sm">Eliminar</button>
            </form>
        </li>
    @empty
        <li>No hay patrocinadores.</li>
    @endforelse
</ul>
@endsection
