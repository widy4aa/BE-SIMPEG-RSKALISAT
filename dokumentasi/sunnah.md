# SUNNAH — Analisis Teknis, Endpoint, & Rekomendasi
## BE-SIMPEG-RSKALISAT

> Dokumen ini berisi pemetaan lengkap seluruh endpoint, struktur kode, temuan masalah, dan saran optimalisasi berdasarkan pembacaan menyeluruh seluruh kode backend.

---

## Daftar Isi

1. [Arsitektur Umum](#1-arsitektur-umum)
2. [Pemetaan Seluruh Endpoint](#2-pemetaan-seluruh-endpoint)
3. [Struktur Layer Kode](#3-struktur-layer-kode)
4. [Analisis Per Modul](#4-analisis-per-modul)
5. [Temuan Masalah](#5-temuan-masalah)
6. [Saran Perbaikan & Optimalisasi](#6-saran-perbaikan--optimalisasi)

---

## 1. Arsitektur Umum

```
Request → Route → Middleware (JWT + Role) → Controller → Service → Repository → DB
```

### Stack
- **Framework:** Laravel 13.x
- **PHP:** 8.3
- **Auth:** Custom JWT (HS256, stateless)
- **DB:** MySQL (raw SQL dominan di AdminPegawaiRepository, Eloquent di modul lainnya)
- **File Storage:** `public/dokumen/` (web-accessible)
- **WhatsApp:** Fonnte API

### Role yang ada
| Role | Keterangan |
|------|------------|
| `admin` | Manajemen akun pegawai, approval change request |
| `hrd` | Manajemen data pegawai, riwayat karir, diklat |
| `direktur` | Read-only view data pegawai & diklat |
| `pegawai` | Self-service data pribadi, keluarga, riwayat karir |

### Middleware
- **JwtAuthMiddleware** — verifikasi token JWT, inject `_jwt_claims` ke request
- **RoleMiddleware** — cek role dari `_jwt_claims`, tolak jika tidak masuk daftar

---

## 2. Pemetaan Seluruh Endpoint

### PUBLIC (Tanpa Token)

| Method | URL | Controller | Keterangan |
|--------|-----|------------|------------|
| `POST` | `/api/login` | AuthController@login | Login dengan NIK + password |
| `POST` | `/api/forgot-password/request-otp` | ForgotPasswordController@requestOtp | Kirim OTP ke WA |
| `POST` | `/api/forgot-password/reset` | ForgotPasswordController@resetPassword | Reset password dengan OTP |
| `GET` | `/api/health` | (closure) | Health check |

---

### SEMUA ROLE (admin, pegawai, hrd, direktur)

| Method | URL | Controller | Keterangan |
|--------|-----|------------|------------|
| `GET` | `/api/role` | RoleController@show | Cek role login |
| `GET` | `/api/dashboard` | DashboardController@show | Dashboard per role |
| `GET` | `/api/diklat` | DiklatController@index | Ringkasan diklat |
| `GET` | `/api/generate/cv` | CvController@generate | Generate CV |
| `POST` | `/api/logout` | AuthController@logout | Logout (stateless) |
| `POST` | `/api/auth/change-password` | AuthController@changePassword | Ganti password |
| `GET` | `/api/profile` | ProfileController@show | Lihat profil sendiri |
| `PATCH` | `/api/profile` | ProfileController@update | Ajukan ubah profil (change request) |
| `POST` | `/api/profil/profil-picture` | ProfileController@updateProfilePicture | Upload foto profil |
| `POST` | `/api/profile/profile-picture` | ProfileController@updateProfilePicture | (alias) |
| `POST` | `/api/profil/ktp` | ProfileController@uploadKtp | Upload file KTP |
| `POST` | `/api/profile/kk` | ProfileController@uploadKk | Upload file KK |
| `GET` | `/api/keluarga` | DataKeluargaController@index | Ringkasan data keluarga |
| `GET` | `/api/keluarga/pasangan` | PasanganController@index | List pasangan |
| `POST` | `/api/keluarga/pasangan` | PasanganController@store | Tambah pasangan |
| `PATCH/POST` | `/api/keluarga/pasangan/{id}` | PasanganController@update | Edit pasangan |
| `DELETE` | `/api/keluarga/pasangan/{id}` | PasanganController@destroy | Hapus pasangan |
| `GET` | `/api/keluarga/anak` | AnakController@index | List anak |
| `POST` | `/api/keluarga/anak` | AnakController@store | Tambah anak |
| `PATCH/POST` | `/api/keluarga/anak/{id}` | AnakController@update | Edit anak |
| `DELETE` | `/api/keluarga/anak/{id}` | AnakController@destroy | Hapus anak |
| `GET` | `/api/keluarga/orang-tua` | OrangTuaController@index | List orang tua |
| `POST` | `/api/keluarga/orang-tua` | OrangTuaController@store | Tambah orang tua |
| `PATCH` | `/api/keluarga/orang-tua/{id}` | OrangTuaController@update | Edit orang tua |
| `DELETE` | `/api/keluarga/orang-tua/{id}` | OrangTuaController@destroy | Hapus orang tua |
| `GET` | `/api/keluarga/kontak-darurat` | KontakDaruratController@index | List kontak darurat |
| `POST` | `/api/keluarga/kontak-darurat` | KontakDaruratController@store | Tambah kontak darurat |
| `PATCH` | `/api/keluarga/kontak-darurat/{id}` | KontakDaruratController@update | Edit kontak darurat |
| `DELETE` | `/api/keluarga/kontak-darurat/{id}` | KontakDaruratController@destroy | Hapus kontak darurat |
| `GET` | `/api/keluarga/tanggungan-lain` | TanggunganLainController@index | List tanggungan lain |
| `POST` | `/api/keluarga/tanggungan-lain` | TanggunganLainController@store | Tambah tanggungan |
| `PATCH` | `/api/keluarga/tanggungan-lain/{id}` | TanggunganLainController@update | Edit tanggungan |
| `DELETE` | `/api/keluarga/tanggungan-lain/{id}` | TanggunganLainController@destroy | Hapus tanggungan |
| `GET` | `/api/riwayat-karir/pendidikan` | RiwayatKarirController@pendidikan | List riwayat pendidikan |
| `POST` | `/api/riwayat-karir/pendidikan` | RiwayatKarirController@storePendidikan | Tambah pendidikan |
| `PATCH/POST` | `/api/riwayat-karir/pendidikan/{id}` | RiwayatKarirController@updatePendidikan | Edit pendidikan |
| `DELETE` | `/api/riwayat-karir/pendidikan/{id}` | RiwayatKarirController@destroyPendidikan | Hapus pendidikan |
| `GET` | `/api/riwayat-karir/jabatan` | RiwayatKarirController@jabatan | List riwayat jabatan |
| `POST` | `/api/riwayat-karir/jabatan` | RiwayatKarirController@storeJabatan | Tambah jabatan |
| `PATCH/POST` | `/api/riwayat-karir/jabatan/{id}` | RiwayatKarirController@updateJabatan | Edit jabatan |
| `DELETE` | `/api/riwayat-karir/jabatan/{id}` | RiwayatKarirController@destroyJabatan | Hapus jabatan |
| `GET` | `/api/riwayat-karir/pangkat` | RiwayatKarirController@pangkat | List riwayat pangkat |
| `POST` | `/api/riwayat-karir/pangkat` | RiwayatKarirController@storePangkat | Tambah pangkat |
| `PATCH/POST` | `/api/riwayat-karir/pangkat/{id}` | RiwayatKarirController@updatePangkat | Edit pangkat |
| `DELETE` | `/api/riwayat-karir/pangkat/{id}` | RiwayatKarirController@destroyPangkat | Hapus pangkat |
| `GET` | `/api/riwayat-karir/sip` | RiwayatKarirController@sip | List riwayat SIP |
| `POST` | `/api/riwayat-karir/sip` | RiwayatKarirController@storeSip | Tambah SIP |
| `PATCH/POST` | `/api/riwayat-karir/sip/{id}` | RiwayatKarirController@updateSip | Edit SIP |
| `DELETE` | `/api/riwayat-karir/sip/{id}` | RiwayatKarirController@destroySip | Hapus SIP |
| `GET` | `/api/riwayat-karir/str` | RiwayatKarirController@str | List riwayat STR |
| `POST` | `/api/riwayat-karir/str` | RiwayatKarirController@storeStr | Tambah STR |
| `PATCH/POST` | `/api/riwayat-karir/str/{id}` | RiwayatKarirController@updateStr | Edit STR |
| `DELETE` | `/api/riwayat-karir/str/{id}` | RiwayatKarirController@destroyStr | Hapus STR |
| `GET` | `/api/riwayat-karir/penugasan-klinis` | RiwayatKarirController@penugasanKlinis | List penugasan klinis |
| `POST` | `/api/riwayat-karir/penugasan-klinis` | RiwayatKarirController@storePenugasanKlinis | Tambah penugasan |
| `PATCH/POST` | `/api/riwayat-karir/penugasan-klinis/{id}` | RiwayatKarirController@updatePenugasanKlinis | Edit penugasan |
| `DELETE` | `/api/riwayat-karir/penugasan-klinis/{id}` | RiwayatKarirController@destroyPenugasanKlinis | Hapus penugasan |
| `GET` | `/api/notifications` | NotificationController@index | List notifikasi |
| `PATCH` | `/api/notifications/{id}/read` | NotificationController@markAsRead | Tandai 1 dibaca |
| `PATCH` | `/api/notifications/read-all` | NotificationController@markAllAsRead | Tandai semua dibaca |

---

### PEGAWAI, HRD, DIREKTUR (bukan admin)

| Method | URL | Controller | Keterangan |
|--------|-----|------------|------------|
| `POST` | `/api/diklat` | DiklatController@store | Daftar diklat |
| `PATCH` | `/api/diklat/{id}` | DiklatController@update | Edit diklat |
| `DELETE` | `/api/diklat/{id}` | DiklatController@destroy | Hapus diklat |
| `POST` | `/api/diklat/{id}/upload-laporan` | DiklatController@uploadLaporan | Upload laporan diklat |

---

### ADMIN, HRD, DIREKTUR

| Method | URL | Controller | Keterangan |
|--------|-----|------------|------------|
| `GET` | `/api/pegawai` | PegawaiController@index | List semua pegawai |
| `GET` | `/api/pegawai/{id}` | PegawaiController@show | Detail pegawai |
| `GET` | `/api/str-sip` | StrSipController@index | Ringkasan STR/SIP seluruh pegawai |
| `POST` | `/api/pesan/pegawai/{id}` | MessageController@sendToPegawai | Kirim WA ke pegawai |

---

### ADMIN ONLY

| Method | URL | Controller | Keterangan |
|--------|-----|------------|------------|
| `POST` | `/api/pegawai` | PegawaiController@store | Buat akun pegawai baru |
| `PATCH` | `/api/pegawai/{id}/change-role` | PegawaiController@changeRole | Ubah role/status pegawai |
| `PATCH` | `/api/auth/change-nik` | AuthController@changeNik | Ubah NIK diri sendiri (admin) |
| `GET` | `/api/settings/whatsapp` | SettingController@getWhatsappSetting | Lihat token WA |
| `PUT` | `/api/settings/whatsapp` | SettingController@updateWhatsappSetting | Update token WA |
| `GET` | `/api/admin/change-requests` | ChangeRequestAdminController@index | List change request |
| `GET` | `/api/admin/change-requests/{id}` | ChangeRequestAdminController@show | Detail change request |
| `PATCH` | `/api/admin/change-requests/{id}/accept` | ChangeRequestAdminController@accept | Setujui change request |
| `PATCH` | `/api/admin/change-requests/{id}/reject` | ChangeRequestAdminController@reject | Tolak change request |

---

### HRD, DIREKTUR

| Method | URL | Controller | Keterangan |
|--------|-----|------------|------------|
| `GET` | `/api/diklat/all` | DiklatController@all | Semua data diklat |

---

### HRD ONLY — Manajemen Pegawai

| Method | URL | Controller | Keterangan |
|--------|-----|------------|------------|
| `PATCH` | `/api/hrd/pegawai/{id}/inti` | HrdPegawaiController@updateInti | Update data inti pegawai |
| `PATCH/POST` | `/api/hrd/pegawai/{id}/pribadi` | HrdPegawaiController@updatePribadi | Update data pribadi |
| `GET/POST/PATCH/DELETE` | `/api/hrd/pegawai/{id}/keluarga/pasangan` | HrdKeluargaController | Kelola pasangan pegawai |
| `GET/POST/PATCH/DELETE` | `/api/hrd/pegawai/{id}/keluarga/anak` | HrdKeluargaController | Kelola anak pegawai |
| `GET/POST/PATCH/DELETE` | `/api/hrd/pegawai/{id}/keluarga/orang-tua` | HrdKeluargaController | Kelola orang tua pegawai |
| `GET/POST/PATCH/DELETE` | `/api/hrd/pegawai/{id}/keluarga/kontak-darurat` | HrdKeluargaController | Kelola kontak darurat |
| `GET/POST/PATCH/DELETE` | `/api/hrd/pegawai/{id}/keluarga/tanggungan-lain` | HrdKeluargaController | Kelola tanggungan lain |
| `GET/POST/PATCH/DELETE` | `/api/hrd/pegawai/{id}/riwayat-karir/jabatan` | HrdRiwayatKarirController | Riwayat jabatan pegawai |
| `GET/POST/PATCH/DELETE` | `/api/hrd/pegawai/{id}/riwayat-karir/str` | HrdRiwayatKarirController | Riwayat STR pegawai |
| `GET/POST/PATCH/DELETE` | `/api/hrd/pegawai/{id}/riwayat-karir/sip` | HrdRiwayatKarirController | Riwayat SIP pegawai |
| `GET/POST/PATCH/DELETE` | `/api/hrd/pegawai/{id}/riwayat-karir/penugasan-klinis` | HrdRiwayatKarirController | Riwayat penugasan klinis |
| `GET/POST/PATCH/DELETE` | `/api/hrd/pegawai/{id}/riwayat-karir/pangkat` | HrdRiwayatKarirController | Riwayat pangkat pegawai |
| `GET/POST/PATCH/DELETE` | `/api/hrd/pegawai/{id}/riwayat-karir/pendidikan` | HrdRiwayatKarirController | Riwayat pendidikan pegawai |
| `POST` | `/api/hrd/pegawai/{id}/reminder/str-sip` | HrdRiwayatKarirController@sendReminderStrSip | Kirim reminder WA STR/SIP |
| `POST` | `/api/hrd/pegawai/{id}/reminder/penugasan-klinis` | HrdRiwayatKarirController@sendReminderPenugasanKlinis | Kirim reminder WA penugasan |

---

### HRD ONLY — Master Diklat

| Method | URL | Controller | Keterangan |
|--------|-----|------------|------------|
| `POST` | `/api/hrd/diklat` | DiklatController@storeMaster | Buat master diklat |
| `PUT` | `/api/hrd/diklat/{id}` | DiklatController@updateMaster | Update master diklat |
| `GET` | `/api/hrd/diklat/{id}/peserta` | DiklatController@peserta | List peserta diklat |
| `POST` | `/api/hrd/diklat/{id}/peserta` | DiklatController@syncPeserta | Sync peserta diklat |
| `GET` | `/api/hrd/diklat/status/layak` | DiklatController@menungguKelayakan | Diklat menunggu kelayakan |
| `GET` | `/api/hrd/diklat/status/validasi` | DiklatController@menungguValidasi | Diklat menunggu validasi |
| `PATCH` | `/api/hrd/diklat/{id}/status/layak` | DiklatController@updateStatusKelayakan | Update status kelayakan |
| `PATCH` | `/api/hrd/diklat/{id}/status/validasi` | DiklatController@updateStatusValidasi | Update status validasi |
| `GET` | `/api/generate/laporan-diklat` | LaporanController@laporanDiklat | Generate laporan diklat |

---

### MASTER DATA (Semua role terauthentikasi — prefix `/api/form`)

| Method | URL | Keterangan |
|--------|-----|------------|
| `GET` | `/api/form/kategori-diklat` | List kategori diklat |
| `GET` | `/api/form/tipe-diklat` | List tipe diklat |
| `GET` | `/api/form/jenis-pegawai` | List jenis pegawai |
| `GET` | `/api/form/unit-kerja` | List unit kerja |
| `GET` | `/api/form/jenis-biaya` | List jenis biaya |
| `GET` | `/api/form/golongan-ruang` | List golongan ruang |
| `GET` | `/api/form/profesi` | List profesi |
| `GET` | `/api/form/jenis-sip` | List jenis SIP |
| `POST/PATCH/DELETE` | `/api/form/kategori-diklat[/{id}]` | CRUD (HRD only) |
| `POST/PATCH/DELETE` | `/api/form/tipe-diklat[/{id}]` | CRUD (HRD only) |
| `POST/PATCH/DELETE` | `/api/form/jenis-pegawai[/{id}]` | CRUD (HRD only) |
| `POST/PATCH/DELETE` | `/api/form/unit-kerja[/{id}]` | CRUD (HRD only) |
| `POST/PATCH/DELETE` | `/api/form/jenis-biaya[/{id}]` | CRUD (HRD only) |
| `POST/PATCH/DELETE` | `/api/form/golongan-ruang[/{id}]` | CRUD (HRD only) |
| `POST/PATCH/DELETE` | `/api/form/profesi[/{id}]` | CRUD (HRD only) |
| `POST/PATCH/DELETE` | `/api/form/jenis-sip[/{id}]` | CRUD (HRD only) |

---

## 3. Struktur Layer Kode

### Format Response Standar
Hampir seluruh endpoint mengikuti format:
```json
{
  "success": true | false,
  "message": "pesan",
  "data": {}
}
```
**Pengecualian:** `MasterDataController` tidak menyertakan field `message` pada response list — inkonsisten.

### Alur Data
```
Request (FormRequest) → Controller (tipis) → Service (logika bisnis) → Repository (akses DB) → Model (Eloquent)
```

### Pola Ekstraksi JWT
Semua controller mengekstrak user context dari `_jwt_claims` yang di-inject oleh `JwtAuthMiddleware`:
```php
$claims  = $request->input('_jwt_claims', []);
$userId  = (int) ($claims['sub'] ?? 0);
$role    = strtolower((string) ($claims['role'] ?? ''));
```

### File Upload
- Disimpan di `public/dokumen/{tipe}/`
- Nama file: `{tipe}-{pegawaiId}-{timestamp}.{ext}`
- Direktori dibuat otomatis dengan `mkdir(..., 0755, true)` jika belum ada
- File lama dihapus dengan `@unlink()` sebelum upload baru

### Error Handling Convention
| Exception | HTTP Status |
|-----------|-------------|
| `InvalidArgumentException` | 422 |
| `ModelNotFoundException` | 404 |
| `RuntimeException` | 400 |
| `Exception` (generic) | 500 |

---

## 4. Analisis Per Modul

### Auth
- Login: NIK sebagai username → validasi password → cek `is_active` user → cek `status_pegawai` → issue JWT
- JWT: Custom HS256, TTL 12 jam (dari config), tidak ada blacklist server-side
- Logout: Stateless — client bertanggung jawab hapus token
- Change Password: Verifikasi password lama via `Hash::check()`, update via Eloquent
- Change NIK (admin only): Update atomik `pegawai.nik` + `users.username` dalam transaction

### AdminPegawaiRepository
Seluruh query menggunakan **raw SQL** (`DB::select`, `DB::selectOne`, `DB::insert`, `DB::update`) — bukan Eloquent. Ini kontras dengan modul lain yang menggunakan Eloquent.

Query utama `getPegawaiDetail()` menghasilkan **9+ query** untuk satu record:
```
1 query utama + pasangan + anak + orang_tua + kontak_darurat + tanggungan_lain
+ str + sip + penugasan_klinis + jabatan_pegawai + riwayat_pangkat + jadwal_diklat
```

### HrdPegawaiService & HrdRiwayatKarirService
File disimpan di lokasi web-accessible (`public/`). Extension file diambil dari `getClientOriginalExtension()` tanpa whitelist validasi.

### ForgotPasswordController
OTP disimpan di Laravel Cache **tanpa hashing**. Rate limiting sudah ada (1x per 60 detik) tapi implementasi check-then-hit bisa race condition.

### SettingController
Token WhatsApp dikembalikan plain text di response GET. SSL verification dinonaktifkan (`withoutVerifying()`) untuk panggilan ke Fonnte API.

---

## 5. Temuan Masalah

### Keamanan (Security)

| # | Masalah | File | Tingkat |
|---|---------|------|---------|
| S1 | File extension tidak divalidasi whitelist saat upload — bisa upload file berbahaya | `HrdPegawaiService.php`, `HrdRiwayatKarirService.php` | Tinggi |
| S2 | File disimpan di `public/` (web-accessible) — bisa diakses langsung via URL | Semua service upload | Tinggi |
| S3 | SSL verification dinonaktifkan (`withoutVerifying()`) pada API call ke Fonnte | `SettingController.php` | Tinggi |
| S4 | Token WhatsApp dikembalikan plain text di response `GET /settings/whatsapp` | `SettingController.php` | Sedang |
| S5 | OTP disimpan di cache tanpa hashing | `ForgotPasswordController.php` | Sedang |
| S6 | Tidak ada rate limiting pada endpoint reminder WA | `HrdRiwayatKarirController.php` | Sedang |
| S7 | Tidak ada JWT blacklist/revocation — token tetap valid walau logout | `JwtService.php` | Sedang |
| S8 | Tidak ada audit logging untuk login gagal, reset password, approval | Semua modul auth | Rendah |

### Performa

| # | Masalah | File | Tingkat |
|---|---------|------|---------|
| P1 | N+1 query pada `getPegawaiDetail()` — 9+ query per request | `AdminPegawaiRepository.php` | Tinggi |
| P2 | Beberapa query overview dipanggil terpisah (overview counts, total aktif, count by role) | `AdminPegawaiService.php` | Sedang |
| P3 | Master data (`/api/form/*`) tidak paginasi — bisa kembalikan ribuan record | `MasterDataController.php` | Rendah |
| P4 | Tidak ada caching pada data yang jarang berubah (master data, profil) | Semua modul | Rendah |

### Konsistensi Kode

| # | Masalah | File | Tingkat |
|---|---------|------|---------|
| K1 | `AdminPegawaiRepository` pakai raw SQL, modul lain pakai Eloquent — inkonsisten | `AdminPegawaiRepository.php` | Sedang |
| K2 | `MasterDataController` tidak menyertakan `message` di response list | `MasterDataController.php` | Rendah |
| K3 | Ekstraksi JWT claims (`$claims['sub']`, `$claims['role']`) diulang di setiap controller | Semua controller | Rendah |
| K4 | Fungsi `formatPhoneNumber()` dan `maskPhoneNumber()` duplikat di `HrdRiwayatKarirController` dan `MessageController` | Dua controller | Rendah |
| K5 | File handling logic (upload, delete lama, rename) duplikat di `HrdPegawaiService` dan `HrdRiwayatKarirService` | Dua service | Rendah |
| K6 | Route duplikat: `POST /keluarga/pasangan/{id}` dan `PATCH /keluarga/pasangan/{id}` ke method yang sama | `routes/api.php` | Rendah |
| K7 | `@unlink()` dipakai untuk suppress error — kegagalan hapus file tidak terdeteksi | Semua service upload | Rendah |

### Validasi

| # | Masalah | File | Tingkat |
|---|---------|------|---------|
| V1 | Password minimum 6 karakter di login dan `StorePegawaiRequest` — di bawah standar modern | `LoginRequest.php`, `StorePegawaiRequest.php` | Sedang |
| V2 | `DiklatController` validasi ID manual dengan `is_numeric()` — tidak pakai FormRequest | `DiklatController.php` | Rendah |
| V3 | Parameter `note` pada accept/reject change request tidak dibatasi panjangnya | `ChangeRequestAdminController.php` | Rendah |
| V4 | Array `pegawai_ids` pada sync peserta tidak divalidasi ukuran maksimal | `DiklatController.php` | Rendah |
| V5 | `ForgotPasswordController@resetPassword` query ulang pegawai padahal sudah diquery sebelumnya | `ForgotPasswordController.php` | Rendah |

---

## 6. Saran Perbaikan & Optimalisasi

### Prioritas Tinggi

#### S1 + S2: Validasi extension + pindahkan file ke storage private
```php
// Tambah validasi di FormRequest
'sk_file' => 'file|mimes:pdf,jpg,jpeg,png|max:5120'

// Simpan di storage/ bukan public/
$path = $file->store("dokumen/{$tipe}", 'local');

// Akses via route yang terautentikasi
Route::get('/dokumen/{path}', [DokumenController::class, 'serve'])
    ->middleware('auth.jwt')
    ->where('path', '.*');
```

#### S3: Aktifkan SSL verification
```php
// SettingController.php — hapus withoutVerifying()
Http::timeout(10)->post($url, $payload);
```

#### P1: Kurangi N+1 query di getPegawaiDetail
Gabungkan query-query anak ke dalam satu query dengan `IN` clause atau gunakan Eloquent eager loading:
```php
// Ganti 9+ query terpisah dengan:
$pegawai = Pegawai::with([
    'pribadi.pasangan', 'pribadi.anak', 'pribadi.orangTua',
    'pribadi.kontakDarurat', 'pribadi.tanggunganLain',
    'str', 'sip', 'penugasanKlinis', 'jabatanPegawai.jabatan.unitKerja',
    'riwayatPangkat.pangkat', 'jadwalDiklat.masterDiklat',
])->find($pegawaiId);
```

---

### Prioritas Sedang

#### S4: Sembunyikan token WA dari response
```php
// SettingController@getWhatsappSetting
return response()->json([
    'success' => true,
    'data' => [
        'token_terkonfigurasi' => !empty($setting->whatsapp_token),
        'device' => $device,
    ]
]);
```

#### S5: Hash OTP sebelum disimpan di cache
```php
$otp = rand(100000, 999999);
Cache::put("otp_reset_{$nik}", hash('sha256', $otp), 300);

// Verifikasi
if (!hash_equals(Cache::get("otp_reset_{$nik}"), hash('sha256', $request->otp))) {
    return response()->json(['success' => false, 'message' => 'OTP tidak valid.'], 400);
}
```

#### K1: Pindahkan AdminPegawaiRepository ke Eloquent secara bertahap
Mulai dari method yang paling sering dipanggil:
```php
// getPaginatedPegawai() — ganti raw SQL dengan Eloquent + filter
$query = Pegawai::with(['user', 'pribadi', 'jabatan.unitKerja', 'profesi', 'jenisPegawai'])
    ->when($filters['search'], fn($q, $s) => $q->where('nama', 'like', "%$s%"))
    ->paginate($perPage);
```

#### V1: Naikkan minimum password
```php
// LoginRequest.php
'password' => ['required', 'string', 'min:8']

// StorePegawaiRequest.php
'password' => 'required|string|min:8'
```

---

### Prioritas Rendah / Peningkatan Kualitas

#### K3: Buat trait atau base controller untuk ekstraksi JWT
```php
// app/Http/Controllers/Api/Concerns/HasJwtClaims.php
trait HasJwtClaims
{
    protected function jwtUserId(Request $request): int
    {
        $claims = $request->input('_jwt_claims', []);
        return (int) (is_array($claims) ? ($claims['sub'] ?? 0) : 0);
    }

    protected function jwtRole(Request $request): string
    {
        $claims = $request->input('_jwt_claims', []);
        return strtolower((string) (is_array($claims) ? ($claims['role'] ?? '') : ''));
    }
}
```

#### K4: Buat helper class untuk format nomor telepon
```php
// app/Helpers/PhoneHelper.php
class PhoneHelper
{
    public static function format(string $phone): string { ... }
    public static function mask(string $phone): string { ... }
}
```

#### K5: Buat trait FileUploadable untuk logic upload yang berulang
```php
// app/Services/Concerns/HandlesFileUpload.php
trait HandlesFileUpload
{
    protected function uploadFile(UploadedFile $file, string $dir, string $prefix, int $id): string
    {
        $ext      = $file->getClientOriginalExtension();
        $filename = "{$prefix}-{$id}-" . time() . ".{$ext}";
        $file->move(public_path("dokumen/{$dir}"), $filename);
        return "dokumen/{$dir}/{$filename}";
    }

    protected function deleteFile(?string $path): void
    {
        if ($path && file_exists(public_path($path))) {
            unlink(public_path($path));
        }
    }
}
```

#### K6: Hapus duplikasi route POST untuk update
Route `POST /keluarga/pasangan/{id}` (untuk update) seharusnya cukup `PATCH`. Duplikasi ini ada di semua modul keluarga dan riwayat karir untuk kompatibilitas form HTML, tapi sebaiknya didokumentasikan jelas alasannya.

#### Tambahkan Rate Limiting pada endpoint sensitif
```php
// routes/api.php
Route::middleware(['throttle:5,1'])->group(function () {
    Route::post('/login', ...);
    Route::post('/forgot-password/request-otp', ...);
    Route::post('/forgot-password/reset', ...);
});

Route::middleware(['throttle:10,1'])->group(function () {
    Route::post('/hrd/pegawai/{id}/reminder/str-sip', ...);
    Route::post('/hrd/pegawai/{id}/reminder/penugasan-klinis', ...);
});
```

#### Tambahkan `message` pada response MasterDataController
```php
return response()->json([
    'success' => true,
    'message' => 'Data berhasil diambil.',
    'data' => $data,
]);
```

---

## Ringkasan Prioritas

| Prioritas | Item | Dampak |
|-----------|------|--------|
| 🔴 Tinggi | Validasi & whitelist extension file upload | Keamanan |
| 🔴 Tinggi | Pindahkan file dari `public/` ke `storage/` | Keamanan |
| 🔴 Tinggi | Aktifkan SSL verification pada Fonnte API | Keamanan |
| 🔴 Tinggi | N+1 query pada `getPegawaiDetail()` | Performa |
| 🟡 Sedang | Hash OTP sebelum simpan ke cache | Keamanan |
| 🟡 Sedang | Sembunyikan token WA dari response | Keamanan |
| 🟡 Sedang | Rate limiting pada endpoint auth & reminder | Keamanan |
| 🟡 Sedang | Naikkan minimum password ke 8 karakter | Validasi |
| 🟡 Sedang | Migrasi AdminPegawaiRepository ke Eloquent | Konsistensi |
| 🟢 Rendah | Trait JWT claims extraction | Kualitas |
| 🟢 Rendah | Helper/trait file upload | Kualitas |
| 🟢 Rendah | Helper format nomor telepon | Kualitas |
| 🟢 Rendah | Konsistensi response format (tambah `message`) | Konsistensi |
