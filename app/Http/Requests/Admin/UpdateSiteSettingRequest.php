<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSiteSettingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'hero_title' => ['required', 'string', 'max:150'],
            'run_button_text' => ['required', 'string', 'max:80'],
            'run_button_url' => ['nullable', 'url', 'max:500'],
            'android_button_text' => ['required', 'string', 'max:80'],
            'android_apk_url' => ['nullable', 'url', 'max:500'],
            'about_title' => ['required', 'string', 'max:150'],
            'about_text' => ['nullable', 'string', 'max:20000'],
            'about_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'about_image_alt' => ['nullable', 'string', 'max:200'],
            'contact_title' => ['required', 'string', 'max:150'],
            'contact_email' => ['nullable', 'email', 'max:150'],
            'contact_phone' => ['nullable', 'string', 'max:80'],
            'facebook_url' => ['nullable', 'url', 'max:500'],
            'instagram_url' => ['nullable', 'url', 'max:500'],
            'twitter_url' => ['nullable', 'url', 'max:500'],
            'youtube_url' => ['nullable', 'url', 'max:500'],
            'address' => ['nullable', 'string', 'max:255'],
            'contact_extra' => ['nullable', 'string', 'max:20000'],
            'extra_socials' => ['nullable', 'array', 'max:12'],
            'extra_socials.*.name' => ['nullable', 'string', 'max:80'],
            'extra_socials.*.url' => ['nullable', 'url', 'max:500'],
        ];
    }

    public function messages(): array
    {
        return [
            'android_apk_url.url' => 'La URL del APK debe ser un enlace válido.',
        ];
    }
}
