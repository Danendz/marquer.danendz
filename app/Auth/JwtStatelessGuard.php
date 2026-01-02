<?php

namespace App\Auth;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Http\Request;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;

class JwtStatelessGuard implements Guard
{
    protected ?JwtUser $user = null;

    /**
     * Store the incoming HTTP request for use when extracting and parsing the JWT.
     *
     * @param Request $request The current HTTP request used to read headers, cookies, or other inputs that may contain the JWT.
     */
    public function __construct(protected Request $request)
    {
    }

    /**
     * Retrieve the current authenticated JwtUser from cache or JWT token.
     *
     * If a user was previously resolved, returns the cached instance. Otherwise attempts to parse the request's
     * JWT, constructs a JwtUser using the payload's `sub` claim as the identifier and the full payload as user data,
     * caches it, and returns it. Returns `null` if the token cannot be parsed or any error occurs.
     *
     * @return JwtUser|null The authenticated JwtUser or `null` when no valid token/user is available.
     */
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

    /**
     * Get the identifier of the currently authenticated user.
     *
     * @return string|int|null The authenticated user's identifier, or `null` if no user is authenticated.
     */
    public function id(): string|int|null
    {
        return $this->user()?->getAuthIdentifier();
    }

    /**
     * Determine whether a user is currently authenticated.
     *
     * @return bool `true` if a JwtUser is present, `false` otherwise.
     */
    public function check(): bool
    {
        return (bool)$this->user();
    }

    /**
     * Determine whether there is no authenticated user for the current request.
     *
     * @return bool `true` if no user is authenticated, `false` otherwise.
     */
    public function guest(): bool
    {
        return !$this->check();
    }

    /**
     * Attempt to validate user credentials (not supported by this stateless guard).
     *
     * @param array $credentials Credentials to validate (commonly keys like 'email' and 'password').
     * @return bool `true` if the credentials are valid, `false` otherwise. This guard does not perform credential validation and always returns `false`.
     */
    public function validate(array $credentials = []): bool
    {
        return false;
    }

    /**
     * Set the current authenticated user for the guard.
     *
     * @param JwtUser|Authenticatable $user The user instance to set as the authenticated user.
     * @return JwtStatelessGuard|static The guard instance with the user set, for method chaining.
     */
    public function setUser(JwtUser|Authenticatable $user): JwtStatelessGuard|static
    {
        $this->user = $user;
        return $this;
    }

    /**
     * Determine whether a cached authenticated user exists on the guard.
     *
     * @return bool `true` if an internal JwtUser is set, `false` otherwise.
     */
    public function hasUser(): bool
    {
        return (bool)$this->user;
    }
}