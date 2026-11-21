# Incident Report: Database Authentication Failure (HTTP 500)

- **Symptom:** Application at `http://localhost/ssb-app/index.php` returned HTTP 500 (`Database connection failed. Check server logs.`).
- **Investigation & Evidence:** Checked HTTP response via `curl` (`500`) and inspected `/var/log/apache2/error.log`, which logged:
  `DB Connection Error: SQLSTATE[HY000] [1045] Access denied for user 'ssb_app'@'localhost' (using password: YES)`
- **Root Cause:** Invalid password (`WrongPassword123`) in `/var/www/html/ssb-app/config.php`.
- **Fix:** Re-ran `./scripts/deploy.sh` to restore the valid `config.php` from source and re-apply `640` permissions (`rp_07:www-data`), returning HTTP `200`.
- **Prevention:** Keep `config.php` restricted (`chmod 640`), deploy exclusively via `scripts/deploy.sh`, and run `scripts/healthcheck.sh` after every release.
