<?php

namespace App\Rules;

use App\Models\AuthorizedEmail;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class AuthorizedEmailRule implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! is_string($value) || ! AuthorizedEmail::isAuthorized($value)) {
            $fail('No fue posible completar la solicitud.');
        }
    }
}
