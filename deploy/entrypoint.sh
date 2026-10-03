#!/bin/sh
set -e

export PORT="${PORT:-8080}"
envsubst '${PORT}' < /etc/nginx/sites-available/default > /tmp/nginx-default.conf
cat /tmp/nginx-default.conf > /etc/nginx/sites-available/default

cd /var/www/html

# A mounted volume hides the folders created at build time
mkdir -p storage/app/public storage/framework/cache/data storage/framework/sessions storage/framework/views storage/logs bootstrap/cache

php artisan config:cache
php artisan view:cache
php artisan storage:link --force

# Wait for MySQL, then migrate
tries=0
until php artisan migrate --force; do
  tries=$((tries + 1))
  if [ "$tries" -ge 15 ]; then
    echo "Migration failed after $tries attempts"
    exit 1
  fi
  echo "Database not ready (attempt $tries). Retrying in 4s..."
  sleep 4
done

chown -R www-data:www-data storage bootstrap/cache

exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
