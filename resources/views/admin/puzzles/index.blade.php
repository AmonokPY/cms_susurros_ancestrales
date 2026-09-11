@extends('layouts.cms')

@section('title', 'Puzzles')
@section('heading', 'Puzzles')

@section('content')
<p><a class="btn-primary" href="{{ route('admin.puzzles.create') }}">Agregar puzzle</a></p>
<ul class="reorder-list" data-reorder="{{ route('admin.puzzles.reorder') }}">
    @forelse ($items as $item)
        <li draggable="true" data-id="{{ $item->id }}">
            <span class="handle">☰</span>
            @if ($media->url($item->cover_image_path))
                <img src="{{ $media->url($item->cover_image_path) }}" alt="">
            @endif
            <div>
                <strong>{{ $item->name }}</strong>
                <p>/puzzles/{{ $item->slug }}</p>
            </div>
            <a href="{{ route('puzzles.show', $item->slug) }}" target="_blank">Ver</a>
            <a href="{{ route('admin.puzzles.edit', $item) }}">Editar</a>
            <form method="POST" action="{{ route('admin.puzzles.destroy', $item) }}" onsubmit="return confirm('¿Eliminar este puzzle?')">
                @csrf
                @method('DELETE')
                <button type="submit">Eliminar</button>
            </form>
        </li>
    @empty
        <li>No hay puzzles.</li>
    @endforelse
</ul>
@endsection
