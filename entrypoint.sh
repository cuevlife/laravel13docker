#!/bin/sh
set -e

echo '🚀 Checking/Installing dependencies...'
if [ ! -f "vendor/autoload.php" ]; then
  composer install --no-interaction --no-progress --optimize-autoloader
fi

echo '🧹 Clearing Laravel cache...'
if [ -f "artisan" ]; then
  php artisan config:clear
  php artisan route:clear
  php artisan view:clear
fi

echo '✨ Starting App...'
exec "$@"