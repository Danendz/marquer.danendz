<?php

namespace App\Auth;

use Illuminate\Contracts\Auth\Authenticatable;

class JwtUser implements Authenticatable
{
    /**
     * Create a JwtUser with a unique identifier and optional JWT claims.
     *
     * @param string|int $id   The user's unique identifier used for authentication.
     * @param array      $claims An associative array of JWT claims (e.g., "email", "roles"); defaults to an empty array.
     */
    public function __construct(
        public readonly string|int $id,
        public readonly array      $claims = [],
    )
    {
    }

    /**
     * Get the name of the unique identifier field used for authentication.
     *
     * @return string The identifier field name ('id').
     */
    public function getAuthIdentifierName(): string
    {
        return 'id';
    }

    /**
     * Get the user's authentication identifier.
     *
     * @return string|int The identifier value used for authentication.
     */
    public function getAuthIdentifier(): string|int
    {
        return $this->id;
    }

    /**
     * Indicate that this user has no password stored.
     *
     * @return string|null The user's password, or null if none is associated.
     */
    public function getAuthPassword(): ?string
    {
        return null;
    }

    /**
     * Retrieve the user's remember token.
     *
     * @return string|null `null` because JwtUser does not store a remember token.
     */
    public function getRememberToken(): ?string
    {
        return null;
    }

    /**
     * No-op implementation required by the Authenticatable contract; does not store or persist the remember token.
     *
     * @param string|null $value The remember token value to set (ignored).
     */
    public function setRememberToken($value): void
    {
    }

    /**
     * Provides the name of the attribute used to store the remember-me token.
     *
     * @return string The remember token attribute name, or an empty string if remember tokens are not supported.
     */
    public function getRememberTokenName(): string
    {
        return '';
    }

    /**
     * Indicates that this user model does not have a password field name.
     *
     * @return null Always `null` to signal that no password field name is defined.
     */
    public function getAuthPasswordName(): null
    {
        return null;
    }
}