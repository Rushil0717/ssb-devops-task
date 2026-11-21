#!/usr/bin/env bash
set -euo pipefail
systemctl is-active --quiet apache2
systemctl is-active --quiet mysql
curl -fsS http://localhost/ssb-app/health.php | grep -q '"status":"ok"'
echo "[OK] Apache, MySQL, and PHP health endpoint are operational."
