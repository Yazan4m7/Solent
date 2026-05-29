#!/bin/bash

# SIGMA Git-based Deployment Script
# This script commits changes and deploys via Git

set -e  # Exit on error

# Colors for output
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Configuration - UPDATE THESE VALUES
CLOUD_USER="root"
CLOUD_HOST="161.35.46.18"
CLOUD_PATH="/var/www/sigma"

echo -e "${BLUE}========================================${NC}"
echo -e "${BLUE}   SIGMA Git Deployment${NC}"
echo -e "${BLUE}========================================${NC}"
echo ""

# Step 1: Commit changes locally
echo -e "${YELLOW}[1/6] Committing changes...${NC}"
git add .
git commit -m "Deploy: Case history fixes, user dropdown, logger disabled - $(date +%Y-%m-%d)" || echo "No changes to commit"
echo -e "${GREEN}✓ Changes committed${NC}"

# Step 2: Push to repository
echo -e "${YELLOW}[2/6] Pushing to repository...${NC}"
git push origin master || git push origin main
echo -e "${GREEN}✓ Pushed to repository${NC}"

# Step 3: Pull changes on server and deploy
echo -e "${YELLOW}[3/6] Deploying on server...${NC}"
ssh ${CLOUD_USER}@${CLOUD_HOST} << 'ENDSSH'
cd /var/www/sigma

# Put in maintenance mode
echo "Enabling maintenance mode..."
php artisan down

# Pull latest changes
echo "Pulling latest changes..."
git pull origin master || git pull origin main

# Install dependencies
echo "Installing dependencies..."
composer install --no-dev --optimize-autoloader --no-interaction

# Run migrations
echo "Running migrations..."
php artisan migrate --force

# Clear and optimize caches
echo "Clearing caches..."
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear
rm -rf bootstrap/cache/*.php

echo "Optimizing..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Disable maintenance mode
echo "Disabling maintenance mode..."
php artisan up

echo "Deployment complete!"
ENDSSH

echo -e "${GREEN}✓ Deployed successfully${NC}"

echo ""
echo -e "${GREEN}========================================${NC}"
echo -e "${GREEN}   Deployment Completed!${NC}"
echo -e "${GREEN}========================================${NC}"
