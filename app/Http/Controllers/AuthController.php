<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Resources\AuthResponseResource;
use App\Services\AuthService;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function __construct(private readonly AuthService $auth) {}

    public function register(RegisterRequest $request)
    {
        $data = $request->validated();

        $result = $this->auth->register(
            $data['name'],
            $data['email'],
            $data['password'],
            $data['device_name'] ?? 'api',
        );

        return (new AuthResponseResource($result))->response()->setStatusCode(201);
    }

    public function login(LoginRequest $request)
    {
        $data = $request->validated();

        $result = $this->auth->login(
            $data['email'],
            $data['password'],
            $data['device_name'] ?? 'api',
        );

        return new AuthResponseResource($result);
    }

    public function logout(Request $request)
    {
        $this->auth->logout($request->user());

        return response()->json(['message' => 'Logged out']);
    }
}
