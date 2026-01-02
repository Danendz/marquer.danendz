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

    /**
     * Create a new JwtStatelessGuard.
     *
     * Stores the incoming HTTP request for use when resolving the JWT from the current request.
     *
     * @param Request $request The current HTTP request that may contain the JWT (headers, cookies, etc.).
     */
    public function __construct(protected Request $request)
    {
    }

    /**
     * Retrieve the authenticated JwtUser from the current request's JWT, caching the result.
     *
     * Attempts to parse the JWT from the request and, if the token contains a `sub` claim,
     * constructs and caches a JwtUser initialized with the subject and the full token payload.
     *
     * @return JwtUser|null JwtUser constructed from the token's `sub` claim and payload, `null` if no valid token, the `sub` claim is missing, or token parsing fails.
     */
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

    /**
     * Get the authenticated user's identifier.
     *
     * @return string|int|null The user's authentication identifier, or null if no user is authenticated.
     */
    public function id(): string|int|null
    {
        return $this->user()?->getAuthIdentifier();
    }

    / **
     * Determine whether a user is authenticated.
     *
     * @return bool `true` if a user is present, `false` otherwise.
     */
    public function check(): bool
    {
        return (bool)$this->user();
    }

    / **
     * Determine whether the current request is unauthenticated.
     *
     * @return bool `true` if there is no authenticated user, `false` otherwise.
     */
    public function guest(): bool
    {
        return !$this->check();
    }

    /**
     * Indicates that credential-based validation is unsupported for this stateless JWT guard.
     *
     * @param array $credentials Credentials passed to this method are ignored.
     * @return bool Always `false`.
     */
    public function validate(array $credentials = []): bool
    {
        return false;
    }

    /**
     * Set and cache the authenticated user for this guard.
     *
     * @param JwtUser|Authenticatable $user The user to set; must be an instance of `JwtUser`.
     * @return JwtStatelessGuard|static The current guard instance.
     * @throws \InvalidArgumentException If the provided `$user` is not an instance of `JwtUser`.
     */
    public function setUser(JwtUser|Authenticatable $user): JwtStatelessGuard|static
    {
        if (!$user instanceof JwtUser) {
            throw new \InvalidArgumentException('JwtStatelessGuard only accepts JwtUser instances');
        }
        $this->user = $user;
        return $this;
    }

    /**
     * Checks whether an authenticated user is available from the guard.
     *
     * @return bool `true` if a user is present, `false` otherwise.
     */
    public function hasUser(): bool
    {
        return $this->user() !== null;
    }
}