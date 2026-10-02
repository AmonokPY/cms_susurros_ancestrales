<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Str;

class StrongPasswordRule implements ValidationRule
{
    public function __construct(
        private readonly ?string $name = null,
        private readonly ?string $email = null,
    ) {}

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $password = (string) $value;
        $min = (int) config('security.password.min', 12);
        $generic = 'La contraseña no cumple los requisitos de seguridad.';

        if (strlen($password) < $min) {
            $fail($generic);

            return;
        }

        if (
            ! preg_match('/[a-z]/', $password)
            || ! preg_match('/[A-Z]/', $password)
            ||             ! preg_match('/\d/', $password)
        ) {
            $fail($generic);

            return;
        }

        $common = array_map('strtolower', config('security.password.common', []));
        if (in_array(strtolower($password), $common, true)) {
            $fail($generic);

            return;
        }

        $haystack = strtolower($password);
        $name = strtolower(preg_replace('/\s+/', '', (string) $this->name) ?? '');
        $local = strtolower((string) Str::before((string) $this->email, '@'));

        if (($name !== '' && str_contains($haystack, $name)) || ($local !== '' && str_contains($haystack, $local))) {
            $fail($generic);
        }
    }
}
