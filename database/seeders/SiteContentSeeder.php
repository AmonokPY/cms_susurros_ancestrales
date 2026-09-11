<?php

namespace Database\Seeders;

use App\Models\PlayItem;
use App\Models\Puzzle;
use App\Models\SiteSetting;
use App\Models\Sponsor;
use Illuminate\Database\Seeder;

class SiteContentSeeder extends Seeder
{
    public function run(): void
    {
        SiteSetting::current()->update([
            'hero_title' => 'Susurranes',
            'run_button_text' => 'Run',
            'run_button_url' => 'https://example.com/susurranes',
            'android_button_text' => 'Instalar Android',
            'android_apk_url' => 'https://example.com/susurranes.apk',
            'about_title' => 'Quiénes somos',
            'about_text' => '<p>Susurros ancestrales es un videojuego Colombiano que transforma historias por contar en puzles basados en lugares reales.</p>',
            'about_image_path' => 'https://assets.codepen.io/16327/portrait-number-01.png',
            'about_image_alt' => 'Paisaje ancestral colombiano',
            'contact_title' => 'Contáctanos',
            'contact_email' => 'hola@susurranes.com',
            'contact_phone' => '3249935042',
            'instagram_url' => 'https://instagram.com',
            'youtube_url' => 'https://youtube.com',
            'address' => 'Cundinamarca, Colombia',
            'contact_extra' => '<p>Escríbenos para alianzas, rutas turísticas y experiencias en territorio.</p>',
        ]);

        $plays = [
            ['Álbum', 'Descárgalo y registra tus experiencias', 'https://assets.codepen.io/16327/portrait-number-01.png'],
            ['Puzzles', 'Resuelve historias en lugares reales', 'https://assets.codepen.io/16327/portrait-number-02.png'],
            ['Avatares', 'Personaliza tu recorrido ancestral', 'https://assets.codepen.io/16327/portrait-number-03.png'],
        ];

        foreach ($plays as $i => [$title, $description, $image]) {
            PlayItem::query()->updateOrCreate(
                ['title' => $title],
                ['description' => $description, 'image_path' => $image, 'sort_order' => $i + 1]
            );
        }

        $sponsors = [
            ['Kroquipollo', 'Proporciona un descuento especial por jugador', 'https://assets.codepen.io/16327/portrait-number-04.png', '3241112233'],
            ['Antiplano Trip', 'Rutas turísticas para vivir el puzzle', 'https://assets.codepen.io/16327/portrait-number-05.png', '3249935042'],
            ['Altiplano Cultura', 'Apoya la memoria territorial', 'https://assets.codepen.io/16327/portrait-number-06.png', null],
        ];

        foreach ($sponsors as $i => [$name, $description, $image, $phone]) {
            Sponsor::query()->updateOrCreate(
                ['name' => $name],
                [
                    'description' => $description,
                    'image_path' => $image,
                    'phone' => $phone,
                    'sort_order' => $i + 1,
                ]
            );
        }

        $puzzles = [
            [
                'name' => 'Farallones de Suta Tausa',
                'slug' => 'farallones-de-suta-tausa',
                'short_description' => 'Explora los imponentes farallones',
                'cover' => 'https://picsum.photos/id/1015/800/450',
                'address' => 'Suta Tausa, Cundinamarca',
                'cta' => 'Contacta a Antiplano Trip para vivir la experiencia',
                'cta_link' => 'tel:3249935042',
            ],
            [
                'name' => 'Santo Cristo',
                'slug' => 'santo-cristo',
                'short_description' => 'Un enigma de fe y territorio',
                'cover' => 'https://picsum.photos/id/1016/800/450',
                'address' => 'Cundinamarca, Colombia',
                'cta' => 'Conoce la ruta',
                'cta_link' => 'https://antiplanotrip.com',
            ],
            [
                'name' => 'Laguna Ancestral',
                'slug' => 'laguna-ancestral',
                'short_description' => 'Aguas que guardan memorias',
                'cover' => 'https://picsum.photos/id/1018/800/450',
                'address' => 'Altiplano cundiboyacense',
                'cta' => 'Escríbenos',
                'cta_link' => 'mailto:hola@susurranes.com',
            ],
        ];

        foreach ($puzzles as $i => $item) {
            Puzzle::query()->updateOrCreate(
                ['slug' => $item['slug']],
                [
                    'name' => $item['name'],
                    'cover_image_path' => $item['cover'],
                    'short_description' => $item['short_description'],
                    'sort_order' => $i + 1,
                    'full_title' => $item['name'].' - Un viaje ancestral',
                    'description' => '<p>Contenido detallado del puzzle y su contexto territorial.</p>',
                    'benefits' => '<p>Puedes hacer la ruta turística y conocer la historia del lugar.</p>',
                    'address' => $item['address'],
                    'coordinates' => '5.0200° N, 73.9800° O',
                    'maps_url' => 'https://maps.google.com/?q='.urlencode($item['address']),
                    'cta_text' => $item['cta'],
                    'cta_link' => $item['cta_link'],
                ]
            );
        }
    }
}
