# Marquer Backend — Claude guidance

## Database — Users Table

The `users` table is managed by the **Auth service** (separate repo/deployment).
This backend has **no migration for `users`** — do not create one and do not add
foreign key constraints referencing `users` in migrations. All tables store
`user_id` as a plain `unsignedBigInteger` without a DB-level FK.

## Before Committing

Always run tests with Sail before committing:

```bash
./vendor/bin/sail test
```

Do not commit if any tests fail.

## PR Title Convention

All PRs must follow: `<type>: <description>`

| Type | Changelog effect |
|------|-----------------|
| `feat` | Individual bullet in changelog |
| `fix` | Individual bullet in changelog |
| `hotfix` | Individual bullet in changelog |
| `chore` | Collapsed → "Performance improvements and minor bug fixes" |
| `refactor` | Collapsed → "Performance improvements and minor bug fixes" |
| `docs` | Collapsed → "Performance improvements and minor bug fixes" |
| `test` | Collapsed → "Performance improvements and minor bug fixes" |
| `bump` | Collapsed → "Performance improvements and minor bug fixes" |

Examples:
- `feat: add changelog to update dialog`
- `fix: null pointer on app release ingest`
- `bump: 1.0.9`
- `chore: update dependencies`

The `pr-title.yml` workflow enforces this on every PR.

## App Release Changelog Flow

1. CI reads `pubspec.yaml` version from marquer-mobile
2. If `v{version}` git tag already exists → rebuild, no changelog update
3. If tag is new → collects PR titles since previous tag → generates changelog → sends to backend
4. Backend stores changelog in `app_releases.changelog` (nullable text)
5. `AppReleaseResource` returns `changelog` in API response
6. Mobile displays it in the update dialog
