# CLAUDE.md — marquer-backend

Laravel 12 REST API. Notes, tasks, study sessions, wishes, app releases.
Auth via shared JWT with Auth service. HTTP POST for analytics. S3/MinIO for APK uploads.
Deployed on Railway.com at `api.danendz.com/api/marquer/`.

## Commands

```bash
composer setup   # install + migrations + npm build (first time)
composer dev     # server + queue worker + Vite (all at once)
composer test    # run tests
./vendor/bin/sail up -d   # start Docker (PostgreSQL + MinIO)
./vendor/bin/sail test    # run tests inside Docker
./vendor/bin/sail artisan migrate
```

Always run `./vendor/bin/sail test` before committing. Do not commit if tests fail.

**Before running any tests**, always restart Sail with a clean state:
```bash
./vendor/bin/sail down --remove-orphans && ./vendor/bin/sail up -d
```

## Sail Troubleshooting

If tests fail in unexpected ways or code changes aren't picked up, Sail may be stale. Always do a clean restart before debugging:

```bash
./vendor/bin/sail down --remove-orphans && ./vendor/bin/sail up -d
```

Only start debugging the actual failure after confirming Sail is running fresh code.

## Architecture

`Controller → FormRequest → Service → Model → DB`

- Controllers in `Private/` (auth required), `Public/` (open), `Internal/` (GitHub OIDC)
- **Controllers never contain logic, never use try/catch** — let exceptions bubble up
- All responses via `ApiResponse::success()` / `ApiResponse::error()` (`app/Http/Resources/ApiResponse.php`)
- Services hold all business logic (`app/Services/`)

## Auth

JWT token from Auth service. Same `JWT_SECRET` shared. No `users` table here, no login/register.
`auth()->id()` gives the current user's ID. Protected routes use `middleware('auth:api')`.

## Database

- **No `users` table**, no FK constraints to users
- `$table->unsignedBigInteger('user_id');` — plain column, no `foreignId()`
- PostgreSQL only — never SQLite

## Model Conventions

Always scope route binding to the auth user (prevents cross-user access):
```php
public function resolveRouteBinding($value, $field = null): ?self
{
    return $this->where('id', $value)->where('user_id', auth()->id())->firstOrFail();
}
```
Always use explicit `$fillable`, never `$guarded = []`.

## Global Error Handler (`bootstrap/app.php`)

| Exception | Status |
|-----------|--------|
| `ValidationException` | 422 |
| `TokenExpiredException` / `TokenInvalidException` / `JWTException` | 401 |
| `UnauthorizedHttpException` / `AuthenticationException` | 401 |
| `ModelNotFoundException` / `NotFoundHttpException` | 404 |
| `HttpExceptionInterface` (e.g. `abort(403)`) | dynamic |
| Fallback | 500 |

Dev environments include full exception details in the response.

## Analytics

`AnalyticsPublisherService` — publish analytics events via HTTP POST from service methods, never from controllers.
`ANALYTICS_ENABLED=false` skips publishing silently (default in dev).

## PR Title Convention

`<type>: <description>` — enforced by `pr-title.yml`

| Type | Effect |
|------|--------|
| `feat`, `fix`, `hotfix` | Individual changelog entry |
| `chore`, `refactor`, `test`, `docs`, `bump` | Collapsed |

## Adding a New Feature

Use `/new-feature` to start — it will ask clarifying questions before any code is written.
