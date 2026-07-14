#!/usr/bin/env bash
# exit on error
set -o errexit

echo "Installing PHP dependencies (Composer)..."
composer install --no-dev --optimize-autoloader

echo "Installing Node dependencies..."
npm install

echo "Building assets (Vite)..."
npm run build

echo "Running migrations..."
php artisan migrate --force

echo "Running seeders..."
php artisan db:seed --force
