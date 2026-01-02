<?php

namespace App\Auth;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Http\Request;
use PHPOpenSourceSaver\JWTAuth\Exceptions\JWTException;
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
            $sub = $payload->get('sub');
            if ($sub === null) {
                return null;
            }
            return $this->user = new JwtUser(
                $sub,
                $payload->toArray()
            );
        } catch (JWTException $e) {
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

    /**
     * Stateless guard does not support credential validation.
     * Authentication is performed via JWT token only.
     */
    public function validate(array $credentials = []): bool
    {
        return false;
    }

    public function setUser(JwtUser|Authenticatable $user): JwtStatelessGuard|static
    {
        if (!$user instanceof JwtUser) {
            throw new \InvalidArgumentException('JwtStatelessGuard only accepts JwtUser instances');
        }
        $this->user = $user;
        return $this;
    }

    public function hasUser(): bool
    {
        return $this->user() !== null;
    }
}
