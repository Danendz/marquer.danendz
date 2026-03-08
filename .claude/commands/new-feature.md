Before writing any code, ask the user these questions one by one (in plain language — they may not know technical terms):

1. **Auth** — "Should this endpoint require the user to be logged in, or can anyone access it without an account?"

2. **Ownership** — "Should each user only see their own data (private), or should all users see the same shared data (public)?"

3. **Fields** — "What information should [resource] store? For example: a name, a description, a date, a number, a yes/no flag? List everything."

4. **Operations** — "What should the user be able to do with [resource]? See a list? See one? Create? Edit? Delete? (Pick only what you need.)"

5. **Relationships** — "Does [resource] belong to something else? For example, a task belongs to a task folder. Does [resource] belong to anything?"

6. **Validation** — "Are any fields required? Any limits? For example: name is required, max 255 characters; date must be in the future."

Once answered, present a short implementation plan (migration → model → service → controller → request → route) and wait for approval before writing any code.

Then implement following these conventions:
- Controller folder: `Private/` (auth), `Public/` (open), `Internal/` (GitHub OIDC)
- `$table->unsignedBigInteger('user_id')` — no `foreignId()`, no FK constraint
- `resolveRouteBinding` scoped to `auth()->id()`
- No try/catch in controllers or services — let exceptions bubble
- `ApiResponse::success()` / `ApiResponse::error()` for all responses
- Route inside `middleware('auth:api')` → `prefix('marquer')` group, with `.whereNumber()` on ID params
- Run `./vendor/bin/sail test` at the end and fix any failures
