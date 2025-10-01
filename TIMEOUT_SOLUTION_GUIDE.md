# Panduan Lengkap Mengatasi Timeout 504 Gateway untuk Report Buku Besar

## 1. OPTIMISASI KODE SUDAH DILAKUKAN
✅ Optimisasi query database di ReportController.php
✅ Mengurangi jumlah query dari 5-6 menjadi 2-3 query
✅ Implementasi limit dan pagination untuk dataset besar
✅ Memory management dan garbage collection
✅ Error handling untuk PDF generation

## 2. LANGKAH-LANGKAH SISTEM YANG HARUS DILAKUKAN

### A. Database Optimization
```bash
# 1. Jalankan migration untuk menambah index
cd /var/www/hisabuna/backend
php artisan migrate

# 2. Optimize database tables
mysql -u root -p hisabuna_db << EOF
ANALYZE TABLE jurnal_details, jurnal_headers, coas;
OPTIMIZE TABLE jurnal_details, jurnal_headers, coas;
EOF

# 3. Clear Laravel cache
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear
php artisan config:cache
php artisan route:cache
```

### B. PHP-FPM Configuration (PHP 8.3)
```bash
# Edit file PHP-FPM pool
sudo nano /etc/php/8.3/fpm/pool.d/www.conf

# Tambahkan/edit baris berikut:
php_admin_value[max_execution_time] = 300
php_admin_value[max_input_time] = 300
php_admin_value[memory_limit] = 512M
php_admin_value[upload_max_filesize] = 50M
php_admin_value[post_max_size] = 50M

pm = dynamic
pm.max_children = 50
pm.start_servers = 5
pm.min_spare_servers = 5
pm.max_spare_servers = 35
pm.max_requests = 500

request_terminate_timeout = 300
```

### C. Nginx Configuration
```bash
# Edit nginx site config
sudo nano /etc/nginx/sites-available/hisabuna

# Tambahkan dalam blok server:
proxy_connect_timeout       300;
proxy_send_timeout          300;  
proxy_read_timeout          300;
send_timeout                300;

# Untuk PHP-FPM
fastcgi_connect_timeout     300;
fastcgi_send_timeout        300;
fastcgi_read_timeout        300;
fastcgi_buffer_size         128k;
fastcgi_buffers             16 64k;
fastcgi_busy_buffers_size   256k;

client_max_body_size        50M;
client_body_timeout         300;
client_header_timeout       300;
```

### D. Restart Services
```bash
# Restart PHP-FPM dan Nginx
sudo systemctl restart php8.3-fpm
sudo systemctl restart nginx

# Verifikasi status
sudo systemctl status php8.3-fpm
sudo systemctl status nginx
```

## 3. MONITORING DAN TROUBLESHOOTING

### A. Monitor Logs
```bash
# Monitor error logs secara real-time
tail -f /var/log/nginx/error.log
tail -f /var/log/php8.3-fpm.log

# Check Laravel logs
tail -f /var/www/hisabuna/backend/storage/logs/laravel.log
```

### B. Test Memory dan Performance
```bash
# Check memory usage
free -h

# Check PHP processes
ps aux | grep php-fpm

# Monitor database connections
mysqladmin -u root -p processlist
```

## 4. REKOMENDASI TAMBAHAN

### A. Untuk Dataset Sangat Besar
- Gunakan filter tanggal yang lebih spesifik (maksimal 1 bulan)
- Filter berdasarkan akun tertentu
- Pertimbangkan implementasi queue untuk report besar

### B. Hardware Recommendations
- RAM minimal 4GB untuk server
- SSD storage untuk database
- CPU minimal 2 cores

### C. Database Tuning
```sql
-- Tambahkan di my.cnf untuk MySQL
[mysqld]
innodb_buffer_pool_size = 1G
query_cache_size = 64M
tmp_table_size = 64M
max_heap_table_size = 64M
```

## 5. TESTING

### A. Test dengan Dataset Kecil
1. Filter tanggal 1 minggu
2. Pilih akun spesifik
3. Verify tidak ada timeout

### B. Gradually Increase
1. Extend ke 1 bulan
2. Test dengan semua akun
3. Monitor performance

## 6. EMERGENCY FALLBACK

Jika masih timeout, implementasi quick fix:
1. Limit data ke 1000 transaksi pertama
2. Tampilkan warning ke user tentang limit
3. Suggest filter yang lebih spesifik

Dengan implementasi di atas, timeout 504 Gateway seharusnya teratasi!
