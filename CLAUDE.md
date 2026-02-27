# Marquer Backend — Claude guidance

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
