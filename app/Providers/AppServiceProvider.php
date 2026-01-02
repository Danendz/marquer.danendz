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
     * Registers application bootstrap behavior, including the custom 'jwt_stateless' authentication driver.
     *
     * The 'jwt_stateless' driver is registered with the Auth manager and resolves to a JwtStatelessGuard
     * instance constructed with the current HTTP request.
     *
     * @return void
     */
    public function boot(): void
    {
        Auth::extend('jwt_stateless', static function ($app, string $name, array $config) {
            return new JwtStatelessGuard($app['request']);
        });
    }
}