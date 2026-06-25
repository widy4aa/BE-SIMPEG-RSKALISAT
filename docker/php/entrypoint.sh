#!/bin/sh
set -e

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
  public/dokumen/anak

chown -R www-data:www-data storage bootstrap/cache public/dokumen
chmod -R 775 storage bootstrap/cache public/dokumen

php artisan view:clear
php artisan cache:clear
php artisan config:clear

exec php-fpm
