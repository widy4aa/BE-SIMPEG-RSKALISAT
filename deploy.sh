#!/usr/bin/env bash
set -euo pipefail

# ==============================================================================
# Konfigurasi Deployment Server
# ==============================================================================
REMOTE_USER="infratek"
REMOTE_HOST="100.96.138.78"
REMOTE_DIR="/home/infratek/BE-SIMPEG-RSKALISAT"
CONTAINER_APP="simpeg_app"

echo "======================================================================="
echo "🚀 Memulai Auto Deployment ke Server ${REMOTE_USER}@${REMOTE_HOST}"
echo "======================================================================="

# Melakukan koneksi SSH dan menjalankan serangkaian perintah di server remote
ssh -t "${REMOTE_USER}@${REMOTE_HOST}" "bash -s" << 'EOF'
set -euo pipefail

REMOTE_DIR="/home/infratek/BE-SIMPEG-RSKALISAT"
CONTAINER_APP="simpeg_app"

echo "📂 Masuk ke direktori project: ${REMOTE_DIR}..."
cd "${REMOTE_DIR}"

echo "📦 [1/5] Menarik pembaruan kode terbaru dari Git (git pull)..."
CURRENT_BRANCH=$(git rev-parse --abbrev-ref HEAD)
git pull origin "${CURRENT_BRANCH}"

echo "🔍 [2/5] Deteksi tool Compose (podman-compose / podman compose)..."
if command -v podman-compose &> /dev/null; then
    COMPOSE_CMD="podman-compose"
elif podman compose version &> /dev/null 2>&1; then
    COMPOSE_CMD="podman compose"
elif command -v docker-compose &> /dev/null; then
    COMPOSE_CMD="docker-compose"
else
    echo "❌ Error: Perintah podman-compose atau podman compose tidak ditemukan!"
    exit 1
fi
echo "✅ Menggunakan tool: ${COMPOSE_CMD}"

echo "🏗️  [3/5] Memperbarui dan membangun ulang container Podman..."
${COMPOSE_CMD} up -d --build

echo "⏳ Menunggu container siap..."
sleep 5

echo "🔧 [4/5] Menjalankan migrasi dan optimasi Laravel di dalam container (${CONTAINER_APP})..."
if podman ps --format "{{.Names}}" | grep -q "^${CONTAINER_APP}$"; then
    EXEC_CMD="podman exec"
else
    EXEC_CMD="docker exec"
fi

${EXEC_CMD} "${CONTAINER_APP}" bash -c "
    echo '  -> Menjalankan Composer Install...' && \
    composer install --no-interaction --prefer-dist --optimize-autoloader && \
    echo '  -> Menjalankan Database Migration...' && \
    php artisan migrate --force && \
    echo '  -> Membersihkan dan rebuild cache Laravel...' && \
    php artisan optimize:clear && \
    php artisan config:cache && \
    php artisan route:cache && \
    php artisan view:cache
"

echo "🧹 [5/5] Membersihkan image tidak terpakai (dangling images)..."
podman image prune -f 2>/dev/null || docker image prune -f 2>/dev/null || true

echo "======================================================================="
echo "🎉 DEPLOYMENT BERHASIL! Server sudah diperbarui dengan kode terbaru."
echo "======================================================================="
EOF
