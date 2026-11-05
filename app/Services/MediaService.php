<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use InvalidArgumentException;

/**
 * Servicio de gestión de archivos multimedia del CMS.
 *
 * Remediación OWASP ZAP:
 * - [ALTO · CWE-22] Validación de rutas con basename() + realpath() garantizando que
 *   toda ruta permanezca dentro de storage_path('app/public').
 */
class MediaService
{
    public function storeImage(UploadedFile $file, string $directory): string
    {
        $safeDirectory = $this->sanitizeDirectory($directory);
        $path = $file->store($safeDirectory, 'public');

        $fullPath = $this->resolveSafePublicStoragePath($path);
        if ($fullPath !== null) {
            $this->optimizeImage($fullPath);
        }

        return $path;
    }

    public function storeAudio(UploadedFile $file, string $directory = 'audios'): string
    {
        $safeDirectory = $this->sanitizeDirectory($directory);

        return $file->store($safeDirectory, 'public');
    }

    public function replaceImage(?string $currentPath, ?UploadedFile $file, string $directory): ?string
    {
        if (! $file) {
            return $currentPath;
        }

        $this->delete($currentPath);

        return $this->storeImage($file, $directory);
    }

    public function replaceAudio(?string $currentPath, ?UploadedFile $file): ?string
    {
        if (! $file) {
            return $currentPath;
        }

        $this->delete($currentPath);

        return $this->storeAudio($file);
    }

    public function delete(?string $path): void
    {
        if (! $path || Str::startsWith($path, ['http://', 'https://'])) {
            return;
        }

        $safeRelative = $this->sanitizeRelativePath($path);
        if ($safeRelative === null) {
            return;
        }

        if (Storage::disk('public')->exists($safeRelative)) {
            Storage::disk('public')->delete($safeRelative);
        }
    }

    public function url(?string $path): ?string
    {
        if (! $path) {
            return null;
        }

        if (Str::startsWith($path, ['http://', 'https://'])) {
            return $path;
        }

        $safeRelative = $this->sanitizeRelativePath($path);
        if ($safeRelative === null) {
            return null;
        }

        return Storage::disk('public')->url($safeRelative);
    }

    /**
     * Valida que una ruta relativa resuelva estrictamente dentro de storage_path('app/public').
     */
    public function resolveSafePublicStoragePath(string $relativePath): ?string
    {
        $safeRelative = $this->sanitizeRelativePath($relativePath);
        if ($safeRelative === null) {
            return null;
        }

        $baseDir = Storage::disk('public')->path('');
        $realBase = realpath($baseDir) ?: rtrim($baseDir, DIRECTORY_SEPARATOR);
        $candidate = Storage::disk('public')->path($safeRelative);
        $realCandidate = realpath($candidate);

        if ($realCandidate === false) {
            return null;
        }

        $normalizedBase = rtrim(str_replace('\\', '/', $realBase), '/').'/';
        $normalizedCandidate = str_replace('\\', '/', $realCandidate);

        if (! str_starts_with($normalizedCandidate, $normalizedBase)) {
            return null;
        }

        return $realCandidate;
    }

    private function sanitizeDirectory(string $directory): string
    {
        $clean = basename(str_replace(['\\', "\0"], '/', trim($directory)));
        if ($clean === '' || $clean === '.' || $clean === '..') {
            throw new InvalidArgumentException('Directorio de almacenamiento no válido.');
        }

        return $clean;
    }

    private function sanitizeRelativePath(string $path): ?string
    {
        if (str_contains($path, "\0") || str_contains($path, '..')) {
            return null;
        }

        $normalized = ltrim(str_replace('\\', '/', trim($path)), '/');
        $segments = array_filter(explode('/', $normalized), fn (string $s) => $s !== '');
        if ($segments === []) {
            return null;
        }

        $safeSegments = array_map(fn (string $segment) => basename($segment), $segments);

        return implode('/', $safeSegments);
    }

    private function optimizeImage(string $fullPath): void
    {
        if (! is_file($fullPath) || ! function_exists('imagecreatefromstring')) {
            return;
        }

        $ext = strtolower(pathinfo($fullPath, PATHINFO_EXTENSION));
        if (! in_array($ext, ['jpg', 'jpeg'], true)) {
            return;
        }

        $binary = file_get_contents($fullPath);
        if ($binary === false) {
            return;
        }

        $image = @imagecreatefromstring($binary);
        if (! $image) {
            return;
        }

        $width = imagesx($image);
        $height = imagesy($image);
        $max = 1600;

        if ($width > $max || $height > $max) {
            $ratio = min($max / $width, $max / $height);
            $newWidth = (int) round($width * $ratio);
            $newHeight = (int) round($height * $ratio);
            $resized = imagecreatetruecolor($newWidth, $newHeight);
            imagecopyresampled($resized, $image, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
            imagedestroy($image);
            $image = $resized;
        }

        $ext = strtolower(pathinfo($fullPath, PATHINFO_EXTENSION));
        if (in_array($ext, ['jpg', 'jpeg'], true)) {
            imagejpeg($image, $fullPath, 82);
        }

        imagedestroy($image);
    }
}
