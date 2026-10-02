<?php

namespace App\Rules;

use App\Services\EmailValidationService;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class NotDisposableEmailRule implements ValidationRule
{
    public function __construct(private readonly EmailValidationService $emails) {}

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! is_string($value) || $this->emails->isDisposable($value)) {
            $fail('No fue posible completar la solicitud.');
        }
    }
}
