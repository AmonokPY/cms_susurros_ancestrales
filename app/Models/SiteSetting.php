<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SiteSetting extends Model
{
    protected $fillable = [
        'hero_title',
        'run_button_text',
        'run_button_url',
        'android_button_text',
        'android_apk_url',
        'about_title',
        'about_text',
        'about_image_path',
        'about_image_alt',
        'contact_title',
        'contact_email',
        'contact_phone',
        'facebook_url',
        'instagram_url',
        'twitter_url',
        'youtube_url',
        'address',
        'contact_extra',
        'extra_socials',
    ];

    protected function casts(): array
    {
        return [
            'extra_socials' => 'array',
        ];
    }

    public static function current(): self
    {
        return static::query()->firstOrCreate([], [
            'hero_title' => 'Susurranes',
            'about_text' => 'Susurros ancestrales es un videojuego Colombiano que transforma historias por contar en puzles basados en lugares reales',
        ]);
    }
}
