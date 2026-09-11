<?php

namespace App\Support;

class Html
{
    public static function sanitize(?string $html): ?string
    {
        if ($html === null || trim($html) === '') {
            return null;
        }

        $allowed = '<p><br><strong><b><em><i><u><ul><ol><li><a><h2><h3><h4><blockquote>';

        $clean = strip_tags($html, $allowed);

        return preg_replace_callback('/<a\s+[^>]*href=["\']([^"\']+)["\'][^>]*>/i', function ($matches) {
            $href = $matches[1];
            if (! preg_match('/^(https?:\/\/|mailto:|tel:|\/)/i', $href)) {
                return '<a>';
            }

            return '<a href="'.e($href).'" rel="noopener noreferrer" target="_blank">';
        }, $clean);
    }
}
