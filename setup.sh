#!/bin/bash

echo "🏗️ TNT Construction System - Professional Setup"
echo "================================================"

# Install dependencies
echo "📦 Installing PHP dependencies..."
composer install --no-dev --optimize-autoloader

echo "📦 Installing JS dependencies..."
npm install && npm run build

# Setup storage
echo "📁 Setting up storage..."
php artisan storage:link

# Run migrations and seed
echo "🗄️ Setting up database..."
php artisan migrate:fresh --seed

# Clear cache
echo "🧹 Optimizing..."
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Set permissions
chmod -R 775 storage bootstrap/cache
chmod -R 775 public/storage

echo ""
echo "✅ Setup Complete!"
echo ""
echo "📊 Access: http://localhost:8000"
echo "👤 Admin: admin@tntconstruction.com / Admin@123"
echo "👤 Manager: manager@tntconstruction.com / password"
echo ""
echo "🚀 Starting server..."
php artisan serve --port=8000
