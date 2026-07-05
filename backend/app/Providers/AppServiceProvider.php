<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void {}

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Force le protocole HTTPS si l'accès se fait via le tunnel ngrok
        if (str_contains(request()->getHttpHost(), 'ngrok-free.dev')) {
            URL::forceScheme('https');
        }
        // Limite générale : 60 requêtes/minute par utilisateur connecté, ou par IP si anonyme.
        RateLimiter::for('api', function ($request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });

        // Limite stricte pour les actions sensibles (inscription, demandes, renvoi email).
        RateLimiter::for('sensitive', function ($request) {
            return Limit::perMinute(5)->by($request->ip());
        });
    }
}
