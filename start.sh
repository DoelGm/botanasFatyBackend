#!/bin/sh

echo "Running migrations..."
php artisan migrate:fresh --force

echo "Starting Laravel server..."
php artisan serve --host=0.0.0.0 --port=${PORT}
