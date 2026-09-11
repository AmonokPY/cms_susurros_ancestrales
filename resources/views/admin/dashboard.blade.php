@extends('layouts.cms')

@section('title', 'Dashboard')
@section('heading', 'Dashboard')

@section('content')
<div class="cms-cards">
    <a class="cms-stat franja-tricolor" href="{{ route('admin.settings.edit') }}">
        <strong>Inicio</strong>
        <span>Título, botones, quiénes somos y contacto</span>
    </a>
    <a class="cms-stat franja-tricolor" href="{{ route('admin.play-items.index') }}">
        <strong>{{ $playCount }}</strong>
        <span>Ítems de Juega</span>
    </a>
    <a class="cms-stat franja-tricolor" href="{{ route('admin.sponsors.index') }}">
        <strong>{{ $sponsorCount }}</strong>
        <span>Patrocinadores</span>
    </a>
    <a class="cms-stat franja-tricolor" href="{{ route('admin.puzzles.index') }}">
        <strong>{{ $puzzleCount }}</strong>
        <span>Puzzles</span>
    </a>
</div>
@endsection
