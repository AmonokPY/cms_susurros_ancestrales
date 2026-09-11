<?php

namespace App\Http\Requests\Admin;

use App\Models\Puzzle;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePuzzleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    protected function prepareForValidation(): void
    {
        $slug = $this->input('slug') ?: $this->input('name');

        if (is_string($slug)) {
            $this->merge(['slug' => Puzzle::makeSlug($slug)]);
        }
    }

    public function rules(): array
    {
        $puzzle = $this->route('puzzle');

        return [
            'name' => ['required', 'string', 'max:150'],
            'slug' => [
                'required',
                'string',
                'max:180',
                'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/',
                Rule::unique('puzzles', 'slug')->ignore($puzzle?->id),
            ],
            'short_description' => ['nullable', 'string', 'max:500'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:9999'],
            'full_title' => ['nullable', 'string', 'max:200'],
            'description' => ['nullable', 'string', 'max:50000'],
            'video_url' => ['nullable', 'url', 'max:500'],
            'audio' => ['nullable', 'file', 'mimes:mp3,wav,mpeg', 'max:20480'],
            'address' => ['nullable', 'string', 'max:255'],
            'coordinates' => ['nullable', 'string', 'max:120'],
            'maps_url' => ['nullable', 'url', 'max:500'],
            'benefits' => ['nullable', 'string', 'max:20000'],
            'cta_text' => ['nullable', 'string', 'max:200'],
            'cta_link' => ['nullable', 'string', 'max:500', 'regex:/^(https?:\/\/|tel:|mailto:)/i'],
            'cover_image' => [$this->isMethod('post') ? 'required' : 'nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'extra_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
        ];
    }
}
