<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;
use Symfony\Component\HttpFoundation\Cookie;

/**
 * Middleware CSRF endurecido.
 *
 * Remediación OWASP ZAP:
 * - [BAJO · CWE-1004] Cookie XSRF-TOKEN marcada con HttpOnly=true, Secure=true y SameSite=Lax.
 *   El frontend consume el token CSRF desde <meta name="csrf-token">.
 */
class VerifyCsrfToken extends ValidateCsrfToken
{
    /**
     * Create a new "XSRF-TOKEN" cookie.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  array<string, mixed>  $config
     */
    protected function newCookie($request, $config): Cookie
    {
        return new Cookie(
            'XSRF-TOKEN',
            $request->session()->token(),
            $this->availableAt(60 * $config['lifetime']),
            $config['path'],
            $config['domain'],
            true,
            true,
            false,
            $config['same_site'] ?? 'lax',
            $config['partitioned'] ?? false
        );
    }
}
