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
4. [Diklat](#5-diklat)
5. [Response Diklat Per Role](#response-diklat-per-role)
6. [Profile](#6-profile)
7. [Response Profile Untuk Role Pegawai](#response-profile-untuk-role-pegawai)
8. [Ajukan Perubahan Profile](#7-ajukan-perubahan-profile)
9. [Upload Foto Profile (Tanpa Approval)](#8-upload-foto-profile-tanpa-approval)
10. [Upload File KTP (Tanpa Approval)](#81-upload-file-ktp-tanpa-approval)
10. [Notifikasi (Mark As Read)](#9-notifikasi-mark-as-read)
11. [Tandai 1 Notifikasi Sudah Dibaca](#91-tandai-1-notifikasi-sudah-dibaca)
12. [Tandai Semua Notifikasi Sudah Dibaca](#92-tandai-semua-notifikasi-sudah-dibaca)

BAB IV Endpoint Per Role

1. [Endpoint Per Role](#endpoint-per-role)
2. [Admin](#admin)
3. [Pegawai](#pegawai)
4. [HRD](#hrd)
5. [Direktur](#direktur)

BAB V Endpoint Admin Approval Change Request

1. [List Change Request](#10-list-change-request-admin)
2. [Detail Change Request](#11-detail-change-request-admin)
3. [Accept Change Request](#12-accept-change-request-admin)
4. [Reject Change Request](#13-reject-change-request-admin)

BAB VI Data Uji dan Simulasi

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
- `list_notifikasi`: hanya menampilkan notifikasi milik user login dengan `is_read = false` (belum dibaca).
- `list_aksi`: daftar notifikasi bertipe `action` yang belum `is_resolved`.
- `list_aksi.action_payload`: detail data aksi, misalnya status STR atau kelengkapan keluarga.

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

### 5. Diklat

- Method: `GET`
- URL: `/api/diklat`
- Auth: Wajib Bearer token
- Role yang diizinkan: `admin`, `pegawai`, `hrd`, `direktur`

Contoh header:

```http
Authorization: Bearer <jwt_token>
```

#### Response Diklat Per Role

Keterangan implementasi saat ini:

- Role `pegawai`: data diambil dari database melalui repository.
- Role `admin`, `hrd`, `direktur`: payload ringkasan tetap dibedakan per role.

Contoh response role `pegawai`:

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
      "riwayat_diklat": [
        {
          "nama": "Pelatihan Komunikasi Efektif",
          "kategori": "Soft Skill",
          "jenis": "Workshop",
          "pelaksana": "RS Kalisat",
          "tanggal_mulai": "2025-11-15",
          "tanggal_selesai": "2025-11-17",
          "status": "selesai",
          "tempat": "Aula Utama",
          "waktu": "08:00 - 16:00",
          "created_by": "Admin SIMPEG",
          "jp": 24,
          "total_biaya": 250000,
          "jenis_biaya": "Mandiri",
          "jenis_pelaksana": "internal"
        }
      ]
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

Aturan hitung `status`:

- `mendatang`: hari ini < `tanggal_mulai`
- `berlangsung`: hari ini di antara `tanggal_mulai` dan `tanggal_selesai`
- `selesai`: hari ini > `tanggal_selesai`

Catatan bentuk payload:

- `admin`: `ringkasan` + `list_diklat`
- `pegawai`: `ringkasan` + `riwayat_diklat`
- `hrd`: `ringkasan` + `list_usulan`
- `direktur`: `ringkasan` + `keputusan_terbaru`

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
      "link_photo_profile": "http://127.0.0.1:8000/dokumen/foto/budi-santoso.jpg",
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
- `email`
- `status_pegawai`
- `tgl_masuk`
- `tmt_cpns`
- `tmt_pns`
- `note` (opsional, sebagai catatan pengajuan)

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
    "link_photo_profile": "http://127.0.0.1:8000/dokumen/foto/profile-4-1713500000.jpg",
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
    "link_ktp_file": "http://127.0.0.1:8000/dokumen/ktp/ktp-4-1713500000.pdf",
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

### 9. Notifikasi (Mark As Read)

#### 9.1 Tandai 1 Notifikasi Sudah Dibaca

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

#### 9.2 Tandai Semua Notifikasi Sudah Dibaca

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
  - `GET /api/diklat`
  - `GET /api/profile`
  - `PATCH /api/profile`
  - `POST /api/profil/profil-picture`
  - `POST /api/profile/profile-picture`
  - `POST /api/profil/ktp`
  - `PATCH /api/notifications/{id}/read`
  - `PATCH /api/notifications/read-all`
  - `GET /api/admin/change-requests`
  - `GET /api/admin/change-requests/{id}`
  - `PATCH /api/admin/change-requests/{id}/accept`
  - `PATCH /api/admin/change-requests/{id}/reject`
- Catatan dashboard:
  - `message`: `Selamat datang admin`
  - `data.dashboard.label`: `Dashboard admin`

### Pegawai

- Endpoint utama:
  - `GET /api/role`
  - `GET /api/dashboard`
  - `GET /api/diklat`
  - `GET /api/profile`
  - `PATCH /api/profile`
  - `POST /api/profil/profil-picture`
  - `POST /api/profile/profile-picture`
  - `POST /api/profil/ktp`
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
  - `GET /api/diklat`
  - `GET /api/profile`
  - `PATCH /api/profile`
  - `POST /api/profil/profil-picture`
  - `POST /api/profile/profile-picture`
  - `POST /api/profil/ktp`
  - `PATCH /api/notifications/{id}/read`
  - `PATCH /api/notifications/read-all`
- Catatan dashboard:
  - `message`: `Selamat datang hrd`
  - `data.dashboard.label`: `Dashboard hrd`

### Direktur

- Endpoint utama:
  - `GET /api/role`
  - `GET /api/dashboard`
  - `GET /api/diklat`
  - `GET /api/profile`
  - `PATCH /api/profile`
  - `POST /api/profil/profil-picture`
  - `POST /api/profile/profile-picture`
  - `POST /api/profil/ktp`
  - `PATCH /api/notifications/{id}/read`
  - `PATCH /api/notifications/read-all`
- Catatan dashboard:
  - `message`: `Selamat datang direktur`
  - `data.dashboard.label`: `Dashboard direktur`

## Endpoint Admin Approval Change Request

### 10. List Change Request (Admin)

- Method: `GET`
- URL: `/api/admin/change-requests`
- Auth: Wajib Bearer token
- Role yang diizinkan: `admin`
- Query opsional:
  - `status`: `pending` | `approved` | `rejected`
  - `fitur`: contoh `profile`

Contoh response `200 OK`:

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

### 11. Detail Change Request (Admin)

- Method: `GET`
- URL: `/api/admin/change-requests/{id}`
- Auth: Wajib Bearer token
- Role yang diizinkan: `admin`

Contoh response `200 OK`:

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

Contoh response `404 Not Found`:

```json
{
  "success": false,
  "message": "Pengajuan perubahan data tidak ditemukan."
}
```

### 12. Accept Change Request (Admin)

- Method: `PATCH`
- URL: `/api/admin/change-requests/{id}/accept`
- Auth: Wajib Bearer token
- Role yang diizinkan: `admin`

Request body opsional:

```json
{
  "note": "Data sudah valid dan bisa diterapkan"
}
```

Perilaku:

- Hanya bisa untuk status `pending`.
- Untuk fitur `profile`, detail perubahan akan diaplikasikan ke tabel `pegawai` dan `pegawai_pribadi`.
- Status pengajuan berubah menjadi `approved`.

Contoh response `200 OK`:

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

Contoh response `422 Unprocessable Entity`:

```json
{
  "success": false,
  "message": "Pengajuan sudah diproses sebelumnya."
}
```

### 13. Reject Change Request (Admin)

- Method: `PATCH`
- URL: `/api/admin/change-requests/{id}/reject`
- Auth: Wajib Bearer token
- Role yang diizinkan: `admin`

Request body opsional:

```json
{
  "note": "Dokumen pendukung belum sesuai"
}
```

Perilaku:

- Hanya bisa untuk status `pending`.
- Tidak mengubah data pada tabel master profile.
- Status pengajuan berubah menjadi `rejected`.

Contoh response `200 OK`:

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
