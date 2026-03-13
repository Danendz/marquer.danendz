<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Sentry DSN
    |--------------------------------------------------------------------------
    |
    | The DSN tells the SDK where to send the events. Get your DSN from
    | your GlitchTip/Sentry project settings. Leave empty to disable.
    |
    */
    'dsn' => env('SENTRY_LARAVEL_DSN', ''),

    /*
    |--------------------------------------------------------------------------
    | Environment & Release
    |--------------------------------------------------------------------------
    */
    'environment' => env('SENTRY_ENVIRONMENT', env('APP_ENV', 'production')),

    'release' => env('SENTRY_RELEASE'),

    /*
    |--------------------------------------------------------------------------
    | Performance Tracing
    |--------------------------------------------------------------------------
    */
    'traces_sample_rate' => (float) env('SENTRY_TRACES_SAMPLE_RATE', 0.01),

    /*
    |--------------------------------------------------------------------------
    | PII
    |--------------------------------------------------------------------------
    */
    'send_default_pii' => false,

    /*
    |--------------------------------------------------------------------------
    | Ignore Exceptions
    |--------------------------------------------------------------------------
    |
    | These exceptions will NOT be reported to GlitchTip — they are either
    | expected auth/validation noise or bot/scanner 404s, not real bugs.
    |
    */
    'ignore_exceptions' => [
        // JWT noise (expected auth failures)
        PHPOpenSourceSaver\JWTAuth\Exceptions\TokenExpiredException::class,
        PHPOpenSourceSaver\JWTAuth\Exceptions\TokenInvalidException::class,
        PHPOpenSourceSaver\JWTAuth\Exceptions\JWTException::class,
        // Auth noise
        Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException::class,
        Illuminate\Auth\AuthenticationException::class,
        // Bot/scanner noise
        Symfony\Component\HttpKernel\Exception\NotFoundHttpException::class,
        Illuminate\Database\Eloquent\ModelNotFoundException::class,
        // Client input errors
        Illuminate\Validation\ValidationException::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Breadcrumbs
    |--------------------------------------------------------------------------
    */
    'breadcrumbs' => [
        'logs'                => true,
        'sql_queries'         => true,
        'sql_bindings'        => false,
        'queue_info'          => true,
        'command_info'        => true,
        'http_client_requests' => true,
        'cache'               => false,
    ],

];
