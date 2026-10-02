<?php

namespace App\Http\Requests;

use App\Models\User;
use App\Rules\NotDisposableEmailRule;
use App\Rules\RealEmailRule;
use App\Rules\StrongPasswordRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class RegisterUserRequest extends FormRequest
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
            'name' => ['required', 'string', 'min:2', 'max:100'],
            'email' => [
                'required',
                'email',
                'max:255',
                app(RealEmailRule::class),
                app(NotDisposableEmailRule::class),
            ],
            'password' => [
                'required',
                'confirmed',
                new StrongPasswordRule(
                    $this->string('name')->toString(),
                    $this->string('email')->toString()
                ),
            ],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        $generic = 'No fue posible completar el registro.';

        return [
            'name.required' => $generic,
            'email.required' => $generic,
            'email.email' => $generic,
            'password.required' => 'La contraseña no cumple los requisitos de seguridad.',
            'password.confirmed' => 'La contraseña no cumple los requisitos de seguridad.',
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            $email = strtolower(trim((string) $this->input('email')));

            if ($email !== '' && User::query()->where('email', $email)->exists()) {
                $validator->errors()->add('email', 'No fue posible completar el registro.');
            }
        });
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('email')) {
            $this->merge([
                'email' => strtolower(trim((string) $this->input('email'))),
            ]);
        }
    }
}
