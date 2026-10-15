#!/usr/bin/env bash
# Smoke test apicareer (JACOS Careers, Fase 4).
# Jalankan: bash tests/api/smoke.sh [BASE_URL]   (atau langsung: php tests/api/smoke.php)
# Default BASE_URL: http://jacos.test:8080/apicareer
# Butuh PHP CLI dengan ekstensi curl (di Laragon: D:/laragon/bin/php/php-7.4.33-Win32-vc15-x64/php.exe tests/api/smoke.php
DIR="$(cd "$(dirname "$0")" && pwd)"
PHP="${PHP_BIN:-php}"
exec "$PHP" -d display_errors=0 "$DIR/smoke.php" "${1:-http://jacos.test:8080/apicareer}"
