#!/bin/sh
# Tidak pakai set -e agar container tidak mati karena error non-kritis

echo "=== Laravel Docker Entrypoint ==="

# [1/6] Install dependencies jika vendor belum ada
if [ ! -f "vendor/autoload.php" ]; then
  echo "[1/6] vendor/autoload.php tidak ditemukan, menjalankan composer update..."
  composer update --optimize-autoloader --no-interaction --no-dev
  if [ $? -ne 0 ]; then
    echo "ERROR: Composer update GAGAL! Periksa koneksi internet atau composer.json."
    exit 1
  fi
else
  echo "[1/6] vendor/ sudah ada, skip composer install."
fi

# [2/6] Buat direktori yang dibutuhkan
echo "[2/6] Membuat direktori storage..."
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

# [3/6] Fix ownership dan permission
echo "[3/6] Fix permission..."
chown -R www-data:www-data storage bootstrap/cache public/dokumen 2>/dev/null || true
chmod -R 775 storage bootstrap/cache public/dokumen 2>/dev/null || true

# [4/6] Tunggu MySQL siap
echo "[4/6] Menunggu database siap..."
RETRIES=30
until php -r "
  \$host = getenv('DB_HOST') ?: 'db';
  \$port = getenv('DB_PORT') ?: 3306;
  \$conn = @fsockopen(\$host, \$port, \$e, \$s, 3);
  if (\$conn) { fclose(\$conn); exit(0); }
  exit(1);
" 2>/dev/null; do
  RETRIES=$((RETRIES - 1))
  if [ "$RETRIES" -le 0 ]; then
    echo "ERROR: Database tidak bisa dijangkau setelah 30 percobaan!"
    exit 1
  fi
  echo "  Database belum siap, coba lagi dalam 3 detik... (sisa: $RETRIES)"
  sleep 3
done
echo "  Database siap!"

# [5/6] Jalankan migrate
echo "[5/6] Menjalankan migrasi..."
php artisan migrate --force || true

# Seed database jika tabel users masih kosong
echo "  Mengecek apakah perlu seed..."
NEEDS_SEED=$(php -r "
  try {
    \$pdo = new PDO(
      'mysql:host=' . getenv('DB_HOST') . ';port=' . getenv('DB_PORT') . ';dbname=' . getenv('DB_DATABASE'),
      getenv('DB_USERNAME') ?: 'root',
      getenv('DB_PASSWORD') ?: 'root'
    );
    \$count = \$pdo->query('SELECT COUNT(*) FROM users')->fetchColumn();
    echo \$count;
  } catch (Exception \$e) {
    echo 0;
  }
" 2>/dev/null)

if [ "$NEEDS_SEED" = "0" ]; then
  echo "  Tabel users kosong, menjalankan db:seed..."
  php artisan db:seed --force || true
else
  echo "  Data sudah ada (users: $NEEDS_SEED), skip seeding."
fi

# [6/6] Clear cache (setelah migrate agar tabel cache sudah ada)
echo "[6/6] Clear cache..."
php artisan view:clear || true
php artisan cache:clear || true
php artisan config:clear || true

echo "=== Setup selesai! Menjalankan PHP-FPM... ==="

# Jalankan PHP-FPM sebagai proses utama
exec php-fpm
