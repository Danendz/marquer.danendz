<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

class JwtAuthMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        try {
            $payload = JWTAuth::parseToken()->payload();
            $request->attributes->set('user_id', $payload->get('sub'));
            $request->attributes->set('jwt_payload', $payload->toArray());
        } catch (\Exception $e) {
            throw new UnauthorizedHttpException('Bearer', 'Invalid or missing token');
        }
        return $next($request);
    }
}
