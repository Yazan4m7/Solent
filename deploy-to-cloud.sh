#!/bin/bash

# SIGMA Cloud Deployment Script
# This script deploys changes to the cloud server

set -e  # Exit on error

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Configuration - UPDATE THESE VALUES
CLOUD_USER="root"  # Change to your SSH user
CLOUD_HOST="161.35.46.18"  # Change to your server IP or domain
CLOUD_PATH="/var/www/sigma"  # Change to your application path on server
SSH_KEY=""  # Optional: path to SSH key file

echo -e "${BLUE}========================================${NC}"
echo -e "${BLUE}   SIGMA Cloud Deployment Script${NC}"
echo -e "${BLUE}========================================${NC}"
echo ""

# Function to run commands on remote server
run_remote() {
    if [ -n "$SSH_KEY" ]; then
        ssh -i "$SSH_KEY" "${CLOUD_USER}@${CLOUD_HOST}" "$1"
    else
        ssh "${CLOUD_USER}@${CLOUD_HOST}" "$1"
    fi
}

# Function to copy files to remote server
copy_to_remote() {
    if [ -n "$SSH_KEY" ]; then
        rsync -avz -e "ssh -i $SSH_KEY" --exclude-from='.gitignore' --exclude '.git' "$1" "${CLOUD_USER}@${CLOUD_HOST}:$2"
    else
        rsync -avz --exclude-from='.gitignore' --exclude '.git' "$1" "${CLOUD_USER}@${CLOUD_HOST}:$2"
    fi
}

# Step 1: Check if we can connect to the server
echo -e "${YELLOW}[1/8] Testing connection to cloud server...${NC}"
if run_remote "echo 'Connection successful'"; then
    echo -e "${GREEN}✓ Connected to ${CLOUD_HOST}${NC}"
else
    echo -e "${RED}✗ Failed to connect to server${NC}"
    exit 1
fi

# Step 2: Create backup on server
echo -e "${YELLOW}[2/8] Creating backup on server...${NC}"
BACKUP_NAME="sigma_backup_$(date +%Y%m%d_%H%M%S)"
run_remote "cd ${CLOUD_PATH} && mkdir -p ../backups && tar -czf ../backups/${BACKUP_NAME}.tar.gz ."
echo -e "${GREEN}✓ Backup created: ${BACKUP_NAME}.tar.gz${NC}"

# Step 3: Put application in maintenance mode
echo -e "${YELLOW}[3/8] Enabling maintenance mode...${NC}"
run_remote "cd ${CLOUD_PATH} && php artisan down --message='Deploying updates, please wait...' --retry=60"
echo -e "${GREEN}✓ Maintenance mode enabled${NC}"

# Step 4: Sync files to server
echo -e "${YELLOW}[4/8] Syncing files to server...${NC}"
copy_to_remote "./" "${CLOUD_PATH}/"
echo -e "${GREEN}✓ Files synced${NC}"

# Step 5: Install/update dependencies
echo -e "${YELLOW}[5/8] Installing dependencies...${NC}"
run_remote "cd ${CLOUD_PATH} && composer install --no-dev --optimize-autoloader --no-interaction"
echo -e "${GREEN}✓ Dependencies installed${NC}"

# Step 6: Run migrations
echo -e "${YELLOW}[6/8] Running database migrations...${NC}"
run_remote "cd ${CLOUD_PATH} && php artisan migrate --force"
echo -e "${GREEN}✓ Migrations completed${NC}"

# Step 7: Clear and optimize caches
echo -e "${YELLOW}[7/8] Clearing and optimizing caches...${NC}"
run_remote "cd ${CLOUD_PATH} && php artisan config:clear && php artisan cache:clear && php artisan view:clear && php artisan route:clear"
run_remote "cd ${CLOUD_PATH} && php artisan config:cache && php artisan route:cache && php artisan view:cache"
run_remote "cd ${CLOUD_PATH} && rm -rf bootstrap/cache/*.php"
echo -e "${GREEN}✓ Caches cleared and optimized${NC}"

# Step 8: Disable maintenance mode
echo -e "${YELLOW}[8/8] Disabling maintenance mode...${NC}"
run_remote "cd ${CLOUD_PATH} && php artisan up"
echo -e "${GREEN}✓ Maintenance mode disabled${NC}"

# Final success message
echo ""
echo -e "${GREEN}========================================${NC}"
echo -e "${GREEN}   Deployment Completed Successfully!${NC}"
echo -e "${GREEN}========================================${NC}"
echo ""
echo -e "${BLUE}Deployed changes:${NC}"
echo -e "  • Fixed case history logging"
echo -e "  • Enhanced user dropdown menu"
echo -e "  • Disabled legacy Laravel logger"
echo ""
echo -e "${BLUE}Backup location:${NC} ../backups/${BACKUP_NAME}.tar.gz"
echo ""
