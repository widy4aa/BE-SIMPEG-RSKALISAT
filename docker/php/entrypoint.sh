#!/bin/sh

# Install dependencies jika vendor belum ada
if [ ! -f "vendor/autoload.php" ]; then
  echo "vendor/ tidak ditemukan, menjalankan composer install..."
  composer install --optimize-autoloader --no-interaction
fi

# Buat direktori yang dibutuhkan
mkdir -p \
  storage/framework/views \
  storage/framework/cache \
  storage/framework/sessions \
  public/dokumen/foto \
  public/dokumen/foto-profil \
  public/dokumen/ktp \
  public/dokumen/kk \
  public/dokumen/str \
  public/dokumen/sip \
  public/dokumen/penugasan-klinis \
  public/dokumen/ijazah \
  public/dokumen/sertif-diklat \
  public/dokumen/jabatan \
  public/dokumen/pangkat \
  public/dokumen/pasangan \
  public/dokumen/anak || true

# Fix ownership dan permission
chown -R www-data:www-data storage bootstrap/cache public/dokumen 2>/dev/null || true
chmod -R 775 storage bootstrap/cache public/dokumen 2>/dev/null || true

# Tunggu MySQL siap
echo "Menunggu database siap..."
until php -r "
  \$host = getenv('DB_HOST') ?: 'db';
  \$port = getenv('DB_PORT') ?: 3306;
  \$conn = @fsockopen(\$host, \$port, \$e, \$s, 3);
  if (\$conn) { fclose(\$conn); exit(0); }
  exit(1);
" 2>/dev/null; do
  echo "Database belum siap, coba lagi dalam 3 detik..."
  sleep 3
done
echo "Database siap!"

# Migrate dulu sebelum clear cache (karena CACHE_STORE=database)
php artisan migrate --force || true

# Baru clear cache (tabel sudah ada setelah migrate)
php artisan view:clear || true
php artisan cache:clear || true
php artisan config:clear || true

# Jalankan PHP-FPM
exec php-fpm
