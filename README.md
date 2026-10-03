# Othy Fast Sold

This project runs locally with Docker for the PHP app, Node/Vite frontend, and MySQL database.

## Start the app

### 1) Start Docker containers
```bash
cd /home/youssef-laayadi/OthyFastSold
docker compose -f docker-compose.dev.yml up -d
```
Starts the PHP, Node, and MySQL services.

### 2) Check running containers
```bash
cd /home/youssef-laayadi/OthyFastSold
make ps
```
Shows the status of all running Docker services.

### 3) Run the Laravel app
```bash
cd /home/youssef-laayadi/OthyFastSold
docker compose -f docker-compose.dev.yml exec php php artisan serve --host=0.0.0.0 --port=8000
```
Runs the Laravel development server on:
- http://localhost:8000

### 4) Run the frontend dev server
```bash
cd /home/youssef-laayadi/OthyFastSold
docker compose -f docker-compose.dev.yml exec node npm run dev
```
Runs Vite on:
- http://localhost:5173

### 5) Stop all services
```bash
cd /home/youssef-laayadi/OthyFastSold
docker compose -f docker-compose.dev.yml down
```
Stops and removes the running Docker containers.

## Common commands

### Run migrations
```bash
cd /home/youssef-laayadi/OthyFastSold
docker compose -f docker-compose.dev.yml exec php php artisan migrate
```
Creates the tables defined in the migration files.

### Run all seeders
```bash
cd /home/youssef-laayadi/OthyFastSold
docker compose -f docker-compose.dev.yml exec php php artisan db:seed
```
Runs all database seeders.

### Run a single seeder
```bash
cd /home/youssef-laayadi/OthyFastSold
docker compose -f docker-compose.dev.yml exec php php artisan db:seed --class=UserSeeder
```
Runs only the `UserSeeder`.

### Refresh database with fresh schema and seed data
```bash
cd /home/youssef-laayadi/OthyFastSold
docker compose -f docker-compose.dev.yml exec php php artisan migrate:fresh --seed
```
Drops all tables, recreates them, and seeds data again.

### Rebuild container image
```bash
cd /home/youssef-laayadi/OthyFastSold
docker compose -f docker-compose.dev.yml up -d --build
```
Rebuilds the PHP container after configuration changes.

## Makefile shortcuts
```bash
cd /home/youssef-laayadi/OthyFastSold
make up
make ps
make down
```

- `make up`: starts the PHP and Node containers
- `make ps`: lists running containers
- `make down`: stops containers

## Login

Default seeded user:
- Email: `yousseflaayadiasape2@gmail.com`

If you want to set a new password manually:
```bash
cd /home/youssef-laayadi/OthyFastSold
docker compose -f docker-compose.dev.yml exec php php artisan tinker
```
Then run:
```php
$user = App\Models\User::first();
$user->update(['password' => bcrypt('your-password-here')]);
```

## Environment summary

- App URL: `http://localhost:8000`
- Vite URL: `http://localhost:5173`
- Database: MySQL in Docker
- Container file: `docker-compose.dev.yml`
