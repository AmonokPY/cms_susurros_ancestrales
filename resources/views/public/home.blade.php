@extends('layouts.public')

@section('title', $settings->hero_title)

@section('content')
<section class="hero hero-gradient" id="inicio">
    <div class="hero-copy">
        <p class="eyebrow">Videojuego colombiano</p>
        <h1>{{ $settings->hero_title }}</h1>
        <p>Historias por contar convertidas en puzzles sobre lugares reales.</p>
        <div class="hero-actions">
            @if ($settings->run_button_url)
                <a class="btn-run" href="{{ $settings->run_button_url }}" target="_blank" rel="noopener noreferrer">
                    {{ $settings->run_button_text }}
                </a>
            @endif
            @if ($settings->android_apk_url)
                <a class="btn-android" href="{{ $settings->android_apk_url }}" download>
                    {{ $settings->android_button_text }}
                </a>
            @endif
        </div>
    </div>
</section>

<section class="about" id="quienes-somos">
    <div class="about-text">
        <h2>{{ $settings->about_title }}</h2>
        <div class="rich-text">{!! $settings->about_text !!}</div>
    </div>
    @if ($media->url($settings->about_image_path))
        <figure>
            <img src="{{ $media->url($settings->about_image_path) }}" alt="{{ $settings->about_image_alt ?: $settings->about_title }}">
        </figure>
    @endif
</section>

<section class="gallery-section" id="juega">
    <h2>Juega</h2>
    @if ($playCards->isEmpty())
        <p class="empty">Aún no hay características publicadas.</p>
    @else
        <div class="gallery">
            <ul class="cards">
                @foreach ($playCards as $item)
                    <li style="background-image: url('{{ $media->url($item->image_path) }}')">
                        <div class="card-caption">
                            <strong>{{ $item->title }}</strong>
                            <span>{{ $item->description }}</span>
                            @if ($item->video_url)
                                <a href="{{ $item->video_url }}" target="_blank" rel="noopener noreferrer">Ver video</a>
                            @endif
                        </div>
                    </li>
                @endforeach
            </ul>
            <div class="actions">
                <button type="button" class="prev">Prev</button>
                <button type="button" class="next">Next</button>
            </div>
        </div>
        <div class="drag-proxy"></div>
    @endif
</section>

<section class="gallery-section" id="explora">
    <h2>Explora</h2>
    @if ($sponsorCards->isEmpty())
        <p class="empty">Aún no hay patrocinadores publicados.</p>
    @else
        <div class="gallery">
            <ul class="cards">
                @foreach ($sponsorCards as $item)
                    <li style="background-image: url('{{ $media->url($item->image_path) }}')">
                        <div class="card-caption">
                            <strong>{{ $item->name }}</strong>
                            <span>{{ $item->description }}</span>
                            @if ($item->website_url)
                                <a href="{{ $item->website_url }}" target="_blank" rel="noopener noreferrer">Sitio web</a>
                            @endif
                            @if ($item->phone || $item->email)
                                @php
                                    $contactHref = $item->phone ? 'tel:'.preg_replace('/\s+/', '', $item->phone) : 'mailto:'.$item->email;
                                @endphp
                                <a href="{{ $contactHref }}">Contactar</a>
                            @endif
                        </div>
                    </li>
                @endforeach
            </ul>
            <div class="actions">
                <button type="button" class="prev">Prev</button>
                <button type="button" class="next">Next</button>
            </div>
        </div>
        <div class="drag-proxy"></div>
    @endif
</section>

<section class="contact" id="contacto">
    <h2>{{ $settings->contact_title }}</h2>
    <div class="contact-grid">
        <div>
            @if ($settings->contact_email)
                <p>Correo: <a href="mailto:{{ $settings->contact_email }}">{{ $settings->contact_email }}</a></p>
            @endif
            @if ($settings->contact_phone)
                <p>Teléfono: <a href="tel:{{ preg_replace('/\s+/', '', $settings->contact_phone) }}">{{ $settings->contact_phone }}</a></p>
            @endif
            @if ($settings->address)
                <p>Dirección: {{ $settings->address }}</p>
            @endif
            <div class="socials">
                @if ($settings->facebook_url)<a href="{{ $settings->facebook_url }}" target="_blank" rel="noopener noreferrer">Facebook</a>@endif
                @if ($settings->instagram_url)<a href="{{ $settings->instagram_url }}" target="_blank" rel="noopener noreferrer">Instagram</a>@endif
                @if ($settings->twitter_url)<a href="{{ $settings->twitter_url }}" target="_blank" rel="noopener noreferrer">X</a>@endif
                @if ($settings->youtube_url)<a href="{{ $settings->youtube_url }}" target="_blank" rel="noopener noreferrer">YouTube</a>@endif
                @foreach ($settings->extra_socials ?? [] as $social)
                    <a href="{{ $social['url'] }}" target="_blank" rel="noopener noreferrer">{{ $social['name'] }}</a>
                @endforeach
            </div>
        </div>
        <div class="rich-text">{!! $settings->contact_extra !!}</div>
        <form method="POST" action="{{ route('contact.send') }}" class="contact-form ds-card franja-tricolor">
            @csrf
            <div class="form-group">
                <label for="home-name">Nombre</label>
                <input id="home-name" name="name" value="{{ old('name') }}" required maxlength="100">
                @error('name') <p class="field-error">{{ $message }}</p> @enderror
            </div>
            <div class="form-group">
                <label for="home-email">Correo</label>
                <input id="home-email" type="email" name="email" value="{{ old('email') }}" required maxlength="150">
                @error('email') <p class="field-error">{{ $message }}</p> @enderror
            </div>
            <div class="form-group">
                <label for="home-message">Mensaje</label>
                <textarea id="home-message" name="message" required maxlength="2000">{{ old('message') }}</textarea>
                @error('message') <p class="field-error">{{ $message }}</p> @enderror
            </div>
            <button type="submit" class="btn-colombia">Enviar</button>
        </form>
    </div>
</section>
@endsection
