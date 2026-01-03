<?php

namespace App\Providers;

use App\Auth\JwtStatelessGuard;
use App\Services\RabbitPublisherService;
use App\Services\S3ClientService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(RabbitPublisherService::class, function () {
            return new RabbitPublisherService();
        });

        $this->app->singleton(S3ClientService::class, function () {
            return new S3ClientService();
        });
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
