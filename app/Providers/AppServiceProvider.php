<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        RateLimiter::for('login', function (Request $request) {
            return Limit::perMinute((int) config('security.rate_limits.login_per_minute', 5))
                ->by((string) $request->ip());
        });

        RateLimiter::for('register', function (Request $request) {
            return Limit::perHour((int) config('security.rate_limits.register_per_hour', 3))
                ->by((string) $request->ip());
        });

        RateLimiter::for('contact', function (Request $request) {
            return Limit::perMinute(10)->by((string) $request->ip());
        });

        Gate::define('manage-users', fn (User $user) => $user->isAdmin());
        Gate::define('manage-content', fn (User $user) => $user->canAccessCms());
        Gate::define('manage-media', fn (User $user) => $user->canAccessCms());
    }
}
