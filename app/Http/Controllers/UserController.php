<?php

namespace App\Http\Controllers;

use App\Http\Resources\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function me(Request $request): JsonResponse
    {
        $userId = $this->get_user_id($request);
        return ApiResponse::success([
            'user_id' => $userId
        ]);
    }
}
