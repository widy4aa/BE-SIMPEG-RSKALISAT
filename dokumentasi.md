# Dokumentasi API BE-SIMPEG-RSKALISAT

Dokumen ini berisi daftar endpoint API yang tersedia pada project backend ini.

## Daftar Isi

BAB I Pendahuluan

1. [Format Response Standar](#format-response-standar)
2. [Authentication](#authentication)

BAB II Endpoint Umum

1. [Endpoint Umum (Tanpa Role)](#endpoint-umum-tanpa-role)
2. [Health Check](#1-health-check)
3. [Login](#2-login)

BAB III Endpoint Untuk Semua Role Login

1. [Endpoint Untuk Semua Role Login](#endpoint-untuk-semua-role-login)
2. [Cek Role Login](#3-cek-role-login)
3. [Dashboard](#4-dashboard)
4. [Response Dashboard Untuk Role Pegawai](#response-dashboard-untuk-role-pegawai)
5. [Notifikasi (Mark As Read)](#5-notifikasi-mark-as-read)
6. [Tandai 1 Notifikasi Sudah Dibaca](#51-tandai-1-notifikasi-sudah-dibaca)
7. [Tandai Semua Notifikasi Sudah Dibaca](#52-tandai-semua-notifikasi-sudah-dibaca)

BAB IV Endpoint Per Role

1. [Endpoint Per Role](#endpoint-per-role)
2. [Admin](#admin)
3. [Pegawai](#pegawai)
4. [HRD](#hrd)
5. [Direktur](#direktur)

BAB V Data Uji dan Simulasi

1. [Akun Seeder Untuk Uji Login](#akun-seeder-untuk-uji-login)
2. [Quick Test via cURL](#quick-test-via-curl)

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

## Endpoint Umum (Tanpa Role)

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

## Endpoint Untuk Semua Role Login

Endpoint di bagian ini bisa dipakai role `admin`, `pegawai`, `hrd`, dan `direktur`.

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

### 4. Dashboard

- Method: `GET`
- URL: `/api/dashboard`
- Auth: Wajib Bearer token
- Role yang diizinkan: `admin`, `pegawai`, `hrd`, `direktur`

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
      "list_notifikasi": [
        {
          "id": 1,
          "title": "Jadwal Diklat Mendatang",
          "message": "Anda memiliki jadwal diklat yang belum terlaksana. Silakan cek detail jadwal.",
          "is_read": false,
          "created_at": "2026-04-17 10:00:00"
        }
      ],
      "list_aksi": {
        "status_str": {
          "status_lengkap": true,
          "sisa_hari": 100,
          "keterangan": [
            "STR aktif"
          ]
        },
        "status_data_keluarga": {
          "status_lengkap": true,
          "keterangan": [
            "data lengkap"
          ]
        }
      }
    }
  }
}
```

Keterangan field dashboard pegawai:

- `jumlah_diklat_selesai`: jumlah diklat dengan status `sudah terlaksana`.
- `jumlah_diklat_dijadwalkan_belum_selesai`: jumlah diklat dengan status `belum terlaksana` atau `sedang terlaksana`.
- `list_jadwal_diklat_mendatang`: list diklat yang statusnya `belum terlaksana`.
- `list_notifikasi`: hanya menampilkan notifikasi milik user login dengan `is_read = false` (belum dibaca).
- `list_aksi.status_str`: informasi sisa masa berlaku STR.
- `list_aksi.status_data_keluarga`: status kelengkapan data keluarga.

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

### 5. Notifikasi (Mark As Read)

#### 5.1 Tandai 1 Notifikasi Sudah Dibaca

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

#### 5.2 Tandai Semua Notifikasi Sudah Dibaca

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

## Endpoint Per Role

### Admin

- Endpoint utama:
  - `GET /api/role`
  - `GET /api/dashboard`
  - `PATCH /api/notifications/{id}/read`
  - `PATCH /api/notifications/read-all`
- Catatan dashboard:
  - `message`: `Selamat datang admin`
  - `data.dashboard.label`: `Dashboard admin`

### Pegawai

- Endpoint utama:
  - `GET /api/role`
  - `GET /api/dashboard`
  - `PATCH /api/notifications/{id}/read`
  - `PATCH /api/notifications/read-all`
- Catatan dashboard:
  - `message`: `Selamat datang pegawai`
  - `data.dashboard` menampilkan ringkasan lengkap pegawai:
    - identitas: `nama`, `nip`, `jabatan`, `jenis_jabatan`, `unit_kerja`
    - diklat: `jumlah_diklat_selesai`, `jumlah_diklat_dijadwalkan_belum_selesai`, `list_jadwal_diklat_mendatang`
    - notifikasi: `list_notifikasi` (hanya unread milik user login)
    - aksi: `list_aksi.status_str`, `list_aksi.status_data_keluarga`

### HRD

- Endpoint utama:
  - `GET /api/role`
  - `GET /api/dashboard`
  - `PATCH /api/notifications/{id}/read`
  - `PATCH /api/notifications/read-all`
- Catatan dashboard:
  - `message`: `Selamat datang hrd`
  - `data.dashboard.label`: `Dashboard hrd`

### Direktur

- Endpoint utama:
  - `GET /api/role`
  - `GET /api/dashboard`
  - `PATCH /api/notifications/{id}/read`
  - `PATCH /api/notifications/read-all`
- Catatan dashboard:
  - `message`: `Selamat datang direktur`
  - `data.dashboard.label`: `Dashboard direktur`

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
