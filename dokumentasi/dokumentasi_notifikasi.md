# Dokumentasi Notifikasi

Dokumen ini menjelaskan metode notifikasi yang berjalan saat ini, data apa yang digenerate, dan kapan notifikasi dibuat atau disinkronkan.

## Ringkasan Arsitektur

- Notifikasi disimpan di tabel `notification` (model: `App\\Models\\NotificationModel`).
- Notifikasi dibagi menjadi dua tipe utama: `info` dan `action`.
- `info` dipakai untuk notifikasi biasa (ditampilkan pada list notifikasi user).
- `action` dipakai untuk notifikasi aksi pada dashboard pegawai (list_aksi) dan memiliki status `is_resolved`.

## Struktur Data Notifikasi

Field penting di tabel `notification`:

- `user_id`: pemilik notifikasi.
- `type`: `info` atau `action` (default `info`).
- `title`, `message`: teks notifikasi.
- `is_read`: status sudah dibaca.
- `action_code`: kode aksi untuk notifikasi `action`.
- `action_payload`: JSON payload untuk kebutuhan UI/aksi.
- `is_resolved`: penanda aksi sudah tidak relevan.
- `unique_key`: kunci unik per user agar idempoten.

## Metode Notifikasi yang Ada

### 1) Notifikasi Info (type = info)

**Cara akses**

- API: `NotificationController@index` -> `NotificationService@listByUserId`.
- Hanya menampilkan notifikasi `info` yang `is_read = false`.

**Cara dibuat**

- Saat ini notifikasi `info` dibuat melalui seeder:
  - `database/seeders/PegawaiNotificationSeeder.php`
  - `database/seeders/PegawaiSeeder.php` (bagian `notifications`)

**Kapan dibuat**

- Saat menjalankan seeding database.
- Belum ada flow aplikasi yang membuat `info` secara otomatis di runtime.

### 2) Notifikasi Action (type = action)

**Fungsi utama**

- Dipakai untuk `list_aksi` pada dashboard pegawai.
- Bersifat idempoten karena menggunakan `unique_key` dan `updateOrCreate`.

**Cara dibuat / disinkronkan**

- Service: `App\\Services\\Notification\\NotificationActionSyncService`.
- Repository: `App\\Repositories\\Notification\\NotificationRepository::upsertAction`.
- Jika kondisi aksi tidak lagi relevan, notifikasi lama ditandai `is_resolved = true` via `resolveActionsNotIn`.

**Kapan dibuat**

1) **On-request** saat dashboard pegawai dipanggil:
   - `App\\Services\\Dashboard\\PegawaiService::build` memanggil `syncDashboardActionsByUserId`.
2) **Scheduled job** harian (batch):
   - Command `notifications:sync-dashboard-actions` terdaftar di `routes/console.php`.
   - Scheduler menjalankan setiap hari pukul 01:00.

**Seeder**

- `database/seeders/PegawaiActionNotificationSeeder.php` menambahkan contoh notifikasi action untuk data awal.

## Rule Notifikasi Action (Yang Digenerate)

Berikut rule yang dievaluasi di `NotificationActionSyncService`:

### A) STR

1) **STR tidak tersedia**
   - `unique_key`: `dashboard.str.missing`
   - `action_code`: `str_missing`
   - Kondisi: data STR tidak ada atau `tanggal_kadaluarsa` kosong.
   - Payload: `status_lengkap = false`, `sisa_hari = null`, `keterangan = ['STR belum tersedia']`

2) **STR sudah kadaluarsa**
   - `unique_key`: `dashboard.str.expired`
   - `action_code`: `str_expired`
   - Kondisi: `sisa_hari < 0`.
   - Payload: `status_lengkap = true`, `sisa_hari` (negatif), `keterangan = ['STR sudah kadaluarsa']`

3) **STR akan segera kadaluarsa**
   - `unique_key`: `dashboard.str.will_expire`
   - `action_code`: `str_will_expire`
   - Kondisi: `sisa_hari <= 90`.
   - Payload: `status_lengkap = true`, `sisa_hari`, `keterangan = ['STR aktif']`

### B) Data Keluarga

4) **Data keluarga belum lengkap**
   - `unique_key`: `dashboard.keluarga.incomplete`
   - `action_code`: `keluarga_incomplete`
   - Kondisi:
     - `buku_nikah_file_path` kosong, atau
     - data keluarga kosong (pasangan, anak, orang tua, kontak darurat), atau
     - pasangan/anak ada tapi nama atau tanggal lahir kosong.
   - Payload: `status_lengkap = false`, `keterangan` berisi daftar masalah.

## Endpoint Notifikasi

- `GET /api/notifications` -> daftar notifikasi `info` belum dibaca.
- `PATCH /api/notifications/{id}/read` -> tandai satu notifikasi dibaca.
- `PATCH /api/notifications/read-all` -> tandai semua notifikasi dibaca.

## Catatan

- Sistem saat ini hanya menggunakan penyimpanan notifikasi di database.
- Tidak ditemukan implementasi email/push/SMS untuk notifikasi pada codebase ini.
