#!/bin/bash

echo "🏗️ TNT Construction System - Complete Setup"
echo "============================================"

# Run migrations
echo "📊 Running migrations..."
php artisan migrate:fresh

# Seed database
echo "🌱 Seeding database..."
php artisan db:seed --class=CompleteSystemSeeder

# Create storage link
echo "🔗 Creating storage link..."
php artisan storage:link

# Clear all cache
echo "🧹 Clearing cache..."
php artisan optimize:clear

# Start Reverb server in background
echo "🔊 Starting Reverb server..."
php artisan reverb:start --port=8080 &

# Start queue worker
echo "⚙️ Starting queue worker..."
php artisan queue:work --daemon &

echo ""
echo "✅ Setup Complete!"
echo ""
echo "📊 System URLs:"
echo "   Web: http://localhost:8000"
echo "   API: http://localhost:8000/api"
echo "   Reverb: http://localhost:8080"
echo ""
echo "👤 Login Credentials:"
echo "   Admin: admin@tntconstruction.com / Admin@123"
echo "   Manager: manager@tntconstruction.com / password"
echo ""
echo "🚀 Starting server..."
php artisan serve --port=8000
