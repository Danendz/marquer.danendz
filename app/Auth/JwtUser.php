<?php

namespace App\Auth;

use Illuminate\Contracts\Auth\Authenticatable;

class JwtUser implements Authenticatable
{
    public function __construct(
        public readonly string|int $id,
        public readonly array      $claims = [],
    )
    {
    }

    public function getAuthIdentifierName(): string
    {
        return 'id';
    }

    public function getAuthIdentifier(): string|int
    {
        return $this->id;
    }

    public function getAuthPassword(): ?string
    {
        return null;
    }

    public function getRememberToken(): ?string
    {
        return null;
    }

    public function setRememberToken($value): void
    {
    }

    public function getRememberTokenName(): string
    {
        return '';
    }

    public function getAuthPasswordName(): null
    {
        return null;
    }
}
