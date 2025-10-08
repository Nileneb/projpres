#!/bin/bash
echo "===== Challenge Roulette Setup Script ====="
echo

echo "Copying .env.example to .env..."
cp .env.example .env

echo
echo "Setting timezone in .env file..."
echo "APP_TIMEZONE=Europe/Berlin" >> .env

echo
echo "Generating application key..."
php artisan key:generate

echo
echo "Running migrations..."
php artisan migrate

echo
echo "Seeding test data..."
php artisan db:seed --class=TestDataSeeder

echo
echo "Clearing configuration cache..."
php artisan config:cache

echo
echo "Setup completed successfully!"
echo
echo "Run 'php artisan serve' to start the development server."
echo "Run 'php artisan schedule:work' to test the scheduler locally."
echo
