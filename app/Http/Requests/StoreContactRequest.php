<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * FormRequest para POST /contacto.
 *
 * Remediación OWASP ZAP:
 * - [ALTO · CWE-89] Inyección SQL en parámetro "name": validación estricta por lista blanca
 *   de caracteres alfabéticos Unicode, espacios, apóstrofes, guiones y puntos.
 * - [ALTO · CWE-22] Path Traversal en parámetro "name": rechazo explícito de separadores
 *   de directorio ("/", "\"), secuencias ".." y bytes nulos ("\0").
 */
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
        $emailRule = ($this->container && ($this->container->environment('testing') || config('security.skip_mx_check')))
            ? 'email:rfc'
            : 'email:rfc,dns';

        return [
            'name' => [
                'required',
                'string',
                'max:100',
                'regex:/^[\pL\s\'\-\.]+$/u',
                'not_regex:/(\.\.|[\/\\\\\x00])/u',
            ],
            'email' => ['required', $emailRule, 'max:255'],
            'message' => ['required', 'string', 'min:10', 'max:5000'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Indique su nombre.',
            'name.regex' => 'El nombre solo puede contener letras, espacios, apóstrofes, guiones y puntos.',
            'name.not_regex' => 'El nombre contiene caracteres o secuencias de ruta no permitidos.',
            'email.required' => 'Indique un correo válido.',
            'email.email' => 'Indique un correo válido.',
            'message.required' => 'Escriba un mensaje.',
            'message.min' => 'El mensaje debe tener al menos 10 caracteres.',
            'message.max' => 'El mensaje es demasiado largo.',
        ];
    }
}
