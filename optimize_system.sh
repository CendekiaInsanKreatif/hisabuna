#!/bin/bash

# Script untuk optimisasi database dan sistem untuk mengatasi timeout 504

echo "=== Optimisasi Database dan Sistem untuk Report Buku Besar ==="

# 1. Jalankan migration untuk menambah index
echo "1. Menjalankan migration untuk menambah database indexes..."
php artisan migrate

# 2. Optimize composer autoload
echo "2. Mengoptimasi composer autoload..."
composer dump-autoload --optimize

# 3. Clear dan optimize Laravel cache
echo "3. Mengoptimasi Laravel cache..."
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 4. Optimize database
echo "4. Mengoptimasi database..."
php artisan optimize

echo "=== Database Optimization Queries ==="
echo "Jalankan query berikut di database untuk optimisasi lebih lanjut:"
echo ""
echo "-- Analyze tables untuk statistik yang lebih baik"
echo "ANALYZE TABLE jurnal_details, jurnal_headers, coas;"
echo ""
echo "-- Optimize tables"
echo "OPTIMIZE TABLE jurnal_details, jurnal_headers, coas;"
echo ""
echo "=== Konfigurasi Server ==="
echo "1. Edit nginx config sesuai file nginx_timeout_config.txt"
echo "2. Edit PHP-FPM config sesuai file php_fpm_timeout_config.txt"
echo "3. Restart services:"
echo "   sudo systemctl restart nginx"
echo "   sudo systemctl restart php8.3-fpm"
echo ""
echo "=== Monitoring ==="
echo "Monitor logs:"
echo "   tail -f /var/log/nginx/error.log"
echo "   tail -f /var/log/php8.3-fpm.log"
echo ""
echo "Optimisasi selesai!"
