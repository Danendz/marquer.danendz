<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;

class UserController extends Controller
{
    public function me(Request $request)
    {
        $userId  = $request->attributes->get('user_id');
        $payload = $request->attributes->get('jwt_payload');
        return response()->json([
            'user_id'   => $userId,
            'payload' => $payload
        ]);

    }
}
