<?php

namespace App\Rules;

use App\Services\EmailValidationService;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class RealEmailRule implements ValidationRule
{
    public function __construct(private readonly EmailValidationService $emails) {}

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! is_string($value) || ! $this->emails->isRealEmail($value)) {
            $fail('No fue posible completar la solicitud.');
        }
    }
}
