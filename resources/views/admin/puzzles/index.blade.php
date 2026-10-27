@extends('layouts.cms')

@section('title', 'Puzzles')
@section('heading', 'Puzzles')

@section('content')
<p><a class="btn-primary" href="{{ route('admin.puzzles.create') }}">Agregar puzzle</a></p>
<p class="field-hint">Puede arrastrar cada tarjeta o enfocar el botón ☰ y usar las flechas Arriba/Abajo del teclado para reordenar.</p>
<ul class="reorder-list" data-reorder="{{ route('admin.puzzles.reorder') }}">
    @forelse ($items as $item)
        <li draggable="true" data-id="{{ $item->id }}">
            <span class="handle" tabindex="0" role="button" aria-label="Reordenar {{ $item->name }} (use las flechas arriba y abajo)">☰</span>
            @if ($media->url($item->cover_image_path))
                <img src="{{ $media->url($item->cover_image_path) }}" alt="">
            @endif
            <div>
                <strong>{{ $item->name }}</strong>
                <p>/puzzles/{{ $item->slug }}</p>
            </div>
            <a href="{{ route('puzzles.show', $item->slug) }}" target="_blank" rel="noopener noreferrer">Ver <span class="sr-only">(abre en nueva pestaña)</span></a>
            <a href="{{ route('admin.puzzles.edit', $item) }}">Editar</a>
            <form method="POST" action="{{ route('admin.puzzles.destroy', $item) }}" onsubmit="return confirm('¿Eliminar este puzzle?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="button-danger btn-sm">Eliminar</button>
            </form>
        </li>
    @empty
        <li>No hay puzzles.</li>
    @endforelse
</ul>
@endsection
