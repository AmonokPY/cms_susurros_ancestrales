<?php

use App\Http\Middleware\EnsureEmailIsAuthorized;
use App\Http\Middleware\RemoveServerHeaders;
use App\Http\Middleware\SecurityHeaders;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->trustProxies(at: '*');

        // Registrados globalmente para cubrir todas las respuestas (incluidas 4xx/5xx)
        // y en los grupos web/api.
        $middleware->append(RemoveServerHeaders::class);
        $middleware->append(SecurityHeaders::class);

        $middleware->web(append: [
            RemoveServerHeaders::class,
            SecurityHeaders::class,
        ]);

        $middleware->api(append: [
            RemoveServerHeaders::class,
            SecurityHeaders::class,
        ]);

        $middleware->alias([
            'authorized' => EnsureEmailIsAuthorized::class,
        ]);
        $middleware->redirectUsersTo(function () {
            $user = auth()->user();

            return $user?->canAccessCms() ? '/dashboard' : '/';
        });
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
