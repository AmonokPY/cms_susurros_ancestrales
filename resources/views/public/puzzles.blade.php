@extends('layouts.public')

@section('title', 'Puzzles | '.($settings->hero_title ?? 'Susurranes'))
@section('body_class', 'puzzles-page')

@section('content')
<div class="main-container">
    <div class="carousel-section">
        <div class="carousel-container">
            <button type="button" class="nav-arrow up" aria-label="Anterior">▲</button>
            <div class="carousel-track">
                @foreach ($puzzles as $index => $puzzle)
                    <div class="card" data-index="{{ $index }}" data-slug="{{ $puzzle->slug }}">
                        <img src="{{ $media->url($puzzle->cover_image_path) }}" alt="{{ $puzzle->name }}">
                        <button type="button" class="more-info" data-open-puzzle="{{ $puzzle->slug }}">Más información</button>
                    </div>
                @endforeach
            </div>
            <button type="button" class="nav-arrow down" aria-label="Siguiente">▼</button>
        </div>
    </div>

    <div class="controls-section">
        <div class="nav-controls">
            <button type="button" class="nav-arrow up" aria-label="Anterior">▲</button>
            <button type="button" class="nav-arrow down" aria-label="Siguiente">▼</button>
        </div>

        <div class="member-info">
            <h2 class="member-name">{{ $puzzles->first()->name ?? 'Puzzles' }}</h2>
            <p class="member-role">{{ $puzzles->first()->short_description ?? 'Explora los mundos' }}</p>
        </div>

        <div class="dots">
            @foreach ($puzzles as $index => $puzzle)
                <div class="dot {{ $index === 0 ? 'active' : '' }}" data-index="{{ $index }}"></div>
            @endforeach
        </div>
    </div>
</div>

@if ($puzzles->isEmpty())
    <p class="empty" style="text-align:center;color:#fff;">Aún no hay puzzles publicados.</p>
@endif

<div class="puzzle-modal" id="puzzle-modal" hidden>
    <div class="puzzle-modal-backdrop" data-close-modal></div>
    <div class="puzzle-modal-dialog franja-tricolor modal-box" role="dialog" aria-modal="true">
        <button type="button" class="modal-close" data-close-modal>&times;</button>
        <div id="puzzle-modal-body"></div>
    </div>
</div>

<script type="application/json" id="puzzles-data">{!! json_encode($puzzles->map->toPublicArray()->values(), JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP) !!}</script>
<script type="application/json" id="puzzles-boot">{!! json_encode(['active' => $activeSlug, 'indexUrl' => route('puzzles.index')], JSON_UNESCAPED_UNICODE) !!}</script>
@endsection
