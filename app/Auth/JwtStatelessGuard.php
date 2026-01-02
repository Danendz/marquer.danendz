<?php

namespace App\Auth;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Http\Request;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;

class JwtStatelessGuard implements Guard
{
    protected ?JwtUser $user = null;

    public function __construct(protected Request $request)
    {
    }

    public function user(): ?JwtUser
    {
        if ($this->user) {
            return $this->user;
        }

        try {
            $payload = JWTAuth::parseToken()->payload();
            return $this->user = new JwtUser(
                $payload->get('sub'),
                $payload->toArray()
            );
        } catch (\Throwable $e) {
            return null;
        }
    }

    public function id(): string|int|null
    {
        return $this->user()?->getAuthIdentifier();
    }

    public function check(): bool
    {
        return (bool)$this->user();
    }

    public function guest(): bool
    {
        return !$this->check();
    }

    public function validate(array $credentials = []): bool
    {
        return false;
    }

    public function setUser(JwtUser|Authenticatable $user): JwtStatelessGuard|static
    {
        $this->user = $user;
        return $this;
    }

    public function hasUser(): bool
    {
        return (bool)$this->user;
    }
}
