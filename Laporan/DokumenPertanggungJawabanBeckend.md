# Dokumen Pertanggungjawaban Backend — SIMPEG RSKALISAT

> **Tanggal Audit:** 30 Juni 2026 (pembaruan)
> **Scope:** REST API (Laravel) — `routes/api.php`, Controllers, Services, Repositories
> **Total Route Aktif:** 191 (termasuk alias `POST` untuk update multipart)

---

## Legenda

| Simbol | Keterangan |
|--------|-----------|
| ✅ | Sudah diimplementasi dan berfungsi |
| ❌ | Belum diimplementasi di backend |
| ⚠️ | Diimplementasi sebagian / perlu klarifikasi |

---

## 1. Checklist Fitur per Role

### 🔐 General (Semua Role)

| Fitur | Status | Endpoint / Catatan |
|-------|:------:|-------------------|
| Login | ✅ | `POST /api/login` |
| Logout | ✅ | `POST /api/logout` (JWT stateless — client wajib hapus token) |
| Mengubah Password (saat login) | ✅ | `POST /api/auth/change-password` |
| Forgot Password (Request OTP) | ✅ | `POST /api/forgot-password/request-otp` |
| Forgot Password (Reset) | ✅ | `POST /api/forgot-password/reset` |
| Melihat Identitas User Login | ✅ | `GET /api/me` (ringkasan user + pegawai dari JWT) |
| Melihat Role User Login | ✅ | `GET /api/role` |
| Health Check API | ✅ | `GET /api/health` (publik, tanpa auth) |

---

### 👤 Admin

| Kategori | Fitur | Status | Endpoint / Catatan |
|----------|-------|:------:|-------------------|
| **Profile** | Melihat Data Akun | ✅ | `GET /api/profile` |
| | Mengedit Data Profile | ✅ | `PATCH /api/profile`, upload foto/KTP/KK |
| **Manajemen Hak Akses** | Mengubah Role Pegawai | ✅ | `PATCH /api/pegawai/{id}/change-role` |
| | Mengubah NIK (sekaligus username login) | ✅ | `PATCH /api/auth/change-nik` (validasi NIK unik, update `pegawai.nik` + `user.username` dalam transaksi) |
| **Dashboard Admin** | Melihat Permintaan Perubahan Data | ✅ | `GET /api/admin/change-requests` |
| | Menyetujui Permintaan Perubahan | ✅ | `PATCH /api/admin/change-requests/{id}/accept` |
| | Menolak Permintaan Perubahan | ✅ | `PATCH /api/admin/change-requests/{id}/reject` |
| **Pegawai** | Melihat Data Pegawai | ✅ | `GET /api/pegawai`, `GET /api/pegawai/{id}` + filter |
| | Mengubah Status Pegawai | ✅ | `PATCH /api/pegawai/{id}/change-role` |
| | Menambahkan Data Pegawai | ✅ | `POST /api/pegawai` |
| **WhatsApp** | Kirim Pesan WA ke Pegawai | ✅ | `POST /api/pesan/pegawai/{id}` |

---

### 🧑‍💼 Pegawai

| Kategori | Fitur | Status | Endpoint / Catatan |
|----------|-------|:------:|-------------------|
| **Profile** | Melihat Data Profile | ✅ | `GET /api/profile` |
| | Mengedit Data Profile | ✅ | `PATCH /api/profile`, upload foto/KTP/KK |
| **Dashboard** | Dashboard Pegawai | ✅ | `GET /api/dashboard` |
| **Data Diklat** | Menambah Laporan Diklat | ✅ | `POST /api/diklat` |
| | Mengubah Laporan Diklat | ✅ | `PATCH /api/diklat/{id}` |
| | Menghapus Laporan Diklat | ✅ | `DELETE /api/diklat/{id}` |
| | Melihat Jadwal Diklat | ✅ | `GET /api/diklat` |
| | Melihat Laporan Diklat | ✅ | `GET /api/diklat` |
| | Upload Laporan Diklat | ✅ | `POST /api/diklat/{id}/upload-laporan` |
| | Mencetak CV | ✅ | `GET /api/generate/cv` |
| **Riwayat Karir** | CRUD Riwayat Pendidikan | ✅ | `GET/POST/PATCH/DELETE /api/riwayat-karir/pendidikan` |
| | CRUD Riwayat Jabatan | ✅ | `GET/POST/PATCH/DELETE /api/riwayat-karir/jabatan` |
| | CRUD Riwayat Pangkat | ✅ | `GET/POST/PATCH/DELETE /api/riwayat-karir/pangkat` |
| | CRUD Riwayat STR | ✅ | `GET/POST/PATCH/DELETE /api/riwayat-karir/str` |
| | CRUD Riwayat SIP | ✅ | `GET/POST/PATCH/DELETE /api/riwayat-karir/sip` |
| | CRUD Riwayat Penugasan Klinis | ✅ | `GET/POST/PATCH/DELETE /api/riwayat-karir/penugasan-klinis` |
| **Data Keluarga** | Melihat Semua Data Keluarga (gabungan) | ✅ | `GET /api/keluarga` |
| | CRUD Data Pasangan | ✅ | `GET/POST/PATCH/DELETE /api/keluarga/pasangan` |
| | CRUD Data Anak | ✅ | `GET/POST/PATCH/DELETE /api/keluarga/anak` |
| | CRUD Data Orang Tua | ✅ | `GET/POST/PATCH/DELETE /api/keluarga/orang-tua` |
| | CRUD Data Kontak Darurat | ✅ | `GET/POST/PATCH/DELETE /api/keluarga/kontak-darurat` |
| | CRUD Data Tanggungan Lain | ✅ | `GET/POST/PATCH/DELETE /api/keluarga/tanggungan-lain` |
| **Notifikasi WA** | Menerima/Melihat Notifikasi WA STR/SIP Akan Habis | ⚠️ | Notifikasi WA dikirim langsung ke HP pegawai via Fonnte; tidak ada pencatatan in-app khusus untuk WA |

---

### 🏥 HRD

| Kategori | Fitur | Status | Endpoint / Catatan |
|----------|-------|:------:|-------------------|
| **Profile** | Melihat & Mengedit Data Profile | ✅ | `GET/PATCH /api/profile` |
| **Dashboard HRD** | Dashboard HRD | ✅ | `GET /api/dashboard` via `HrdDashboardRepository` |
| **Data Diklat (Personal)** | CRUD Laporan & Jadwal Diklat | ✅ | Sama seperti pegawai |
| | Mencetak CV | ✅ | `GET /api/generate/cv` |
| **Riwayat Karir (Personal)** | CRUD Semua Riwayat | ✅ | Sama seperti pegawai |
| **Data Keluarga (Personal)** | CRUD Semua Data Keluarga | ✅ | Sama seperti pegawai |
| **Manajemen Diklat** | Melihat Data Diklat Semua Pegawai | ✅ | `GET /api/diklat/all` |
| | CRUD Master Jadwal Diklat | ✅ | `POST/PUT /api/hrd/diklat` |
| | Melihat & Sinkronisasi Peserta Diklat | ✅ | `GET /api/hrd/diklat/{id}/peserta`, `POST /api/hrd/diklat/{id}/peserta` (`syncPeserta`) |
| | Melihat Diklat Menunggu Kelayakan | ✅ | `GET /api/hrd/diklat/status/layak` |
| | Melihat Diklat Menunggu Validasi | ✅ | `GET /api/hrd/diklat/status/validasi` |
| | Verifikasi Layak/Tidak Layak | ✅ | `PATCH /api/hrd/diklat/{id}/status/layak` |
| | Verifikasi Validasi | ✅ | `PATCH /api/hrd/diklat/{id}/status/validasi` |
| | Kirim Reminder WA Upload Laporan ke Peserta | ✅ | `POST /api/hrd/diklat/{diklatId}/pegawai/{pegawaiId}/reminder-upload-laporan` (`remindUploadLaporan`) |
| | Cetak Rekap Diklat | ✅ | `GET /api/generate/laporan-diklat` |
| | CRUD Master Data (Jenis/Tipe/dll) | ✅ | `POST/PATCH/DELETE /api/form/*` |
| | Notifikasi Upload Dokumen ke Pegawai (WA Auto) | ✅ | Auto-trigger di `HrdService::sendNotifDiklatWa()` saat `updateStatusKelayakan` & `updateStatusValidasi` |
| **STR/SIP Monitoring** | Melihat Data STR/SIP Akan Habis | ✅ | `GET /api/str-sip` |
| | Melihat Data STR/SIP Aktif | ✅ | `GET /api/str-sip` |
| **Manajemen Pegawai** | Melihat Daftar & Detail Pegawai | ✅ | `GET /api/pegawai`, `GET /api/pegawai/{id}` |
| | Melihat Detail Pegawai per Bagian | ✅ | `GET /api/pegawai/{id}/{bagian}` (`bagian` ∈ `pegawai`, `keluarga`, `riwayat-karir`, `diklat`) |
| | Filter (Jenis, Profesi, Kelengkapan, Waktu, dll) | ✅ | Query params via `AdminPegawaiRepository` |
| | Update Data Inti Pegawai | ✅ | `PATCH /api/hrd/pegawai/{id}/inti` |
| | Update Data Pribadi Pegawai | ✅ | `PATCH/POST /api/hrd/pegawai/{id}/pribadi` |
| | CRUD Keluarga Pegawai (Pasangan, Anak, Orang Tua, Kontak Darurat, Tanggungan Lain) | ✅ | `/api/hrd/pegawai/{id}/keluarga/*` |
| | CRUD Riwayat Jabatan Pegawai | ✅ | `/api/hrd/pegawai/{id}/riwayat-karir/jabatan` |
| | CRUD Riwayat Pangkat Pegawai | ✅ | `/api/hrd/pegawai/{id}/riwayat-karir/pangkat` |
| | CRUD Riwayat STR Pegawai | ✅ | `/api/hrd/pegawai/{id}/riwayat-karir/str` |
| | CRUD Riwayat SIP Pegawai | ✅ | `/api/hrd/pegawai/{id}/riwayat-karir/sip` |
| | CRUD Riwayat Penugasan Klinis Pegawai | ✅ | `/api/hrd/pegawai/{id}/riwayat-karir/penugasan-klinis` |
| | CRUD Riwayat Pendidikan Pegawai | ✅ | `GET/POST/PATCH/DELETE /api/hrd/pegawai/{id}/riwayat-karir/pendidikan` |
| **WhatsApp** | Kirim Pesan WA Manual ke Pegawai | ✅ | `POST /api/pesan/pegawai/{id}` |
| | Kirim Reminder WA Dokumen STR/SIP | ✅ | `POST /api/hrd/pegawai/{id}/reminder/str-sip` |
| | Kirim Reminder WA Penugasan Klinis | ✅ | `POST /api/hrd/pegawai/{id}/reminder/penugasan-klinis` |

---

### 🏛️ Direktur RS

| Kategori | Fitur | Status | Endpoint / Catatan |
|----------|-------|:------:|-------------------|
| **Profile** | Melihat & Mengedit Data Profile | ✅ | `GET/PATCH /api/profile` |
| **Dashboard Direktur** | Dashboard Utama | ✅ | `GET /api/dashboard` via `DirekturDashboardRepository` |
| **Data Diklat (Personal)** | CRUD & Mencetak CV | ✅ | Sama seperti pegawai |
| **Riwayat Karir (Personal)** | CRUD Semua Riwayat | ✅ | Sama seperti pegawai |
| **Data Keluarga (Personal)** | CRUD Semua Data Keluarga | ✅ | Sama seperti pegawai |
| **Diklat (Monitoring)** | Melihat Data Diklat Semua Pegawai | ✅ | `GET /api/diklat/all` |
| | Melihat Status Diklat | ✅ | Via `DirekturService` |
| **Pegawai** | Melihat Daftar & Detail Pegawai | ✅ | `GET /api/pegawai`, `GET /api/pegawai/{id}` |
| | Filter Pegawai (semua filter) | ✅ | Termasuk filter berdasarkan waktu masuk |
| **STR/SIP** | Monitoring STR/SIP Masa Berlaku | ✅ | `GET /api/str-sip` |
| **WhatsApp** | Kirim Pesan WA Manual ke Pegawai | ✅ | `POST /api/pesan/pegawai/{id}` |

---

## 2. Checklist Sprint Tasks (Perspektif Backend)

### Sprint 1 — Profile, Diklat Personal, Riwayat Karir, Change Request

| No | Fitur Backend | Status |
|----|---------------|:------:|
| 1 | Autentikasi JWT (Login) | ✅ |
| 2 | CRUD Profile & Upload Foto/KTP/KK | ✅ |
| 3 | CRUD Diklat Personal (Laporan, Jadwal, Upload) | ✅ |
| 4 | Generate CV | ✅ |
| 5 | CRUD Riwayat Pendidikan | ✅ |
| 6 | CRUD Riwayat Jabatan | ✅ |
| 7 | CRUD Riwayat Pangkat | ✅ |
| 8 | CRUD Riwayat STR | ✅ |
| 9 | CRUD Riwayat SIP | ✅ |
| 10 | CRUD Riwayat Penugasan Klinis | ✅ |
| 11 | Admin: List/Detail/Accept/Reject Change Request | ✅ |

---

### Sprint 2 — Data Keluarga, Manajemen Diklat HRD, Master Data

| No | Fitur Backend | Status |
|----|---------------|:------:|
| 1 | CRUD Data Keluarga — Pasangan | ✅ |
| 2 | CRUD Data Keluarga — Anak | ✅ |
| 3 | CRUD Data Keluarga — Orang Tua | ✅ |
| 4 | CRUD Data Keluarga — Kontak Darurat | ✅ |
| 5 | Database & Model Data Keluarga | ✅ |
| 6 | CRUD Master Diklat HRD | ✅ |
| 7 | Manajemen Peserta Diklat | ✅ |
| 8 | Status Kelayakan & Validasi Laporan Diklat | ✅ |
| 9 | Cetak Rekap Diklat | ✅ |
| 10 | CRUD Master Data (Kategori/Tipe Diklat, Jenis Pegawai, dll) | ✅ |
| 11 | Integrasi WhatsApp Fonnte (`WhatsappService`) | ✅ |
| 12 | Auto-notif WA saat status diklat berubah | ✅ |

---

### Sprint 3 — Admin Management, HRD Monitoring, Dashboard, STR/SIP

| No | Fitur Backend | Status |
|----|---------------|:------:|
| 1 | Admin: CRUD Pegawai (tambah, ubah role/status) | ✅ |
| 2 | Admin: Dashboard (statistik change request) | ✅ |
| 3 | HRD: Daftar Pegawai + filter lengkap (profesi, jenis, pendidikan, waktu, kelengkapan) | ✅ |
| 4 | HRD: Detail Pegawai | ✅ |
| 5 | HRD Dashboard (statistik diklat, STR/SIP, pegawai) | ✅ |
| 6 | STR/SIP Monitoring (data akan habis, aktif/tidak aktif) | ✅ |
| 7 | Direktur Dashboard (real-time stats dari DB) | ✅ |
| 8 | Filter Pegawai by Waktu (`tgl_masuk_dari`, `tgl_masuk_sampai`, `tahun_masuk`) | ✅ |
| 9 | Settings WhatsApp Token (Admin) | ✅ |
| 10 | Notifikasi in-app (list, mark read, mark all read) | ✅ |

---

### Sprint 4 — HRD Manajemen Data Pegawai, Reminder WA

| No | Fitur Backend | Status |
|----|---------------|:------:|
| 1 | HRD: Update Data Inti Pegawai (nama, NIK, jabatan, dll) | ✅ |
| 2 | HRD: Update Data Pribadi Pegawai (alamat, no_telp, upload foto/KTP/KK) | ✅ |
| 3 | HRD: CRUD Keluarga Pegawai (pasangan, anak, orang tua, kontak, tanggungan lain) | ✅ |
| 4 | HRD: CRUD Riwayat Jabatan Pegawai | ✅ |
| 5 | HRD: CRUD Riwayat Pangkat Pegawai | ✅ |
| 6 | HRD: CRUD Riwayat STR Pegawai | ✅ |
| 7 | HRD: CRUD Riwayat SIP Pegawai | ✅ |
| 8 | HRD: CRUD Riwayat Penugasan Klinis Pegawai | ✅ |
| 9 | **HRD: CRUD Riwayat Pendidikan Pegawai** | ✅ |
| 10 | Kirim Pesan WA Manual ke Pegawai (`MessageController`) | ✅ |
| 11 | Reminder WA dokumen STR/SIP (`sendReminderStrSip`) | ✅ |
| 12 | Reminder WA Penugasan Klinis (`sendReminderPenugasanKlinis`) | ✅ |

---

## 3. Ringkasan Gap yang Telah Diselesaikan

> **Update 19 Juni 2026:** Semua gap yang sebelumnya tercatat sebagai belum diimplementasi kini telah diselesaikan.

| # | Fitur | Role Terdampak | Status |
|---|-------|---------------|--------|
| 1 | **CRUD Riwayat Pendidikan Pegawai oleh HRD** (`/api/hrd/pegawai/{id}/riwayat-karir/pendidikan`) | HRD | ✅ Selesai |
| 2 | **CRUD Tanggungan Lain (self-service Pegawai)** (`/api/keluarga/tanggungan-lain`) | Semua | ✅ Selesai |
| 3 | **Logout Endpoint** (`POST /api/logout`) | Semua | ✅ Selesai |
| 4 | **Ganti Password (saat login)** (`POST /api/auth/change-password`) | Semua | ✅ Selesai |
| 5 | **Auto-notif WA saat status diklat berubah** (`HrdService::sendNotifDiklatWa`) | HRD | ✅ Selesai |

---

## 3a. Endpoint Baru — Pembaruan 30 Juni 2026

> Endpoint berikut sudah aktif di backend namun belum tercatat pada audit 19 Juni 2026. Kini ditambahkan ke dokumen.

| # | Endpoint | Method | Role | Controller | Keterangan |
|---|----------|:------:|------|------------|-----------|
| 1 | `/api/me` | GET | Semua | `ProfileController@me` | Ringkasan identitas user + pegawai berdasarkan klaim JWT |
| 2 | `/api/role` | GET | Semua | `RoleController@show` | Mengembalikan role user yang sedang login |
| 3 | `/api/health` | GET | Publik | closure | Health check API, tanpa autentikasi |
| 4 | `/api/auth/change-nik` | PATCH | Admin | `AuthController@changeNik` | Ubah NIK pegawai sekaligus `username` login; cek NIK unik, dijalankan dalam transaksi DB |
| 5 | `/api/keluarga` | GET | Semua | `Self\DataKeluargaController@index` | Melihat seluruh data keluarga (gabungan) milik sendiri |
| 6 | `/api/pegawai/{id}/{bagian}` | GET | Admin/HRD/Direktur | `PegawaiListController@showBagian` | Detail pegawai per bagian (`pegawai`, `keluarga`, `riwayat-karir`, `diklat`) |
| 7 | `/api/hrd/diklat/status/layak` | GET | HRD | `Managed\DiklatController@menungguKelayakan` | Daftar diklat menunggu verifikasi kelayakan |
| 8 | `/api/hrd/diklat/status/validasi` | GET | HRD | `Managed\DiklatController@menungguValidasi` | Daftar diklat menunggu validasi laporan |
| 9 | `/api/hrd/diklat/{id}/peserta` | POST | HRD | `Managed\DiklatController@syncPeserta` | Sinkronisasi (tambah/hapus) peserta sebuah jadwal diklat |
| 10 | `/api/hrd/diklat/{diklatId}/pegawai/{pegawaiId}/reminder-upload-laporan` | POST | HRD | `Managed\DiklatController@remindUploadLaporan` | Kirim reminder WA agar peserta upload laporan diklat |

---

## 4. Saran

### 4.1 Sudah Diimplementasi ✅

Semua saran berikut telah selesai diimplementasi:

- **A. CRUD Riwayat Pendidikan HRD** — `HrdRiwayatKarirController` + `HrdRiwayatKarirService` + `HrdRiwayatKarirRepository` + 5 routes ✅
- **B. Tanggungan Lain Self-Service** — `TanggunganLainController` + `TanggunganLainService` + `TanggunganLainRepository` + 4 routes ✅
- **C. Logout Endpoint** — `AuthController::logout()` + `POST /api/logout` ✅
- **D. Ganti Password** — `AuthController::changePassword()` + `ChangePasswordRequest` + `POST /api/auth/change-password` ✅
- **E. Auto-Notif WA Diklat** — `HrdService::sendNotifDiklatWa()` dipanggil otomatis di `updateStatusKelayakan()` & `updateStatusValidasi()` ✅

### 4.2 Opsional (Nice-to-Have, Belum Diimplementasi)

**F. Tracking Notifikasi WA In-App untuk Pegawai**

Saat ini pengiriman WA ke pegawai tidak dicatat di tabel `notifications`. Jika pegawai perlu bisa melihat riwayat reminder WA yang diterima dalam aplikasi, tambahkan `Notification::create()` setelah setiap pemanggilan `WhatsappService::sendMessage()` di `MessageController` dan `HrdRiwayatKarirController`.

---

## 5. Ringkasan Status Keseluruhan

| Sprint | Total Fitur BE | Selesai | Belum | Persentase |
|--------|---------------|---------|-------|-----------|
| Sprint 1 | 11 | 11 | 0 | **100%** |
| Sprint 2 | 12 | 12 | 0 | **100%** |
| Sprint 3 | 10 | 10 | 0 | **100%** |
| Sprint 4 | 12 | 12 | 0 | **100%** |
| **Total** | **45** | **45** | **0** | **100%** |

> **Catatan:** Semua gap telah diselesaikan. Di luar tabel sprint, 3 fitur tambahan juga telah diimplementasi: Logout (`POST /api/logout`), Ganti Password (`POST /api/auth/change-password`), dan Tanggungan Lain self-service (`/api/keluarga/tanggungan-lain`).
>
> **Pembaruan 30 Juni 2026:** Ditambahkan 10 endpoint baru yang sebelumnya belum tercatat (lihat **bagian 3a**): `GET /api/me`, `GET /api/role`, `GET /api/health`, `PATCH /api/auth/change-nik`, `GET /api/keluarga`, `GET /api/pegawai/{id}/{bagian}`, `GET /api/hrd/diklat/status/layak`, `GET /api/hrd/diklat/status/validasi`, `POST /api/hrd/diklat/{id}/peserta`, dan `POST /api/hrd/diklat/{diklatId}/pegawai/{pegawaiId}/reminder-upload-laporan`. Total route aktif kini **191**.
