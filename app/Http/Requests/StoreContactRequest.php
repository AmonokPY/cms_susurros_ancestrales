<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreContactRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:255'],
            'message' => ['required', 'string', 'max:5000'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Indique su nombre.',
            'email.required' => 'Indique un correo válido.',
            'email.email' => 'Indique un correo válido.',
            'message.required' => 'Escriba un mensaje.',
            'message.max' => 'El mensaje es demasiado largo.',
        ];
    }
}
