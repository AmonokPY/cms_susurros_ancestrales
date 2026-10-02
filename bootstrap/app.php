<?php

use App\Http\Middleware\EnsureEmailIsAuthorized;
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
