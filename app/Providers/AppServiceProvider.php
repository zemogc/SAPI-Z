<?php

namespace App\Providers;

use App\Services\IA\ClaudeProveedor;
use App\Services\IA\ProveedorIA;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(ProveedorIA::class, fn () => new ClaudeProveedor(
            apiKey: config('services.anthropic.api_key'),
            modelo: config('services.anthropic.model'),
            esfuerzo: config('services.anthropic.effort'),
        ));
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Cada llamada al asistente consume tokens: se limita por usuario.
        RateLimiter::for('asistente', fn (Request $request) => Limit::perMinute(15)->by($request->user()?->id ?: $request->ip()));
    }
}
