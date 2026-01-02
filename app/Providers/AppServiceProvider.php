<?php

namespace App\Providers;

use App\Auth\JwtStatelessGuard;
use Illuminate\Support\Facades\Auth;
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
        Auth::extend('jwt_stateless', static function ($app, string $name, array $config) {
            return new JwtStatelessGuard($app['request']);
        });
    }
}
