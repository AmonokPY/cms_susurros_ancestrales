<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MediaService
{
    public function storeImage(UploadedFile $file, string $directory): string
    {
        $path = $file->store($directory, 'public');

        $this->optimizeImage(Storage::disk('public')->path($path));

        return $path;
    }

    public function storeAudio(UploadedFile $file, string $directory = 'audios'): string
    {
        return $file->store($directory, 'public');
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

        if (Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
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

        return Storage::disk('public')->url($path);
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
