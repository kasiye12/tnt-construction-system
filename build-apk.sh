#!/bin/bash

echo "📱 Building TNT Construction APK..."
echo "======================================"

# Build web assets
echo "📦 Building web assets..."
npm run build

# Copy to Capacitor
echo "📁 Syncing with Capacitor..."
npx cap sync android

# Build APK
echo "🔨 Building APK..."
cd android
./gradlew assembleDebug

# Copy APK
echo "📋 Copying APK..."
cp app/build/outputs/apk/debug/app-debug.apk ../tnt-construction.apk

echo ""
echo "✅ APK Built Successfully!"
echo "📱 APK Location: tnt-construction.apk"
echo "📱 Install on your Android phone and connect to your server!"
