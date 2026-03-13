<?php

namespace App\Providers;

use App\Auth\JwtStatelessGuard;
use App\Services\RabbitPublisherService;
use App\Services\S3ClientService;
use App\Support\SentryBeforeSend;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;
use Sentry\SentrySdk;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(RabbitPublisherService::class);
        $this->app->singleton(S3ClientService::class);
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

        $client = SentrySdk::getCurrentHub()->getClient();
        if ($client !== null) {
            $client->getOptions()->setBeforeSendCallback(new SentryBeforeSend());
        }
    }
}
