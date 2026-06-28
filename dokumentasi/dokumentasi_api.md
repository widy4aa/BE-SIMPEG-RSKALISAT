# Dokumentasi API BE-SIMPEG-RSKALISAT

Dokumentasi lengkap endpoint REST API untuk sistem informasi manajemen pegawai RS Kalisat. Dokumen ini mencakup seluruh endpoint yang tersedia beserta format request, validasi, dan contoh response.

---

## Daftar Isi

**BAB I — Pendahuluan**
1. [Format Response Standar](#format-response-standar)
2. [Authentication](#authentication)
3. [Status Sinkronisasi Endpoint](#status-sinkronisasi-endpoint)

**BAB II — Endpoint Umum (Tanpa Login)**
1. [Health Check](#1-health-check)  
2. [Login](#2-login)
3. [Request OTP Lupa Password](#3-request-otp-lupa-password)
4. [Reset Password dengan OTP](#4-reset-password-dengan-otp)

**BAB III — Endpoint Semua Role**
1. [Logout](#1-logout)
2. [Ganti Password (Saat Login)](#2-ganti-password-saat-login)
3. [Cek Role Login](#3-cek-role-login)
4. [Me — Identitas Login](#4-me--identitas-login)
5. [Dashboard](#5-dashboard)
  - [Response Dashboard Untuk Role Pegawai](#response-dashboard-untuk-role-pegawai)
  - [Response Dashboard Untuk Role Admin](#response-dashboard-untuk-role-admin)
  - [Response Dashboard Untuk Role HRD](#response-dashboard-untuk-role-hrd)
3. [Diklat](#5-diklat)
  - [Response Diklat Per Role](#response-diklat-per-role)
  - [GET Diklat (All - HRD & Direktur)](#get-diklat-all-hrd-direktur)
  - [Create Master Diklat (HRD)](#create-master-diklat-hrd)
  - [Get Peserta Diklat (HRD)](#get-peserta-diklat-hrd)
  - [Sync Peserta Diklat (HRD)](#sync-peserta-diklat-hrd)
  - [Get Diklat Menunggu Kelayakan (HRD)](#get-diklat-menunggu-kelayakan-hrd)
  - [Update Status Kelayakan (HRD)](#update-status-kelayakan-hrd)
  - [Get Diklat Menunggu Validasi (HRD)](#get-diklat-menunggu-validasi-hrd)
  - [Update Status Validasi (HRD)](#update-status-validasi-hrd)
  - [Create Diklat Pengguna](#create-diklat-pengguna)
  - [Edit Diklat Pengguna](#edit-diklat-pengguna)
  - [Upload Laporan Diklat Pegawai](#upload-laporan-diklat-pegawai)
  - [Delete Diklat Pengguna](#delete-diklat-pengguna)
  - [Generate Laporan Diklat (HRD)](#generate-laporan-diklat-hrd)
4. [Profile](#6-profile)
  - [Response Profile Untuk Role Pegawai](#response-profile-untuk-role-pegawai)
  - [Ajukan Perubahan Profile](#7-ajukan-perubahan-profile)
  - [Upload Foto Profile (Tanpa Approval)](#8-upload-foto-profile-tanpa-approval)
  - [Upload File KTP (Tanpa Approval)](#81-upload-file-ktp-tanpa-approval)
  - [Upload File KK (Tanpa Approval)](#82-upload-file-kk-tanpa-approval)
5. [Notifikasi](#9-notifikasi)
   - [List Notifikasi](#91-list-notifikasi)
   - [Tandai Dibaca](#92-tandai-1-notifikasi-sudah-dibaca)
   - [Tandai Semua Dibaca](#93-tandai-semua-notifikasi-sudah-dibaca)
6. [Riwayat Karir Pendidikan](#10-riwayat-karir-pendidikan)
  - [GET Riwayat Pendidikan](#get-riwayat-pendidikan)
  - [POST Riwayat Pendidikan](#post-riwayat-pendidikan)
  - [POST / PATCH Riwayat Pendidikan (Update)](#post-patch-riwayat-pendidikan-update)
  - [DELETE Riwayat Pendidikan](#delete-riwayat-pendidikan)
7. [Riwayat Karir Jabatan](#11-riwayat-karir-jabatan)
  - [GET Riwayat Jabatan](#get-riwayat-jabatan)
  - [POST Riwayat Jabatan](#post-riwayat-jabatan)
  - [POST / PATCH Riwayat Jabatan (Update)](#post-patch-riwayat-jabatan-update)
  - [DELETE Riwayat Jabatan](#delete-riwayat-jabatan)
8. [Riwayat Karir Pangkat](#12-riwayat-karir-pangkat)
  - [GET Riwayat Pangkat](#get-riwayat-pangkat)
  - [POST Riwayat Pangkat](#post-riwayat-pangkat)
  - [POST / PATCH Riwayat Pangkat (Update)](#post-patch-riwayat-pangkat-update)
  - [DELETE Riwayat Pangkat](#delete-riwayat-pangkat)
9. [Riwayat Karir SIP](#13-riwayat-karir-sip)
  - [GET Riwayat SIP](#get-riwayat-sip)
  - [POST Riwayat SIP](#post-riwayat-sip)
  - [POST / PATCH Riwayat SIP (Update)](#post-patch-riwayat-sip-update)
  - [DELETE Riwayat SIP](#delete-riwayat-sip)
10. [Riwayat Karir STR](#14-riwayat-karir-str)
  - [GET Riwayat STR](#get-riwayat-str)
  - [POST Riwayat STR](#post-riwayat-str)
  - [POST / PATCH Riwayat STR (Update)](#post-patch-riwayat-str-update)
  - [DELETE Riwayat STR](#delete-riwayat-str)
11. [Riwayat Karir Penugasan Klinis](#15-riwayat-karir-penugasan-klinis)
  - [GET Riwayat Penugasan Klinis](#get-riwayat-penugasan-klinis)
  - [POST Riwayat Penugasan Klinis](#post-riwayat-penugasan-klinis)
  - [POST / PATCH Riwayat Penugasan Klinis (Update)](#post-patch-riwayat-penugasan-klinis-update)
  - [DELETE Riwayat Penugasan Klinis](#delete-riwayat-penugasan-klinis)
12. [Data Keluarga](#16-data-keluarga)
    - [Ringkasan Data Keluarga](#1-get-ringkasan-data-keluarga)
    - [Modul Pasangan](#2-modul-pasangan)
     - [Get Data Pasangan](#a-get-data-pasangan)
     - [Tambah Data Pasangan](#b-tambah-data-pasangan)
     - [Ubah Data Pasangan](#c-ubah-data-pasangan)
     - [Hapus Data Pasangan](#d-hapus-data-pasangan)
    - [Modul Anak](#3-modul-anak)
     - [Get Data Anak](#a-get-data-anak)
     - [Tambah Data Anak](#b-tambah-data-anak)
     - [Ubah Data Anak](#c-ubah-data-anak)
     - [Hapus Data Anak](#d-hapus-data-anak)
    - [Modul Orang Tua](#4-modul-orang-tua)
     - [Get Data Orang Tua](#a-get-data-orang-tua)
     - [Tambah Data Orang Tua](#b-tambah-data-orang-tua)
     - [Ubah Data Orang Tua](#c-ubah-data-orang-tua)
     - [Hapus Data Orang Tua](#d-hapus-data-orang-tua)
    - [Modul Kontak Darurat](#5-modul-kontak-darurat)
     - [Get Data Kontak Darurat](#a-get-data-kontak-darurat)
     - [Tambah Data Kontak Darurat](#b-tambah-data-kontak-darurat)
     - [Ubah Data Kontak Darurat](#c-ubah-data-kontak-darurat)
     - [Hapus Data Kontak Darurat](#d-hapus-data-kontak-darurat)
    - [Modul Tanggungan Lain (Self-Service)](#6-modul-tanggungan-lain)
     - [Get Data Tanggungan Lain](#a-get-data-tanggungan-lain)
     - [Tambah Data Tanggungan Lain](#b-tambah-data-tanggungan-lain)
     - [Ubah Data Tanggungan Lain](#c-ubah-data-tanggungan-lain)
     - [Hapus Data Tanggungan Lain](#d-hapus-data-tanggungan-lain)
13. [Master Data (Form Dropdowns)](#17-master-data-form-dropdowns)
   - [List Master Data (Semua Role Login)](#171-list-master-data-semua-role-login)
   - [CRUD Master Data (Khusus HRD)](#172-crud-master-data-khusus-hrd)
14. [Pegawai](#18-pegawai)
  - [Get Pegawai Detail (Admin/HRD/Direktur)](#get-pegawai-detail-adminhrddirektur)
  - [Get Pegawai Detail Per Bagian (Admin/HRD/Direktur)](#get-pegawai-detail-per-bagian-adminhrddirektur)
   - [Tambah Data Pegawai Baru (Hanya Admin)](#tambah-data-pegawai-baru-hanya-admin)
   - [Ubah Role / Status Pegawai (Hanya Admin)](#ubah-role-status-pegawai-hanya-admin)
   - [Ubah NIK Sendiri (Hanya Admin)](#ubah-nik-sendiri-hanya-admin)
15. [STR/SIP (Admin/HRD/Direktur)](#19-strsip-adminhrddirektur)
16. [Generate CV](#20-generate-cv)
17. [HRD Manajemen Data Pegawai](#21-hrd-manajemen-data-pegawai)
   - [21.4 Riwayat Karir Pegawai (HRD)](#214-riwayat-karir-pegawai-hrd) *(Jabatan, STR, SIP, Penugasan Klinis, Pangkat, Pendidikan)*
   - [21.5 Reminder WhatsApp STR/SIP & Penugasan Klinis](#215-reminder-whatsapp-strsip--penugasan-klinis-hrd)
18. [Kirim Pesan WhatsApp ke Pegawai](#22-kirim-pesan-whatsapp-ke-pegawai)

**BAB IV — Ringkasan Endpoint Per Role**
1. [Admin](#admin) *(termasuk Admin Approval Change Request)*
  - [Admin Approval Change Request](#admin-approval-change-request)
    - [List Change Request](#10-list-change-request-admin)
    - [Detail Change Request](#11-detail-change-request-admin)
    - [Accept Change Request](#12-accept-change-request-admin)
    - [Reject Change Request](#13-reject-change-request-admin)
2. [Pegawai](#pegawai)
3. [HRD](#hrd)
4. [Direktur](#direktur)

**BAB V — Data Uji & Simulasi**
1. [Akun Seeder](#akun-seeder-untuk-uji-login)
2. [Quick Test via cURL](#quick-test-via-curl)

**BAB VI — Postman**
1. [Postman Collection](#postman-collection)
2. [Daftar Request di Collection](#daftar-request-di-collection)

## Format Response Standar

### Sukses

```json
{
  "success": true,
  "message": "Pesan sukses",
  "data": {}
}
```

### Gagal

```json
{
  "success": false,
  "message": "Pesan error"
}
```

## Authentication

Endpoint yang dilindungi middleware JWT wajib mengirim header:

```http
Authorization: Bearer <jwt_token>
```

## Status Sinkronisasi Endpoint

Dokumen ini sudah dicocokkan ulang dengan hasil `php artisan route:list --path=api` pada code saat ini. Total route API aktif: **178 route** (119 route lama + 55 HRD manajemen pegawai + 3 pesan & reminder WA + 1 detail bagian pegawai).

Catatan umum syarat akses:

- Endpoint public tanpa token: `GET /api/health`, `POST /api/login`
- Endpoint dengan token saja: `GET /api/notifications`, `PATCH /api/notifications/{id}/read`, `PATCH /api/notifications/read-all`, semua `GET /api/form/*`
- Endpoint semua role (`admin`, `pegawai`, `hrd`, `direktur`): `GET /api/me`, `GET /api/role`, `GET /api/dashboard`, `GET /api/diklat`, `GET /api/generate/cv`, `GET/PATCH /api/profile`, upload file profile/KTP/KK, semua CRUD keluarga, semua CRUD riwayat karir
- Endpoint `admin`, `hrd`, `direktur`: `GET /api/pegawai`, `GET /api/pegawai/{id}`, `GET /api/pegawai/{id}/{bagian}`, `GET /api/str-sip`, `POST /api/pesan/pegawai/{id}`
- Endpoint `pegawai`, `hrd`, `direktur`: `POST /api/diklat`, `PATCH /api/diklat/{id}`, `DELETE /api/diklat/{id}`, `POST /api/diklat/{id}/upload-laporan`
- Endpoint `hrd`, `direktur`: `GET /api/diklat/all`
- Endpoint khusus `admin`: `POST /api/pegawai`, `PATCH /api/pegawai/{id}/change-role`, `PATCH /api/auth/change-nik`, semua `/api/admin/change-requests/*`
- Endpoint khusus `hrd`: `POST/PATCH/DELETE /api/form/*`, semua `/api/hrd/diklat/*`, `GET /api/generate/laporan-diklat`
- Endpoint khusus `hrd` (manajemen pegawai): `PATCH|POST /api/hrd/pegawai/{id}/inti`, `PATCH|POST /api/hrd/pegawai/{id}/pribadi`, semua `GET|POST|PATCH|DELETE /api/hrd/pegawai/{id}/keluarga/*`, semua `GET|POST|PATCH|DELETE /api/hrd/pegawai/{id}/riwayat-karir/*`, `POST /api/hrd/pegawai/{id}/reminder/str-sip`, `POST /api/hrd/pegawai/{id}/reminder/penugasan-klinis`

Untuk request dengan file, gunakan `multipart/form-data`. Untuk request tanpa file, gunakan `application/json`.

## Endpoint Umum (Tanpa Login)

### 1. Health Check

- Method: `GET`
- URL: `/api/health`
- Auth: Tidak perlu

Contoh response `200 OK`:

```json
{
  "success": true,
  "message": "API is running",
  "data": {
    "status": "up"
  }
}
```

### 2. Login

- Method: `POST`
- URL: `/api/login`
- Auth: Tidak perlu

Request body:

```json
{
  "nik": "3174010101010099",
  "password": "password"
}
```

Validasi request:

- `nik`: wajib, string, maksimal 30 karakter
- `password`: wajib, string, minimal 6 karakter

Contoh response `200 OK`:

```json
{
  "success": true,
  "message": "Login berhasil.",
  "data": {
    "token_type": "Bearer",
    "access_token": "<jwt_token>",
    "expires_in": 43200,
    "user": {
      "id": 1,
      "nik": "3174010101010099",
      "role": "admin",
      "nama": "Admin SIMPEG"
    }
  }
}
```

Contoh response `422 Unprocessable Entity` (validasi gagal):

```json
{
  "success": false,
  "message": "Validasi gagal.",
  "errors": {
    "nik": [
      "NIK wajib diisi."
    ],
    "password": [
      "Password wajib diisi."
    ]
  }
}
```

Contoh response `401 Unauthorized` (kredensial salah):

```json
{
  "success": false,
  "message": "NIK atau password tidak valid."
}
```

Contoh response `403 Forbidden` (akun tidak aktif):

```json
{
  "success": false,
  "message": "Akun tidak aktif. Silakan hubungi admin."
}
```

### 3. Request OTP Lupa Password

- Method: `POST`
- URL: `/api/forgot-password/request-otp`
- Auth: Tidak perlu

Digunakan untuk meminta kode OTP via WhatsApp guna mereset password.

Request body:

```json
{
  "nik": "3509121234567890"
}
```

Contoh response `200 OK`:

```json
{
  "success": true,
  "message": "OTP berhasil dikirim ke nomor WhatsApp Anda."
}
```

Contoh response `404 Not Found` (NIK tidak ditemukan):

```json
{
  "success": false,
  "message": "User dengan NIK tersebut tidak ditemukan."
}
```

Contoh response `400 Bad Request` (No HP belum didaftarkan):

```json
{
  "success": false,
  "message": "Nomor telepon belum terdaftar. Silakan hubungi admin."
}
```

Contoh response `429 Too Many Requests` (Meminta OTP kurang dari 60 detik):

```json
{
  "success": false,
  "message": "Harap tunggu 45 detik sebelum meminta OTP lagi."
}
```

### 4. Reset Password dengan OTP

- Method: `POST`
- URL: `/api/forgot-password/reset`
- Auth: Tidak perlu

Digunakan untuk mereset password berdasarkan OTP yang telah diterima dari WhatsApp.

Request body:

```json
{
  "nik": "3509121234567890",
  "otp": "123456",
  "password": "newpassword123",
  "password_confirmation": "newpassword123"
}
```

Validasi:
- `nik`: string, wajib.
- `otp`: string, panjang harus tepat 6 karakter.
- `password`: string, minimal 6 karakter, dikonfirmasi dengan `password_confirmation`.

Contoh response `200 OK`:

```json
{
  "success": true,
  "message": "Password berhasil diubah. Silakan login menggunakan password baru Anda."
}
```

Contoh response `400 Bad Request` (OTP salah / kadaluarsa):

```json
{
  "success": false,
  "message": "Kode OTP tidak valid atau sudah kadaluarsa."
}
```

## Endpoint Semua Role (Login Required)

Endpoint berikut bisa dipakai oleh role `admin`, `pegawai`, `hrd`, dan `direktur`. Wajib menyertakan token JWT.

### 1. Logout

- Method: `POST`
- URL: `/api/logout`
- Auth: Wajib Bearer token
- Role yang diizinkan: `admin`, `pegawai`, `hrd`, `direktur`

JWT bersifat stateless — tidak ada server-side blacklist. Client wajib menghapus token setelah endpoint ini dipanggil.

Contoh response `200 OK`:

```json
{
  "success": true,
  "message": "Logout berhasil. Silakan hapus token di sisi client."
}
```

---

### 2. Ganti Password (Saat Login)

- Method: `POST`
- URL: `/api/auth/change-password`
- Auth: Wajib Bearer token
- Role yang diizinkan: `admin`, `pegawai`, `hrd`, `direktur`

Request body (`application/json`):

```json
{
  "password_lama": "passwordLama123",
  "password_baru": "passwordBaru456",
  "password_baru_confirmation": "passwordBaru456"
}
```

| Field | Tipe | Wajib | Keterangan |
|-------|------|-------|------------|
| `password_lama` | String | Ya | Password yang sedang aktif |
| `password_baru` | String | Ya | Password baru, min 8 karakter |
| `password_baru_confirmation` | String | Ya | Konfirmasi password baru, harus sama |

Contoh response `200 OK`:

```json
{
  "success": true,
  "message": "Password berhasil diubah."
}
```

Contoh response `422 Unprocessable Entity` (password lama salah):

```json
{
  "success": false,
  "message": "Password lama tidak sesuai."
}
```

Contoh response `404 Not Found` (user tidak ditemukan):

```json
{
  "success": false,
  "message": "User tidak ditemukan."
}
```

---

### 3. Cek Role Login

- Method: `GET`
- URL: `/api/role`
- Auth: Wajib Bearer token
- Role yang diizinkan: `admin`, `pegawai`, `hrd`, `direktur`

Contoh header:

```http
Authorization: Bearer <jwt_token>
```

Contoh response `200 OK`:

```json
{
  "success": true,
  "message": "Selamat datang admin.",
  "data": {
    "role": "admin"
  }
}
```

Kemungkinan response berdasarkan role:

- `admin` -> `Selamat datang admin.`
- `pegawai` -> `Selamat datang pegawai.`
- `hrd` -> `Selamat datang hrd.`
- `direktur` -> `Selamat datang direktur.`

Contoh response `401 Unauthorized` (token tidak valid/tidak ada):

```json
{
  "success": false,
  "message": "Access denied."
}
```

Contoh response `403 Forbidden` (role tidak diizinkan):

```json
{
  "success": false,
  "message": "Access denied."
}
```

### 4. Me — Identitas Login

- Method: `GET`
- URL: `/api/me`
- Auth: Wajib Bearer token
- Role yang diizinkan: `admin`, `pegawai`, `hrd`, `direktur`

Mengambil ringkasan identitas user yang sedang login: nama, NIK, foto profil, dan role. Cocok digunakan untuk header/navbar aplikasi frontend.

Contoh response `200 OK`:

```json
{
  "success": true,
  "message": "OK",
  "data": {
    "nama": "Budi Santoso",
    "nik": "3174010101010001",
    "foto_profil": "/dokumen/foto/profile-1-1782360593.jpg",
    "role": "pegawai"
  }
}
```

Keterangan field:

| Field | Tipe | Keterangan |
|-------|------|------------|
| `nama` | string \| null | Nama lengkap pegawai |
| `nik` | string \| null | NIK pegawai |
| `foto_profil` | string \| null | URL foto profil (relatif dari root), `null` jika belum diunggah |
| `role` | string | Role aktif dari JWT: `admin`, `pegawai`, `hrd`, atau `direktur` |

Contoh response `404 Not Found` (data pegawai belum tersedia):

```json
{
  "success": false,
  "message": "Data pegawai tidak ditemukan."
}
```

---

### 5. Dashboard

- Method: `GET`
- URL: `/api/dashboard`
- Auth: Wajib Bearer token
- Role yang diizinkan: `admin`, `pegawai`, `hrd`, `direktur`
- Parameter URL (Opsional, khusus role HRD dan Direktur): `?type=pegawai`, `?type=diklat_asn`, atau `?type=diklat_tenkes`

Contoh header:

```http
Authorization: Bearer <jwt_token>
```

#### Response Dashboard Untuk Role Pegawai

Contoh response `200 OK`:

```json
{
  "success": true,
  "message": "Selamat datang pegawai",
  "data": {
    "role": "pegawai",
    "dashboard": {
      "label": "Dashboard pegawai",
      "nama": "Budi Santoso",
      "nip": "198901012010011001",
      "jabatan": "Staf Kepegawaian",
      "jenis_jabatan": "PNS",
      "unit_kerja": "SDM",
      "jumlah_diklat_selesai": 1,
      "jumlah_diklat_dijadwalkan_belum_selesai": 3,
      "list_jadwal_diklat_mendatang": [
        {
          "jadwal_id": 1,
          "status_diklat": "belum terlaksana",
          "nama_kegiatan": "Diklat Manajemen SDM Dasar",
          "penyelenggara": "Bagian SDM RS Kalisat",
          "tanggal_mulai": "2026-06-10",
          "tanggal_selesai": "2026-06-12",
          "tempat": "Aula RS Kalisat",
          "waktu": "08:00:00"
        }
      ],
      "list_aksi": [
        {
          "id": 10,
          "action_code": "str_will_expire",
          "title": "STR akan segera kadaluarsa",
          "message": "STR Anda akan kadaluarsa dalam waktu dekat. Segera lakukan perpanjangan.",
          "action_payload": {
            "status_lengkap": true,
            "sisa_hari": 20,
            "keterangan": [
              "STR aktif"
            ]
          },
          "is_read": false,
          "is_resolved": false,
          "created_at": "2026-04-18 09:30:00"
        }
      ]
    }
  }
}
```

Keterangan field dashboard pegawai:

- `jumlah_diklat_selesai`: jumlah diklat dengan status `sudah terlaksana`.
- `jumlah_diklat_dijadwalkan_belum_selesai`: jumlah diklat dengan status `belum terlaksana` atau `sedang terlaksana`.
- `list_jadwal_diklat_mendatang`: list diklat yang statusnya `belum terlaksana`.
- `list_aksi`: daftar notifikasi bertipe `action` yang belum `is_resolved` (memberikan peringatan/info jika data pegawai belum lengkap).
- `list_aksi.action_payload`: detail data aksi, misalnya status STR, kelengkapan keluarga, atau kelengkapan profil pribadi (`keterangan` berisi daftar kolom/dokumen yang belum diisi seperti KTP, KK, profesi, NIK/NIP, dll).

Contoh response `401 Unauthorized` (token tidak valid/tidak ada):

```json
{
  "success": false,
  "message": "Access denied."
}
```

Contoh response `403 Forbidden` (role tidak diizinkan):

```json
{
  "success": false,
  "message": "Access denied."
}
```

#### Response Dashboard Untuk Role Admin

Contoh response `200 OK`:

```json
{
  "success": true,
  "message": "Selamat datang admin",
  "data": {
    "role": "admin",
    "dashboard": {
      "label": "Dashboard admin",
      "jumlah_pegawai": 150,
      "jumlah_pegawai_aktif": 145,
      "jumlah_permintaan_update_data": 20,
      "jumlah_permintaan_disetujui": 15
    }
  }
}
```

#### Response Dashboard Untuk Role Direktur

Struktur response direktur identik dengan HRD. Parameter `?type` juga didukung.

Contoh response `200 OK`:

```json
{
  "success": true,
  "message": "Selamat datang direktur",
  "data": {
    "role": "direktur",
    "dashboard": {
      "label": "Dashboard direktur",
      "pegawai": {
        "total_pegawai": 15,
        "total_pegawai_kurang_lengkap": 1,
        "total_pegawai_lengkap": 14,
        "jenis_pegawai": {
          "PNS": 7,
          "BLUD": 3
        },
        "profesi": {
          "Dokter": 4,
          "Perawat": 1
        },
        "tingkat_pendidikan": {
          "S1/D4": 5,
          "S2": 5
        },
        "tahun_masuk_5_tahun_terakhir": {
          "2022": 0,
          "2023": 1,
          "2024": 0,
          "2025": 0,
          "2026": 0
        }
      },
      "diklat_asn": {
        "total_diklat": 12,
        "selesai": 8,
        "berlangsung": 4,
        "pegawai_sudah_ikut": 10,
        "pegawai_belum_ikut": 5,
        "diklat_per_kategori": {
          "Struktural": 2,
          "Fungsional": 3,
          "Teknis": 5,
          "Akred": 2
        }
      },
      "diklat_tenkes": {
        "total_diklat": 20,
        "selesai": 15,
        "berlangsung": 5,
        "pegawai_sudah_ikut": 12,
        "pegawai_belum_ikut": 3,
        "diklat_per_kategori": {
          "Struktural": 0,
          "Fungsional": 8,
          "Teknis": 10,
          "Akred": 2
        }
      }
    }
  }
}
```

#### Response Dashboard Untuk Role HRD

Contoh response `200 OK`:

```json
{
  "success": true,
  "message": "Selamat datang hrd",
  "data": {
    "role": "hrd",
    "dashboard": {
      "label": "Dashboard hrd",
      "pegawai": {
        "total_pegawai": 15,
        "total_pegawai_kurang_lengkap": 1,
        "total_pegawai_lengkap": 14,
        "jenis_pegawai": {
          "PNS": 7,
          "BLUD": 3
        },
        "profesi": {
          "Dokter": 4,
          "Perawat": 1
        },
        "tingkat_pendidikan": {
          "S1/D4": 5,
          "S2": 5
        },
        "tahun_masuk_5_tahun_terakhir": {
          "2022": 0,
          "2023": 1,
          "2024": 0,
          "2025": 0,
          "2026": 0
        }
      },
      "diklat_asn": {
        "total_diklat": 12,
        "selesai": 8,
        "berlangsung": 4,
        "pegawai_sudah_ikut": 10,
        "pegawai_belum_ikut": 5,
        "diklat_per_kategori": {
          "Struktural": 2,
          "Fungsional": 3,
          "Teknis": 5,
          "Akred": 2
        }
      },
      "diklat_tenkes": {
        "total_diklat": 20,
        "selesai": 15,
        "berlangsung": 5,
        "pegawai_sudah_ikut": 12,
        "pegawai_belum_ikut": 3,
        "diklat_per_kategori": {
          "Struktural": 0,
          "Fungsional": 8,
          "Teknis": 10,
          "Akred": 2
        }
      }
    }
  }
}
```

Catatan indikator kelengkapan data pegawai (`total_pegawai_kurang_lengkap` dan `total_pegawai_lengkap`):
Seorang pegawai dihitung **lengkap** apabila telah melengkapi parameter Data Inti (`nik`/`nip`, `jenis_pegawai_id`, `profesi_id`, `tgl_masuk`) serta Data Pribadi (`tanggal_lahir`, `jenis_kelamin`, `agama`, `alamat`, `no_telp`, `pendidikan_terakhir`, dokumen KTP `ktp_file_path`, dan dokumen Kartu Keluarga `kk_file_path`).

### 5. Diklat

- Method: `GET`
- URL: `/api/diklat`
- Auth: Wajib Bearer token
- Role yang diizinkan: `admin`, `pegawai`, `hrd`, `direktur`

Query parameter opsional untuk riwayat diklat role `pegawai` dan `hrd`:

| Parameter | Type | Default | Keterangan |
|-----------|------|---------|------------|
| `page` | Integer | `1` | Halaman yang diminta. |
| `per_page` | Integer | `7` | Jumlah data per halaman. Nilai dibatasi maksimal 100. |
| `search` | String | - | Cari berdasarkan `nama_kegiatan`, `penyelenggara`, nama kategori, atau nama jenis diklat. |
| `jenis` | String | - | Filter berdasarkan nama jenis diklat, contoh `ASN` atau `Tenaga Kesehatan`. |
| `status` | String | - | Filter status tanggal: `mendatang`, `berlangsung`, atau `selesai`. |

Contoh URL dengan filter:

```http
GET /api/diklat?page=1&per_page=7&search=pelatihan&jenis=ASN&status=berlangsung
```

Contoh header:

```http
Authorization: Bearer <jwt_token>
```

#### Response Diklat Per Role

Keterangan implementasi saat ini:

- Role `pegawai`: data diambil dari database melalui repository.
- Role `admin`: payload ringkasan tersendiri.
- Role `hrd`: data diambil dari database berdasarkan peserta (hanya diklat yang diikuti HRD login).
- Role `direktur`: data diambil dari database melalui `DirekturDashboardRepository`. Menampilkan ringkasan agregat (`total_diklat`, `selesai`, `berlangsung`, `mendatang`) dan daftar semua master diklat yang dapat difilter.

Contoh response role `pegawai` (dengan pagination 7 item):

```json
{
  "success": true,
  "message": "Daftar diklat pegawai berhasil diambil.",
  "data": {
    "role": "pegawai",
    "diklat": {
      "label": "Diklat pegawai",
      "ringkasan": {
        "total_riwayat": 6,
        "selesai": 4,
        "akan_datang": 2
      },
      "riwayat_diklat": {
        "current_page": 1,
        "data": [
          {
            "id": 12,
            "nama": "Pelatihan Komunikasi Efektif",
            "kategori": "Soft Skill",
            "jenis": "Workshop",
            "pelaksana": "RS Kalisat",
            "tanggal_mulai": "2025-11-15",
            "tanggal_selesai": "2025-11-17",
            "status": "selesai",
            "tempat": "Aula Utama",
            "waktu": "08:00:00",
            "created_by": "Admin SIMPEG",
            "jp": 24,
            "total_biaya": 250000,
            "jenis_biaya": "Mandiri",
            "jenis_pelaksana": "internal",
            "catatan": "Workshop peningkatan komunikasi lintas unit.",
            "sertif_file_path": "dokumen/sertif-diklat/budi-audit-internal.pdf",
            "no_sertif": "SERTIF/SDM/2026/0001",
            "status_validasi": "sudah di validasi",
            "uploadlaporan": false
          }
        ],
        "first_page_url": "http://localhost:8000/api/diklat?page=1",
        "from": 1,
        "last_page": 1,
        "last_page_url": "http://localhost:8000/api/diklat?page=1",
        "links": [
          {
            "url": null,
            "label": "&laquo; Previous",
            "active": false
          },
          {
            "url": "http://localhost:8000/api/diklat?page=1",
            "label": "1",
            "active": true
          },
          {
            "url": null,
            "label": "Next &raquo;",
            "active": false
          }
        ],
        "next_page_url": null,
        "path": "http://localhost:8000/api/diklat",
        "per_page": 7,
        "prev_page_url": null,
        "to": 1,
        "total": 1
      }
    }
  }
}
```


Keterangan field `riwayat_diklat` (role `pegawai`):

- `nama`: nama diklat.
- `kategori`: kategori diklat.
- `jenis`: jenis diklat.
- `pelaksana`: penyelenggara diklat.
- `tanggal_mulai`: tanggal mulai format `Y-m-d`.
- `tanggal_selesai`: tanggal selesai format `Y-m-d`.
- `status`: status berdasarkan tanggal (`mendatang`, `berlangsung`, `selesai`).
- `tempat`: lokasi diklat.
- `waktu`: jam/waktu pelaksanaan.
- `created_by`: nama pembuat data.
- `jp`: jumlah jam pelatihan.
- `total_biaya`: nominal total biaya.
- `jenis_biaya`: referensi jenis biaya.
- `jenis_pelaksana`: `internal` atau `external`.
- `catatan`: catatan tambahan diklat.
- `sertif_file_path`: path file sertifikat diklat.
- `no_sertif`: nomor sertifikat diklat.
- `status_validasi`: status validasi khusus untuk diklat `internal`.
- `uploadlaporan`: boolean penanda apakah user masih perlu/boleh upload laporan/sertifikat diklat.

Untuk role `hrd`, field di `riwayat_diklat` mengikuti struktur yang sama dengan `riwayat_diklat` (role `pegawai`).

Catatan filter:

- `search` menerapkan pencarian pada tabel master diklat melalui relasi `diklat`.
- `jenis` memfilter relasi `jenis_diklat.nama`.
- `status` dihitung dari kolom tanggal pada tabel `diklat`, bukan dari teks status yang sudah dimapping di response.

Aturan hitung `status`:

- `mendatang`: `tanggal_mulai > hari_ini`
- `berlangsung`: `tanggal_mulai <= hari_ini` dan `tanggal_selesai >= hari_ini`
- `selesai`: `tanggal_selesai < hari_ini`

Aturan hitung `status_validasi` (hanya ada jika `jenis_pelaksana` bernilai `internal`, jika `external` maka `null`):

- `Upload laporan`: jika belum mengunggah sertifikat (`sertif_file_path` null)
- `menunggu validasi`: jika sertifikat sudah diunggah tapi status validasi di database masih null
- `di tolak`: jika status validasi di database adalah `tidak valid`
- `diklat valid`: jika status validasi di database adalah `valid`

Aturan `uploadlaporan`:

- Untuk diklat `external`: `true` jika `sertif_file_path` atau `no_sertif` masih kosong; `false` jika keduanya sudah terisi.
- Untuk diklat `internal`: `true` jika `sertif_file_path` atau `no_sertif` masih kosong, atau `status_validasi` database masih null/`pending`/`tidak valid` (`di tolak`); `false` jika laporan lengkap dan `status_validasi` sudah `valid`.
- **Aturan Tambahan Wajib**: Jika status pelaksanaan diklat (`status`) **belum selesai** (masih `mendatang` atau `berlangsung`), maka `uploadlaporan` akan **selalu `false`** (tidak boleh upload laporan sebelum diklat selesai).

Catatan bentuk payload:

- `admin`: `ringkasan` + `list_diklat`
- `pegawai`: `ringkasan` + `riwayat_diklat`
- `hrd`: `ringkasan` + `riwayat_diklat` (berisi riwayat diklat peserta HRD login)
- `direktur`: `ringkasan` (total_diklat, selesai, berlangsung, mendatang) + `list_diklat` (paginated, dapat difilter)

Contoh response role `direktur`:

```json
{
  "success": true,
  "message": "Ringkasan diklat direktur berhasil diambil.",
  "data": {
    "role": "direktur",
    "diklat": {
      "label": "Diklat direktur",
      "ringkasan": {
        "total_diklat": 32,
        "selesai": 23,
        "berlangsung": 4,
        "mendatang": 5
      },
      "list_diklat": {
        "current_page": 1,
        "data": [
          {
            "id_diklat": 12,
            "nama": "Workshop Pelayanan Prima",
            "kategori": "Teknis",
            "jenis": "ASN",
            "pelaksana": "RS Kalisat",
            "tanggal_mulai": "2026-05-10",
            "tanggal_selesai": "2026-05-12",
            "status": "selesai",
            "tempat": "Aula RS",
            "waktu": "08:00:00",
            "created_by": "Admin SIMPEG",
            "jp": 24,
            "total_biaya": "2500000.00",
            "jenis_biaya": "BLUD",
            "jenis_pelaksana": "internal",
            "catatan": "Usulan pelatihan unit SDM",
            "jumlah_peserta": 5
          }
        ],
        "per_page": 7,
        "total": 32
      }
    }
  }
}
```

Catatan field `catatan`:

- Untuk role `pegawai`, `catatan` berada di setiap item `riwayat_diklat`.
- Untuk role `admin` dan `direktur`, `catatan` juga berada di setiap item list sesuai role.

Catatan field `status`:

- Status hitung by tanggal (`mendatang`, `berlangsung`, `selesai`) diterapkan pada item role `pegawai`, `hrd`, dan `direktur`.
- Item role `admin` saat ini belum menggunakan field `status`.

#### GET Diklat (All - HRD & Direktur)

- Method: `GET`
- URL: `/api/diklat/all`
- Auth: Wajib Bearer token
- Role yang diizinkan: `hrd`, `direktur`

Endpoint ini menampilkan data diklat beserta atributnya untuk role HRD dan Direktur. Secara default endpoint ini hanya menampilkan diklat `internal`. Untuk menampilkan diklat `external`, kirim query `jenis_pelaksana=external` atau `jenis_pelaksanaan=external`.

Query parameter opsional:

| Parameter | Type | Default | Keterangan |
|-----------|------|---------|------------|
| `page` | Integer | `1` | Halaman yang diminta. |
| `per_page` | Integer | `7` | Jumlah data per halaman. Nilai dibatasi maksimal 100. |
| `search` | String | - | Cari berdasarkan `nama_kegiatan`, `penyelenggara`, nama kategori, atau nama jenis diklat. |
| `jenis` | String | - | Filter berdasarkan nama jenis diklat, contoh `ASN` atau `Tenaga Kesehatan`. |
| `jenis_pelaksana` / `jenis_pelaksanaan` | String | `internal` | Filter jenis pelaksanaan. Nilai valid: `internal` atau `external`. Jika tidak dikirim, default `internal`. |

Contoh URL dengan filter:

```http
GET /api/diklat/all?page=1&per_page=7&search=workshop&jenis=ASN
GET /api/diklat/all?page=1&per_page=7&jenis_pelaksana=external
```

Contoh response `200 OK` (dengan pagination 7 item):

```json
{
  "success": true,
  "message": "Data semua diklat berhasil diambil.",
  "data": {
    "current_page": 1,
    "data": [
      {
        "id_diklat": 12,
        "nama": "Workshop Pelayanan Prima",
        "kategori": "Teknis",
        "jenis": "ASN",
        "pelaksana": "RS Kalisat",
        "tanggal_mulai": "2026-05-10",
        "tanggal_selesai": "2026-05-12",
        "status": "mendatang",
        "tempat": "Aula RS",
        "waktu": "08:00:00",
        "created_by": "Admin SIMPEG",
        "jp": 24,
        "total_biaya": "2500000.00",
        "jenis_biaya": "BLUD",
        "jenis_pelaksana": "internal",
        "catatan": "Usulan pelatihan unit SDM",
        "jumlah_peserta": 5,
        "jumlah_peserta_sudah_validasi": 3,
        "jumlah_peserta_belum_validasi": 2
      }
    ],
    "first_page_url": "http://localhost:8000/api/diklat/all?page=1",
    "from": 1,
    "last_page": 1,
    "last_page_url": "http://localhost:8000/api/diklat/all?page=1",
    "links": [
      {
        "url": null,
        "label": "&laquo; Previous",
        "active": false
      },
      {
        "url": "http://localhost:8000/api/diklat/all?page=1",
        "label": "1",
        "active": true
      },
      {
        "url": null,
        "label": "Next &raquo;",
        "active": false
      }
    ],
    "next_page_url": null,
    "path": "http://localhost:8000/api/diklat/all",
    "per_page": 7,
    "prev_page_url": null,
    "to": 1,
    "total": 1
  }
}
```

Keterangan field `data` (di dalam pagination):

- `id`: ID master diklat.
- `nama`: nama kegiatan.
- `kategori`: kategori diklat.
- `jenis`: jenis diklat.
- `pelaksana`: penyelenggara.
- `tanggal_mulai`: tanggal mulai format `Y-m-d`.
- `tanggal_selesai`: tanggal selesai format `Y-m-d`.
- `status`: status diklat berdasarkan tanggal mulai dan selesai.
- `tempat`: lokasi.
- `waktu`: jam pelaksanaan.
- `created_by`: nama pembuat data.
- `jp`: jumlah jam pelatihan.
- `total_biaya`: nominal total biaya.
- `jenis_biaya`: referensi jenis biaya.
- `jenis_pelaksana`: `internal` atau `external`.
- `catatan`: catatan tambahan.
- `jumlah_peserta`: jumlah pegawai yang terdaftar sebagai peserta pada jadwal diklat.
- `jumlah_peserta_sudah_validasi`: jumlah peserta yang `status_validasi`-nya sudah terisi, baik `valid` maupun `tidak valid`.
- `jumlah_peserta_belum_validasi`: jumlah peserta yang `status_validasi`-nya masih `null` atau kosong.

Catatan status validasi peserta:

- `valid`: peserta sudah divalidasi dan diterima.
- `tidak valid`: peserta sudah divalidasi tetapi ditolak.
- `null` atau kosong: peserta belum divalidasi.

#### Create Master Diklat (HRD)

- Method: `POST`
- URL: `/api/hrd/diklat`
- Auth: Wajib Bearer token
- Role yang diizinkan: `hrd`
- Content-Type: `application/json` atau `multipart/form-data`

Endpoint ini digunakan oleh HRD untuk menambahkan data master diklat ke dalam sistem tanpa mendaftarkan peserta (tidak membuat data di `list_jadwal_diklat`).

Field request:

- `nama_kegiatan` (required, string, max 255)
- `kategori` (required, string, max 100)
- `jenis_diklat` (required, string, max 100)
- `penyelenggara` (required, string, max 255)
- `lokasi` (required, string, max 255)
- `tanggal_mulai` (required, date)
- `tanggal_selesai` (required, date, harus sama atau setelah `tanggal_mulai`)
- `waktu` (nullable, string, format: `HH:MM` atau `HH:MM:SS`)
- `jp` (required, integer, min 1)
- `jenis_biaya` (required jika `jenis_pelaksana=internal`, nullable, string, max 100)
- `total_biaya` (required jika `jenis_pelaksana=internal`, nullable, numeric, min 0)
- `catatan` (nullable, string, max 1000)
- `jenis_pelaksana` (required: wajib `internal`, tidak bisa `external`)

Contoh request payload (JSON):

```json
{
  "nama_kegiatan": "Workshop Kepemimpinan",
  "kategori": "Manajemen",
  "jenis_diklat": "Workshop",
  "penyelenggara": "RS Kalisat",
  "lokasi": "Aula RS",
  "tanggal_mulai": "2026-08-10",
  "tanggal_selesai": "2026-08-12",
  "waktu": "08:00:00",
  "jp": 24,
  "jenis_biaya": "BLUD",
  "total_biaya": 1500000,
  "jenis_pelaksana": "internal",
  "catatan": "Diklat khusus manajerial"
}
```

Contoh response sukses (`201`):

```json
{
  "success": true,
  "message": "Master Diklat berhasil dibuat.",
  "data": {
    "id_diklat": 13,
    "nama_kegiatan": "Workshop Kepemimpinan",
    "kategori": "Manajemen",
    "jenis_diklat": "Workshop",
    "penyelenggara": "RS Kalisat",
    "lokasi": "Aula RS",
    "tanggal_mulai": "2026-08-10",
    "tanggal_selesai": "2026-08-12",
    "waktu": "08:00:00",
    "jp": 24,
    "jenis_biaya": "BLUD",
    "total_biaya": 1500000,
    "catatan": "Diklat khusus manajerial",
    "jenis_pelaksana": "internal"
  }
}
```

#### Edit Master Diklat (HRD)

- Method: `PUT`
- URL: `/api/hrd/diklat/{id}`
- Parameter URL: `id` (required, int) - ID dari Master Diklat
- Auth: Wajib Bearer token
- Role yang diizinkan: `hrd`
- Content-Type: `application/json` atau `multipart/form-data`

Endpoint ini digunakan oleh HRD untuk mengubah data master diklat.

Field request:

- `nama_kegiatan` (required, string, max 255)
- `kategori` (required, string, max 100)
- `jenis_diklat` (required, string, max 100)
- `penyelenggara` (required, string, max 255)
- `lokasi` (required, string, max 255)
- `tanggal_mulai` (required, date)
- `tanggal_selesai` (required, date, harus sama atau setelah `tanggal_mulai`)
- `waktu` (nullable, string, format: `HH:MM` atau `HH:MM:SS`)
- `jp` (required, integer, min 1)
- `jenis_biaya` (required jika `jenis_pelaksana=internal`, nullable, string, max 100)
- `total_biaya` (required jika `jenis_pelaksana=internal`, nullable, numeric, min 0)
- `catatan` (nullable, string, max 1000)
- `jenis_pelaksana` (required: wajib `internal`, tidak bisa `external`)

Contoh request payload (JSON):

```json
{
  "nama_kegiatan": "Workshop Kepemimpinan Update",
  "kategori": "Manajemen",
  "jenis_diklat": "Workshop",
  "penyelenggara": "RS Kalisat",
  "lokasi": "Aula RS",
  "tanggal_mulai": "2026-08-10",
  "tanggal_selesai": "2026-08-12",
  "waktu": "08:00:00",
  "jp": 24,
  "jenis_biaya": "BLUD",
  "total_biaya": 1500000,
  "jenis_pelaksana": "internal",
  "catatan": "Diklat khusus manajerial"
}
```

Contoh response sukses (`200`):

```json
{
  "success": true,
  "message": "Master Diklat berhasil diupdate.",
  "data": {
    "id_diklat": 13,
    "nama_kegiatan": "Workshop Kepemimpinan Update",
    "kategori": "Manajemen",
    "jenis_diklat": "Workshop",
    "penyelenggara": "RS Kalisat",
    "lokasi": "Aula RS",
    "tanggal_mulai": "2026-08-10",
    "tanggal_selesai": "2026-08-12",
    "waktu": "08:00:00",
    "jp": 24,
    "jenis_biaya": "BLUD",
    "total_biaya": 1500000,
    "catatan": "Diklat khusus manajerial",
    "jenis_pelaksana": "internal"
  }
}
```

#### Get Peserta Diklat (HRD)

- Method: `GET`
- URL: `/api/hrd/diklat/{id}/peserta`
- Parameter URL: `id` (required, int) - ID dari Master Diklat
- Parameter Query Opsional: `section` (string, contoh: `?section=all` atau `?section=semua_pegawai` untuk memunculkan seluruh pegawai di dalam array `list`)
- Auth: Wajib Bearer token
- Role yang diizinkan: `hrd`

Endpoint ini digunakan oleh HRD untuk melihat data peserta diklat. Secara default (`GET /api/hrd/diklat/{id}/peserta`), array `list` hanya menampilkan peserta yang sudah terdaftar (`status=true`) lengkap dengan `status_validasi` mereka. Jika frontend membutuhkan daftar seluruh pegawai di dalam array `list` (misalnya saat membuka modal checklist update/patch peserta), cukup tambahkan parameter query `?section=all`.

Contoh request:
`GET /api/hrd/diklat/13/peserta` atau `GET /api/hrd/diklat/13/peserta?section=all`

Contoh response sukses (`200 OK` untuk request default):

```json
{
  "success": true,
  "message": "Data peserta diklat berhasil diambil.",
  "data": {
    "diklat_id": 13,
    "total_peserta": 1,
    "total_pegawai": 2,
    "list": [
      {
        "pegawai_id": 1,
        "nama": "Budi Santoso",
        "nik": "350912345678",
        "unit_kerja": "IGD",
        "profesi": "Dokter Umum",
        "status": true,
        "status_validasi": "sudah di validasi"
      }
    ]
  }
}
```

Contoh response sukses (`200 OK` untuk request `GET /api/hrd/diklat/13/peserta?section=all`):

```json
{
  "success": true,
  "message": "Data peserta diklat berhasil diambil.",
  "data": {
    "diklat_id": 13,
    "total_peserta": 1,
    "total_pegawai": 150,
    "list": [
      {
        "pegawai_id": 1,
        "nama": "Budi Santoso",
        "nik": "350912345678",
        "unit_kerja": "IGD",
        "profesi": "Dokter Umum",
        "status": true,
        "status_validasi": "sudah di validasi"
      },
      {
        "pegawai_id": 2,
        "nama": "Siti Aminah",
        "nik": "350987654321",
        "unit_kerja": "Poli Gigi",
        "profesi": "Dokter Gigi",
        "status": false,
        "status_validasi": null
      }
    ]
  }
}
```

Keterangan `status_validasi` pada list peserta:

- `sudah di validasi`: peserta diklat internal sudah divalidasi dengan status database `valid`.
- `Validasi di tolak`: peserta diklat internal sudah divalidasi dengan status database `tidak valid`.
- `udah upload laporan namun belum di validasi`: peserta diklat internal sudah upload sertifikat/laporan tetapi belum divalidasi.
- `Belum upload laporan`: peserta diklat internal belum upload sertifikat/laporan.
- `None`: peserta diklat external.
- `null`: pegawai bukan peserta diklat tersebut (`status=false`).

#### Sync Peserta Diklat (HRD)

- Method: `POST`
- URL: `/api/hrd/diklat/{id}/peserta`
- Parameter URL: `id` (required, int) - ID dari Master Diklat
- Auth: Wajib Bearer token
- Role yang diizinkan: `hrd`
- Content-Type: `application/json`

Endpoint ini digunakan oleh HRD untuk menyimpan status checklist peserta. Frontend cukup mengirimkan daftar `pegawai_id` yang memiliki status `true` (di-checklist/mengikuti diklat). Sistem akan menghapus peserta yang tidak ada di list dan menambahkan peserta baru sesuai list dengan status kelayakan otomatis `layak` dan `status_diklat` otomatis menyesuaikan tanggal.

Field request:

- `pegawai_ids` (array of integers). Di code saat ini field ini tidak wajib; jika tidak dikirim akan diproses sebagai array kosong sehingga semua peserta pada diklat tersebut dapat terhapus.

Contoh request payload:

```json
{
  "pegawai_ids": [1, 5, 8]
}
```

Contoh response sukses (`200 OK`):

```json
{
  "success": true,
  "message": "Peserta diklat berhasil diperbarui.",
  "data": {
    "diklat_id": 13,
    "peserta_terdaftar": 3
  }
}
```

#### Get Diklat Menunggu Kelayakan (HRD)

- Method: `GET`
- URL: `/api/hrd/diklat/status/layak`
- Auth: Wajib Bearer token
- Role yang diizinkan: `hrd`

Endpoint ini digunakan oleh HRD untuk melihat daftar peserta diklat yang sudah mengunggah laporan/sertifikat (`sertif_file_path` tidak kosong) tetapi status kelayakannya belum ditentukan (`status_kelayakan` masih `null`). Response mencakup data diklat, list_jadwal_diklat, serta nama dan NIK pegawai.

Contoh response sukses (`200 OK`):

```json
{
  "success": true,
  "message": "Data diklat menunggu kelayakan berhasil diambil.",
  "data": {
    "total": 1,
    "list": [
      {
        "id_diklat": 1,
        "id_jadwal_diklat": 5,
        "nama": "Pelatihan Keselamatan Kerja",
        "kategori": "Wajib",
        "jenis": "Klinis",
        "pelaksana": "RS Kalisat",
        "tanggal_mulai": "2026-05-01",
        "tanggal_selesai": "2026-05-02",
        "status": "sudah terlaksana",
        "tempat": "Aula RS Kalisat",
        "waktu": null,
        "jp": 16,
        "total_biaya": null,
        "jenis_biaya": "",
        "jenis_pelaksana": "external",
        "catatan": "",
        "pegawai_id": 3,
        "pegawai_nama": "Dr. Siti",
        "pegawai_nik": "350912345678",
        "sertif_file_path": "uploads/sertifikat/1234.pdf",
        "no_sertif": "SRT/123/2026",
        "status_kelayakan": null,
        "status_validasi": null
      }
    ]
  }
}
```

#### Update Status Kelayakan (HRD)

- Method: `PATCH`
- URL: `/api/hrd/diklat/{id}/status/layak`
- Parameter URL: `id` (required, int) - ID dari `list_jadwal_diklat`
- Auth: Wajib Bearer token
- Role yang diizinkan: `hrd`
- Content-Type: `application/json`

Endpoint ini digunakan oleh HRD untuk mengubah status kelayakan peserta. Nilai boolean `true` akan disimpan sebagai `layak`, dan `false` menjadi `tidak layak`.

Aturan validasi:

- Untuk diklat `external`, HRD tidak bisa meng-approve kelayakan (`status_kelayakan: true`) jika `sertif_file_path` atau `no_sertif` masih kosong.
- Jika rule tersebut dilanggar, API mengembalikan `422` dengan message `belum upload laporan`.
- Reject kelayakan (`status_kelayakan: false`) tetap bisa dilakukan.

Field request:

- `status_kelayakan` (required, boolean)

Contoh request payload:

```json
{
  "status_kelayakan": true
}
```

Contoh response sukses (`200 OK`):

```json
{
  "success": true,
  "message": "Status kelayakan berhasil diperbarui.",
  "data": {
    "id_jadwal_diklat": 5,
    "diklat_id": 1,
    "pegawai_id": 3,
    "status_kelayakan": "layak"
  }
}
```

Contoh response gagal karena laporan belum lengkap (`422 Unprocessable Entity`):

```json
{
  "success": false,
  "message": "belum upload laporan"
}
```

#### Get Diklat Menunggu Validasi (HRD)

- Method: `GET`
- URL: `/api/hrd/diklat/status/validasi`
- Auth: Wajib Bearer token
- Role yang diizinkan: `hrd`

Endpoint ini digunakan oleh HRD untuk melihat daftar peserta diklat yang sudah mengunggah laporan/sertifikat (`sertif_file_path` tidak kosong) tetapi status validasinya belum ditentukan (`status_validasi` masih `null`).

Contoh response sukses (`200 OK`):

```json
{
  "success": true,
  "message": "Data diklat menunggu validasi berhasil diambil.",
  "data": {
    "total": 1,
    "list": [
      {
        "id_diklat": 1,
        "id_jadwal_diklat": 5,
        "nama": "Pelatihan Keselamatan Kerja",
        "kategori": "Wajib",
        "jenis": "Klinis",
        "pelaksana": "RS Kalisat",
        "tanggal_mulai": "2026-05-01",
        "tanggal_selesai": "2026-05-02",
        "status": "sudah terlaksana",
        "tempat": "Aula RS Kalisat",
        "waktu": null,
        "jp": 16,
        "total_biaya": null,
        "jenis_biaya": "",
        "jenis_pelaksana": "external",
        "catatan": "",
        "pegawai_id": 3,
        "pegawai_nama": "Dr. Siti",
        "pegawai_nik": "350912345678",
        "sertif_file_path": "uploads/sertifikat/1234.pdf",
        "no_sertif": "SRT/123/2026",
        "status_kelayakan": "layak",
        "status_validasi": "menunggu validasi"
      }
    ]
  }
}
```

#### Update Status Validasi (HRD)

- Method: `PATCH`
- URL: `/api/hrd/diklat/{id}/status/validasi`
- Parameter URL: `id` (required, int) - ID dari `list_jadwal_diklat`
- Auth: Wajib Bearer token
- Role yang diizinkan: `hrd`
- Content-Type: `application/json`

Endpoint ini digunakan oleh HRD untuk mengubah status validasi peserta. Nilai boolean `true` akan disimpan sebagai `valid`, dan `false` menjadi `tidak valid`.

Aturan validasi:

- Untuk diklat `internal`, HRD tidak bisa meng-approve validasi (`status_validasi: true`) jika `sertif_file_path` atau `no_sertif` masih kosong.
- Jika rule tersebut dilanggar, API mengembalikan `422` dengan message `belum upload laporan`.
- Reject validasi (`status_validasi: false`) tetap bisa dilakukan.

Field request:

- `status_validasi` (required, boolean)

Contoh request payload:

```json
{
  "status_validasi": true
}
```

Contoh response sukses (`200 OK`):

```json
{
  "success": true,
  "message": "Status validasi berhasil diperbarui.",
  "data": {
    "id_jadwal_diklat": 5,
    "diklat_id": 1,
    "pegawai_id": 3,
    "status_validasi": "valid"
  }
}
```

Contoh response gagal karena laporan belum lengkap (`422 Unprocessable Entity`):

```json
{
  "success": false,
  "message": "belum upload laporan"
}
```

#### Create Diklat Pengguna

- Method: `POST`
- URL: `/api/diklat`
- Auth: Wajib Bearer token
- Role yang diizinkan: `pegawai`, `hrd`, `direktur`
- Content-Type: `multipart/form-data`

Field request:

- `nama_kegiatan` (required, string, max 255)
- `kategori` (required, string, max 100)
- `jenis_diklat` (required, string, max 100)
- `penyelenggara` (required, string, max 255)
- `lokasi` (required, string, max 255)
- `tanggal_mulai` (required, date)
- `tanggal_selesai` (required, date, harus sama atau setelah `tanggal_mulai`)
- `waktu` (nullable, string, format: `HH:MM` atau `HH:MM:SS`)
- `no_sertif` (required, string, max 100)
- `upload_sertif` (required, file: pdf/jpg/jpeg/png/webp, max 5MB)
- `jp` (required, integer, min 1)
- `jenis_biaya` (required jika `jenis_pelaksana=internal`, nullable, string, max 100)
- `total_biaya` (required jika `jenis_pelaksana=internal`, nullable, numeric, min 0)
- `catatan` (nullable, string, max 1000)
- `jenis_pelaksana` (required: `internal|external`)

Aturan bisnis:

- Jika `jenis_pelaksana=internal`:
  - `status_kelayakan` otomatis `layak`
  - `status_validasi` otomatis `null`
- Jika `jenis_pelaksana=external`:
  - `jenis_biaya` otomatis `null`
  - `total_biaya` otomatis `null`
  - `status_kelayakan` otomatis `null`
  - `status_validasi` otomatis `valid`

Contoh response sukses (`201`):

```json
{
  "success": true,
  "message": "Diklat berhasil dibuat.",
  "data": {
    "id_diklat": 12,
    "id_jadwal_diklat": 9,
    "nama_kegiatan": "Workshop Pelayanan Prima",
    "kategori": "Teknis",
    "jenis_diklat": "ASN",
    "penyelenggara": "RS Kalisat",
    "lokasi": "Aula RS",
    "tanggal_mulai": "2026-05-10",
    "tanggal_selesai": "2026-05-12",
    "waktu": "08:00:00",
    "status_diklat": "belum terlaksana",
    "no_sertif": "SERTIF/SDM/2026/0099",
    "sertif_file_path": "dokumen/sertif-diklat/sertif-3-1713542400.pdf",
    "jp": 24,
    "jenis_biaya": "BLUD",
    "total_biaya": "2500000.00",
    "catatan": "Usulan pelatihan unit SDM",
    "jenis_pelaksana": "internal",
    "status_kelayakan": "layak",
    "status_validasi": "menunggu validasi"
  }
}
```

#### Edit Diklat Pengguna

- Method: `PATCH`
- URL: `/api/diklat/{id}`
- Auth: Wajib Bearer token
- Role yang diizinkan: `pegawai`, `hrd`, `direktur`
- Content-Type: `multipart/form-data`

Field request (opsional / partial update):

- `nama_kegiatan` (sometimes, nullable, string, max 255)
- `kategori` (sometimes, nullable, string, max 100)
- `jenis_diklat` (sometimes, nullable, string, max 100)
- `penyelenggara` (sometimes, nullable, string, max 255)
- `lokasi` (sometimes, nullable, string, max 255)
- `tanggal_mulai` (sometimes, nullable, date)
- `tanggal_selesai` (sometimes, nullable, date, harus sama atau setelah `tanggal_mulai`)
- `waktu` (sometimes, nullable, string, format: `HH:MM` atau `HH:MM:SS`)
- `no_sertif` (sometimes, nullable, string, max 100)
- `upload_sertif` (sometimes, nullable, file: pdf/jpg/jpeg/png/webp, max 5MB)
- `jp` (sometimes, nullable, integer, min 1)
- `jenis_biaya` (sometimes, nullable, string, max 100)
- `total_biaya` (sometimes, nullable, numeric, min 0)
- `catatan` (sometimes, nullable, string, max 1000)
- `jenis_pelaksana` (sometimes, nullable, `internal|external`; boleh dikirim, tapi tidak boleh beda dengan data awal)

Aturan bisnis edit:

- `jenis_pelaksana` (`internal`/`external`) tidak bisa diubah.
- Jika diklat `internal` and `status_validasi = valid`, data tidak bisa diedit. (Jika status validasi nya `null` atau `tidak valid`/ditolak, data bisa diedit lagi).
- Jika diklat `external` sudah dilihat/diterima HRD (`status_kelayakan = layak`), data tidak bisa diedit lagi. Response error: `Diklat sudah dilihat oleh HRD, sehingga diklat external tidak bisa diedit lagi.` Jika status kelayakan masih `null` atau `tidak layak`/ditolak, data bisa diedit lagi.
- Untuk diklat `internal`, saat diedit `status_kelayakan` dipertahankan `layak`, dan `status_validasi` akan di-reset menjadi `null` agar divalidasi ulang oleh HRD.
- Untuk diklat `external`, saat diedit `jenis_biaya`, `total_biaya`, dan `status_validasi` diset `null`, serta `status_kelayakan` akan di-reset menjadi `null` agar dievaluasi ulang.

Contoh response sukses (`200`):

```json
{
  "success": true,
  "message": "Diklat berhasil diupdate.",
  "data": {
    "id_diklat": 12,
    "id_jadwal_diklat": 9,
    "nama_kegiatan": "Workshop Pelayanan Prima Update",
    "kategori": "Teknis",
    "jenis_diklat": "ASN",
    "penyelenggara": "RS Kalisat",
    "lokasi": "Aula RS",
    "tanggal_mulai": "2026-05-10",
    "tanggal_selesai": "2026-05-12",
    "waktu": "08:00:00",
    "status_diklat": "belum terlaksana",
    "no_sertif": "SERTIF/SDM/2026/0099",
    "sertif_file_path": "dokumen/sertif-diklat/sertif-3-1713542400.pdf",
    "jp": 24,
    "jenis_biaya": "BLUD",
    "total_biaya": "2500000.00",
    "catatan": "Revisi data diklat",
    "jenis_pelaksana": "internal",
    "status_kelayakan": "layak",
    "status_validasi": "menunggu validasi"
  }
}
```

#### Upload Laporan Diklat Pegawai

- Method: `POST`
- URL: `/api/diklat/{id}/upload-laporan`
- Auth: Wajib Bearer token
- Role yang diizinkan: `pegawai`, `hrd`, `direktur`
- Content-Type: `multipart/form-data`

Endpoint ini digunakan khusus untuk mengupload laporan (sertifikat) atau mengedit nomor sertifikat ketika status validasi bukan `valid` (misalnya karena `tidak valid` atau masih `null`). Endpoint ini hanya memproses file sertifikat dan no_sertif.

Field request:

- `upload_laporan` / `upload_sertif` (required, file: pdf/jpg/jpeg/png/webp, max 5MB)
- `no_sertif` (required, string, max 255)

Aturan bisnis:

- Jika `status_validasi = valid`, maka laporan tidak dapat diupload/diedit.
- Jika diklat berjenis `internal`, proses upload laporan akan mereset `status_validasi` menjadi `null` agar divalidasi ulang oleh HRD.

Contoh response sukses (`200`):

```json
{
  "success": true,
  "message": "Laporan berhasil diupload/diedit.",
  "data": {
    "id_diklat": 12,
    "id_jadwal_diklat": 9,
    "no_sertif": "SERTIF/SDM/2026/0099",
    "sertif_file_path": "dokumen/sertif-diklat/sertif-3-1713542400.pdf",
    "status_kelayakan": "layak",
    "status_validasi": "menunggu validasi",
    "uploaded_at": "2026-05-15 14:30:00"
  }
}
```

#### Delete Diklat Pengguna

- Method: `DELETE`
- URL: `/api/diklat/{id}`
- Auth: Wajib Bearer token
- Role yang diizinkan: `pegawai`, `hrd`, `direktur`

Aturan bisnis delete:

- Pegawai boleh menghapus diklat miliknya jika data belum masuk kelayakan dan belum validasi.
- Jika `status_kelayakan = layak` atau `status_validasi = valid`, maka data tidak bisa dihapus.

Contoh response sukses (`200`):

```json
{
  "success": true,
  "message": "Diklat berhasil dihapus.",
  "data": {
    "id_diklat": 12,
    "id_jadwal_diklat": 9,
    "deleted": true
  }
}
```

Contoh response gagal (`422`):

```json
{
  "success": false,
  "message": "Diklat tidak bisa dihapus karena sudah masuk kelayakan atau sudah validasi."
}
```

#### Generate Laporan Diklat (HRD)

- Method: `GET`
- URL: `/api/generate/laporan-diklat`
- Auth: Wajib Bearer token
- Role yang diizinkan: `hrd`

Endpoint ini digunakan untuk men-generate laporan rekap diklat internal berdasarkan bulan dan tahun pembuatan (dari field `created_at`).

Parameter Query:
- `bulan_awal` (required, int 1-12)
- `tahun_awal` (required, int e.g. 2026)
- `bulan_akhir` (required, int 1-12)
- `tahun_akhir` (required, int e.g. 2026)

Contoh request:
`GET /api/generate/laporan-diklat?bulan_awal=1&tahun_awal=2026&bulan_akhir=5&tahun_akhir=2026`

Contoh response sukses (`200 OK`):

```json
{
  "success": true,
  "message": "Data rekap diklat berhasil diambil.",
  "data": {
    "periode_awal": "2026-01",
    "periode_akhir": "2026-05",
    "waktu_generate": "2026-05-24 23:16:24",
    "Rekap Diklat": {
      "total": 1,
      "total_biaya keseluruhan": 2000000,
      "list_diklat": [
        {
          "nama_kegiatan": "Pelatihan Bantuan Hidup Dasar",
          "jenis_diklat": "Tenkes",
          "penyelenggara": "Tim Code Blue RS",
          "tanggal_mulai": "2026-05-15",
          "tanggal_selesai": "2026-05-16",
          "waktu": "08:00:00",
          "JP": 16,
          "jenis_biaya": "BLUD",
          "Total Peserta": 25,
          "Total Peserta yang udah Validasi": 20,
          "total_biaya per peserta": 100000,
          "Total Biaya Diklat": 2000000
        }
      ]
    },
    "rekap_pegawai": {
      "total pegawai": 20,
      "total biaya": 2000000,
      "list_pegawai": [
        {
          "Nama Orang": "Budi Santoso",
          "NIK": "3174010101010001",
          "NIP": "198901012010011001",
          "unit kerja": "IGD",
          "nama_kegiatan": "Pelatihan Bantuan Hidup Dasar",
          "jenis_diklat": "Tenkes",
          "penyelenggara": "Tim Code Blue RS",
          "tanggal_mulai": "2026-05-15",
          "tanggal_selesai": "2026-05-16",
          "waktu": "08:00:00",
          "jp": 16,
          "jenis_biaya": "BLUD",
          "biaya": 100000
        }
      ]
    }
  }
}
```

### 6. Profile

- Method: `GET`
- URL: `/api/profile`
- Auth: Wajib Bearer token
- Role yang diizinkan: `admin`, `pegawai`, `hrd`, `direktur`

Contoh header:

```http
Authorization: Bearer <jwt_token>
```

#### Response Profile Untuk Role Pegawai

Contoh response `200 OK`:

```json
{
  "success": true,
  "message": "Selamat datang pegawai",
  "data": {
    "role": "pegawai",
    "profile": {
      "label": "Profile pegawai",
      "nip": "198901012010011001",
      "nik": "3174010101010001",
      "nama": "Budi Santoso",
      "jenis_pegawai": "PNS",
      "profesi": "Analis SDM",
      "pendidikan_terakhir": "S1/D4",
      "unit_kerja": "SDM",
      "jk": "L",
      "tanggal_lahir": "1990-01-01",
      "jabatan_sekarang": "Staf Kepegawaian",
      "agama": "Islam",
      "status_kawin": "kawin",
      "alamat": "Jakarta",
      "no_telp": "081234567890",
      "email": "budi.santoso@example.com",
      "no_kk": "3506123456789012",
      "link_kk": "/dokumen/kk/kk-4-1713500000.pdf",
      "link_photo_profile": "/dokumen/foto/budi-santoso.jpg",
      "status_pegawai": "aktif",
      "tgl_masuk": "2020-01-01",
      "pangkat": "Penata Muda",
      "golongan_ruang": "III/a",
      "tmt_cpns": "2020-01-01",
      "tmt_pns": "2021-01-01",
      "tmt_pangkat": "2020-01-01",
      "masa_kerja": "6 tahun 3 bulan",
      "status_perubahan": {
        "fitur": "profile",
        "status": "pending",
        "note": "Mohon update data profile terbaru",
        "last_update": "2026-04-19 08:30:00"
      }
    }
  }
}
```

Keterangan field tambahan:

- `profesi`, `jabatan_sekarang`, `unit_kerja`, `pangkat`, `golongan_ruang`: prioritas data `is_current = true`.
- `masa_kerja`: hasil hitung dari `tgl_masuk` sampai tanggal sekarang.
- `status_perubahan`: ringkasan perubahan profile terbaru milik user.
- `status_perubahan.fitur`: fitur pengajuan perubahan terbaru.
- `status_perubahan.status`: status pengajuan (`pending`/`approved`/`rejected`).
- `status_perubahan.note`: catatan pada pengajuan terbaru.
- `status_perubahan.last_update`: waktu update terakhir dari data profile utama dan relasi current.

Contoh response `401 Unauthorized` (token tidak valid/tidak ada):

```json
{
  "success": false,
  "message": "Access denied."
}
```

Contoh response `403 Forbidden` (role tidak diizinkan):

```json
{
  "success": false,
  "message": "Access denied."
}
```

### 7. Ajukan Perubahan Profile

- Method: `PATCH`
- URL: `/api/profile`
- Auth: Wajib Bearer token
- Role yang diizinkan: `admin`, `pegawai`, `hrd`, `direktur`

Request body (contoh):

```json
{
  "nama": "Budi Santoso Update",
  "alamat": "Jl. Mawar No. 10",
  "no_telp": "081298765432",
  "note": "Mohon update data profile terbaru"
}
```

Catatan:

- Minimal satu field profile harus dikirim.
- Endpoint ini tidak langsung mengubah tabel master profile.
- Sistem membuat pengajuan ke tabel `perubahan_data` + `detail_perubahan_data` dengan status `pending`.

Daftar field yang bisa diubah:

- `nip`
- `nik`
- `nama`
- `profesi`
- `jenis_pegawai`
- `jenis_kelamin`
- `tanggal_lahir`
- `agama`
- `status_kawin`
- `alamat`
- `no_telp`
- `no_kk`
- `email`
- `status_pegawai`
- `tgl_masuk`
- `tmt_cpns`
- `tmt_pns`
- `note` (opsional, sebagai catatan pengajuan)

Validasi request:

- `nip`, `nik`, `no_kk`, `no_telp`: string, maksimal 30 karakter
- `nama`, `email`: maksimal 255 karakter; `email` harus format email valid
- `profesi`, `jenis_pegawai`: string, maksimal 100 karakter
- `jenis_kelamin`: salah satu `L`, `P`, `l`, `p`
- `tanggal_lahir`, `tgl_masuk`, `tmt_cpns`, `tmt_pns`: format tanggal valid
- `agama`, `status_kawin`, `status_pegawai`: string, maksimal 50 karakter
- `alamat`: string, maksimal 500 karakter
- `note`: string, maksimal 1000 karakter
- Minimal satu field profile selain `note` wajib dikirim.

Contoh response `200 OK`:

```json
{
  "success": true,
  "message": "Pengajuan perubahan profile berhasil dikirim dan menunggu persetujuan admin.",
  "data": {
    "id_perubahan_data": 1,
    "status": "pending",
    "fitur": "profile",
    "jumlah_detail": 3
  }
}
```

Contoh response `422 Unprocessable Entity`:

```json
{
  "success": false,
  "message": "Tidak ada perubahan data yang bisa diajukan."
}
```

### 8. Upload Foto Profile (Tanpa Approval)

- Method: `POST`
- URL utama: `/api/profil/profil-picture`
- URL alias: `/api/profile/profile-picture`
- Auth: Wajib Bearer token
- Role yang diizinkan: `admin`, `pegawai`, `hrd`, `direktur`
- Content-Type: `multipart/form-data`

Request form-data:

- `foto`: file image (`jpg/jpeg/png/webp`), max 2MB

Perilaku:

- Langsung update `pegawai_pribadi.foto_path`.
- Menyimpan file ke folder `public/dokumen/foto`.
- Tidak membuat pengajuan `perubahan_data` (tanpa approval admin).

Contoh response `200 OK`:

```json
{
  "success": true,
  "message": "Foto profile berhasil diupdate.",
  "data": {
    "foto_path": "dokumen/foto/profile-4-1713500000.jpg",
    "link_photo_profile": "/dokumen/foto/profile-4-1713500000.jpg",
    "updated_at": "2026-04-19 12:30:00"
  }
}
```

#### 8.1 Upload File KTP (Tanpa Approval)

- Method: `POST`
- URL: `/api/profil/ktp`
- Auth: Wajib Bearer token
- Role yang diizinkan: `admin`, `pegawai`, `hrd`, `direktur`
- Content-Type: `multipart/form-data`

Request form-data:

- `ktp`: file PDF (`application/pdf`), max 2MB

Perilaku:

- Langsung update `pegawai_pribadi.ktp_file_path`.
- Menyimpan file ke folder `public/dokumen/ktp`.
- Tidak membuat pengajuan `perubahan_data` (tanpa approval admin).

Contoh response `200 OK`:

```json
{
  "success": true,
  "message": "File KTP berhasil diupload.",
  "data": {
    "ktp_file_path": "dokumen/ktp/ktp-4-1713500000.pdf",
    "link_ktp_file": "/dokumen/ktp/ktp-4-1713500000.pdf",
    "updated_at": "2026-04-19 12:45:00"
  }
}
```

Contoh response `422 Unprocessable Entity`:

```json
{
  "success": false,
  "message": "Validasi gagal.",
  "errors": {
    "ktp": [
      "The ktp field must be a file of type: pdf.",
      "The ktp field must be a file of type: application/pdf."
    ]
  }
}
```

#### 8.2 Upload File KK (Tanpa Approval)

- Method: `POST`
- URL: `/api/profile/kk`
- Auth: Wajib Bearer token
- Role yang diizinkan: `admin`, `pegawai`, `hrd`, `direktur`
- Content-Type: `multipart/form-data`

Request form-data:

- `kk`: file PDF (`application/pdf`), max 2MB

Perilaku:

- Langsung update `pegawai_pribadi.kk_file_path` dan `pegawai_pribadi.link_kk`.
- Menyimpan file ke folder `public/dokumen/kk`.
- Tidak membuat pengajuan `perubahan_data` (tanpa approval admin).

Contoh response `200 OK`:

```json
{
  "success": true,
  "message": "File KK berhasil diupload.",
  "data": {
    "kk_file_path": "dokumen/kk/kk-4-1713500000.pdf",
    "link_kk": "/dokumen/kk/kk-4-1713500000.pdf",
    "updated_at": "2026-04-22 12:45:00"
  }
}
```

### 9. Notifikasi

#### 9.1 List Notifikasi

- Method: `GET`
- URL: `/api/notifications`
- Auth: Wajib Bearer token

Contoh response `200 OK`:

```json
{
  "success": true,
  "message": "Daftar notifikasi berhasil diambil.",
  "data": {
    "notifications": [
      {
        "id": 1,
        "title": "Jadwal Diklat Mendatang",
        "message": "Anda memiliki jadwal diklat yang belum terlaksana. Silakan cek detail jadwal.",
        "is_read": false,
        "created_at": "2026-04-17 10:00:00"
      }
    ]
  }
}
```

#### 9.2 Tandai 1 Notifikasi Sudah Dibaca

- Method: `PATCH`
- URL: `/api/notifications/{id}/read`
- Auth: Wajib Bearer token

Contoh response `200 OK`:

```json
{
  "success": true,
  "message": "Notifikasi ditandai sudah dibaca."
}
```

Contoh response `404 Not Found`:

```json
{
  "success": false,
  "message": "Notifikasi tidak ditemukan."
}
```

#### 9.3 Tandai Semua Notifikasi Sudah Dibaca

- Method: `PATCH`
- URL: `/api/notifications/read-all`
- Auth: Wajib Bearer token

Contoh response `200 OK`:

```json
{
  "success": true,
  "message": "Semua notifikasi ditandai sudah dibaca.",
  "data": {
    "updated_count": 2
  }
}
```

### 10. Riwayat Karir Pendidikan

- Method: `GET` dan `POST`
- URL: `/api/riwayat-karir/pendidikan`
- Auth: Wajib Bearer token
- Role yang diizinkan: `admin`, `pegawai`, `hrd`, `direktur`

#### GET Riwayat Pendidikan

Mengambil data riwayat pendidikan milik user yang sedang login.

Contoh response `200 OK`:

```json
{
  "success": true,
  "message": "Data riwayat pendidikan berhasil diambil.",
  "data": {
    "label": "Riwayat pendidikan",
    "total": 1,
    "items": [
      {
        "id": 1,
        "jenjang": "S1",
        "institusi": "Universitas Jember",
        "jurusan": "Teknik Informatika",
        "tahun_lulus": 2012,
        "nomor_ijazah": "IJZ-2012-001",
        "link_ijazah": "/dokumen/ijazah/ijazah-4-1713500000.pdf"
      }
    ]
  }
}
```

#### POST Riwayat Pendidikan

Menambahkan data riwayat pendidikan baru untuk user yang sedang login.
Request menggunakan `multipart/form-data`.

Field request:
- `jenjang` (required, string, max:50)
- `institusi` (required, string, max:255)
- `jurusan` (required, string, max:255)
- `tahun_lulus` (required, integer, min:1900, max:2100)
- `nomor_ijazah` (nullable, string, max:100)
- `ijazah` (nullable, file: pdf/jpg/jpeg/png/webp, max 5MB)

Contoh response `201 Created`:

```json
{
  "success": true,
  "message": "Riwayat pendidikan berhasil ditambahkan.",
  "data": {
    "id": 2,
    "jenjang": "S2",
    "institusi": "Universitas Indonesia",
    "jurusan": "Ilmu Komputer",
    "tahun_lulus": 2015,
    "nomor_ijazah": "IJZ-S2-2015",
    "link_ijazah": "/dokumen/ijazah/ijazah-4-1713500001.pdf"
  }
}
```

#### POST / PATCH Riwayat Pendidikan (Update)

Mengubah data riwayat pendidikan milik user yang sedang login berdasarkan `{id}`.
Untuk menghindari limitasi *multipart/form-data* di PHP, Anda dapat menggunakan *method* **`POST`** (tanpa perlu `_method=PATCH`).

Field request (*multipart/form-data*):
- `jenjang` (sometimes, string, max:50)
- `institusi` (sometimes, string, max:255)
- `jurusan` (sometimes, string, max:255)
- `tahun_lulus` (sometimes, integer, min:1900, max:2100)
- `nomor_ijazah` (nullable, string, max:100)
- `ijazah` (nullable, file: pdf/jpg/jpeg/png/webp, max 5MB)

Contoh response `200 OK`:

```json
{
  "success": true,
  "message": "Riwayat pendidikan berhasil diupdate.",
  "data": {
    "id": 2,
    "jenjang": "S2",
    "institusi": "Universitas Indonesia Update",
    "jurusan": "Ilmu Komputer",
    "tahun_lulus": 2016,
    "nomor_ijazah": "IJZ-S2-2016",
    "link_ijazah": "/dokumen/ijazah/ijazah-4-1713500001.pdf"
  }
}
```

#### DELETE Riwayat Pendidikan

Menghapus data riwayat pendidikan beserta file ijazahnya (jika ada) milik user yang sedang login.

Contoh response `200 OK`:

```json
{
  "success": true,
  "message": "Riwayat pendidikan berhasil dihapus."
}
```

### 11. Riwayat Karir Jabatan

- Method: `GET`
- URL: `/api/riwayat-karir/jabatan`
- Auth: Wajib Bearer token
- Role yang diizinkan: `admin`, `pegawai`, `hrd`, `direktur`

#### GET Riwayat Jabatan

Mengambil data riwayat jabatan milik user yang sedang login.

Contoh response `200 OK`:

```json
{
  "success": true,
  "message": "Data riwayat jabatan berhasil diambil.",
  "data": {
    "label": "Riwayat jabatan",
    "total": 1,
    "items": [
      {
        "id": 1,
        "unit_kerja_id": 1,
        "unit_kerja_nama": "SDM",
        "nama_jabatan": "Perawat Pelaksana",
        "is_current": false,
        "tmt_mulai": "2020-01-01",
        "tmt_selesai": "2023-12-31",
        "link_sk": "/dokumen/jabatan/sk-jabatan-1-123456789.pdf",
        "note": "Awal masuk"
      }
    ]
  }
}
```

#### POST Riwayat Jabatan

Menambahkan data riwayat jabatan baru untuk user yang sedang login beserta lampiran SK.

| Parameter | Tipe | Wajib | Keterangan |
| :--- | :--- | :--- | :--- |
| `unit_kerja_id` | Integer | Tidak | ID Unit Kerja (dari tabel unit_kerja) |
| `nama_jabatan` | String | Ya | Nama jabatan |
| `is_current` | Boolean (0/1) | Ya | Apakah jabatan ini masih dijabat? |
| `tmt_mulai` | Date | Tidak | Tanggal mulai menjabat (Format: YYYY-MM-DD) |
| `tmt_selesai` | Date | Tidak | Tanggal selesai menjabat |
| `sk_jabatan` | File | Tidak | File SK jabatan (max 5MB, format pdf/jpg/png) |
| `note` | String | Tidak | Catatan tambahan |

Contoh response `201 Created`:

```json
{
  "success": true,
  "message": "Riwayat jabatan berhasil ditambahkan.",
  "data": {
    "id": 2,
    "unit_kerja_id": 1,
    "unit_kerja_nama": "SDM",
    "nama_jabatan": "Perawat Madya",
    "is_current": true,
    "tmt_mulai": "2024-01-01",
    "tmt_selesai": null,
    "link_sk": "/dokumen/jabatan/sk-jabatan-2-123456789.pdf",
    "note": "Promosi"
  }
}
```

#### POST / PATCH Riwayat Jabatan (Update)

Memperbarui sebagian data riwayat jabatan berdasarkan `{id}`.
Untuk menghindari limitasi *multipart/form-data* di PHP, Anda dapat menggunakan *method* **`POST`** (tanpa perlu `_method=PATCH`). File SK lama akan otomatis dihapus jika Anda mengunggah file SK baru.

Field request (*multipart/form-data*):
- `unit_kerja_id` (sometimes, integer)
- `nama_jabatan` (sometimes, string)
- `is_current` (sometimes, boolean: 1/0)
- `tmt_mulai` (sometimes, date)
- `tmt_selesai` (sometimes, date)
- `sk_jabatan` (sometimes, file: pdf/jpg/png, max 5MB)
- `note` (sometimes, string)

Contoh response `200 OK`:

```json
{
  "success": true,
  "message": "Riwayat jabatan berhasil diupdate.",
  "data": {
    "id": 1,
    "unit_kerja_id": 1,
    "unit_kerja_nama": "SDM",
    "nama_jabatan": "Kepala Perawat",
    "is_current": false,
    "tmt_mulai": "2020-01-01",
    "tmt_selesai": "2026-01-01",
    "note": "Promosi jabatan"
  }
}
```

#### DELETE Riwayat Jabatan

Menghapus riwayat jabatan beserta file SK-nya (jika ada) milik user yang sedang login.

Contoh response `200 OK`:

```json
{
  "success": true,
  "message": "Riwayat jabatan berhasil dihapus."
}
```

### 12. Riwayat Karir Pangkat

- URL dasar: `/api/riwayat-karir/pangkat`
- Auth: Bearer token
- Role: `admin`, `pegawai`, `hrd`, `direktur`

#### GET Riwayat Pangkat

- Method: `GET`
- URL: `/api/riwayat-karir/pangkat`

Data diurutkan berdasarkan `started_at` menurun.

Response `200 OK`:

```json
{
  "success": true,
  "message": "Berhasil mengambil riwayat pangkat.",
  "data": {
    "label": "Riwayat pangkat",
    "total": 1,
    "items": [
      {
        "id": 1,
        "nama_pangkat": "Penata Muda",
        "is_current": true,
        "pejabat_penetap": "Gubernur",
        "tmt_sk": "2020-01-01",
        "started_at": "2020-01-01",
        "ended_at": null,
        "link_sk": "/dokumen/pangkat/sk-pangkat-1-123456789.pdf",
        "note": "Pangkat pertama"
      }
    ]
  }
}
```

#### POST Riwayat Pangkat

- Method: `POST`
- URL: `/api/riwayat-karir/pangkat`
- Content-Type: `multipart/form-data`

| Parameter | Tipe | Wajib | Validasi | Keterangan |
| :--- | :--- | :--- | :--- | :--- |
| `nama_pangkat` | String | Ya | `required`, `string`, `max:255` | Nama pangkat |
| `is_current` | Boolean (0/1) | Ya | `required`, `boolean` | Pangkat masih aktif? |
| `pejabat_penetap` | String | Tidak | `nullable`, `string`, `max:255` | Nama pejabat penetap |
| `tmt_sk` | Date | Tidak | `nullable`, `date` | Tanggal SK (YYYY-MM-DD) |
| `started_at` | Date | Tidak | `nullable`, `date` | Tanggal mulai pangkat |
| `ended_at` | Date | Tidak | `nullable`, `date`, `after_or_equal:started_at` | Tanggal selesai |
| `sk_pangkat` | File | Tidak | `nullable`, `file`, `mimes:pdf,jpg,jpeg,png`, `max:5120` | File SK (maks 5MB) |
| `note` | String | Tidak | `nullable`, `string` | Catatan tambahan |

Contoh raw input (form-data):

```text
nama_pangkat: Penata Tingkat I
is_current: 1
pejabat_penetap: Gubernur
tmt_sk: 2024-01-01
started_at: 2024-01-01
sk_pangkat: <File Binary>
note: Promosi
```

Response `201 Created`:

```json
{
  "success": true,
  "message": "Riwayat pangkat berhasil ditambahkan.",
  "data": {
    "id": 2,
    "nama_pangkat": "Penata Tingkat I",
    "is_current": true,
    "pejabat_penetap": "Gubernur",
    "tmt_sk": "2024-01-01",
    "started_at": "2024-01-01",
    "ended_at": null,
    "link_sk": "/dokumen/pangkat/sk-pangkat-2-123456789.pdf",
    "note": "Promosi"
  }
}
```

#### POST / PATCH Riwayat Pangkat (Update)

- Method: `POST /api/riwayat-karir/pangkat/{id}` atau `PATCH /api/riwayat-karir/pangkat/{id}`
- Content-Type: `multipart/form-data`

Gunakan `POST` kalau kirim file (PHP tidak support file upload via `PATCH`). File SK lama otomatis dihapus saat upload file baru.

| Parameter | Tipe | Wajib | Validasi | Keterangan |
| :--- | :--- | :--- | :--- | :--- |
| `nama_pangkat` | String | Opsional | `sometimes`, `required`, `string`, `max:255` | Nama pangkat |
| `is_current` | Boolean (0/1) | Opsional | `sometimes`, `required`, `boolean` | Pangkat masih aktif? |
| `pejabat_penetap` | String | Opsional | `sometimes`, `nullable`, `string`, `max:255` | Pejabat penetap |
| `tmt_sk` | Date | Opsional | `sometimes`, `nullable`, `date` | Tanggal SK |
| `started_at` | Date | Opsional | `sometimes`, `nullable`, `date` | Tanggal mulai |
| `ended_at` | Date | Opsional | `sometimes`, `nullable`, `date`, `after_or_equal:started_at` | Tanggal selesai |
| `sk_pangkat` | File | Opsional | `sometimes`, `nullable`, `file`, `mimes:pdf,jpg,jpeg,png`, `max:5120` | File SK (maks 5MB) |
| `note` | String | Opsional | `sometimes`, `nullable`, `string` | Catatan |

Response `200 OK`:

```json
{
  "success": true,
  "message": "Riwayat pangkat berhasil diupdate.",
  "data": {
    "id": 1,
    "nama_pangkat": "Penata Muda",
    "is_current": false,
    "pejabat_penetap": "Bupati",
    "tmt_sk": "2020-01-01",
    "started_at": "2020-01-01",
    "ended_at": "2024-01-01",
    "note": "Berakhir"
  }
}
```

#### DELETE Riwayat Pangkat

- Method: `DELETE`
- URL: `/api/riwayat-karir/pangkat/{id}`

Menghapus data pangkat beserta file SK-nya (kalau ada).

Response `200 OK`:

```json
{
  "success": true,
  "message": "Riwayat pangkat berhasil dihapus."
}
```

### 13. Riwayat Karir SIP

- URL dasar: `/api/riwayat-karir/sip`
- Auth: Bearer token
- Role: `admin`, `pegawai`, `hrd`, `direktur`

#### GET Riwayat SIP

- Method: `GET`
- URL: `/api/riwayat-karir/sip`

Data diurutkan berdasarkan `tanggal_terbit` menurun.

Response `200 OK`:

```json
{
  "success": true,
  "message": "Berhasil mengambil riwayat SIP.",
  "data": {
    "label": "Riwayat SIP",
    "total": 1,
    "items": [
      {
        "id": 1,
        "jenis_sip_id": 1,
        "jenis_sip_nama": "SIP Dokter Umum",
        "nomor_sip": "SIP.123/456/2023",
        "tanggal_terbit": "2023-01-01",
        "tanggal_kadaluarsa": "2028-01-01",
        "status": "aktif",
        "link_sk": "/dokumen/sip/sk-sip-1-123456789.pdf"
      }
    ]
  }
}
```

#### POST Riwayat SIP

- Method: `POST`
- URL: `/api/riwayat-karir/sip`
- Content-Type: `multipart/form-data`

| Parameter | Tipe | Wajib | Validasi | Keterangan |
| :--- | :--- | :--- | :--- | :--- |
| `jenis_sip_id` | Integer | Tidak | `nullable`, `exists:jenis_sip,id` | ID Jenis SIP (dari master data) |
| `nomor_sip` | String | Ya | `required`, `string`, `max:255` | Nomor surat SIP |
| `tanggal_terbit` | Date | Ya | `required`, `date` | Tanggal terbit (YYYY-MM-DD) |
| `tanggal_kadaluarsa` | Date | Tidak | `nullable`, `date`, `after_or_equal:tanggal_terbit` | Tanggal kedaluwarsa |
| `sk_sip` | File | Tidak | `nullable`, `file`, `mimes:pdf,jpg,jpeg,png`, `max:5120` | File SK SIP (maks 5MB) |

Contoh raw input (form-data):

```text
jenis_sip_id: 1
nomor_sip: SIP.Baru/789/2024
tanggal_terbit: 2024-01-01
tanggal_kadaluarsa: 2029-01-01
sk_sip: <File Binary>
```

Response `201 Created`:

```json
{
  "success": true,
  "message": "Riwayat SIP berhasil ditambahkan.",
  "data": {
    "id": 2,
    "jenis_sip_id": null,
    "jenis_sip_nama": "",
    "nomor_sip": "SIP.Baru/789/2024",
    "tanggal_terbit": "2024-01-01",
    "tanggal_kadaluarsa": "2029-01-01",
    "status": "aktif",
    "link_sk": "/dokumen/sip/sk-sip-2-123456789.pdf"
  }
}
```

#### POST / PATCH Riwayat SIP (Update)

- Method: `POST /api/riwayat-karir/sip/{id}` atau `PATCH /api/riwayat-karir/sip/{id}`
- Content-Type: `multipart/form-data`

Gunakan `POST` kalau kirim file. File SK lama otomatis dihapus saat upload baru.

| Parameter | Tipe | Wajib | Validasi | Keterangan |
| :--- | :--- | :--- | :--- | :--- |
| `jenis_sip_id` | Integer | Opsional | `sometimes`, `nullable`, `exists:jenis_sip,id` | ID Jenis SIP |
| `nomor_sip` | String | Opsional | `sometimes`, `required`, `string`, `max:255` | Nomor SIP |
| `tanggal_terbit` | Date | Opsional | `sometimes`, `required`, `date` | Tanggal terbit |
| `tanggal_kadaluarsa` | Date | Opsional | `sometimes`, `nullable`, `date`, `after_or_equal:tanggal_terbit` | Tanggal kedaluwarsa |
| `sk_sip` | File | Opsional | `sometimes`, `nullable`, `file`, `mimes:pdf,jpg,jpeg,png`, `max:5120` | File SK (maks 5MB) |

Response `200 OK`:

```json
{
  "success": true,
  "message": "Riwayat SIP berhasil diupdate.",
  "data": {
    "id": 1,
    "jenis_sip_id": 1,
    "jenis_sip_nama": "SIP Dokter Umum",
    "nomor_sip": "SIP.123/456/2023",
    "tanggal_terbit": "2023-01-01",
    "tanggal_kadaluarsa": "2028-01-01",
    "status": "aktif",
    "link_sk": null
  }
}
```

#### DELETE Riwayat SIP

- Method: `DELETE`
- URL: `/api/riwayat-karir/sip/{id}`

Menghapus data SIP beserta file-nya (kalau ada).

Response `200 OK`:

```json
{
  "success": true,
  "message": "Riwayat SIP berhasil dihapus."
}
```

### 14. Riwayat Karir STR

- URL dasar: `/api/riwayat-karir/str`
- Auth: Bearer token
- Role: `admin`, `pegawai`, `hrd`, `direktur`

#### GET Riwayat STR

- Method: `GET`
- URL: `/api/riwayat-karir/str`

Data diurutkan berdasarkan `tanggal_terbit` menurun.

Response `200 OK`:

```json
{
  "success": true,
  "message": "Berhasil mengambil riwayat STR.",
  "data": {
    "label": "Riwayat STR",
    "total": 1,
    "items": [
      {
        "id": 1,
        "nomor_str": "STR.123/456/2023",
        "tanggal_terbit": "2023-01-01",
        "tanggal_kadaluarsa": "2028-01-01",
        "status": "aktif",
        "link_sk": "/dokumen/str/sk-str-1-123456789.pdf"
      }
    ]
  }
}
```

#### POST Riwayat STR

- Method: `POST`
- URL: `/api/riwayat-karir/str`
- Content-Type: `multipart/form-data`

| Parameter | Tipe | Wajib | Validasi | Keterangan |
| :--- | :--- | :--- | :--- | :--- |
| `nomor_str` | String | Ya | `required`, `string`, `max:255` | Nomor surat STR |
| `tanggal_terbit` | Date | Ya | `required`, `date` | Tanggal terbit (YYYY-MM-DD) |
| `tanggal_kadaluarsa` | Date | Tidak | `nullable`, `date`, `after_or_equal:tanggal_terbit` | Tanggal kedaluwarsa |
| `sk_str` | File | Tidak | `nullable`, `file`, `mimes:pdf,jpg,jpeg,png`, `max:5120` | File SK STR (maks 5MB) |

Contoh raw input (form-data):

```text
nomor_str: STR.Baru/789/2024
tanggal_terbit: 2024-01-01
tanggal_kadaluarsa: 2029-01-01
sk_str: <File Binary>
```

Response `201 Created`:

```json
{
  "success": true,
  "message": "Riwayat STR berhasil ditambahkan.",
  "data": {
    "id": 2,
    "nomor_str": "STR.Baru/789/2024",
    "tanggal_terbit": "2024-01-01",
    "tanggal_kadaluarsa": "2029-01-01",
    "status": "aktif",
    "link_sk": "/dokumen/str/sk-str-2-123456789.pdf"
  }
}
```

#### POST / PATCH Riwayat STR (Update)

- Method: `POST /api/riwayat-karir/str/{id}` atau `PATCH /api/riwayat-karir/str/{id}`
- Content-Type: `multipart/form-data`

Gunakan `POST` kalau kirim file. File SK lama otomatis dihapus saat upload baru.

| Parameter | Tipe | Wajib | Validasi | Keterangan |
| :--- | :--- | :--- | :--- | :--- |
| `nomor_str` | String | Opsional | `sometimes`, `required`, `string`, `max:255` | Nomor STR |
| `tanggal_terbit` | Date | Opsional | `sometimes`, `required`, `date` | Tanggal terbit |
| `tanggal_kadaluarsa` | Date | Opsional | `sometimes`, `nullable`, `date`, `after_or_equal:tanggal_terbit` | Tanggal kedaluwarsa |
| `sk_str` | File | Opsional | `sometimes`, `nullable`, `file`, `mimes:pdf,jpg,jpeg,png`, `max:5120` | File SK (maks 5MB) |

Response `200 OK`:

```json
{
  "success": true,
  "message": "Riwayat STR berhasil diupdate.",
  "data": {
    "id": 1,
    "nomor_str": "STR.123/456/2023",
    "tanggal_terbit": "2023-01-01",
    "tanggal_kadaluarsa": "2028-01-01",
    "status": "aktif",
    "link_sk": null
  }
}
```

#### DELETE Riwayat STR

- Method: `DELETE`
- URL: `/api/riwayat-karir/str/{id}`

Menghapus data STR beserta file-nya (kalau ada).

Response `200 OK`:

```json
{
  "success": true,
  "message": "Riwayat STR berhasil dihapus."
}
```

### 15. Riwayat Karir Penugasan Klinis

- URL dasar: `/api/riwayat-karir/penugasan-klinis`
- Auth: Bearer token
- Role: `admin`, `pegawai`, `hrd`, `direktur`

#### GET Riwayat Penugasan Klinis

- Method: `GET`
- URL: `/api/riwayat-karir/penugasan-klinis`

Data diurutkan berdasarkan `tgl_mulai` menurun.

Response `200 OK`:

```json
{
  "success": true,
  "message": "Berhasil mengambil riwayat penugasan klinis.",
  "data": {
    "label": "Riwayat Penugasan Klinis",
    "total": 1,
    "items": [
      {
        "id": 1,
        "nomor_surat": "PK.123/456/2023",
        "tgl_mulai": "2023-01-01",
        "tgl_kadaluarsa": "2028-01-01",
        "status": "aktif",
        "link_dokumen": "/dokumen/penugasan-klinis/sk-penugasan-klinis-1-123456789.pdf"
      }
    ]
  }
}
```

#### POST Riwayat Penugasan Klinis

- Method: `POST`
- URL: `/api/riwayat-karir/penugasan-klinis`
- Content-Type: `multipart/form-data`

| Parameter | Tipe | Wajib | Validasi | Keterangan |
| :--- | :--- | :--- | :--- | :--- |
| `nomor_surat` | String | Ya | `required`, `string`, `max:255` | Nomor surat penugasan klinis |
| `tgl_mulai` | Date | Ya | `required`, `date` | Tanggal mulai (YYYY-MM-DD) |
| `tgl_kadaluarsa` | Date | Tidak | `nullable`, `date`, `after_or_equal:tgl_mulai` | Tanggal kedaluwarsa |
| `dokumen_file` | File | Tidak | `nullable`, `file`, `mimes:pdf,jpg,jpeg,png`, `max:5120` | File dokumen (maks 5MB) |

Contoh raw input (form-data):

```text
nomor_surat: PK.Baru/789/2024
tgl_mulai: 2024-01-01
tgl_kadaluarsa: 2029-01-01
dokumen_file: <File Binary>
```

Response `201 Created`:

```json
{
  "success": true,
  "message": "Riwayat penugasan klinis berhasil ditambahkan.",
  "data": {
    "id": 2,
    "nomor_surat": "PK.Baru/789/2024",
    "tgl_mulai": "2024-01-01",
    "tgl_kadaluarsa": "2029-01-01",
    "status": "aktif",
    "link_dokumen": "/dokumen/penugasan-klinis/sk-penugasan-klinis-2-123456789.pdf"
  }
}
```

#### POST / PATCH Riwayat Penugasan Klinis (Update)

- Method: `POST /api/riwayat-karir/penugasan-klinis/{id}` atau `PATCH /api/riwayat-karir/penugasan-klinis/{id}`
- Content-Type: `multipart/form-data`

Gunakan `POST` kalau kirim file. File dokumen lama otomatis dihapus saat upload baru.

| Parameter | Tipe | Wajib | Validasi | Keterangan |
| :--- | :--- | :--- | :--- | :--- |
| `nomor_surat` | String | Opsional | `sometimes`, `required`, `string`, `max:255` | Nomor surat |
| `tgl_mulai` | Date | Opsional | `sometimes`, `required`, `date` | Tanggal mulai |
| `tgl_kadaluarsa` | Date | Opsional | `sometimes`, `nullable`, `date`, `after_or_equal:tgl_mulai` | Tanggal kedaluwarsa |
| `dokumen_file` | File | Opsional | `sometimes`, `nullable`, `file`, `mimes:pdf,jpg,jpeg,png`, `max:5120` | File dokumen (maks 5MB) |

Response `200 OK`:

```json
{
  "success": true,
  "message": "Riwayat penugasan klinis berhasil diupdate.",
  "data": {
    "id": 1,
    "nomor_surat": "PK.123/456/2023",
    "tgl_mulai": "2023-01-01",
    "tgl_kadaluarsa": "2028-01-01",
    "status": "aktif",
    "link_dokumen": null
  }
}
```

#### DELETE Riwayat Penugasan Klinis

- Method: `DELETE`
- URL: `/api/riwayat-karir/penugasan-klinis/{id}`

Menghapus data penugasan klinis beserta file-nya (kalau ada).

Response `200 OK`:

```json
{
  "success": true,
  "message": "Riwayat penugasan klinis berhasil dihapus."
}
```

### 16. Data Keluarga

Dokumentasi CRUD Data Keluarga yang terbagi menjadi entitas: **Pasangan**, **Anak**, **Orang Tua**, dan **Kontak Darurat**. Semua endpoint butuh Bearer token.

---

### 1. Get Ringkasan Data Keluarga
- **Nama Fitur:** Mendapatkan Ringkasan Seluruh Data Keluarga
- **Penjelasan:** Mengambil ringkasan dari semua modul keluarga milik pegawai yang sedang login.
- **Route:** `GET /api/keluarga`
- **Headers:** `Authorization: Bearer {token}`
- **Response:** `200 OK`
  ```json
  {
    "success": true,
    "message": "Data keluarga berhasil diambil.",
    "data": {
      "pasangan": {
        "label": "Data Pasangan",
        "total": 1,
        "items": [
          {
            "id": 1,
            "nama_lengkap": "Siti Nurhaliza",
            "pekerjaan": "Dokter"
          }
        ]
      },
      "anak": { "label": "Data Anak", "total": 0, "items": [] },
      "orang_tua": { "label": "Data Orang Tua", "total": 0, "items": [] },
      "kontak_darurat": { "label": "Data Kontak Darurat", "total": 0, "items": [] }
    }
  }
  ```

---

### 2. Modul Pasangan

#### A. Get Data Pasangan
- **Nama Fitur:** Mendapatkan Daftar Pasangan
- **Penjelasan:** Mengambil daftar seluruh data pasangan milik pegawai yang sedang login.
- **Route:** `GET /api/keluarga/pasangan`
- **Headers:** `Authorization: Bearer {token}`
- **Response:** `200 OK`
  ```json
  {
    "success": true,
    "message": "Data pasangan berhasil diambil.",
    "data": [
      {
        "id": 1,
        "nama_lengkap": "Budi Santoso",
        "pekerjaan": "Guru"
      }
    ]
  }
  ```

#### B. Tambah Data Pasangan
- **Nama Fitur:** Menambahkan Pasangan Baru
- **Penjelasan:** Membuat entri pasangan baru beserta unggah dokumen buku nikah.
- **Route:** `POST /api/keluarga/pasangan`
- **Headers:** `Authorization: Bearer {token}`
- **Body Type:** `multipart/form-data`
- **Tabel Parameter:**

| Field | Tipe | Wajib | Keterangan |
|-------|------|-------|------------|
| `nama_lengkap` | String | Ya | Nama lengkap pasangan |
| `nik` | String | Tidak | NIK pasangan |
| `tempat_lahir` | String | Tidak | Tempat lahir |
| `tanggal_lahir` | Date (Y-m-d) | Tidak | Contoh: `1990-12-01` |
| `pekerjaan` | String | Tidak | Pekerjaan pasangan |
| `instansi` | String | Tidak | Instansi tempat bekerja |
| `status_pernikahan` | String | Tidak | Contoh: Sah, Cerai |
| `tanggal_pernikahan`| Date (Y-m-d) | Tidak | Contoh: `2015-08-10` |
| `nomor_buku_nikah` | String | Tidak | - |
| `status_tanggungan` | Boolean/Int | Tidak | `1` (Ya) atau `0` (Tidak) |
| `npwp_pasangan` | String | Tidak | - |
| `buku_nikah_file` | File (PDF/Image)| Tidak | Bukti buku nikah, Maksimal 2MB |

- **Contoh Request Payload (Form-Data):**
  ```text
  nama_lengkap: Budi Santoso
  pekerjaan: Guru
  status_tanggungan: 1
  buku_nikah_file: <File Binary>
  ```
- **Response:** `201 Created`
  ```json
  {
    "success": true,
    "message": "Data pasangan berhasil ditambahkan.",
    "data": {
      "id": 1,
      "nama_lengkap": "Budi Santoso",
      "buku_nikah_file_path": "/public/dokumen/pasangan/FILE_ABC123.pdf"
    }
  }
  ```

#### C. Ubah Data Pasangan
- **Nama Fitur:** Memperbarui Data Pasangan
- **Penjelasan:** Mengubah atribut pada entri pasangan. Jika menyertakan file, WAJIB menggunakan method `POST` di Laravel karena keterbatasan `multipart/form-data` pada method `PATCH`.
- **Route:** `POST /api/keluarga/pasangan/{id}` (Jika ada file) ATAU `PATCH /api/keluarga/pasangan/{id}` (Jika JSON murni)
- **Body Type:** `multipart/form-data` atau `application/json`
- **Tabel Parameter:** Menggunakan field yang sama dengan pembuatan (semuanya opsional saat *update*).
- **Contoh Request Payload (JSON / PATCH):**
  ```json
  {
    "pekerjaan": "Wiraswasta",
    "status_tanggungan": 0
  }
  ```
- **Response:** `200 OK`
  ```json
  {
    "success": true,
    "message": "Data pasangan berhasil diperbarui.",
    "data": {
      "id": 1,
      "nama_lengkap": "Budi Santoso",
      "buku_nikah_file_path": "/public/dokumen/pasangan/FILE_ABC123.pdf"
    }
  }
  ```

#### D. Hapus Data Pasangan
- **Nama Fitur:** Menghapus Data Pasangan
- **Route:** `DELETE /api/keluarga/pasangan/{id}`
- **Response:** `200 OK`
  ```json
  {
    "success": true,
    "message": "Data pasangan berhasil dihapus.",
    "data": {
      "id": 1
    }
  }
  ```

---

### 3. Modul Anak

#### A. Get Data Anak
- **Nama Fitur:** Mendapatkan Daftar Anak
- **Penjelasan:** Mengambil daftar seluruh data anak milik pegawai yang sedang login.
- **Route:** `GET /api/keluarga/anak`
- **Headers:** `Authorization: Bearer {token}`
- **Response:** `200 OK`
  ```json
  {
    "success": true,
    "message": "Data anak berhasil diambil.",
    "data": [
      {
        "id": 1,
        "nama_lengkap": "Putri Santoso",
        "usia": 8
      }
    ]
  }
  ```

#### B. Tambah Data Anak
- **Nama Fitur:** Menambahkan Anak Baru
- **Penjelasan:** Membuat entri anak baru beserta unggah dokumen akta kelahiran.
- **Route:** `POST /api/keluarga/anak`
- **Body Type:** `multipart/form-data`
- **Tabel Parameter:**

| Field | Tipe | Wajib | Keterangan |
|-------|------|-------|------------|
| `nama_lengkap` | String | Ya | Nama anak |
| `nik` | String | Tidak | NIK anak |
| `tempat_lahir` | String | Tidak | - |
| `tanggal_lahir` | Date (Y-m-d) | Tidak | Contoh: `2018-05-15` |
| `jenis_kelamin` | String | Tidak | `L` atau `P` |
| `status_anak` | String | Tidak | Kandung, Tiri, Angkat |
| `pendidikan_terakhir`| String | Tidak | - |
| `status_tanggungan` | Boolean/Int | Tidak | `1` atau `0` |
| `usia` | Integer | Tidak | Usia dalam tahun |
| `keterangan_disabilitas` | String | Tidak | - |
| `akta_kelahiran_file` | File (PDF/Image)| Tidak | Maksimal 2MB |

- **Contoh Request Payload (Form-Data):**
  ```text
  nama_lengkap: Putri Santoso
  jenis_kelamin: P
  status_anak: Kandung
  akta_kelahiran_file: <File Binary>
  ```
- **Response:** `201 Created`
  ```json
  {
    "success": true,
    "message": "Data anak berhasil ditambahkan.",
    "data": {
      "id": 1,
      "nama_lengkap": "Putri Santoso",
      "akta_kelahiran_file_path": "/public/dokumen/anak/FILE_XYZ789.pdf"
    }
  }
  ```

#### C. Ubah Data Anak
- **Nama Fitur:** Memperbarui Data Anak
- **Route:** `POST /api/keluarga/anak/{id}` (dengan form-data) ATAU `PATCH /api/keluarga/anak/{id}` (JSON murni)
- **Body Type:** `multipart/form-data` atau `application/json`
- **Contoh Request Payload (JSON / PATCH):**
  ```json
  {
    "pendidikan_terakhir": "SD",
    "usia": 8
  }
  ```
- **Response:** `200 OK`
  ```json
  {
    "success": true,
    "message": "Data anak berhasil diperbarui.",
    "data": {
      "id": 1,
      "nama_lengkap": "Putri Santoso",
      "akta_kelahiran_file_path": "/public/dokumen/anak/FILE_XYZ789.pdf"
    }
  }
  ```

#### D. Hapus Data Anak
- **Route:** `DELETE /api/keluarga/anak/{id}`
- **Response:** `200 OK`
  ```json
  {
    "success": true,
    "message": "Data anak berhasil dihapus.",
    "data": { "id": 1 }
  }
  ```

---

### 4. Modul Orang Tua

#### A. Get Data Orang Tua
- **Nama Fitur:** Mendapatkan Daftar Orang Tua
- **Penjelasan:** Mengambil daftar seluruh data orang tua milik pegawai yang sedang login.
- **Route:** `GET /api/keluarga/orang-tua`
- **Headers:** `Authorization: Bearer {token}`
- **Response:** `200 OK`
  ```json
  {
    "success": true,
    "message": "Data orang tua berhasil diambil.",
    "data": [
      {
        "id": 1,
        "nama_ayah": "Agus Santoso",
        "nama_ibu": "Siti Aminah"
      }
    ]
  }
  ```

#### B. Tambah Data Orang Tua
- **Nama Fitur:** Menambahkan Orang Tua
- **Route:** `POST /api/keluarga/orang-tua`
- **Body Type:** `application/json` atau `application/x-www-form-urlencoded`
- **Tabel Parameter:**

| Field | Tipe | Wajib | Keterangan |
|-------|------|-------|------------|
| `nama_ayah` | String | Tidak | - |
| `nama_ibu` | String | Tidak | - |
| `status_hidup` | String | Tidak | Hidup, Meninggal, dsb |
| `alamat` | String | Tidak | Alamat domisili |

- **Contoh Request Payload (JSON):**
  ```json
  {
    "nama_ayah": "Agus Santoso",
    "nama_ibu": "Siti Aminah",
    "status_hidup": "Hidup",
    "alamat": "Jl. Mawar No. 10"
  }
  ```
- **Response:** `201 Created`
  ```json
  {
    "success": true,
    "message": "Data orang tua berhasil ditambahkan.",
    "data": {
      "id": 1,
      "nama_ayah": "Agus Santoso",
      "nama_ibu": "Siti Aminah"
    }
  }
  ```

#### C. Ubah Data Orang Tua
- **Route:** `PATCH /api/keluarga/orang-tua/{id}`
- **Body Type:** `application/json`
- **Contoh Request Payload (JSON):**
  ```json
  {
    "status_hidup": "Meninggal",
    "alamat": "Pindah ke alamat lain"
  }
  ```
- **Response:** `200 OK`
  ```json
  {
    "success": true,
    "message": "Data orang tua berhasil diperbarui.",
    "data": {
      "id": 1,
      "nama_ayah": "Agus Santoso",
      "nama_ibu": "Siti Aminah"
    }
  }
  ```

#### D. Hapus Data Orang Tua
- **Route:** `DELETE /api/keluarga/orang-tua/{id}`
- **Response:** `200 OK`
  ```json
  {
    "success": true,
    "message": "Data orang tua berhasil dihapus.",
    "data": { "id": 1 }
  }
  ```

---

### 5. Modul Kontak Darurat

#### A. Get Data Kontak Darurat
- **Nama Fitur:** Mendapatkan Daftar Kontak Darurat
- **Penjelasan:** Mengambil daftar seluruh data kontak darurat milik pegawai yang sedang login.
- **Route:** `GET /api/keluarga/kontak-darurat`
- **Headers:** `Authorization: Bearer {token}`
- **Response:** `200 OK`
  ```json
  {
    "success": true,
    "message": "Data kontak darurat berhasil diambil.",
    "data": [
      {
        "id": 1,
        "nama_kontak": "Rudi Hartono",
        "hubungan_keluarga": "Saudara Kandung"
      }
    ]
  }
  ```

#### B. Tambah Data Kontak Darurat
- **Route:** `POST /api/keluarga/kontak-darurat`
- **Body Type:** `application/json` atau `application/x-www-form-urlencoded`
- **Tabel Parameter:**

| Field | Tipe | Wajib | Keterangan |
|-------|------|-------|------------|
| `nama_kontak` | String | Ya | Nama kerabat/kontak |
| `hubungan_keluarga` | String | Ya | Saudara Kandung, Paman, dll |
| `nomor_hp` | String | Ya | Nomor yang bisa dihubungi |
| `alamat` | String | Tidak | - |

- **Contoh Request Payload (JSON):**
  ```json
  {
    "nama_kontak": "Rudi Hartono",
    "hubungan_keluarga": "Saudara Kandung",
    "nomor_hp": "081234567890",
    "alamat": "Jl. Melati No. 5"
  }
  ```
- **Response:** `201 Created`
  ```json
  {
    "success": true,
    "message": "Data kontak darurat berhasil ditambahkan.",
    "data": {
      "id": 1,
      "nama_kontak": "Rudi Hartono"
    }
  }
  ```

#### C. Ubah Data Kontak Darurat
- **Route:** `PATCH /api/keluarga/kontak-darurat/{id}`
- **Body Type:** `application/json`
- **Contoh Request Payload (JSON):**
  ```json
  {
    "nomor_hp": "08987654321"
  }
  ```
- **Response:** `200 OK`
  ```json
  {
    "success": true,
    "message": "Data kontak darurat berhasil diperbarui.",
    "data": {
      "id": 1,
      "nama_kontak": "Rudi Hartono"
    }
  }
  ```

#### D. Hapus Data Kontak Darurat
- **Route:** `DELETE /api/keluarga/kontak-darurat/{id}`
- **Response:** `200 OK`
  ```json
  {
    "success": true,
    "message": "Data kontak darurat berhasil dihapus.",
    "data": { "id": 1 }
  }
  ```

---

### 6. Modul Tanggungan Lain

Pegawai dapat mengelola data tanggungan lain secara mandiri (self-service). Semua operasi mengacu pada data milik pegawai yang sedang login berdasarkan JWT.

#### A. Get Data Tanggungan Lain
- **Nama Fitur:** Mendapatkan Daftar Tanggungan Lain
- **Route:** `GET /api/keluarga/tanggungan-lain`
- **Headers:** `Authorization: Bearer {token}`
- **Role:** `admin`, `pegawai`, `hrd`, `direktur`
- **Response:** `200 OK`
  ```json
  {
    "success": true,
    "message": "Data tanggungan lain berhasil diambil.",
    "data": {
      "label": "Data Tanggungan Lain",
      "total": 1,
      "items": [
        {
          "id": 1,
          "nama": "Budi Santoso",
          "hubungan_keluarga": "Adik",
          "status_tanggungan": true
        }
      ]
    }
  }
  ```

#### B. Tambah Data Tanggungan Lain
- **Route:** `POST /api/keluarga/tanggungan-lain`
- **Body Type:** `application/json`
- **Tabel Parameter:**

| Field | Tipe | Wajib | Keterangan |
|-------|------|-------|------------|
| `nama` | String | Ya | Nama tanggungan, max 255 karakter |
| `hubungan_keluarga` | String | Ya | Hubungan keluarga, max 100 karakter |
| `status_tanggungan` | Boolean | Tidak | Status tanggungan aktif/tidak |

- **Contoh Request Payload (JSON):**
  ```json
  {
    "nama": "Budi Santoso",
    "hubungan_keluarga": "Adik",
    "status_tanggungan": true
  }
  ```
- **Response:** `201 Created`
  ```json
  {
    "success": true,
    "message": "Data tanggungan lain berhasil ditambahkan.",
    "data": {
      "id": 1,
      "nama": "Budi Santoso"
    }
  }
  ```

#### C. Ubah Data Tanggungan Lain
- **Route:** `PATCH /api/keluarga/tanggungan-lain/{id}`
- **Body Type:** `application/json`
- **Contoh Request Payload (JSON):**
  ```json
  {
    "status_tanggungan": false
  }
  ```
- **Response:** `200 OK`
  ```json
  {
    "success": true,
    "message": "Data tanggungan lain berhasil diperbarui.",
    "data": {
      "id": 1,
      "nama": "Budi Santoso"
    }
  }
  ```

#### D. Hapus Data Tanggungan Lain
- **Route:** `DELETE /api/keluarga/tanggungan-lain/{id}`
- **Response:** `200 OK`
  ```json
  {
    "success": true,
    "message": "Data tanggungan lain berhasil dihapus.",
    "data": { "id": 1 }
  }
  ```

---

### 17. Master Data (Form Dropdowns)

Endpoint master data menyediakan opsi dropdown untuk form dan CRUD master data (khusus HRD).

#### 17.1 List Master Data (Semua Role Login)

Semua endpoint list master data diakses menggunakan metode `GET` dan wajib menyertakan Header `Authorization: Bearer <token>`.
Respons mengembalikan array `data` yang memuat `id` dan `nama` untuk keperluan opsi *dropdown*.

**List Endpoint (GET):**

- `GET /api/form/kategori-diklat`
- `GET /api/form/tipe-diklat`
- `GET /api/form/jenis-pegawai`
- `GET /api/form/unit-kerja`
- `GET /api/form/jenis-biaya`
- `GET /api/form/golongan-ruang`
- `GET /api/form/profesi`
- `GET /api/form/jenis-sip`

**Contoh Response (`GET /api/form/jenis-pegawai`):**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "nama": "PNS"
    },
    {
      "id": 2,
      "nama": "PPPK"
    },
    {
      "id": 3,
      "nama": "BLUD"
    }
  ]
}
```

#### 17.2 CRUD Master Data (Khusus HRD)

Endpoint CRUD hanya untuk role `hrd` dan wajib menyertakan `Authorization: Bearer <token_hrd>`.

**List Endpoint (POST / PATCH / DELETE):**

- `POST /api/form/kategori-diklat`
- `PATCH /api/form/kategori-diklat/{id}`
- `DELETE /api/form/kategori-diklat/{id}`
- `POST /api/form/tipe-diklat`
- `PATCH /api/form/tipe-diklat/{id}`
- `DELETE /api/form/tipe-diklat/{id}`
- `POST /api/form/jenis-pegawai`
- `PATCH /api/form/jenis-pegawai/{id}`
- `DELETE /api/form/jenis-pegawai/{id}`
- `POST /api/form/unit-kerja`
- `PATCH /api/form/unit-kerja/{id}`
- `DELETE /api/form/unit-kerja/{id}`
- `POST /api/form/jenis-biaya`
- `PATCH /api/form/jenis-biaya/{id}`
- `DELETE /api/form/jenis-biaya/{id}`
- `POST /api/form/golongan-ruang`
- `PATCH /api/form/golongan-ruang/{id}`
- `DELETE /api/form/golongan-ruang/{id}`
- `POST /api/form/profesi`
- `PATCH /api/form/profesi/{id}`
- `DELETE /api/form/profesi/{id}`
- `POST /api/form/jenis-sip`
- `PATCH /api/form/jenis-sip/{id}`
- `DELETE /api/form/jenis-sip/{id}`

**Validasi Umum:**

- `nama`: wajib, string, maksimal 255, unik per master data.
- `kategori_tenaga`: opsional (khusus `profesi`), string, maksimal 100.

**Contoh Request (POST /api/form/jenis-sip):**
```json
{
  "nama": "SIP Praktik Mandiri"
}
```

**Contoh Response (201 Created):**
```json
{
  "success": true,
  "message": "Jenis SIP berhasil dibuat.",
  "data": {
    "id": 12,
    "nama": "SIP Praktik Mandiri"
  }
}
```

**Contoh Request (PATCH /api/form/profesi/3):**
```json
{
  "nama": "Perawat",
  "kategori_tenaga": "Tenaga Kesehatan"
}
```

**Contoh Response (200 OK):**
```json
{
  "success": true,
  "message": "Profesi berhasil diperbarui.",
  "data": {
    "id": 3,
    "nama": "Perawat",
    "kategori_tenaga": "Tenaga Kesehatan"
  }
}
```

**Contoh Response Delete (200 OK):**
```json
{
  "success": true,
  "message": "Jenis SIP berhasil dihapus.",
  "data": {
    "deleted_id": 12
  }
}
```


### 18. Pegawai

- Method: `GET`
- URL: `/api/pegawai`
- Auth: Wajib Bearer token
- Role yang diizinkan: `admin`, `hrd`, `direktur`

Mengambil daftar seluruh pegawai beserta ringkasan jumlahnya secara ter-paginasi. Data list dapat dicari dan difilter melalui query parameter.

Query parameter opsional:

| Parameter | Type | Default | Keterangan |
|-----------|------|---------|------------|
| `page` | Integer | `1` | Halaman yang diminta. |
| `per_page` | Integer | `10` | Jumlah data per halaman. Nilai dibatasi maksimal 100. |
| `search` | String | - | Cari berdasarkan `nama`, `nik`, atau nama profesi pegawai. |
| `status_kelengkapan` | String | - | Filter kelengkapan data: `lengkap` atau `belum-lengkap`. |
| `jenis_pegawai` | String | - | Filter nama jenis pegawai, contoh `PNS`, `PPPK`, atau `Pegawai Kontrak`. |
| `pendidikan` | String | - | Filter pendidikan terakhir, contoh `S1`, `D3`, atau `SMA/SMK Sederajat`. |
| `status_pegawai` | String | - | Filter status keaktifan pegawai, contoh `aktif` atau `tidak aktif`. |
| `profesi` | String | - | Filter nama profesi pegawai. |
| `tahun_masuk` | Integer | - | Filter berdasarkan tahun masuk pegawai, contoh `2023`. Hanya nilai angka valid yang diproses. |
| `tgl_masuk_dari` | String | - | Filter pegawai yang tanggal masuknya mulai dari tanggal ini, format `Y-m-d`. |
| `tgl_masuk_sampai` | String | - | Filter pegawai yang tanggal masuknya sampai dengan tanggal ini, format `Y-m-d`. |

Contoh URL dengan filter:

```http
GET /api/pegawai?page=1&per_page=10&search=budi&status_kelengkapan=lengkap&jenis_pegawai=PNS&pendidikan=S1&status_pegawai=aktif&profesi=Dokter
GET /api/pegawai?tahun_masuk=2023
GET /api/pegawai?tgl_masuk_dari=2022-01-01&tgl_masuk_sampai=2024-12-31
```

Catatan:

- Filter hanya memengaruhi data di paginator `pegawai`.
- Parameter filter `status_kelengkapan=lengkap` akan memvalidasi apakah pegawai telah mengisi Data Inti (`nik`/`nip`, `jenis_pegawai_id`, `profesi_id`, `tgl_masuk`) serta Data Pribadi lengkap beserta unggahan dokumen KTP (`ktp_file_path`) dan Kartu Keluarga (`kk_file_path`).
- `total_pegawai`, `jumlah_dokter`, `jumlah_perawat`, dan `jumlah_profesi` adalah ringkasan global sesuai implementasi saat ini.
- `jumlah_pegawai_aktif`, `jumlah_hrd`, dan `jumlah_direktur` adalah jumlah global user berdasarkan role dan tidak ikut terfilter oleh parameter pencarian.

Contoh response `200 OK`:

```json
{
  "success": true,
  "message": "Data pegawai berhasil diambil",
  "data": {
    "total_pegawai": 100,
    "jumlah_dokter": 10,
    "jumlah_perawat": 30,
    "jumlah_profesi": 15,
    "jumlah_pegawai_aktif": 2,
    "jumlah_hrd": 3,
    "jumlah_direktur": 1,
    "pegawai": {
      "current_page": 1,
      "data": [
        {
          "id_pegawai": 1,
          "nama": "Dr. Andi",
          "nik": "198001012005011001",
          "role": "pegawai",
          "link_photo_profil": "/dokumen/foto/andi.jpg",
          "jabatan": "Dokter Spesialis",
          "unit_kerja": "Poli Penyakit Dalam",
          "email": "andi@example.com",
          "no_telp": "08123456789",
          "status": "aktif",
          "status_kelengkapan": "Lengkap"
        }
      ],
      "first_page_url": "http://localhost:8000/api/pegawai?page=1",
      "from": 1,
      "last_page": 10,
      "last_page_url": "http://localhost:8000/api/pegawai?page=10",
      "links": [
        {
          "url": null,
          "label": "&laquo; Previous",
          "active": false
        },
        {
          "url": "http://localhost:8000/api/pegawai?page=1",
          "label": "1",
          "active": true
        },
        {
          "url": "http://localhost:8000/api/pegawai?page=2",
          "label": "2",
          "active": false
        },
        {
          "url": "http://localhost:8000/api/pegawai?page=2",
          "label": "Next &raquo;",
          "active": false
        }
      ],
      "next_page_url": "http://localhost:8000/api/pegawai?page=2",
      "path": "http://localhost:8000/api/pegawai",
      "per_page": 10,
      "prev_page_url": null,
      "to": 10,
      "total": 100
    }
  }
}
```

#### Get Pegawai Detail (Admin/HRD/Direktur)

- Method: `GET`
- URL: `/api/pegawai/{id}`
- Auth: Wajib Bearer token
- Role yang diizinkan: `admin`, `hrd`, `direktur`

Endpoint ini digunakan untuk melihat seluruh detail data seorang pegawai (Data Pribadi, Riwayat Karir, Keluarga, dan Diklat) berdasarkan ID pegawai.

Contoh response sukses (`200 OK`):

```json
{
  "success": true,
  "message": "Detail data pegawai berhasil diambil",
  "data": {
    "pegawai": {
      "id_pegawai": 1,
      "nik": "3509...",
      "nip": "1990...",
      "nama": "Dr. Budi",
      "email": "budi@example.com",
      "link_photo_profil": "...",
      "jabatan": "Dokter Umum",
      "unit_kerja": "IGD",
      "profesi": "Dokter Umum",
      "golongan_ruang": "III/c",
      "pangkat": "Penata",
      "jenis_pegawai": "PNS",
      "status_pegawai": "aktif",
      "tgl_masuk": "2020-01-01",
      "tmt_cpns": "2020-01-01",
      "tmt_pns": "2021-01-01"
    },
    "pribadi": {
      "jenis_kelamin": "L",
      "tanggal_lahir": "1990-01-01",
      "agama": "Islam",
      "status_perkawinan": "Menikah",
      "alamat": "Jl. Mawar No. 1",
      "no_hp": "0812...",
      "no_telp": null,
      "ktp_file_path": "dokumen/ktp/ktp-1-1713500000.jpg",
      "kk_file_path": "dokumen/kk/kk-1-1713500000.pdf"
    },
    "keluarga": {
      "pasangan": [],
      "anak": [],
      "orang_tua": [],
      "kontak_darurat": [],
      "tanggungan_lain": []
    },
    "riwayat_karir": {
      "pendidikan": [
        {
          "id": 1,
          "jenjang": "S1/D4",
          "institusi": "Universitas Jember",
          "jurusan": "Kedokteran",
          "tahun_lulus": "2015",
          "nomor_ijazah": "IJZ/2015/001",
          "ijazah_file_path": "dokumen/ijazah/ijazah-1-1713500000.pdf"
        }
      ],
      "jabatan": [
        {
          "id": 1,
          "jabatan": "Dokter Umum",
          "unit_kerja": "IGD",
          "tanggal_mulai": "2020-01-01",
          "tanggal_selesai": null,
          "is_current": true,
          "file_path": "dokumen/sk/sk-jabatan-123.pdf"
        }
      ],
      "pangkat": [
        {
          "id": 1,
          "pangkat": "Penata",
          "pejabat_penetap": "Bupati Jember",
          "tanggal_mulai": "2021-01-01",
          "tanggal_selesai": null,
          "is_current": true
        }
      ],
      "str": [
        {
          "id": 1,
          "nomor_str": "123456789",
          "tanggal_terbit": "2023-01-01",
          "tanggal_kadaluarsa": "2028-01-01",
          "status": "aktif",
          "file_path": "dokumen/str/str-123.pdf"
        }
      ],
      "sip": [
        {
          "id": 1,
          "jenis_sip": "SIP Dokter Umum",
          "nomor_sip": "987654321",
          "tanggal_terbit": "2023-01-01",
          "tanggal_kadaluarsa": "2028-01-01",
          "status": "aktif",
          "file_path": "dokumen/sip/sip-123.pdf"
        }
      ],
      "penugasan_klinis": [
        {
          "id": 1,
          "nomor_surat": "PK-001",
          "tanggal_mulai": "2023-01-01",
          "tanggal_kadaluarsa": "2026-01-01",
          "status": "aktif",
          "file_path": "dokumen/penugasan-klinis/pk-123.pdf"
        }
      ]
    },
    "diklat": [
      {
        "id": 1,
        "nama": "Pelatihan BHD",
        "jenis": "Klinis",
        "kategori": "Wajib",
        "penyelenggara": "RS Kalisat",
        "tanggal_mulai": "2023-01-01",
        "tanggal_selesai": "2023-01-02",
        "waktu": "08:00:00",
        "created_by": "HRD SIMPEG",
        "jp": 16,
        "total_biaya": "2500000.00",
        "jenis_biaya": "BLUD",
        "jenis_pelaksana": "internal",
        "catatan": "Contoh catatan diklat.",
        "sertif": "dokumen/sertif-diklat/sertif-pegawai.pdf",
        "no_sertif": "SERTIF/INT/RSK/2026/0001",
        "status_diklat": "sudah terlaksana",
        "status_kelayakan": "layak",
        "status_validasi": "valid"
      }
    ]
  }
}
```

#### Get Pegawai Detail Per Bagian (Admin/HRD/Direktur)

- Method: `GET`
- URL: `/api/pegawai/{id}/{bagian}`
- Auth: Wajib Bearer token
- Role yang diizinkan: `admin`, `hrd`, `direktur`

Endpoint ini digunakan ketika frontend hanya membutuhkan satu bagian dari detail pegawai, tanpa mengambil seluruh payload detail. Nilai `{bagian}` yang valid:

| Bagian | URL | Isi `data` |
|--------|-----|------------|
| `pegawai` | `/api/pegawai/{id}/pegawai` | Hanya objek `pegawai` |
| `keluarga` | `/api/pegawai/{id}/keluarga` | Hanya objek `keluarga` |
| `riwayat-karir` | `/api/pegawai/{id}/riwayat-karir` | Hanya objek `riwayat_karir` |
| `diklat` | `/api/pegawai/{id}/diklat` | Paginator data `diklat` |

Query parameter khusus `/api/pegawai/{id}/diklat`:

| Parameter | Type | Default | Keterangan |
|-----------|------|---------|------------|
| `page` | Integer | `1` | Halaman data diklat. |
| `per_page` | Integer | `10` | Jumlah data per halaman. Nilai dibatasi maksimal 100. |
| `search` | String | - | Cari pada nama diklat, jenis, kategori, penyelenggara, pembuat, jenis biaya, jenis pelaksana, catatan, sertifikat, nomor sertifikat, dan status. |
| `kelayakan` / `status_kelayakan` | String | - | Default menyembunyikan diklat dengan `status_kelayakan` `tidak layak` atau `false`. Gunakan `all` / `semua` untuk menampilkan semua, atau isi `layak` / `tidak layak` untuk filter spesifik. |
| `all` | Boolean/String | - | Alternatif untuk menampilkan semua diklat. Nilai yang diterima: `true`, `1`, `yes`, `all`, atau `semua`. |
| `jenis` | String | - | Filter nama jenis diklat. |
| `kategori` | String | - | Filter nama kategori diklat. |
| `status_diklat` | String | - | Filter status pelaksanaan diklat. |
| `status_validasi` | String | - | Filter status validasi. |
| `jenis_pelaksana` / `jenis_pelaksanaan` | String | - | Filter jenis pelaksanaan, contoh `internal` atau `external`. |
| `jenis_biaya` | String | - | Filter jenis biaya. |
| `created_by` | String | - | Filter nama pembuat diklat. |

Contoh URL:

```http
GET /api/pegawai/3/diklat?page=1&per_page=10&search=keselamatan
GET /api/pegawai/3/diklat?kelayakan=all&page=1&per_page=10
GET /api/pegawai/3/diklat?status_kelayakan=tidak%20layak
GET /api/pegawai/3/diklat?jenis=Tenkes&kategori=Teknis&status_diklat=sudah%20terlaksana
```

Contoh response `GET /api/pegawai/3/diklat`:

```json
{
  "success": true,
  "message": "Detail data pegawai berhasil diambil",
  "data": {
    "diklat": {
      "current_page": 1,
      "data": [
        {
          "id": 2,
          "nama": "Internal - Pelatihan Keselamatan Pasien",
          "jenis": "Tenkes",
          "kategori": "Teknis",
          "penyelenggara": "Bagian SDM RS Kalisat",
          "tanggal_mulai": "2026-06-15",
          "tanggal_selesai": "2026-06-17",
          "waktu": "09:00:00",
          "created_by": "HRD SIMPEG",
          "jp": 12,
          "total_biaya": "1800000.00",
          "jenis_biaya": "APBD",
          "jenis_pelaksana": "internal",
          "catatan": "Contoh internal lengkap dan sudah valid.",
          "sertif": "dokumen/sertif-diklat/budi-keselamatan-pasien.pdf",
          "no_sertif": "SERTIF/INT/RSK/2026/0001",
          "status_diklat": "sudah terlaksana",
          "status_kelayakan": "layak",
          "status_validasi": "valid"
        }
      ],
      "from": 1,
      "last_page": 1,
      "per_page": 10,
      "to": 1,
      "total": 1
    }
  }
}
```

Contoh response `GET /api/pegawai/3/keluarga`:

```json
{
  "success": true,
  "message": "Detail data pegawai berhasil diambil",
  "data": {
    "keluarga": {
      "pasangan": [],
      "anak": [],
      "orang_tua": [],
      "kontak_darurat": [],
      "tanggungan_lain": []
    }
  }
}
```

Catatan:

- `/api/pegawai/{id}` tetap mengembalikan semua bagian: `pegawai`, `pribadi`, `keluarga`, `riwayat_karir`, dan `diklat`.
- `/api/pegawai/{id}/pegawai` hanya mengembalikan objek `pegawai`. Data `pribadi` tetap tersedia pada endpoint full detail `/api/pegawai/{id}`.
- `/api/pegawai/{id}/diklat` secara default tidak menampilkan diklat dengan `status_kelayakan` `tidak layak` atau `false`. Gunakan `?kelayakan=all` atau `?all=true` untuk menampilkan semua diklat.
- Route `{bagian}` dibatasi pada `pegawai`, `keluarga`, `riwayat-karir`, dan `diklat`.

Catatan status STR/SIP/Penugasan Klinis:

- Field `status` pada STR dan SIP dihitung dari `tanggal_kadaluarsa`.
- Field `status` pada Penugasan Klinis dihitung dari `tgl_kadaluarsa` / `tanggal_kadaluarsa`.
- Jika tanggal kadaluarsa sudah lewat dari tanggal hari ini, status bernilai `tidak aktif`.
- Jika tanggal kadaluarsa masih hari ini atau di masa depan, status bernilai `aktif`.
- Jika tanggal kadaluarsa kosong, status dianggap `aktif`.

#### Tambah Data Pegawai Baru (Hanya Admin)

- **Route:** `POST /api/pegawai`
- **Body Type:** `application/json`
- **Auth:** Wajib Bearer token
- **Role yang diizinkan:** `admin`

Digunakan oleh Admin untuk membuat data pegawai baru dengan informasi yang sangat dasar (NIK, Nama, Password). Akun `User` untuk pegawai tersebut akan terbuat otomatis.

- **Request Payload:**

| Field | Type | Wajib | Keterangan |
|-------|------|-------|------------|
| `nik` | String | Ya | NIK Pegawai (Max 16 karakter, harus unik) |
| `nama` | String | Ya | Nama Pegawai |
| `password` | String | Ya | Password untuk akun Pegawai (Min 6 karakter) |

- **Contoh Request Payload (JSON):**
  ```json
  {
    "nik": "3509191234567890",
    "nama": "Ahmad Subarjo",
    "password": "password123"
  }
  ```

- **Response:** `201 Created`
  ```json
  {
    "success": true,
    "message": "Pegawai berhasil ditambahkan",
    "data": {
      "id": 101,
      "nik": "3509191234567890",
      "nama": "Ahmad Subarjo"
    }
  }
  ```

#### Ubah Role / Status Pegawai (Hanya Admin)

- **Route:** `PATCH /api/pegawai/{id}/change-role`
- **Body Type:** `application/json`
- **Auth:** Wajib Bearer token
- **Role yang diizinkan:** `admin`

Digunakan oleh Admin untuk mengubah role akun Pegawai yang sudah ada, serta mengubah status pegawainya. Catatan: Admin tidak dapat mengubah role/statusnya sendiri.

- **Request Payload:**

| Field | Type | Wajib | Keterangan |
|-------|------|-------|------------|
| `role` | String | Opsional | Salah satu dari: `pegawai`, `admin`, `hrd`, `direktur` |
| `status_pegawai` | String | Opsional | Salah satu dari: `aktif`, `tidak aktif` |

Minimal salah satu field `role` atau `status_pegawai` harus dikirim.

- **Contoh Request Payload (JSON):**
  ```json
  {
    "role": "hrd",
    "status_pegawai": "aktif"
  }
  ```

- **Response:** `200 OK`
  ```json
  {
    "success": true,
    "message": "Role/status pegawai berhasil diubah",
    "data": {
      "id": 101,
      "nik": "3509191234567890",
      "nama": "Ahmad Subarjo",
      "role": "hrd",
      "status_pegawai": "aktif"
    }
  }
  ```

- **Response Gagal (Mengubah diri sendiri):** `400 Bad Request`
  ```json
  {
    "success": false,
    "message": "Tidak dapat mengubah role/status diri sendiri."
  }
  ```

#### Ubah NIK Sendiri (Hanya Admin)

- **Route:** `PATCH /api/auth/change-nik`
- **Body Type:** `application/json`
- **Auth:** Wajib Bearer token
- **Role yang diizinkan:** `admin`

Digunakan oleh Admin untuk mengubah NIK-nya sendiri. NIK juga merupakan `username` untuk login, sehingga perubahan ini akan memperbarui data di tabel `pegawai` dan `users` sekaligus.

- **Request Payload:**

| Field | Type | Wajib | Keterangan |
|-------|------|-------|------------|
| `nik` | String | Ya | NIK baru, max 20 karakter, harus unik |

- **Contoh Request Payload (JSON):**
  ```json
  {
    "nik": "3509199999900001"
  }
  ```

- **Response:** `200 OK`
  ```json
  {
    "success": true,
    "message": "NIK berhasil diubah.",
    "data": {
      "nik": "3509199999900001"
    }
  }
  ```

- **Response Gagal (NIK sudah digunakan):** `422 Unprocessable Entity`
  ```json
  {
    "success": false,
    "message": "NIK sudah digunakan."
  }
  ```

- **Response Gagal (user tidak ditemukan):** `404 Not Found`
  ```json
  {
    "success": false,
    "message": "User tidak ditemukan."
  }
  ```

---

### 19. STR/SIP (Admin/HRD/Direktur)

- Method: `GET`
- URL: `/api/str-sip`
- Auth: Wajib Bearer token
- Role yang diizinkan: `admin`, `hrd`, `direktur`

Mengambil daftar STR dan SIP seluruh pegawai beserta ringkasan statusnya. Mendukung pencarian, filter tipe, status, jenis SIP, dan rentang tanggal kadaluarsa.

Query parameter opsional:

| Parameter | Type | Default | Keterangan |
|-----------|------|---------|------------|
| `page` | Integer | `1` | Halaman yang diminta. |
| `per_page` | Integer | `15` | Jumlah data per halaman. Nilai dibatasi maksimal 100. |
| `search` | String | - | Cari berdasarkan nama pegawai. |
| `tipe` | String | - | Filter tipe dokumen: `STR` atau `SIP`. Jika tidak diisi, menampilkan keduanya. |
| `jenis_sip` | String | - | Filter berdasarkan nama jenis SIP, contoh `Dokter Umum`. Hanya berlaku jika `tipe=SIP`. |
| `status` | String | - | Filter status: `aktif`, `hampir_habis`, atau `tidak_aktif`. |
| `tanggal_dari` | String | - | Filter tanggal kadaluarsa mulai dari, format `Y-m-d`. |
| `tanggal_sampai` | String | - | Filter tanggal kadaluarsa sampai dengan, format `Y-m-d`. |

Contoh URL dengan filter:

```http
GET /api/str-sip?search=budi&tipe=SIP&status=hampir_habis
GET /api/str-sip?tanggal_dari=2026-01-01&tanggal_sampai=2026-12-31&tipe=STR
GET /api/str-sip?jenis_sip=Dokter+Umum&status=aktif
```

Aturan status:

- **Aktif:** `tanggal_selesai > today + 30 hari`
- **Hampir Habis:** `tanggal_selesai` antara `today` dan `today + 30 hari`
- **Tidak Aktif:** `tanggal_selesai < today` atau `tanggal_selesai` kosong

Catatan:

- Field `summary` mencerminkan jumlah dari data yang sudah difilter (sesuai parameter yang dikirim).
- Filter `status`, `tanggal_dari`, `tanggal_sampai`, dan `search` diterapkan pada level query database.
- Field `jenis` pada item STR selalu `null`; field `jenis` pada item SIP berisi nama jenis SIP.

Contoh response `200 OK`:

```json
{
  "success": true,
  "message": "Data STR/SIP berhasil diambil.",
  "data": {
    "summary": {
      "total": 45,
      "aktif": 10,
      "hampir_habis": 5,
      "tidak_aktif": 3
    },
    "items": [
      {
        "id": 10,
        "pegawai_id": 1,
        "nama": "Dr. Ahmad Wijaya",
        "nip": "198501152010011001",
        "profesi": "Dokter Spesialis Penyakit Dalam",
        "str_sip": "STR",
        "jenis": null,
        "nomor": "123415161717",
        "link_pdf": "/dokumen/str/sk-str-1-1715778987.pdf",
        "tanggal_terbit": "2026-03-25",
        "tanggal_selesai": "2026-03-30",
        "status": "Aktif",
        "is_current": true
      },
      {
        "id": 11,
        "pegawai_id": 1,
        "nama": "Dr. Ahmad Wijaya",
        "nip": "198501152010011001",
        "profesi": "Dokter Spesialis Penyakit Dalam",
        "str_sip": "SIP",
        "jenis": "SIP Dokter",
        "nomor": "123415161717",
        "link_pdf": "/dokumen/sip/sk-sip-1-1715778987.pdf",
        "tanggal_terbit": "2026-03-25",
        "tanggal_selesai": "2026-03-30",
        "status": "Hampir Habis",
        "is_current": true
      }
    ]
  }
}
```

### 20. Generate CV

- Method: `GET`
- URL: `/api/generate/cv`
- Auth: Wajib Bearer token
- Role yang diizinkan: `admin`, `pegawai`, `hrd`, `direktur`
- Query opsional: `?pegawai_id={id}` (Hanya berlaku untuk role `admin`, `hrd`, dan `direktur` jika ingin melihat CV orang lain).

Mengambil data terstruktur untuk pembuatan CV. Jika tidak menyertakan `pegawai_id`, otomatis akan menarik data milik pengguna yang sedang login.

Contoh response `200 OK`:

```json
{
  "success": true,
  "message": "Data CV berhasil diambil.",
  "data": {
    "header": {
      "nama": "SITI RAHAYU NINGRUM, A.Md.Kep.",
      "alamat": "Jl. Mawar No. 14, Kalisat, Jember",
      "no_telp": "+62 813-5678-9012",
      "email": "siti.rahayu@rsdkalisat.go.id"
    },
    "profil": {
      "jabatan": "Perawat Terampil",
      "profesi": "Perawat",
      "unit_kerja": "Instalasi Rawat Inap RSD Kalisat",
      "masa_kerja": "8 tahun 2 bulan"
    },
    "data_diri": {
      "nip": "199002152012032001",
      "nik": "3509121234567890",
      "tanggal_lahir": "1990-02-15",
      "jenis_kelamin": "P",
      "golongan_ruang": "II/c",
      "pangkat": "Pengatur",
      "jabatan": "Perawat Terampil",
      "unit_kerja": "Instalasi Rawat Inap RSD Kalisat",
      "tmt_pns": "2012-03-01",
      "status_kepegawaian": "PNS"
    },
    "pendidikan": [
      {
        "jenjang": "D-III",
        "jurusan": "Keperawatan",
        "institusi": "Politeknik Kesehatan Kemenkes Malang",
        "tahun_lulus": 2011
      }
    ],
    "diklat": [
      {
        "nama": "Pelatihan Basic Life Support (BLS)",
        "jenis": "Teknis",
        "pelaksana": "RSUD Dr. Soebandi Jember",
        "tanggal_mulai": "2022-03-10",
        "tanggal_selesai": "2022-03-12",
        "jp": 24,
        "no_sertif": "BLS-001/2022"
      }
    ],
    "riwayat_jabatan": [
      {
        "jabatan": "Perawat Terampil",
        "unit_kerja": "Instalasi Rawat Inap RSD Kalisat",
        "tanggal_mulai": "2018-01-01",
        "tanggal_selesai": "-",
        "is_current": true,
        "catatan": "Jabatan aktif saat ini."
      }
    ],
    "str": [
      {
        "nomor_str": "1234567890",
        "tanggal_terbit": "2020-05-10",
        "tanggal_kadaluarsa": "2025-05-10"
      }
    ],
    "sip": [
      {
        "jenis_sip": "SIP Praktik Rumah Sakit",
        "nomor_sip": "SIP-1234",
        "tanggal_terbit": "2021-06-01",
        "tanggal_kadaluarsa": "2026-06-01"
      }
    ],
    "penugasan_klinis": [
      {
        "nomor_surat": "SK-KLINIS-2022",
        "tanggal_mulai": "2022-01-01",
        "tanggal_kadaluarsa": "2026-12-31"
      }
    ],
    "ttd": {
      "kota": "Kalisat",
      "tanggal": "2026-04-28"
    }
  }
}
```

### 21. Settings (Admin)

Endpoint ini digunakan untuk mengelola konfigurasi sistem. Saat ini baru mendukung pengelolaan token WhatsApp.

#### GET Pengaturan WhatsApp

- Method: `GET`
- URL: `/api/settings/whatsapp`
- Auth: Wajib Bearer token
- Role yang diizinkan: `admin`

Mengambil token WhatsApp saat ini dan informasi perangkat Fonnte yang terhubung.

Contoh response `200 OK`:

```json
{
  "success": true,
  "message": "Berhasil mengambil pengaturan WhatsApp",
  "data": {
    "whatsapp_token": "mnsve3hD8m9qLLq6gW8n",
    "device": {
      "device": "628970702352",
      "device_status": "connect",
      "name": "Admin WhatsApp",
      "quota": "971"
    }
  }
}
```

#### PUT Pengaturan WhatsApp

- Method: `PUT`
- URL: `/api/settings/whatsapp`
- Auth: Wajib Bearer token
- Role yang diizinkan: `admin`

Menyimpan atau memperbarui token WhatsApp.

Request body:

```json
{
  "whatsapp_token": "tokenbaru123"
}
```

Contoh response `200 OK`:

```json
{
  "success": true,
  "message": "Pengaturan WhatsApp berhasil diperbarui",
  "data": {
    "whatsapp_token": "tokenbaru123"
  }
}
```

### 21. HRD Manajemen Data Pegawai

Semua endpoint pada bagian ini hanya dapat diakses oleh role `hrd`. Parameter `{id}` pada URL mengacu pada `pegawai_id` yang ingin dikelola, bukan `id` user yang sedang login.

Base URL semua endpoint di bagian ini: `/api/hrd/pegawai/{id}`

---

#### 21.1 Update Data Inti Pegawai

- Method: `PATCH`
- URL: `/api/hrd/pegawai/{id}/inti`
- Auth: Wajib Bearer token
- Role: `hrd`
- Content-Type: `application/json`

Semua field bersifat opsional (`sometimes`). Hanya field yang dikirim yang akan diupdate.

| Field | Type | Deskripsi |
|-------|------|-----------|
| `nik` | string, max:20 | Nomor Induk Kependudukan |
| `nip` | string\|null, max:20 | Nomor Induk Pegawai |
| `nama` | string, max:255 | Nama lengkap |
| `jenis_pegawai_id` | integer\|null | FK ke tabel jenis_pegawai |
| `profesi_id` | integer\|null | FK ke tabel profesi |
| `golongan_ruang_id` | integer\|null | FK ke tabel golongan_ruang |
| `status_pegawai` | string | `aktif`, `nonaktif`, atau `cuti` |
| `tgl_masuk` | date\|null | Tanggal masuk kerja (Y-m-d) |
| `tmt_cpns` | date\|null | TMT CPNS (Y-m-d) |
| `tmt_pns` | date\|null | TMT PNS (Y-m-d) |

Response `200 OK`:

```json
{
  "success": true,
  "message": "Data inti pegawai berhasil diperbarui.",
  "data": {
    "id": 1,
    "nik": "3174010101010001",
    "nip": "199001012020011001",
    "nama": "Budi Santoso",
    "status_pegawai": "aktif",
    "tgl_masuk": "2020-01-01"
  }
}
```

Response `422 Unprocessable Entity`:
```json
{"success": false, "message": "Data pegawai tidak ditemukan."}
```

---

#### 21.2 Update Data Pribadi Pegawai

- Method: `PATCH` atau `POST`
- URL: `/api/hrd/pegawai/{id}/pribadi`
- Auth: Wajib Bearer token
- Role: `hrd`
- Content-Type: `multipart/form-data` (jika upload file) atau `application/json`

Semua field bersifat opsional.

| Field | Type | Deskripsi |
|-------|------|-----------|
| `jenis_kelamin` | string | `L` atau `P` |
| `tanggal_lahir` | date\|null | Format Y-m-d |
| `agama` | string\|null | Agama |
| `status_perkawinan` | string\|null | Status perkawinan |
| `alamat` | string\|null | Alamat lengkap |
| `no_telp` | string\|null | Nomor telepon (max:20) |
| `pendidikan_terakhir` | string\|null | Pendidikan terakhir (max:100) |
| `no_kk` | string\|null | Nomor KK (max:20) |
| `foto_profil` | file\|null | Foto profil (jpg/jpeg/png, max 2 MB) |
| `ktp_file` | file\|null | File KTP (pdf/jpg/jpeg/png, max 5 MB) |
| `kk_file` | file\|null | File KK (pdf/jpg/jpeg/png, max 5 MB) |

Catatan: `POST` digunakan sebagai alias untuk `PATCH` karena browser/form HTML tidak mendukung `PATCH` dengan `multipart/form-data`.

Response `200 OK`:
```json
{
  "success": true,
  "message": "Data pribadi pegawai berhasil diperbarui.",
  "data": {
    "id": 1,
    "jenis_kelamin": "L",
    "tanggal_lahir": "1990-01-01",
    "agama": "Islam",
    "status_perkawinan": "Menikah",
    "alamat": "Jl. Mawar No. 1",
    "no_telp": "081234567890"
  }
}
```

---

#### 21.3 Data Keluarga Pegawai (HRD)

Semua endpoint keluarga berada di bawah prefix `/api/hrd/pegawai/{id}/keluarga/`. Parameter `{keluargaId}` adalah `id` record keluarga yang ingin diubah atau dihapus.

Response sukses umum:
- `200 OK` untuk GET, update, dan delete
- `201 Created` untuk store

Response error umum:
- `422` jika pegawai tidak ditemukan atau validasi gagal
- `404` jika record keluarga tidak ditemukan (update/delete)

---

##### Pasangan

| Method | URL | Fungsi |
|--------|-----|--------|
| `GET` | `/api/hrd/pegawai/{id}/keluarga/pasangan` | List semua pasangan |
| `POST` | `/api/hrd/pegawai/{id}/keluarga/pasangan` | Tambah pasangan baru |
| `PATCH` / `POST` | `/api/hrd/pegawai/{id}/keluarga/pasangan/{keluargaId}` | Update data pasangan |
| `DELETE` | `/api/hrd/pegawai/{id}/keluarga/pasangan/{keluargaId}` | Hapus pasangan |

Body `POST` tambah pasangan (`multipart/form-data`):

| Field | Type | Deskripsi |
|-------|------|-----------|
| `nama` | string, required | Nama pasangan |
| `status_perkawinan` | string, required | Status perkawinan |
| `tgl_perkawinan` | date, nullable | Tanggal perkawinan |
| `pekerjaan` | string, nullable | Pekerjaan pasangan |
| `buku_nikah_file` | file, nullable | Buku nikah (pdf/jpg/png, max 5 MB) |

---

##### Anak

| Method | URL | Fungsi |
|--------|-----|--------|
| `GET` | `/api/hrd/pegawai/{id}/keluarga/anak` | List semua anak |
| `POST` | `/api/hrd/pegawai/{id}/keluarga/anak` | Tambah anak |
| `PATCH` / `POST` | `/api/hrd/pegawai/{id}/keluarga/anak/{keluargaId}` | Update data anak |
| `DELETE` | `/api/hrd/pegawai/{id}/keluarga/anak/{keluargaId}` | Hapus anak |

Body `POST` tambah anak (`multipart/form-data`):

| Field | Type | Deskripsi |
|-------|------|-----------|
| `nama` | string, required | Nama anak |
| `tanggal_lahir` | date, nullable | Tanggal lahir |
| `jenis_kelamin` | string, nullable | `L` atau `P` |
| `akta_kelahiran_file` | file, nullable | Akta kelahiran (pdf/jpg/png, max 5 MB) |

---

##### Orang Tua

| Method | URL | Fungsi |
|--------|-----|--------|
| `GET` | `/api/hrd/pegawai/{id}/keluarga/orang-tua` | List orang tua |
| `POST` | `/api/hrd/pegawai/{id}/keluarga/orang-tua` | Tambah orang tua |
| `PATCH` | `/api/hrd/pegawai/{id}/keluarga/orang-tua/{keluargaId}` | Update orang tua |
| `DELETE` | `/api/hrd/pegawai/{id}/keluarga/orang-tua/{keluargaId}` | Hapus orang tua |

Body `POST` tambah orang tua (`application/json`):

| Field | Type | Deskripsi |
|-------|------|-----------|
| `nama` | string, required | Nama orang tua |
| `hubungan` | string, required | `ayah` atau `ibu` |
| `tanggal_lahir` | date, nullable | Tanggal lahir |
| `pekerjaan` | string, nullable | Pekerjaan |

---

##### Kontak Darurat

| Method | URL | Fungsi |
|--------|-----|--------|
| `GET` | `/api/hrd/pegawai/{id}/keluarga/kontak-darurat` | List kontak darurat |
| `POST` | `/api/hrd/pegawai/{id}/keluarga/kontak-darurat` | Tambah kontak darurat |
| `PATCH` | `/api/hrd/pegawai/{id}/keluarga/kontak-darurat/{keluargaId}` | Update kontak darurat |
| `DELETE` | `/api/hrd/pegawai/{id}/keluarga/kontak-darurat/{keluargaId}` | Hapus kontak darurat |

Body `POST` tambah kontak darurat (`application/json`):

| Field | Type | Deskripsi |
|-------|------|-----------|
| `nama` | string, required | Nama kontak |
| `hubungan` | string, required | Hubungan dengan pegawai |
| `no_telp` | string, required | Nomor telepon |

---

##### Tanggungan Lain

| Method | URL | Fungsi |
|--------|-----|--------|
| `GET` | `/api/hrd/pegawai/{id}/keluarga/tanggungan-lain` | List tanggungan lain |
| `POST` | `/api/hrd/pegawai/{id}/keluarga/tanggungan-lain` | Tambah tanggungan lain |
| `PATCH` | `/api/hrd/pegawai/{id}/keluarga/tanggungan-lain/{keluargaId}` | Update tanggungan lain |
| `DELETE` | `/api/hrd/pegawai/{id}/keluarga/tanggungan-lain/{keluargaId}` | Hapus tanggungan lain |

Body `POST` tambah tanggungan lain (`application/json`):

| Field | Type | Deskripsi |
|-------|------|-----------|
| `nama` | string, required, max:255 | Nama tanggungan |
| `hubungan_keluarga` | string, required, max:100 | Hubungan keluarga |
| `status_tanggungan` | boolean, nullable | Status tanggungan |

---

#### 21.4 Riwayat Karir Pegawai (HRD)

Semua endpoint riwayat karir berada di bawah prefix `/api/hrd/pegawai/{id}/riwayat-karir/`. Parameter `{riwayatId}` adalah `id` record riwayat yang ingin diubah atau dihapus.

---

##### Jabatan

| Method | URL | Fungsi |
|--------|-----|--------|
| `GET` | `/api/hrd/pegawai/{id}/riwayat-karir/jabatan` | List riwayat jabatan |
| `POST` | `/api/hrd/pegawai/{id}/riwayat-karir/jabatan` | Tambah riwayat jabatan |
| `PATCH` / `POST` | `/api/hrd/pegawai/{id}/riwayat-karir/jabatan/{riwayatId}` | Update riwayat jabatan |
| `DELETE` | `/api/hrd/pegawai/{id}/riwayat-karir/jabatan/{riwayatId}` | Hapus riwayat jabatan |

Body `POST` tambah jabatan (`multipart/form-data`):

| Field | Type | Deskripsi |
|-------|------|-----------|
| `nama_jabatan` | string, required | Nama jabatan |
| `unit_kerja_id` | integer, nullable | FK ke unit_kerja |
| `tmt_mulai` | date, nullable | TMT mulai jabatan |
| `tmt_selesai` | date, nullable | TMT selesai jabatan |
| `is_current` | boolean, required | Jabatan aktif saat ini |
| `note` | string, nullable | Catatan |
| `sk_jabatan` | file, nullable | SK jabatan (pdf/jpg/png, max 5 MB) |

Response `200 OK` (list):
```json
{
  "success": true,
  "message": "Data riwayat jabatan berhasil diambil.",
  "data": {
    "label": "Riwayat jabatan",
    "total": 1,
    "items": [
      {
        "id": 1,
        "unit_kerja_id": 2,
        "unit_kerja_nama": "Instalasi Rawat Inap",
        "nama_jabatan": "Perawat Terampil",
        "is_current": true,
        "tmt_mulai": "2020-01-01",
        "tmt_selesai": null,
        "link_sk": "/dokumen/jabatan/sk-jabatan-1-1715778987.pdf",
        "note": ""
      }
    ]
  }
}
```

---

##### STR

| Method | URL | Fungsi |
|--------|-----|--------|
| `GET` | `/api/hrd/pegawai/{id}/riwayat-karir/str` | List riwayat STR |
| `POST` | `/api/hrd/pegawai/{id}/riwayat-karir/str` | Tambah riwayat STR |
| `PATCH` / `POST` | `/api/hrd/pegawai/{id}/riwayat-karir/str/{riwayatId}` | Update riwayat STR |
| `DELETE` | `/api/hrd/pegawai/{id}/riwayat-karir/str/{riwayatId}` | Hapus riwayat STR |

Body `POST` tambah STR (`multipart/form-data`):

| Field | Type | Deskripsi |
|-------|------|-----------|
| `nomor_str` | string, required | Nomor STR |
| `tanggal_terbit` | date, required | Tanggal terbit STR |
| `tanggal_kadaluarsa` | date, nullable | Tanggal kadaluarsa |
| `sk_str` | file, nullable | File STR (pdf/jpg/png, max 5 MB) |

---

##### SIP

| Method | URL | Fungsi |
|--------|-----|--------|
| `GET` | `/api/hrd/pegawai/{id}/riwayat-karir/sip` | List riwayat SIP |
| `POST` | `/api/hrd/pegawai/{id}/riwayat-karir/sip` | Tambah riwayat SIP |
| `PATCH` / `POST` | `/api/hrd/pegawai/{id}/riwayat-karir/sip/{riwayatId}` | Update riwayat SIP |
| `DELETE` | `/api/hrd/pegawai/{id}/riwayat-karir/sip/{riwayatId}` | Hapus riwayat SIP |

Body `POST` tambah SIP (`multipart/form-data`):

| Field | Type | Deskripsi |
|-------|------|-----------|
| `jenis_sip_id` | integer, nullable | FK ke jenis_sip |
| `nomor_sip` | string, required | Nomor SIP |
| `tanggal_terbit` | date, required | Tanggal terbit SIP |
| `tanggal_kadaluarsa` | date, nullable | Tanggal kadaluarsa |
| `sk_sip` | file, nullable | File SIP (pdf/jpg/png, max 5 MB) |

---

##### Penugasan Klinis

| Method | URL | Fungsi |
|--------|-----|--------|
| `GET` | `/api/hrd/pegawai/{id}/riwayat-karir/penugasan-klinis` | List penugasan klinis |
| `POST` | `/api/hrd/pegawai/{id}/riwayat-karir/penugasan-klinis` | Tambah penugasan klinis |
| `PATCH` / `POST` | `/api/hrd/pegawai/{id}/riwayat-karir/penugasan-klinis/{riwayatId}` | Update penugasan klinis |
| `DELETE` | `/api/hrd/pegawai/{id}/riwayat-karir/penugasan-klinis/{riwayatId}` | Hapus penugasan klinis |

Body `POST` tambah penugasan klinis (`multipart/form-data`):

| Field | Type | Deskripsi |
|-------|------|-----------|
| `nomor_surat` | string, required | Nomor surat penugasan |
| `tgl_mulai` | date, required | Tanggal mulai |
| `tgl_kadaluarsa` | date, nullable | Tanggal kadaluarsa |
| `dokumen_file` | file, nullable | Dokumen penugasan (pdf/jpg/png, max 5 MB) |

---

##### Pangkat

| Method | URL | Fungsi |
|--------|-----|--------|
| `GET` | `/api/hrd/pegawai/{id}/riwayat-karir/pangkat` | List riwayat pangkat |
| `POST` | `/api/hrd/pegawai/{id}/riwayat-karir/pangkat` | Tambah riwayat pangkat |
| `PATCH` / `POST` | `/api/hrd/pegawai/{id}/riwayat-karir/pangkat/{riwayatId}` | Update riwayat pangkat |
| `DELETE` | `/api/hrd/pegawai/{id}/riwayat-karir/pangkat/{riwayatId}` | Hapus riwayat pangkat |

Body `POST` tambah pangkat (`multipart/form-data`):

| Field | Type | Deskripsi |
|-------|------|-----------|
| `nama_pangkat` | string, required | Nama pangkat |
| `pejabat_penetap` | string, nullable | Pejabat yang menetapkan |
| `tmt_sk` | date, nullable | TMT SK pangkat |
| `is_current` | boolean, required | Pangkat aktif |
| `started_at` | date, nullable | Tanggal mulai berlaku |
| `ended_at` | date, nullable | Tanggal selesai berlaku |
| `note` | string, nullable | Catatan |
| `sk_pangkat` | file, nullable | SK pangkat (pdf/jpg/png, max 5 MB) |

---

##### Pendidikan

| Method | URL | Fungsi |
|--------|-----|--------|
| `GET` | `/api/hrd/pegawai/{id}/riwayat-karir/pendidikan` | List riwayat pendidikan |
| `POST` | `/api/hrd/pegawai/{id}/riwayat-karir/pendidikan` | Tambah riwayat pendidikan |
| `PATCH` / `POST` | `/api/hrd/pegawai/{id}/riwayat-karir/pendidikan/{riwayatId}` | Update riwayat pendidikan |
| `DELETE` | `/api/hrd/pegawai/{id}/riwayat-karir/pendidikan/{riwayatId}` | Hapus riwayat pendidikan |

> **Catatan:** Model `Pendidikan` menggunakan `pegawai_pribadi_id` (FK ke `pegawai_pribadi`). Service secara otomatis meresolve `pegawai_id → pribadi_id`.

Body `POST` tambah pendidikan (`multipart/form-data`):

| Field | Type | Deskripsi |
|-------|------|-----------|
| `jenjang` | string, required, max:50 | Jenjang pendidikan (SD, SMP, SMA, D3, S1, S2, S3) |
| `institusi` | string, required, max:255 | Nama institusi/universitas |
| `jurusan` | string, required, max:255 | Jurusan/program studi |
| `tahun_lulus` | integer, required | Tahun kelulusan (1900–2100) |
| `nomor_ijazah` | string, nullable, max:100 | Nomor ijazah |
| `ijazah` | file, nullable | File ijazah (pdf/jpg/jpeg/png/webp, max 5 MB) |

Response `200 OK` (list):
```json
{
  "success": true,
  "message": "Data riwayat pendidikan berhasil diambil.",
  "data": {
    "label": "Riwayat pendidikan",
    "total": 1,
    "items": [
      {
        "id": 1,
        "jenjang": "S1",
        "institusi": "Universitas Jember",
        "jurusan": "Kedokteran",
        "tahun_lulus": 2015,
        "nomor_ijazah": "123/UN25/2015",
        "ijazah_url": null
      }
    ]
  }
}
```

Response `201 Created` (tambah/update):
```json
{
  "success": true,
  "message": "Riwayat pendidikan berhasil ditambahkan.",
  "data": {
    "id": 1,
    "jenjang": "S1",
    "institusi": "Universitas Jember",
    "jurusan": "Kedokteran",
    "tahun_lulus": 2015
  }
}
```

---

#### 21.5 Reminder WhatsApp STR/SIP & Penugasan Klinis (HRD)

Endpoint untuk mengirim pesan reminder WhatsApp kepada pegawai terkait dokumen izin yang akan atau telah kedaluwarsa. Pesan dikirim secara manual oleh HRD.

| Method | URL | Fungsi |
|--------|-----|--------|
| `POST` | `/api/hrd/pegawai/{id}/reminder/str-sip` | Kirim reminder dokumen STR atau SIP |
| `POST` | `/api/hrd/pegawai/{id}/reminder/penugasan-klinis` | Kirim reminder penugasan klinis |

Body `POST` reminder STR/SIP (`application/json`):

| Field | Type | Deskripsi |
|-------|------|-----------|
| `tipe_dokumen` | string, required | `"str"` atau `"sip"` |
| `dokumen_id` | integer, required | ID dokumen STR atau SIP |

Body `POST` reminder penugasan klinis (`application/json`):

| Field | Type | Deskripsi |
|-------|------|-----------|
| `dokumen_id` | integer, required | ID dokumen penugasan klinis |

Response `200 OK` (berhasil):
```json
{
  "success": true,
  "message": "Pesan pengingat STR (0001/STR/2024) berhasil dikirim."
}
```

Response `422` (nomor HP tidak tersedia):
```json
{
  "success": false,
  "message": "Pegawai belum memasukkan nomor HP/Telepon."
}
```

Response `404` (dokumen tidak ditemukan):
```json
{
  "success": false,
  "message": "Data dokumen tidak ditemukan."
}
```

Urgensi pesan ditentukan otomatis berdasarkan sisa hari kadaluarsa:
- **Kadaluarsa (< 0 hari):** Pesan sangat mendesak 🚨
- **≤ 30 hari:** Pesan peringatan penting ⚠️
- **> 30 hari:** Pesan informasi ℹ️

---

### 22. Kirim Pesan WhatsApp ke Pegawai

- **Role akses:** `admin`, `hrd`, `direktur`
- **Method:** `POST`
- **URL:** `/api/pesan/pegawai/{id}`
- **Auth:** Wajib Bearer token

Body (`application/json`):

| Field | Type | Deskripsi |
|-------|------|-----------|
| `pesan` | string, required, max:2000 | Isi pesan yang akan dikirim |

Response `200 OK` (berhasil):
```json
{
  "success": true,
  "message": "Pesan berhasil dikirim ke nomor 0812xxxx"
}
```

Response `422` (nomor HP tidak tersedia):
```json
{
  "success": false,
  "message": "Pegawai belum memasukkan nomor HP/Telepon."
}
```

Response `422` (gagal kirim — kuota habis):
```json
{
  "success": false,
  "message": "Gagal mengirim pesan: Kuota WhatsApp (Fonnte) habis."
}
```

Response `422` (gagal kirim — token tidak valid):
```json
{
  "success": false,
  "message": "Gagal mengirim pesan: Token WhatsApp tidak valid atau belum disetting."
}
```

---

## Ringkasan Endpoint Per Role

Berikut rangkuman endpoint yang bisa diakses masing-masing role. Semua endpoint butuh header `Authorization: Bearer <jwt_token>`.

### Admin

- **Umum:** `GET /api/role`, `GET /api/dashboard`, `GET /api/diklat`, `GET /api/profile`, `GET /api/pegawai`, `GET /api/pegawai/{id}`, `GET /api/pegawai/{id}/{bagian}`, `GET /api/str-sip`, `GET /api/generate/cv`
- **Profile:** `PATCH /api/profile`, `POST /api/profil/profil-picture`, `POST /api/profile/profile-picture`, `POST /api/profil/ktp`, `POST /api/profile/kk`
- **Notifikasi:** `GET /api/notifications`, `PATCH /api/notifications/{id}/read`, `PATCH /api/notifications/read-all`
- **Master Data Dropdown:** `GET /api/form/kategori-diklat`, `GET /api/form/tipe-diklat`, `GET /api/form/jenis-pegawai`, `GET /api/form/unit-kerja`, `GET /api/form/jenis-biaya`, `GET /api/form/golongan-ruang`, `GET /api/form/profesi`, `GET /api/form/jenis-sip`
- **Riwayat Pendidikan:** `GET|POST /api/riwayat-karir/pendidikan`, `PATCH|POST|DELETE /api/riwayat-karir/pendidikan/{id}`
- **Riwayat Jabatan:** `GET|POST /api/riwayat-karir/jabatan`, `PATCH|POST|DELETE /api/riwayat-karir/jabatan/{id}`
- **Riwayat Pangkat:** `GET|POST /api/riwayat-karir/pangkat`, `PATCH|POST|DELETE /api/riwayat-karir/pangkat/{id}`
- **Riwayat SIP:** `GET|POST /api/riwayat-karir/sip`, `PATCH|POST|DELETE /api/riwayat-karir/sip/{id}`
- **Riwayat STR:** `GET|POST /api/riwayat-karir/str`, `PATCH|POST|DELETE /api/riwayat-karir/str/{id}`
- **Riwayat Penugasan Klinis:** `GET|POST /api/riwayat-karir/penugasan-klinis`, `PATCH|POST|DELETE /api/riwayat-karir/penugasan-klinis/{id}`
- **Keluarga:** CRUD Pasangan, Anak, Orang Tua, Kontak Darurat
- **Kirim Pesan WA:** `POST /api/pesan/pegawai/{id}`
- **Pegawai (Admin only):** `POST /api/pegawai`, `PATCH /api/pegawai/{id}/change-role`
- **NIK (Admin only):** `PATCH /api/auth/change-nik`
- **Change Request (Admin only):** `GET /api/admin/change-requests`, `GET /api/admin/change-requests/{id}`, `PATCH /api/admin/change-requests/{id}/accept`, `PATCH /api/admin/change-requests/{id}/reject`

#### Admin Approval Change Request

Endpoint ini hanya bisa diakses oleh role `admin`. Digunakan untuk melihat dan memproses pengajuan perubahan data pegawai.

##### 10. List Change Request (Admin)

- Method: `GET`
- URL: `/api/admin/change-requests`
- Auth: Wajib Bearer token
- Role: `admin`
- Query opsional: `status` (`pending`|`approved`|`rejected`), `fitur` (contoh: `profile`)

Response `200 OK`:

```json
{
  "success": true,
  "message": "Daftar pengajuan perubahan data berhasil diambil.",
  "data": [
    {
      "id": 1,
      "by_user": {
        "id": 4,
        "username": "3174010101010001",
        "role": "pegawai",
        "nama": "Budi Santoso"
      },
      "fitur": "profile",
      "status": "pending",
      "note": "Pengajuan dari profile update",
      "jumlah_detail": 5,
      "created_at": "2026-04-19 09:00:00",
      "updated_at": "2026-04-19 09:00:00"
    }
  ]
}
```

##### 11. Detail Change Request (Admin)

- Method: `GET`
- URL: `/api/admin/change-requests/{id}`
- Auth: Wajib Bearer token
- Role: `admin`

Response `200 OK`:

```json
{
  "success": true,
  "message": "Detail pengajuan perubahan data berhasil diambil.",
  "data": {
    "id": 1,
    "fitur": "profile",
    "status": "pending",
    "details": [
      {
        "id": 10,
        "target_table": "pegawai_pribadi",
        "kolom": "alamat",
        "old_value": "Alamat lama",
        "value": "Alamat baru"
      }
    ]
  }
}
```

Response `404 Not Found`:

```json
{
  "success": false,
  "message": "Pengajuan perubahan data tidak ditemukan."
}
```

##### 12. Accept Change Request (Admin)

- Method: `PATCH`
- URL: `/api/admin/change-requests/{id}/accept`
- Auth: Wajib Bearer token
- Role: `admin`

Request body (opsional):

```json
{
  "note": "Data sudah valid dan bisa diterapkan"
}
```

Catatan: hanya bisa untuk status `pending`. Untuk fitur `profile`, perubahan langsung diaplikasikan ke tabel `pegawai` dan `pegawai_pribadi`.

Response `200 OK`:

```json
{
  "success": true,
  "message": "Pengajuan perubahan data berhasil disetujui.",
  "data": {
    "id": 1,
    "status": "approved"
  }
}
```

Response `422 Unprocessable Entity`:

```json
{
  "success": false,
  "message": "Pengajuan sudah diproses sebelumnya."
}
```

##### 13. Reject Change Request (Admin)

- Method: `PATCH`
- URL: `/api/admin/change-requests/{id}/reject`
- Auth: Wajib Bearer token
- Role: `admin`

Request body (opsional):

```json
{
  "note": "Dokumen pendukung belum sesuai"
}
```

Catatan: hanya bisa untuk status `pending`. Data master tidak berubah, status berubah menjadi `rejected`.

Response `200 OK`:

```json
{
  "success": true,
  "message": "Pengajuan perubahan data berhasil ditolak.",
  "data": {
    "id": 1,
    "status": "rejected"
  }
}
```

### Pegawai

- **Umum:** `GET /api/role`, `GET /api/dashboard`, `GET /api/diklat`, `GET /api/profile`, `GET /api/generate/cv`
- **Diklat:** `POST /api/diklat`, `PATCH /api/diklat/{id}`, `DELETE /api/diklat/{id}`, `POST /api/diklat/{id}/upload-laporan`
- **Profile:** `PATCH /api/profile`, `POST /api/profil/profil-picture`, `POST /api/profile/profile-picture`, `POST /api/profil/ktp`, `POST /api/profile/kk`
- **Notifikasi:** `GET /api/notifications`, `PATCH /api/notifications/{id}/read`, `PATCH /api/notifications/read-all`
- **Master Data Dropdown:** `GET /api/form/kategori-diklat`, `GET /api/form/tipe-diklat`, `GET /api/form/jenis-pegawai`, `GET /api/form/unit-kerja`, `GET /api/form/jenis-biaya`, `GET /api/form/golongan-ruang`, `GET /api/form/profesi`, `GET /api/form/jenis-sip`
- **Riwayat Pendidikan:** `GET|POST /api/riwayat-karir/pendidikan`, `PATCH|POST|DELETE /api/riwayat-karir/pendidikan/{id}`
- **Riwayat Jabatan:** `GET|POST /api/riwayat-karir/jabatan`, `PATCH|POST|DELETE /api/riwayat-karir/jabatan/{id}`
- **Riwayat Pangkat:** `GET|POST /api/riwayat-karir/pangkat`, `PATCH|POST|DELETE /api/riwayat-karir/pangkat/{id}`
- **Riwayat SIP:** `GET|POST /api/riwayat-karir/sip`, `PATCH|POST|DELETE /api/riwayat-karir/sip/{id}`
- **Riwayat STR:** `GET|POST /api/riwayat-karir/str`, `PATCH|POST|DELETE /api/riwayat-karir/str/{id}`
- **Riwayat Penugasan Klinis:** `GET|POST /api/riwayat-karir/penugasan-klinis`, `PATCH|POST|DELETE /api/riwayat-karir/penugasan-klinis/{id}`
- **Keluarga:** CRUD Pasangan, Anak, Orang Tua, Kontak Darurat

Dashboard pegawai menampilkan ringkasan: identitas (`nama`, `nip`, `jabatan`, `unit_kerja`), diklat (`jumlah_diklat_selesai`, `jumlah_diklat_dijadwalkan_belum_selesai`, `list_jadwal_diklat_mendatang`), dan aksi (`list_aksi`).

### HRD

- **Umum:** `GET /api/role`, `GET /api/dashboard`, `GET /api/diklat`, `GET /api/profile`, `GET /api/pegawai`, `GET /api/pegawai/{id}`, `GET /api/pegawai/{id}/{bagian}`, `GET /api/str-sip`, `GET /api/generate/cv`
- **Diklat:** `GET /api/diklat/all`, `POST /api/diklat`, `PATCH /api/diklat/{id}`, `DELETE /api/diklat/{id}`, `POST /api/diklat/{id}/upload-laporan`
- **Diklat HRD:** `POST /api/hrd/diklat`, `PUT /api/hrd/diklat/{id}`, `GET /api/hrd/diklat/{id}/peserta`, `POST /api/hrd/diklat/{id}/peserta`, `GET /api/hrd/diklat/status/layak`, `PATCH /api/hrd/diklat/{id}/status/layak`, `GET /api/hrd/diklat/status/validasi`, `PATCH /api/hrd/diklat/{id}/status/validasi`
- **Laporan:** `GET /api/generate/laporan-diklat`
- **Profile:** `PATCH /api/profile`, `POST /api/profil/profil-picture`, `POST /api/profile/profile-picture`, `POST /api/profil/ktp`, `POST /api/profile/kk`
- **Notifikasi:** `GET /api/notifications`, `PATCH /api/notifications/{id}/read`, `PATCH /api/notifications/read-all`
- **Master Data Dropdown:** `GET /api/form/kategori-diklat`, `GET /api/form/tipe-diklat`, `GET /api/form/jenis-pegawai`, `GET /api/form/unit-kerja`, `GET /api/form/jenis-biaya`, `GET /api/form/golongan-ruang`, `GET /api/form/profesi`, `GET /api/form/jenis-sip`
- **Master Data CRUD:** `POST|PATCH|DELETE /api/form/{kategori-diklat|tipe-diklat|jenis-pegawai|unit-kerja|jenis-biaya|golongan-ruang|profesi|jenis-sip}`
- **Riwayat Pendidikan:** `GET|POST /api/riwayat-karir/pendidikan`, `PATCH|POST|DELETE /api/riwayat-karir/pendidikan/{id}`
- **Riwayat Jabatan:** `GET|POST /api/riwayat-karir/jabatan`, `PATCH|POST|DELETE /api/riwayat-karir/jabatan/{id}`
- **Riwayat Pangkat:** `GET|POST /api/riwayat-karir/pangkat`, `PATCH|POST|DELETE /api/riwayat-karir/pangkat/{id}`
- **Riwayat SIP:** `GET|POST /api/riwayat-karir/sip`, `PATCH|POST|DELETE /api/riwayat-karir/sip/{id}`
- **Riwayat STR:** `GET|POST /api/riwayat-karir/str`, `PATCH|POST|DELETE /api/riwayat-karir/str/{id}`
- **Riwayat Penugasan Klinis:** `GET|POST /api/riwayat-karir/penugasan-klinis`, `PATCH|POST|DELETE /api/riwayat-karir/penugasan-klinis/{id}`
- **Keluarga:** CRUD Pasangan, Anak, Orang Tua, Kontak Darurat
- **HRD Manajemen Data Pegawai:** `PATCH /api/hrd/pegawai/{id}/inti`, `PATCH|POST /api/hrd/pegawai/{id}/pribadi`, CRUD Keluarga (pasangan, anak, orang tua, kontak darurat, tanggungan lain), CRUD Riwayat Karir (jabatan, STR, SIP, penugasan klinis, pangkat) via `/api/hrd/pegawai/{id}/keluarga/*` dan `/api/hrd/pegawai/{id}/riwayat-karir/*`
- **Reminder WA:** `POST /api/hrd/pegawai/{id}/reminder/str-sip`, `POST /api/hrd/pegawai/{id}/reminder/penugasan-klinis`
- **Kirim Pesan WA:** `POST /api/pesan/pegawai/{id}`

### Direktur

- **Umum:** `GET /api/role`, `GET /api/dashboard`, `GET /api/diklat`, `GET /api/profile`, `GET /api/pegawai`, `GET /api/pegawai/{id}`, `GET /api/pegawai/{id}/{bagian}`, `GET /api/str-sip`, `GET /api/generate/cv`
- **Diklat:** `GET /api/diklat/all`, `POST /api/diklat`, `PATCH /api/diklat/{id}`, `DELETE /api/diklat/{id}`, `POST /api/diklat/{id}/upload-laporan`
- **Profile:** `PATCH /api/profile`, `POST /api/profil/profil-picture`, `POST /api/profile/profile-picture`, `POST /api/profil/ktp`, `POST /api/profile/kk`
- **Notifikasi:** `GET /api/notifications`, `PATCH /api/notifications/{id}/read`, `PATCH /api/notifications/read-all`
- **Master Data Dropdown:** `GET /api/form/kategori-diklat`, `GET /api/form/tipe-diklat`, `GET /api/form/jenis-pegawai`, `GET /api/form/unit-kerja`, `GET /api/form/jenis-biaya`, `GET /api/form/golongan-ruang`, `GET /api/form/profesi`, `GET /api/form/jenis-sip`
- **Riwayat Pendidikan:** `GET|POST /api/riwayat-karir/pendidikan`, `PATCH|POST|DELETE /api/riwayat-karir/pendidikan/{id}`
- **Riwayat Jabatan:** `GET|POST /api/riwayat-karir/jabatan`, `PATCH|POST|DELETE /api/riwayat-karir/jabatan/{id}`
- **Riwayat Pangkat:** `GET|POST /api/riwayat-karir/pangkat`, `PATCH|POST|DELETE /api/riwayat-karir/pangkat/{id}`
- **Riwayat SIP:** `GET|POST /api/riwayat-karir/sip`, `PATCH|POST|DELETE /api/riwayat-karir/sip/{id}`
- **Riwayat STR:** `GET|POST /api/riwayat-karir/str`, `PATCH|POST|DELETE /api/riwayat-karir/str/{id}`
- **Riwayat Penugasan Klinis:** `GET|POST /api/riwayat-karir/penugasan-klinis`, `PATCH|POST|DELETE /api/riwayat-karir/penugasan-klinis/{id}`
- **Keluarga:** CRUD Pasangan, Anak, Orang Tua, Kontak Darurat
- **Kirim Pesan WA:** `POST /api/pesan/pegawai/{id}`


## Akun Seeder Untuk Uji Login

- Admin: `3174010101010099` / `password`
- HRD: `3174010101010098` / `password`
- Direktur: `3174010101010003` / `password`
- Pegawai: `3174010101010001` / `password`

## Quick Test via cURL

Login:

```bash
curl -X POST http://127.0.0.1:8000/api/login \
  -H "Content-Type: application/json" \
  -d "{\"nik\":\"3174010101010099\",\"password\":\"password\"}"
```

Health check:

```bash
curl http://127.0.0.1:8000/api/health
```

Cek role (ganti token):

```bash
curl http://127.0.0.1:8000/api/role \
  -H "Authorization: Bearer <jwt_token>"
```

Dashboard pegawai (ganti token pegawai):

```bash
curl http://127.0.0.1:8000/api/dashboard \
  -H "Authorization: Bearer <jwt_token>"
```

Diklat (ganti token sesuai role):

```bash
curl http://127.0.0.1:8000/api/diklat \
  -H "Authorization: Bearer <jwt_token>"
```

Profile pegawai (ganti token pegawai):

```bash
curl http://127.0.0.1:8000/api/profile \
  -H "Authorization: Bearer <jwt_token>"
```

Ajukan perubahan profile (ganti token):

```bash
curl -X PATCH http://127.0.0.1:8000/api/profile \
  -H "Authorization: Bearer <jwt_token>" \
  -H "Content-Type: application/json" \
  -d "{\"alamat\":\"Jl. Mawar No. 10\",\"no_telp\":\"081298765432\",\"note\":\"Mohon update\"}"
```

Upload foto profile (endpoint utama):

```bash
curl -X POST http://127.0.0.1:8000/api/profil/profil-picture \
  -H "Authorization: Bearer <jwt_token>" \
  -F "foto=@C:/path/foto-profile.jpg"
```

Upload file KTP (PDF):

```bash
curl -X POST http://127.0.0.1:8000/api/profil/ktp \
  -H "Authorization: Bearer <jwt_token>" \
  -F "ktp=@C:/path/ktp.pdf"
```

Upload file KK (PDF):

```bash
curl -X POST http://127.0.0.1:8000/api/profile/kk \
  -H "Authorization: Bearer <jwt_token>" \
  -F "kk=@C:/path/kk.pdf"
```

List notifikasi:

```bash
curl http://127.0.0.1:8000/api/notifications \
  -H "Authorization: Bearer <jwt_token>"
```

Tandai 1 notifikasi sudah dibaca (ganti id dan token):

```bash
curl -X PATCH http://127.0.0.1:8000/api/notifications/1/read \
  -H "Authorization: Bearer <jwt_token>"
```

Tandai semua notifikasi sudah dibaca:

```bash
curl -X PATCH http://127.0.0.1:8000/api/notifications/read-all \
  -H "Authorization: Bearer <jwt_token>"
```

Get riwayat pendidikan (ganti token):

```bash
curl http://127.0.0.1:8000/api/riwayat-karir/pendidikan \
  -H "Authorization: Bearer <jwt_token>"
```

Create riwayat pendidikan (ganti token):

```bash
curl -X POST http://127.0.0.1:8000/api/riwayat-karir/pendidikan \
  -H "Authorization: Bearer <jwt_token>" \
  -F "jenjang=S1" \
  -F "institusi=Universitas Contoh" \
  -F "jurusan=Teknik Informatika" \
  -F "tahun_lulus=2020"
```

Update riwayat pendidikan (ganti token dan id):

```bash
curl -X POST http://127.0.0.1:8000/api/riwayat-karir/pendidikan/1 \
  -H "Authorization: Bearer <jwt_token>" \
  -F "_method=PATCH" \
  -F "institusi=Universitas Brawijaya"
```

Delete riwayat pendidikan (ganti token dan id):

```bash
curl -X DELETE http://127.0.0.1:8000/api/riwayat-karir/pendidikan/1 \
  -H "Authorization: Bearer <jwt_token>"
```

List riwayat jabatan (ganti token):

```bash
curl -X GET http://127.0.0.1:8000/api/riwayat-karir/jabatan \
  -H "Authorization: Bearer <jwt_token>"
```

Tambah riwayat jabatan (ganti token):

```bash
curl -X POST http://127.0.0.1:8000/api/riwayat-karir/jabatan \
  -H "Authorization: Bearer <jwt_token>" \
  -F "nama_jabatan=Perawat" \
  -F "is_current=1" \
  -F "sk_jabatan=@/path/to/sk.pdf"
```

Update riwayat jabatan (ganti token dan id):

```bash
curl -X POST http://127.0.0.1:8000/api/riwayat-karir/jabatan/1 \
  -H "Authorization: Bearer <jwt_token>" \
  -F "nama_jabatan=Kepala Perawat"
```

Delete riwayat jabatan (ganti token dan id):

```bash
curl -X DELETE http://127.0.0.1:8000/api/riwayat-karir/jabatan/1 \
  -H "Authorization: Bearer <jwt_token>"
```

List riwayat pangkat (ganti token):

```bash
curl -X GET http://127.0.0.1:8000/api/riwayat-karir/pangkat \
  -H "Authorization: Bearer <jwt_token>"
```

Tambah riwayat pangkat (ganti token):

```bash
curl -X POST http://127.0.0.1:8000/api/riwayat-karir/pangkat \
  -H "Authorization: Bearer <jwt_token>" \
  -F "nama_pangkat=Penata Muda" \
  -F "is_current=1" \
  -F "sk_pangkat=@/path/to/sk.pdf"
```

Update riwayat pangkat (ganti token dan id):

```bash
curl -X POST http://127.0.0.1:8000/api/riwayat-karir/pangkat/1 \
  -H "Authorization: Bearer <jwt_token>" \
  -F "pejabat_penetap=Gubernur"
```

Delete riwayat pangkat (ganti token dan id):

```bash
curl -X DELETE http://127.0.0.1:8000/api/riwayat-karir/pangkat/1 \
  -H "Authorization: Bearer <jwt_token>"
```

List riwayat SIP (ganti token):

```bash
curl -X GET http://127.0.0.1:8000/api/riwayat-karir/sip \
  -H "Authorization: Bearer <jwt_token>"
```

Tambah riwayat SIP (ganti token):

```bash
curl -X POST http://127.0.0.1:8000/api/riwayat-karir/sip \
  -H "Authorization: Bearer <jwt_token>" \
  -F "nomor_sip=SIP.123" \
  -F "tanggal_terbit=2024-01-01" \
  -F "tanggal_kadaluarsa=2029-01-01" \
  -F "sk_sip=@/path/to/sk.pdf"
```

Update riwayat SIP (ganti token dan id):

```bash
curl -X POST http://127.0.0.1:8000/api/riwayat-karir/sip/1 \
  -H "Authorization: Bearer <jwt_token>" \
  -F "nomor_sip=SIP.BARU"
```

Delete riwayat SIP (ganti token dan id):

```bash
curl -X DELETE http://127.0.0.1:8000/api/riwayat-karir/sip/1 \
  -H "Authorization: Bearer <jwt_token>"
```

List riwayat STR (ganti token):

```bash
curl -X GET http://127.0.0.1:8000/api/riwayat-karir/str \
  -H "Authorization: Bearer <jwt_token>"
```

Tambah riwayat STR (ganti token):

```bash
curl -X POST http://127.0.0.1:8000/api/riwayat-karir/str \
  -H "Authorization: Bearer <jwt_token>" \
  -F "nomor_str=STR.123" \
  -F "tanggal_terbit=2024-01-01" \
  -F "tanggal_kadaluarsa=2029-01-01" \
  -F "sk_str=@/path/to/sk.pdf"
```

Update riwayat STR (ganti token dan id):

```bash
curl -X POST http://127.0.0.1:8000/api/riwayat-karir/str/1 \
  -H "Authorization: Bearer <jwt_token>" \
  -F "nomor_str=STR.BARU"
```

Delete riwayat STR (ganti token dan id):

```bash
curl -X DELETE http://127.0.0.1:8000/api/riwayat-karir/str/1 \
  -H "Authorization: Bearer <jwt_token>"
```

List riwayat penugasan klinis (ganti token):

```bash
curl -X GET http://127.0.0.1:8000/api/riwayat-karir/penugasan-klinis \
  -H "Authorization: Bearer <jwt_token>"
```

Tambah riwayat penugasan klinis (ganti token):

```bash
curl -X POST http://127.0.0.1:8000/api/riwayat-karir/penugasan-klinis \
  -H "Authorization: Bearer <jwt_token>" \
  -F "nomor_surat=PK.123" \
  -F "tgl_mulai=2024-01-01" \
  -F "tgl_kadaluarsa=2029-01-01" \
  -F "dokumen_file=@/path/to/dokumen.pdf"
```

Update riwayat penugasan klinis (ganti token dan id):

```bash
curl -X POST http://127.0.0.1:8000/api/riwayat-karir/penugasan-klinis/1 \
  -H "Authorization: Bearer <jwt_token>" \
  -F "nomor_surat=PK.BARU"
```

Delete riwayat penugasan klinis (ganti token dan id):

```bash
curl -X DELETE http://127.0.0.1:8000/api/riwayat-karir/penugasan-klinis/1 \
  -H "Authorization: Bearer <jwt_token>"
```

List change request admin:

```bash
curl http://127.0.0.1:8000/api/admin/change-requests \
  -H "Authorization: Bearer <jwt_token_admin>"
```

Accept change request admin:

```bash
curl -X PATCH http://127.0.0.1:8000/api/admin/change-requests/1/accept \
  -H "Authorization: Bearer <jwt_token_admin>" \
  -H "Content-Type: application/json" \
  -d "{\"note\":\"Data valid\"}"
```

Reject change request admin:

```bash
curl -X PATCH http://127.0.0.1:8000/api/admin/change-requests/1/reject \
  -H "Authorization: Bearer <jwt_token_admin>" \
  -H "Content-Type: application/json" \
  -d "{\"note\":\"Perlu revisi\"}"
```

## Postman Collection

File Postman sudah disiapkan di folder dokumentasi:

- Collection: `dokumentasi/postman/BE-SIMPEG-RSKALISAT.postman_collection.json`
- Environment local: `dokumentasi/postman/BE-SIMPEG-RSKALISAT.local.postman_environment.json`

Langkah pakai di Postman:

1. Import file collection.
2. Import file environment.
3. Pilih environment `BE-SIMPEG-RSKALISAT Local`.
4. Jalankan request `Login`, lalu copy `access_token` ke variable `token`, `token_admin`, `token_hrd`, `token_pegawai`, atau `token_direktur` sesuai role yang sedang diuji.
5. Jalankan request lain sesuai kebutuhan test.


## Daftar Request di Collection

Collection Postman berisi 177 request utama yang sudah disesuaikan dengan 177 route aktif dari `php artisan route:list --path=api`, ditambah 41 request skenario testing end-to-end.

Folder yang tersedia:

1. `01. Umum & Auth`
  - Health check, login, cek role, dashboard.
2. `02. Profile & Generate`
  - Get/patch profile, upload foto profile, upload KTP, upload KK, generate CV.
3. `03. Data Keluarga`
  - Ringkasan keluarga, CRUD Pasangan, Anak, Orang Tua, dan Kontak Darurat.
4. `04. Riwayat Karir`
  - CRUD Pendidikan, Jabatan, Pangkat, SIP, STR, dan Penugasan Klinis, termasuk route update alias `POST /api/riwayat-karir/{jenis}/{id}`.
5. `05. Diklat`
  - Diklat pengguna, upload laporan, diklat all, master diklat HRD, peserta, status kelayakan, status validasi, dan generate laporan diklat.
6. `06. Master Data`
  - List dropdown master data untuk semua user login dan CRUD master data khusus HRD.
7. `07. Pegawai & STR/SIP`
  - List/detail pegawai, tambah pegawai, ubah role/status, dan rekap STR/SIP.
8. `08. Notifikasi`
  - List notifikasi, tandai satu dibaca, tandai semua dibaca.
9. `09. Admin Change Request`
  - List, detail, accept, dan reject change request.
10. `10. Testing Scenarios`
  - Auth token dan dashboard.
  - Profile change request sampai approval admin.
  - Diklat pegawai sampai validasi HRD.
  - Master diklat HRD.
  - Riwayat karir pendidikan.
  - Keluarga anak.
  - Pegawai admin management.
  - Notifikasi.
11. `11. HRD Manajemen Pegawai`
  - Update data inti pegawai (`PATCH /api/hrd/pegawai/{id}/inti`).
  - Update data pribadi pegawai (`PATCH|POST /api/hrd/pegawai/{id}/pribadi`).
  - CRUD keluarga: pasangan, anak, orang tua, kontak darurat, tanggungan lain.
  - CRUD riwayat karir: jabatan, STR, SIP, penugasan klinis, pangkat.
  - Reminder WA: kirim reminder STR/SIP dan penugasan klinis.
  - Kirim pesan WA bebas ke pegawai (`POST /api/pesan/pegawai/{id}`).
