<?php

namespace App\Auth;

use Illuminate\Contracts\Auth\Authenticatable;

class JwtUser implements Authenticatable
{
    /**
     * Create a JwtUser with an identifier and optional JWT claims.
     *
     * @param string|int $id The user's unique identifier used by the authentication system.
     * @param array $claims Optional JWT claims associated with the user (e.g., roles, scopes, custom claims).
     */
    public function __construct(
        public readonly string|int $id,
        public readonly array      $claims = [],
    )
    {
    }

    /**
     * Get the attribute name used as the user's unique authentication identifier.
     *
     * @return string The identifier attribute name, always 'id'.
     */
    public function getAuthIdentifierName(): string
    {
        return 'id';
    }

    /**
     * Retrieve the user's authentication identifier.
     *
     * @return string|int The user's unique identifier used by the authentication system.
     */
    public function getAuthIdentifier(): string|int
    {
        return $this->id;
    }

    /**
     * Retrieve the user's stored password hash, if present.
     *
     * @return string|null The password hash, or `null` when this user representation does not store a password.
     */
    public function getAuthPassword(): ?string
    {
        return null;
    }

    /**
     * Retrieve the current "remember me" token for the user.
     *
     * @return string|null The "remember me" token value, or `null` when not available.
     */
    public function getRememberToken(): ?string
    {
        return null;
    }

    /**
     * Intentionally does nothing because JWT-based users do not persist a "remember me" token.
     *
     * @param mixed $value Ignored.
     */
    public function setRememberToken($value): void
    {
    }

    /**
     * Get the name of the remember-token attribute for the user.
     *
     * @return string The attribute name used for the "remember me" token; an empty string indicates remember-token is not supported.
     */
    public function getRememberTokenName(): string
    {
        return '';
    }

    /**
     * Provide the name of the password field for this user.
     *
     * @return null Always `null` because this user representation does not expose a password field.
     */
    public function getAuthPasswordName(): null
    {
        return null;
    }
}