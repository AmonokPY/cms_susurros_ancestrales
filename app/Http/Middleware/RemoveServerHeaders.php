<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware para ocultar versiones de servidor y runtime PHP.
 *
 * Remediación OWASP ZAP:
 * - [BAJO · CWE-497] Exposición de información sensible a través de cabeceras
 *   HTTP "Server" y "X-Powered-By".
 */
class RemoveServerHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        if (function_exists('header_remove')) {
            @header_remove('X-Powered-By');
            @header_remove('Server');
        }

        /** @var Response $response */
        $response = $next($request);

        $response->headers->remove('X-Powered-By');
        $response->headers->set('Server', 'webserver');

        return $response;
    }
}
