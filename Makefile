SHELL := /bin/bash
.PHONY: build composer-install php-key php-migrate storage-link npm-install npm-dev up down logs-php logs-node ps

build:
	docker compose -f docker-compose.dev.yml build

composer-install:
	docker compose -f docker-compose.dev.yml run --rm php composer install --no-interaction

php-key:
	docker compose -f docker-compose.dev.yml run --rm php bash -lc "[ -f .env.example ] && cp .env.example .env || [ -f .env ] || touch .env; php artisan key:generate"

php-migrate:
	mkdir -p database
	touch database/database.sqlite
	docker compose -f docker-compose.dev.yml run --rm php php artisan migrate --force

storage-link:
	docker compose -f docker-compose.dev.yml run --rm php php artisan storage:link

npm-install:
	docker compose -f docker-compose.dev.yml run --rm node npm install

npm-dev:
	docker compose -f docker-compose.dev.yml run -d --service-ports node npm run dev

up:
	docker compose -f docker-compose.dev.yml up -d --build php node

down:
	docker compose -f docker-compose.dev.yml down

logs-php:
	docker compose -f docker-compose.dev.yml logs -f php

logs-node:
	docker compose -f docker-compose.dev.yml logs -f node

ps:
	docker compose -f docker-compose.dev.yml ps

mysql-up:
	docker compose -f docker-compose.dev.yml up -d mysql

mysql-client:
	docker compose -f docker-compose.dev.yml exec mysql mysql -u${MYSQL_USER:-future_user} -p${MYSQL_PASSWORD:-future_pass} ${MYSQL_DATABASE:-future_app}
