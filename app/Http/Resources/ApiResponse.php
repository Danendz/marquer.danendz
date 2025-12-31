<?php

namespace App\Http\Resources;

use Illuminate\Http\JsonResponse;

class ApiResponse
{
    public static function success(mixed $data = null, string $message = 'Success', int $status = 200): JsonResponse
    {
        return response()->json([
            'status' => $status,
            'success' => true,
            'message' => $message,
            'data' => $data
        ], $status);
    }

    public static function error(mixed $data = null, string $message = 'Error', int $status = 500): JsonResponse
    {
        return response()->json([
            'status' => $status,
            'success' => false,
            'message' => $message,
            'data' => $data
        ], $status);
    }
}
