<?php

namespace App\Services;

use App\Models\AuthorizedEmail;

class EmailValidationService
{
    public function isRealEmail(string $email): bool
    {
        $email = strtolower(trim($email));

        if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return false;
        }

        if ($this->isDisposable($email)) {
            return false;
        }

        return $this->hasValidMX($email);
    }

    public function isDisposable(string $email): bool
    {
        $domain = $this->domain($email);
        $blocked = array_map('strtolower', config('security.disposable_domains', []));

        return in_array($domain, $blocked, true);
    }

    public function hasValidMX(string $email): bool
    {
        if (config('security.skip_mx_check') || app()->environment('testing')) {
            return str_contains($this->domain($email), '.');
        }

        $domain = $this->domain($email);

        if ($domain === '') {
            return false;
        }

        return checkdnsrr($domain, 'MX')
            || checkdnsrr($domain, 'A')
            || $this->hasDnsRecords($domain);
    }

    public function isAuthorized(string $email): bool
    {
        return AuthorizedEmail::isAuthorized($email);
    }

    private function hasDnsRecords(string $domain): bool
    {
        $mx = @dns_get_record($domain, DNS_MX);
        $a = @dns_get_record($domain, DNS_A);

        return (is_array($mx) && $mx !== []) || (is_array($a) && $a !== []);
    }

    private function domain(string $email): string
    {
        $parts = explode('@', strtolower(trim($email)));

        return $parts[1] ?? '';
    }
}
