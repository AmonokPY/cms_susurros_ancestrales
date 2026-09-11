@extends('layouts.cms')

@section('title', 'Juega')
@section('heading', 'Carrusel Juega')

@section('content')
<p><a class="btn-primary" href="{{ route('admin.play-items.create') }}">Agregar ítem</a></p>
<ul class="reorder-list" data-reorder="{{ route('admin.play-items.reorder') }}">
    @forelse ($items as $item)
        <li draggable="true" data-id="{{ $item->id }}">
            <span class="handle">☰</span>
            @if ($media->url($item->image_path))
                <img src="{{ $media->url($item->image_path) }}" alt="">
            @endif
            <div>
                <strong>{{ $item->title }}</strong>
                <p>{{ $item->description }}</p>
            </div>
            <a href="{{ route('admin.play-items.edit', $item) }}">Editar</a>
            <form method="POST" action="{{ route('admin.play-items.destroy', $item) }}" onsubmit="return confirm('¿Eliminar este ítem?')">
                @csrf
                @method('DELETE')
                <button type="submit">Eliminar</button>
            </form>
        </li>
    @empty
        <li>No hay ítems. Crea el primero.</li>
    @endforelse
</ul>
@endsection
