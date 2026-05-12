#!/bin/bash
set -e

# Fix MPM setiap kali container start
a2dismod mpm_event mpm_worker 2>/dev/null || true
a2enmod mpm_prefork 2>/dev/null || true

# Jalankan command aslinya (apache2-foreground)
exec "$@"