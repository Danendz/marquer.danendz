# Marquer Backend

Laravel 12 REST API for the Marquer service. Handles notes, tasks, study sessions, calendar plans, wishes, and app releases. Deployed on Railway.com at `https://api.danendz.com/api/marquer/`.

## Tech Stack

| | |
|---|---|
| **Language** | PHP ^8.5 |
| **Framework** | Laravel 12 |
| **Database** | PostgreSQL 18 |
| **File storage** | MinIO (S3-compatible) |
| **Message broker** | RabbitMQ → Analytics service |
| **Auth** | JWT (shared secret with Auth service) |
| **Testing** | Pest |

## Architecture

```
Controller → FormRequest → Service → Model → DB
```

- `Private/` controllers — require JWT auth
- `Public/` controllers — open endpoints
- `Internal/` controllers — GitHub OIDC (CI/CD only)
- Controllers contain no logic and no try/catch — exceptions bubble up to the global error handler
- All responses use `ApiResponse::success()` / `ApiResponse::error()`

## API Endpoints

**Private** (require `Authorization: Bearer <token>`):

| Resource | Endpoints |
|---|---|
| Notes | `GET/POST /notes`, `GET/PUT/DELETE /notes/{id}` |
| Tasks | `GET/POST /tasks`, `PUT/DELETE /tasks/{id}` |
| Task Folders | `GET/POST /task-folders`, `PUT/DELETE /task-folders/{id}` |
| Task Categories | `POST /task-categories`, `PUT/DELETE /task-categories/{id}` |
| Calendar | `GET /calendar/overview`, `GET /calendar/week` |
| Countdowns | `GET/POST /calendar/countdowns`, `PUT/DELETE /calendar/countdowns/{id}` |
| Plans | `GET/POST /calendar/plans`, `GET/PUT/DELETE /calendar/plans/{id}`, `GET /calendar/plans/for-date` |
| Plan tasks | `POST /calendar/plan-tasks/{id}/toggle` |
| Study subjects | `GET/POST /study/subjects`, `PUT/DELETE /study/subjects/{id}` |
| Study sessions | `GET/POST /study/sessions`, `PUT /study/sessions/{id}`, `POST /study/sessions/{id}/complete|cancel`, `GET /study/sessions/stats` |
| Study settings | `GET/PUT /study/settings` |

**Public:**

| Endpoint | Description |
|---|---|
| `GET /marquer/app/latest` | Latest app release info |
| `GET /marquer/app/latest/download` | Download latest APK |
| `POST /marquer/wish` | Submit a wish |
| `GET /marquer/wish/my` | Get user's own wishes |
| `GET /marquer/wish/random` | Get a random wish |

**Internal** (GitHub OIDC):

| Endpoint | Description |
|---|---|
| `POST /marquer/internal/app-releases` | Register new APK release from CI |

## Getting Started

```bash
# Start Docker services (PostgreSQL + MinIO)
./vendor/bin/sail up -d

# First-time setup: install deps + run migrations
composer setup

# Start dev server + queue worker + Vite
composer dev
```

## Testing

```bash
# Always restart Sail before running tests to ensure fresh state
./vendor/bin/sail down --remove-orphans && ./vendor/bin/sail up -d

# Run all tests
./vendor/bin/sail test

# Run a specific test
./vendor/bin/sail test --filter=TaskTest
```

## Environment Variables

Key variables (see `.env.example` for the full list):

```env
APP_PORT=8081
DB_CONNECTION=pgsql
DB_HOST=pgsql
DB_DATABASE=marquer
DB_USERNAME=sail
DB_PASSWORD=password

JWT_SECRET=                  # must match Auth service

AWS_ACCESS_KEY_ID=
AWS_SECRET_ACCESS_KEY=
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=
AWS_URL=
AWS_ENDPOINT=                # MinIO endpoint

RABBITMQ_ENABLED=false       # set to true to publish analytics events
RABBITMQ_HOST=
RABBITMQ_PORT=5672
RABBITMQ_USER=
RABBITMQ_PASSWORD=
RABBITMQ_QUEUE=analytics
```

## Deployment

Deployed on Railway.com. Base URL: `https://api.danendz.com/api/marquer/`

Caddy reverse proxy handles routing and rate limiting. JWT tokens issued by the Auth service are validated here using the shared `JWT_SECRET`.
