# Local development (keeps deployment unchanged)

This project ships separate dev assets so you can run locally without touching deployment files.

Files added:

Quick start (from project root):

1. Build services:

```bash
docker compose -f docker-compose.dev.yml build
```

2. Install PHP dependencies (runs inside `php` container):

```bash
docker compose -f docker-compose.dev.yml run --rm php composer install --no-interaction
```

3. Create `.env` and app key (if `.env.example` exists):

```bash
cp .env.example .env
docker compose -f docker-compose.dev.yml run --rm php php artisan key:generate
```

4. Use SQLite for quick setup (recommended):

```bash
mkdir -p database
touch database/database.sqlite
# update .env DB_CONNECTION=sqlite and DB_DATABASE absolute path if needed
docker compose -f docker-compose.dev.yml run --rm php php artisan migrate --force
```

5. Link storage:

```bash
docker compose -f docker-compose.dev.yml run --rm php php artisan storage:link
```

6. Install JS deps and run Vite dev server (in `node` container):

```bash
docker compose -f docker-compose.dev.yml run --rm node npm install
docker compose -f docker-compose.dev.yml run --rm -p 5173:5173 node npm run dev
```

7. Serve Laravel (from `php` container):

```bash
docker compose -f docker-compose.dev.yml up -d php
docker compose -f docker-compose.dev.yml exec php php artisan serve --host=0.0.0.0 --port=8000
```

Open http://localhost:8000 and the Vite client at http://localhost:5173

Notes:

MySQL (future features)

I added a MySQL dev service that is intentionally separated from the app's current DB. It will not be used by the app until code is updated to use the `future_mysql` connection.

- Service name: `mysql` (image: `mysql:8.1`)
- Exposed host port: `33060` mapped to container `3306` (prevents conflicts with host MySQL)
- Default database/user/password created: `future_app` / `future_user` / `future_pass`
- To start MySQL only:

```bash
make mysql-up
```

- To open a MySQL client (container must be running):

```bash
make mysql-client
```

Config changes:

- A new database connection `future_mysql` was added to `config/database.php`. It reads `FUTURE_DB_*` env vars and will not affect the current `DB_CONNECTION`.
- `.env` now includes `FUTURE_DB_*` variables pre-filled for local dev.

When you're ready to integrate, update `config/database.php` usage or set `DB_CONNECTION` to `future_mysql` (not recommended until features are ready).

Auth & Migrations added

- I added a minimal `App\Models\User` model and migrations for `users`, `sessions`, and `password_resets` under `database/migrations` so the project has the standard tables ready for auth and session-db use.
- To run the migrations inside the dev php container:

```bash
make composer-install
make php-migrate
```

- If you want to use database-backed sessions later, set `SESSION_DRIVER=database` in `.env` and run the migrations (`make php-migrate`).
