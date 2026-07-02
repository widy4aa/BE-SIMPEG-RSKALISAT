# Pembaruan: Fix Generate CV & Cooldown OTP Lupa Password

Dokumen ini merangkum dua pembaruan pada backend SIMPEG RS Kalisat beserta unit/feature test-nya.

---

## 1. Fix Error Generate CV (`is_current`)

### Masalah
Endpoint `POST /api/generate/cv` melempar error:

```
Undefined property: stdClass::$is_current
app/Services/Generate/CvService.php:84
```

### Penyebab
Migrasi `2026_06_30_000001_drop_is_current_from_riwayat_tables.php` menghapus kolom
`is_current` dari tabel `jabatan_pegawai`, `str`, `sip`, dan `penugasan_klinis`.
Status "current" kini dihitung dari rentang tanggal.

Di model Eloquent hal ini ditangani lewat accessor `getIsCurrentAttribute()`, tetapi
`CvRepository` memakai **raw SQL** (`DB::select`) yang mengembalikan `stdClass`,
sehingga accessor tidak pernah dijalankan. `CvService` masih membaca `$x->is_current`
untuk keempat data tersebut → error (baris 84 = riwayat jabatan kena duluan).

### Solusi
Menghitung `is_current` langsung di `CvRepository`, meniru logika accessor /
`AdminPegawaiRepository::isCurrentPeriod`:

| Data | Field periode |
|------|---------------|
| Riwayat jabatan | `started_at` / `ended_at` |
| SIP | `tanggal_terbit` / `tanggal_kadaluarsa` |
| STR | `tanggal_terbit` / `tanggal_kadaluarsa` |
| Penugasan Klinis | `tgl_mulai` / `tgl_kadaluarsa` |

Ditambahkan helper `isCurrentPeriod(?Carbon $startedAt, ?Carbon $endedAt): bool` dan
parameter `currentPeriodFields` pada `getSimpleRows()`. `CvService` tidak diubah.

### File
- `app/Repositories/Generate/CvRepository.php`

### Test
`tests/Feature/Generate/CvServiceTest.php` (Feature — karena repository memakai raw SQL
sehingga butuh database asli):

- `test_generate_cv_data_computes_is_current_from_date_ranges` — seed pegawai lengkap
  (jabatan aktif + berakhir, STR aktif, SIP kadaluarsa, penugasan klinis aktif),
  memverifikasi `is_current` dihitung benar. Ini regression guard untuk error di atas.
- `test_generate_cv_data_handles_pegawai_without_riwayat` — pegawai tanpa riwayat →
  semua list kosong, tanpa error.

---

## 2. Cooldown Kirim (Ulang) OTP Lupa Password

### Tujuan
Memberi jeda waktu **60 detik** sebelum user dapat meminta / mengklik "Kirim Ulang OTP",
untuk mencegah spam SMS/WhatsApp akibat klik berulang.

### Perubahan
Cooldown sudah ada berbasis `RateLimiter`, namun dirapikan:

1. Menambahkan konstanta `RESEND_COOLDOWN_SECONDS = 60` (menggantikan magic number `60`).
2. Mengembalikan `cooldown_seconds` pada response agar frontend bisa menampilkan
   hitung mundur tombol "Kirim Ulang OTP":
   - Sukses (`200`): `"cooldown_seconds": 60`
   - Masih cooldown (`429`): `"cooldown_seconds": <sisa detik>`

### Cara kerja
- Key rate limiter: `request-otp:<nik>` (per-NIK).
- Request pertama → OTP dikirim, limiter di-`hit` selama 60 detik.
- Request kedua dalam masa cooldown → `429`, OTP/WhatsApp **tidak** dikirim ulang.

### File
- `app/Http/Controllers/Api/Auth/ForgotPasswordController.php`

### Test
`tests/Feature/Api/ForgotPasswordOtpCooldownTest.php` (`WhatsappService` di-mock via Mockery
agar tidak memanggil API Fonnte):

- `test_request_otp_succeeds_and_returns_cooldown_seconds` — request pertama `200` +
  `cooldown_seconds = 60`.
- `test_second_request_within_cooldown_is_blocked_without_sending` — request kedua `429`,
  WhatsApp dikirim tepat 1× (anti-spam terbukti).
- `test_request_otp_allowed_again_after_cooldown_expires` — setelah cooldown habis
  (disimulasikan `RateLimiter::clear()`), request diizinkan lagi `200`.

---

## Perintah `git diff`

Lihat seluruh perubahan (file test baru masih untracked, jadi tandai dulu dengan `-N`):

```bash
git add -N app/Repositories/Generate/CvRepository.php \
           app/Http/Controllers/Api/Auth/ForgotPasswordController.php \
           tests/Feature/Generate/CvServiceTest.php \
           tests/Feature/Api/ForgotPasswordOtpCooldownTest.php

git diff
```

Ringkasan statistik perubahan:

```bash
git diff --stat
```

Diff per pembaruan:

```bash
# Pembaruan 1 — Fix Generate CV
git diff app/Repositories/Generate/CvRepository.php tests/Feature/Generate/CvServiceTest.php

# Pembaruan 2 — Cooldown OTP
git diff app/Http/Controllers/Api/Auth/ForgotPasswordController.php tests/Feature/Api/ForgotPasswordOtpCooldownTest.php
```

## Menjalankan test

```bash
php artisan test tests/Feature/Generate/CvServiceTest.php
php artisan test tests/Feature/Api/ForgotPasswordOtpCooldownTest.php
```
