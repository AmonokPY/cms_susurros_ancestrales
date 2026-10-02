<?php

namespace App\Http\Middleware;

use App\Models\AuditLog;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureEmailIsAuthorized
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user) {
            return $next($request);
        }

        if ($user->canAccessCms()) {
            return $next($request);
        }

        AuditLog::record(
            'auth.access_denied',
            'Intento de acceso al CMS sin habilitación del administrador.',
            'denied',
            $user
        );

        return redirect()
            ->route('home')
            ->with('status', 'Su cuenta puede ver el sitio. Un administrador debe habilitar el acceso al CMS.');
    }
}
