#!/usr/bin/env bash
set -euo pipefail

BACKUP_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)/backups"
APP_DIR="/var/www/html/ssb-app"
DB_NAME="ssb_internal"
DB_USER="ssb_app"
DB_PASS="SsbStrongPass2026"
TIMESTAMP=$(date +"%Y%m%d_%H%M%S")

mkdir -p "$BACKUP_DIR"

if [[ ! -d "$APP_DIR" ]]; then
    echo "[ERROR] Deployed directory $APP_DIR does not exist." >&2
    exit 1
fi

# Database dump (compressed)
mysqldump --single-transaction -u "$DB_USER" -p"$DB_PASS" --no-tablespaces "$DB_NAME" | gzip > "$BACKUP_DIR/db_${DB_NAME}_${TIMESTAMP}.sql.gz"

# Application files archive
tar -czf "$BACKUP_DIR/app_files_${TIMESTAMP}.tar.gz" -C /var/www/html ssb-app

echo "[SUCCESS] Backups created in $BACKUP_DIR:"
ls -lh "$BACKUP_DIR"/*"$TIMESTAMP"*
