<?php

use App\Http\Middleware\EnsureGitHubOidc;
use App\Http\Middleware\ForceJsonResponseMiddleware;
use App\Http\Middleware\JwtAuthMiddleware;
use App\Http\Resources\ApiResponse;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Validation\ValidationException;
use PHPOpenSourceSaver\JWTAuth\Exceptions\JWTException;
use PHPOpenSourceSaver\JWTAuth\Exceptions\TokenExpiredException;
use PHPOpenSourceSaver\JWTAuth\Exceptions\TokenInvalidException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;


return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'auth' => JwtAuthMiddleware::class,
            'github.oidc' => EnsureGitHubOidc::class
        ]);

        $middleware->api(prepend: [
            ForceJsonResponseMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (Throwable $e, Request $request) {

            function get_error_data(Throwable $e): array
            {
                return [
                    'exception' => get_class($e),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'trace' => $e->getTrace(),
                ];
            }

            if (!$request->is('api/*')) {
                return null;
            }

            $isDev = app()->environment(['local', 'development', 'testing']);

            // Validation
            if ($e instanceof ValidationException) {
                return ApiResponse::error(
                    data: $e->errors(),
                    message: $isDev ? $e->getMessage() : 'Validation failed.',
                    status: 422,
                );
            }

            // Token expired
            if ($e instanceof TokenExpiredException) {
                Log::warning('Token expired', [
                    'ip' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                    'url' => $request->fullUrl(),
                ]);

                return ApiResponse::error(
                    data: $isDev ? get_error_data($e) : null,
                    message: $isDev ? $e->getMessage() : 'Token expired.',
                    status: 401,
                );
            }

            // Token invalid (bad format/signature/etc)
            if ($e instanceof TokenInvalidException) {
                return ApiResponse::error(
                    data: $isDev ? get_error_data($e) : null,
                    message: $isDev ? $e->getMessage() : 'Token invalid.',
                    status: 401,
                );
            }

            // Token missing / not provided / not parsed
            if ($e instanceof JWTException) {
                return ApiResponse::error(
                    data: $isDev ? get_error_data($e) : null,
                    message: $isDev ? $e->getMessage() : 'Token not provided.',
                    status: 401,
                );
            }

            // Sometimes missing Bearer token or auth middleware issues come as UnauthorizedHttpException
            if ($e instanceof UnauthorizedHttpException || $e instanceof AuthenticationException) {
                return ApiResponse::error(
                    data: $isDev ? get_error_data($e) : null,
                    message: $isDev ? $e->getMessage() : 'Unauthenticated.',
                    status: 401,
                );
            }

            // Eloquent model not found (findOrFail/route binding)
            if ($e instanceof ModelNotFoundException) {
                return ApiResponse::error(
                    data: $isDev ? [
                        'model' => $e->getModel(),
                        'ids' => method_exists($e, 'getIds') ? $e->getIds() : null,
                    ] : null,
                    message: $isDev ? $e->getMessage() : 'Resource not found.',
                    status: 404,
                );
            }

            // Generic 404 (route not found, etc.)
            if ($e instanceof NotFoundHttpException) {
                return ApiResponse::error(
                    data: $isDev ? get_error_data($e) : null,
                    message: $isDev ? $e->getMessage() : 'Not found.',
                    status: 404,
                );
            }

            // Fallback 500
            return ApiResponse::error(
                data: $isDev ? [
                    'exception' => get_class($e),
                    'message' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'trace' => $e->getTrace(),
                ] : null,
                message: $isDev ? $e->getMessage() : 'Server error.',
                status: 500,
            );
        });
    })->create();
