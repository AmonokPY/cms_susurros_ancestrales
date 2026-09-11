<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Puzzle extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'cover_image_path',
        'short_description',
        'sort_order',
        'full_title',
        'description',
        'video_url',
        'audio_path',
        'address',
        'coordinates',
        'maps_url',
        'benefits',
        'extra_image_path',
        'cta_text',
        'cta_link',
    ];

    public function slugRedirects(): HasMany
    {
        return $this->hasMany(PuzzleSlugRedirect::class);
    }

    public static function makeSlug(string $value): string
    {
        $slug = Str::slug($value);

        return $slug !== '' ? $slug : 'puzzle';
    }

    public static function uniqueSlug(string $value, ?int $ignoreId = null): string
    {
        $base = static::makeSlug($value);
        $slug = $base;
        $i = 2;

        while (
            static::query()
                ->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))
                ->where('slug', $slug)
                ->exists()
        ) {
            $slug = $base.'-'.$i;
            $i++;
        }

        return $slug;
    }

    public function toPublicArray(): array
    {
        $media = app(\App\Services\MediaService::class);

        return [
            'id' => $this->id,
            'nombre' => $this->name,
            'slug' => $this->slug,
            'imagen_portada' => $media->url($this->cover_image_path),
            'descripcion_corta' => $this->short_description,
            'orden' => $this->sort_order,
            'detalles' => [
                'titulo_completo' => $this->full_title ?: $this->name,
                'texto_descripcion' => $this->description,
                'video_url' => $this->video_url,
                'audio_url' => $media->url($this->audio_path),
                'ubicacion' => [
                    'direccion' => $this->address,
                    'coordenadas' => $this->coordinates,
                    'maps_url' => $this->maps_url,
                ],
                'beneficios' => $this->benefits,
                'imagen_adicional' => $media->url($this->extra_image_path),
                'cta' => [
                    'texto' => $this->cta_text,
                    'enlace' => $this->cta_link,
                ],
            ],
        ];
    }
}
