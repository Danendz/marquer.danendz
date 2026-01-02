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
     * Register the 'jwt_stateless' authentication guard using JwtStatelessGuard bound to the current request.
     *
     * Adds a custom auth driver named 'jwt_stateless' to the Auth manager so authentication requests use JwtStatelessGuard
     * instantiated with the current HTTP request.
     */
    public function boot(): void
    {
        Auth::extend('jwt_stateless', static function ($app, string $name, array $config) {
            return new JwtStatelessGuard($app['request']);
        });
    }
}