<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware global de endurecimiento de cabeceras HTTP, cookies, redirecciones y caché.
 *
 * Remediación OWASP ZAP:
 * - [MEDIO · CWE-693] Content-Security-Policy (CSP) definida con dominios estrictos.
 * - [MEDIO · CWE-1021] X-Frame-Options: DENY contra Clickjacking.
 * - [BAJO · CWE-319] Strict-Transport-Security (HSTS) con preload.
 * - [BAJO · CWE-693] X-Content-Type-Options: nosniff en todas las respuestas (2xx/3xx/4xx/5xx).
 * - [BAJO · CWE-1004] Cookie XSRF-TOKEN y cookies de sesión con HttpOnly, Secure y SameSite=Lax.
 * - [BAJO · CWE-201] Eliminación del cuerpo HTML ("Redirecting to...") en respuestas 3xx.
 * - [BAJO · CWE-525] Cache-Control restrictivo (no-store, no-cache, must-revalidate, private)
 *   en rutas de autenticación, formularios y panel CMS.
 */
class SecurityHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        /** @var Response $response */
        $response = $next($request);

        $csp = implode('; ', [
            "default-src 'self'",
            "script-src 'self' https://cdn.jsdelivr.net 'unsafe-inline' 'unsafe-eval'",
            "style-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net https://fonts.googleapis.com",
            "img-src 'self' data: https:",
            "font-src 'self' data: https://fonts.gstatic.com",
            "connect-src 'self'",
            "frame-src 'self' https://www.youtube.com https://player.vimeo.com",
            "frame-ancestors 'none'",
            "base-uri 'self'",
            "form-action 'self'",
            'upgrade-insecure-requests',
        ]).';';

        $response->headers->set('Content-Security-Policy', $csp);
        $response->headers->set('X-Frame-Options', 'DENY');
        $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains; preload');
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->headers->set('Permissions-Policy', 'camera=(), microphone=(), geolocation=()');

        // [BAJO · CWE-201] Evitar cuerpo HTML "Redirecting to..." en redirecciones 301/302
        if ($response->isRedirection()) {
            $response->setContent('');
            $response->headers->set('Content-Length', '0');
        }

        // [BAJO · CWE-525] Deshabilitar caché en rutas autenticadas, formularios y redirecciones
        if ($this->shouldDisableCache($request, $response)) {
            $response->headers->set('Cache-Control', 'no-store, no-cache, must-revalidate, private');
            $response->headers->set('Pragma', 'no-cache');
            $response->headers->set('Expires', '0');
        }

        // [BAJO · CWE-1004] Garantizar HttpOnly, Secure y SameSite=Lax en todas las cookies (incluida XSRF-TOKEN)
        foreach ($response->headers->getCookies() as $cookie) {
            $response->headers->setCookie(new Cookie(
                $cookie->getName(),
                $cookie->getValue(),
                $cookie->getExpiresTime(),
                $cookie->getPath(),
                $cookie->getDomain(),
                true,
                true,
                $cookie->isRaw(),
                $cookie->getSameSite() ?: Cookie::SAMESITE_LAX,
                $cookie->isPartitioned()
            ));
        }

        return $response;
    }

    private function shouldDisableCache(Request $request, Response $response): bool
    {
        if ($request->user() !== null || $response->isRedirection() || ! $request->isMethodSafe()) {
            return true;
        }

        return $request->is(
            'login',
            'logout',
            'registro',
            'register',
            'contacto',
            'dashboard',
            'cms',
            'cms/*'
        );
    }
}
