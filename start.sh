#!/bin/bash
echo "🏗️ Starting TNT Construction System..."
php artisan config:clear
php artisan cache:clear
php artisan serve --port=8000
