#!/bin/bash

# Configuration
DB_HOST="127.0.0.1"
DB_USER="root"
DB_PASS=""
DB_NAME="pmdcrm"
BACKUP_DIR="backups"
TIMESTAMP=$(date +"%Y-%m-%d_%H-%M-%S")
DB_FILE="$BACKUP_DIR/db_backup_$TIMESTAMP.sql"
ZIP_FILE="$BACKUP_DIR/files_backup_$TIMESTAMP.zip"

# Ensure backup directory exists
mkdir -p "$BACKUP_DIR"

echo "Starting backup for PMDCRM..."

# 1. Dump Database
echo "Dumping database '$DB_NAME'..."
# Check if password is set
if [ -z "$DB_PASS" ]; then
    mysqldump -h "$DB_HOST" -u "$DB_USER" "$DB_NAME" > "$DB_FILE"
else
    mysqldump -h "$DB_HOST" -u "$DB_USER" -p"$DB_PASS" "$DB_NAME" > "$DB_FILE"
fi

if [ $? -eq 0 ]; then
    echo "Database backup created: $DB_FILE"
else
    echo "Error backing up database!"
    exit 1
fi

# 2. Zip Files (exclude backups/ and .git/)
echo "Zipping project files..."
zip -r "$ZIP_FILE" . -x "*.git*" -x "backups/*" -x "node_modules/*" -x "*.DS_Store"

if [ $? -eq 0 ]; then
    echo "Files backup created: $ZIP_FILE"
else
    echo "Error zipping files!"
    exit 1
fi

echo "Backup completed successfully!"
echo "Database: $DB_FILE"
echo "Files: $ZIP_FILE"
