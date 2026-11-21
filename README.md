# SSB Digital — Junior DevOps / Web Operations Take-Home Assignment

## Overview & Environment
- **Candidate:** Rushil Panchal
- **Environment:** Ubuntu (WSL2 on Windows 11), Apache 2.4, PHP 8.5, MySQL 8.0, phpMyAdmin 5.2.3
- **Time Spent:** ~3.5 hours

## Repository Layout
- `app/` — Core PHP task tracker (`index.php`, `health.php`, `config.example.php`)
- `database/` — SQL schema and seed data (`schema.sql`)
- `scripts/` — Automation scripts (`deploy.sh`, `backup.sh`, `healthcheck.sh`)
- `docs/` — SQL query explanations (`queries.md`) and troubleshooting incident report (`incident_note.md`)
- `screenshots/` — Verification screenshots for setup, phpMyAdmin, rsync, backup/restore, and troubleshooting
- `.github/workflows/` — Optional CI pipeline (`php-lint.yml`)

## 1. Setup & Verification
~~~bash
sudo apt update && sudo apt install -y apache2 mysql-server php libapache2-mod-php php-mysql phpmyadmin
sudo systemctl status apache2 mysql
sudo ss -tulpn | grep -E ':(80|3306)'
~~~
- **Apache Logs:** `/var/log/apache2/access.log`, `/var/log/apache2/error.log`
- **MySQL Logs:** `/var/log/mysql/error.log`

## 2. Database & Security Setup
~~~bash
sudo mysql -e "CREATE DATABASE IF NOT EXISTS ssb_internal;"
sudo mysql -e "CREATE USER IF NOT EXISTS 'ssb_app'@'localhost' IDENTIFIED BY 'SsbStrongPass2026';"
sudo mysql -e "GRANT SELECT, INSERT, UPDATE, DELETE ON ssb_internal.* TO 'ssb_app'@'localhost'; FLUSH PRIVILEGES;"
sudo mysql ssb_internal < database/schema.sql
~~~
Copy `app/config.example.php` to `app/config.php` and set credentials (never committed to Git).

## 3. Deployment (`rsync`)
~~~bash
./scripts/deploy.sh --dry-run   # Preview changes
./scripts/deploy.sh             # Sync to /var/www/html/ssb-app/ and enforce 755/644/640 permissions
~~~

## 4. Backup & Restore
~~~bash
./scripts/backup.sh
sudo mysql -e "CREATE DATABASE IF NOT EXISTS ssb_internal_restore;"
gunzip -c backups/db_ssb_internal_*.sql.gz | sudo mysql ssb_internal_restore
sudo mysql -e "SELECT * FROM ssb_internal_restore.tasks;"
~~~

## 5. Health Check & Troubleshooting
- Run `./scripts/healthcheck.sh` to verify Apache, MySQL, and `/ssb-app/health.php`.
- See `docs/incident_note.md` for full log evidence and recovery steps for the simulated DB credential outage.
