<?php

namespace App\Exceptions;

use App\Http\Resources\ApiResponse;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class Handler extends ExceptionHandler
{
    public function render($request, Throwable $e): JsonResponse|Response
    {
        if ($request->expectsJson()) {
            if ($e instanceof ValidationException) {
                return ApiResponse::error(
                    'Validation failed',
                    422,
                    $e->errors()
                );
            }

            if ($e instanceof ModelNotFoundException) {
                return ApiResponse::error('Resource not found', 404);
            }

            return ApiResponse::error('Server error', 500);
        }

        return parent::render($request, $e);
    }
}
