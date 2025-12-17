<?php

namespace App\Http\Resources;

use Illuminate\Http\JsonResponse;

class ApiResponse
{
    public static function success($data = null, string $message = 'Success', int $status = 200): JsonResponse
    {
        return response()->json([
            'status' => $status,
            'success' => true,
            'message' => $message,
            'data' => $data
        ], $status);
    }

    public static function error(string $message, int $status = 400, $errors = null): JsonResponse
    {
        return response()->json([
            'status' => $status,
            'success' => false,
            'message' => $message,
            'errors' => $errors ?? []
        ], $status);
    }
}
