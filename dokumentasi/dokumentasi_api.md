# Dokumentasi API BE-SIMPEG-RSKALISAT

Dokumentasi lengkap endpoint REST API untuk sistem informasi manajemen pegawai RS Kalisat. Dokumen ini mencakup seluruh endpoint yang tersedia beserta format request, validasi, dan contoh response.

---

## Daftar Isi

**BAB I — Pendahuluan**
1. [Format Response Standar](#format-response-standar)
2. [Authentication](#authentication)

**BAB II — Endpoint Umum (Tanpa Login)**
1. [Health Check](#1-health-check)
2. [Login](#2-login)

**BAB III — Endpoint Semua Role**
1. [Cek Role Login](#3-cek-role-login)
2. [Dashboard](#4-dashboard)
  - [Response Dashboard Untuk Role Pegawai](#response-dashboard-untuk-role-pegawai)
  - [Response Dashboard Untuk Role Admin](#response-dashboard-untuk-role-admin)
  - [Response Dashboard Untuk Role HRD](#response-dashboard-untuk-role-hrd)
3. [Diklat](#5-diklat)
  - [Response Diklat Per Role](#response-diklat-per-role)
  - [GET Diklat (All - HRD)](#get-diklat-all---hrd)
  - [Create Master Diklat (HRD)](#create-master-diklat-hrd)
  - [Get Peserta Diklat (HRD)](#get-peserta-diklat-hrd)
  - [Sync Peserta Diklat (HRD)](#sync-peserta-diklat-hrd)
  - [Get Diklat Menunggu Kelayakan (HRD)](#get-diklat-menunggu-kelayakan-hrd)
  - [Update Status Kelayakan (HRD)](#update-status-kelayakan-hrd)
  - [Get Diklat Menunggu Validasi (HRD)](#get-diklat-menunggu-validasi-hrd)
  - [Update Status Validasi (HRD)](#update-status-validasi-hrd)
  - [Create Diklat Pegawai](#create-diklat-pegawai)
  - [Edit Diklat Pegawai](#edit-diklat-pegawai)
  - [Delete Diklat Pegawai](#delete-diklat-pegawai)
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
  - [POST / PATCH Riwayat Pendidikan (Update)](#post--patch-riwayat-pendidikan-update)
  - [DELETE Riwayat Pendidikan](#delete-riwayat-pendidikan)
7. [Riwayat Karir Jabatan](#11-riwayat-karir-jabatan)
  - [GET Riwayat Jabatan](#get-riwayat-jabatan)
  - [POST Riwayat Jabatan](#post-riwayat-jabatan)
  - [POST / PATCH Riwayat Jabatan (Update)](#post--patch-riwayat-jabatan-update)
  - [DELETE Riwayat Jabatan](#delete-riwayat-jabatan)
8. [Riwayat Karir Pangkat](#12-riwayat-karir-pangkat)
  - [GET Riwayat Pangkat](#get-riwayat-pangkat)
  - [POST Riwayat Pangkat](#post-riwayat-pangkat)
  - [POST / PATCH Riwayat Pangkat (Update)](#post--patch-riwayat-pangkat-update)
  - [DELETE Riwayat Pangkat](#delete-riwayat-pangkat)
9. [Riwayat Karir SIP](#13-riwayat-karir-sip)
  - [GET Riwayat SIP](#get-riwayat-sip)
  - [POST Riwayat SIP](#post-riwayat-sip)
  - [POST / PATCH Riwayat SIP (Update)](#post--patch-riwayat-sip-update)
  - [DELETE Riwayat SIP](#delete-riwayat-sip)
10. [Riwayat Karir STR](#14-riwayat-karir-str)
  - [GET Riwayat STR](#get-riwayat-str)
  - [POST Riwayat STR](#post-riwayat-str)
  - [POST / PATCH Riwayat STR (Update)](#post--patch-riwayat-str-update)
  - [DELETE Riwayat STR](#delete-riwayat-str)
11. [Riwayat Karir Penugasan Klinis](#15-riwayat-karir-penugasan-klinis)
  - [GET Riwayat Penugasan Klinis](#get-riwayat-penugasan-klinis)
  - [POST Riwayat Penugasan Klinis](#post-riwayat-penugasan-klinis)
  - [POST / PATCH Riwayat Penugasan Klinis (Update)](#post--patch-riwayat-penugasan-klinis-update)
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
13. [Master Data (Form Dropdowns)](#17-master-data-form-dropdowns)
   - [List Endpoint Master Data](#list-endpoint-master-data)
14. [Pegawai](#18-pegawai)
  - [Get Pegawai Detail (Admin/HRD/Direktur)](#get-pegawai-detail-adminhrddirektur)
   - [Tambah Data Pegawai Baru (Hanya Admin)](#tambah-data-pegawai-baru-hanya-admin)
   - [Ubah Role Pegawai (Hanya Admin)](#ubah-role-pegawai-hanya-admin)
15. [STR/SIP (Admin/HRD/Direktur)](#19-strsip-adminhrddirektur)
16. [Generate CV](#20-generate-cv)

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

## Endpoint Semua Role (Login Required)

Endpoint berikut bisa dipakai oleh role `admin`, `pegawai`, `hrd`, dan `direktur`. Wajib menyertakan token JWT.

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
- Parameter URL (Opsional, khusus role HRD): `?type=pegawai`, `?type=diklat_asn`, atau `?type=diklat_tenkes`

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
- Role `admin`, `direktur`: payload ringkasan tetap dibedakan per role.
- Role `hrd`: data diambil dari database berdasarkan peserta (hanya diklat yang diikuti HRD login).

Contoh response role `pegawai` (dengan pagination 7 item, parameter `?page=1`):

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
            "waktu": "08:00 - 16:00",
            "created_by": "Admin SIMPEG",
            "jp": 24,
            "total_biaya": 250000,
            "jenis_biaya": "Mandiri",
            "jenis_pelaksana": "internal",
            "catatan": "Workshop peningkatan komunikasi lintas unit.",
            "sertif_file_path": "dokumen/sertif-diklat/budi-audit-internal.pdf",
            "no_sertif": "SERTIF/SDM/2026/0001",
            "status_validasi": "diklat valid"
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

Untuk role `hrd`, field di `riwayat_diklat` mengikuti struktur yang sama dengan `riwayat_diklat` (role `pegawai`).

Aturan hitung `status`:

- `mendatang`: hari ini < `tanggal_mulai`
- `berlangsung`: hari ini di antara `tanggal_mulai` dan `tanggal_selesai`
- `selesai`: hari ini > `tanggal_selesai`

Aturan hitung `status_validasi` (hanya ada jika `jenis_pelaksana` bernilai `internal`, jika `external` maka `null`):

- `Upload laporan`: jika belum mengunggah sertifikat (`sertif_file_path` null)
- `menunggu validasi`: jika sertifikat sudah diunggah tapi status validasi di database masih null
- `di tolak`: jika status validasi di database adalah `tidak valid`
- `diklat valid`: jika status validasi di database adalah `valid`

Catatan bentuk payload:

- `admin`: `ringkasan` + `list_diklat`
- `pegawai`: `ringkasan` + `riwayat_diklat`
- `hrd`: `ringkasan` + `riwayat_diklat` (berisi riwayat diklat peserta HRD login)
- `direktur`: `ringkasan` + `keputusan_terbaru`

Catatan field `catatan`:

- Untuk role `pegawai`, `catatan` berada di setiap item `riwayat_diklat`.
- Untuk role `admin` dan `direktur`, `catatan` juga berada di setiap item list sesuai role.

Catatan field `status`:

- Status hitung by tanggal (`mendatang`, `berlangsung`, `selesai`) saat ini diterapkan pada item role `pegawai` dan `hrd`.
- Item role `admin` dan `direktur` saat ini belum menggunakan field `status`.

#### GET Diklat (All - HRD)

- Method: `GET`
- URL: `/api/diklat/all`
- Auth: Wajib Bearer token
- Role yang diizinkan: `hrd`

Endpoint ini menampilkan seluruh data diklat beserta atributnya untuk role HRD.

Contoh response `200 OK` (dengan pagination 7 item, parameter `?page=1`):

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
        "jumlah_peserta": 5
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

#### Create Master Diklat (HRD)

- Method: `POST`
- URL: `/api/hrd/diklat`
- Auth: Wajib Bearer token
- Role yang diizinkan: `hrd`
- Content-Type: `application/json` atau `multipart/form-data`

Endpoint ini digunakan oleh HRD untuk menambahkan data master diklat ke dalam sistem tanpa mendaftarkan peserta (tidak membuat data di `list_jadwal_diklat`).

Field request:

- `nama_kegiatan` (required, string)
- `kategori` (required, string)
- `jenis_diklat` (required, string)
- `penyelenggara` (required, string)
- `lokasi` (required, string)
- `tanggal_mulai` (required, date)
- `tanggal_selesai` (required, date)
- `jp` (required, integer)
- `jenis_biaya` (required jika `jenis_pelaksana=internal`)
- `total_biaya` (required jika `jenis_pelaksana=internal`)
- `catatan` (nullable, string)
- `jenis_pelaksana` (required: `internal|external`)

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
- Auth: Wajib Bearer token
- Role yang diizinkan: `hrd`

Endpoint ini digunakan oleh HRD untuk melihat daftar semua pegawai beserta status apakah mereka mengikuti diklat tertentu atau tidak.

Contoh request:
`GET /api/hrd/diklat/13/peserta`

Contoh response sukses (`200 OK`):

```json
{
  "success": true,
  "message": "Data peserta diklat berhasil diambil.",
  "data": {
    "diklat_id": 13,
    "total_pegawai": 2,
    "list": [
      {
        "pegawai_id": 1,
        "nama": "Budi Santoso",
        "nik": "350912345678",
        "unit_kerja": "IGD",
        "profesi": "Dokter Umum",
        "status": true
      },
      {
        "pegawai_id": 2,
        "nama": "Siti Aminah",
        "nik": "350987654321",
        "unit_kerja": "Poli Gigi",
        "profesi": "Dokter Gigi",
        "status": false
      }
    ]
  }
}
```

#### Sync Peserta Diklat (HRD)

- Method: `POST`
- URL: `/api/hrd/diklat/{id}/peserta`
- Parameter URL: `id` (required, int) - ID dari Master Diklat
- Auth: Wajib Bearer token
- Role yang diizinkan: `hrd`
- Content-Type: `application/json`

Endpoint ini digunakan oleh HRD untuk menyimpan status checklist peserta. Frontend cukup mengirimkan daftar `pegawai_id` yang memiliki status `true` (di-checklist/mengikuti diklat). Sistem akan menghapus peserta yang tidak ada di list dan menambahkan peserta baru sesuai list dengan status kelayakan otomatis `layak` dan `status_diklat` otomatis menyesuaikan tanggal.

Field request:

- `pegawai_ids` (required, array of integers)

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
        "status_validasi": null
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

#### Create Diklat Pegawai

- Method: `POST`
- URL: `/api/diklat`
- Auth: Wajib Bearer token
- Role yang diizinkan: `pegawai`, `hrd`, `direktur`
- Content-Type: `multipart/form-data`

Field request:

- `nama_kegiatan` (required)
- `kategori` (required)
- `jenis_diklat` (required)
- `penyelenggara` (required)
- `lokasi` (required)
- `tanggal_mulai` (required, date)
- `tanggal_selesai` (required, date)
- `no_sertif` (nullable)
- `upload_sertif` (nullable, file: pdf/jpg/jpeg/png/webp, max 5MB)
- `jp` (required)
- `jenis_biaya` (required jika `jenis_pelaksana=internal`)
- `total_biaya` (required jika `jenis_pelaksana=internal`)
- `catatan` (nullable)
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
    "status_diklat": "belum terlaksana",
    "no_sertif": "SERTIF/SDM/2026/0099",
    "sertif_file_path": "dokumen/sertif-diklat/sertif-3-1713542400.pdf",
    "jp": 24,
    "jenis_biaya": "BLUD",
    "total_biaya": "2500000.00",
    "catatan": "Usulan pelatihan unit SDM",
    "jenis_pelaksana": "internal",
    "status_kelayakan": "layak",
    "status_validasi": null
  }
}
```

#### Edit Diklat Pegawai

- Method: `PATCH`
- URL: `/api/diklat/{id}`
- Auth: Wajib Bearer token
- Role yang diizinkan: `pegawai`
- Content-Type: `multipart/form-data`

Field request (opsional / partial update):

- `nama_kegiatan`
- `kategori`
- `jenis_diklat`
- `penyelenggara`
- `lokasi`
- `tanggal_mulai`
- `tanggal_selesai`
- `no_sertif`
- `upload_sertif`
- `jp`
- `jenis_biaya`
- `total_biaya`
- `catatan`
- `jenis_pelaksana` (boleh dikirim, tapi tidak boleh beda dengan data awal)

Aturan bisnis edit:

- `jenis_pelaksana` (`internal`/`external`) tidak bisa diubah.
- Jika diklat `internal` dan `status_validasi = valid`, data tidak bisa diedit.
- Jika diklat `external` dan `status_kelayakan = layak`, data tidak bisa diedit.
- Untuk diklat `internal`, `status_kelayakan` dipertahankan `layak`, dan `status_validasi` bisa tetap `valid` atau `tidak valid` sesuai proses verifikasi.
- Untuk diklat `external`, `jenis_biaya`, `total_biaya`, dan `status_validasi` diset `null`.

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
    "status_diklat": "belum terlaksana",
    "no_sertif": "SERTIF/SDM/2026/0099",
    "sertif_file_path": "dokumen/sertif-diklat/sertif-3-1713542400.pdf",
    "jp": 24,
    "jenis_biaya": "BLUD",
    "total_biaya": "2500000.00",
    "catatan": "Revisi data diklat",
    "jenis_pelaksana": "internal",
    "status_kelayakan": "layak",
    "status_validasi": null
  }
}
```

#### Delete Diklat Pegawai

- Method: `DELETE`
- URL: `/api/diklat/{id}`
- Auth: Wajib Bearer token
- Role yang diizinkan: `pegawai`

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
      "link_kk": "http://127.0.0.1:8000/dokumen/kk/kk-4-1713500000.pdf",
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
- `no_kk`
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
    "link_kk": "http://127.0.0.1:8000/dokumen/kk/kk-4-1713500000.pdf",
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
        "link_ijazah": "http://127.0.0.1:8000/dokumen/ijazah/ijazah-4-1713500000.pdf"
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
    "link_ijazah": "http://127.0.0.1:8000/dokumen/ijazah/ijazah-4-1713500001.pdf"
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
    "link_ijazah": "http://127.0.0.1:8000/dokumen/ijazah/ijazah-4-1713500001.pdf"
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
        "link_sk": "http://127.0.0.1:8000/dokumen/jabatan/sk-jabatan-1-123456789.pdf",
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
    "link_sk": "http://127.0.0.1:8000/dokumen/jabatan/sk-jabatan-2-123456789.pdf",
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
        "link_sk": "http://127.0.0.1:8000/dokumen/pangkat/sk-pangkat-1-123456789.pdf",
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
    "link_sk": "http://127.0.0.1:8000/dokumen/pangkat/sk-pangkat-2-123456789.pdf",
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
        "is_current": true,
        "link_sk": "http://127.0.0.1:8000/dokumen/sip/sk-sip-1-123456789.pdf"
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
| `is_current` | Boolean (0/1) | Ya | `required`, `boolean` | SIP masih aktif? |
| `sk_sip` | File | Tidak | `nullable`, `file`, `mimes:pdf,jpg,jpeg,png`, `max:5120` | File SK SIP (maks 5MB) |

Contoh raw input (form-data):

```text
jenis_sip_id: 1
nomor_sip: SIP.Baru/789/2024
tanggal_terbit: 2024-01-01
tanggal_kadaluarsa: 2029-01-01
is_current: 1
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
    "is_current": true,
    "link_sk": "http://127.0.0.1:8000/dokumen/sip/sk-sip-2-123456789.pdf"
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
| `is_current` | Boolean (0/1) | Opsional | `sometimes`, `required`, `boolean` | SIP masih aktif? |
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
    "is_current": false,
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
        "is_current": true,
        "link_sk": "http://127.0.0.1:8000/dokumen/str/sk-str-1-123456789.pdf"
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
| `is_current` | Boolean (0/1) | Ya | `required`, `boolean` | STR masih aktif? |
| `sk_str` | File | Tidak | `nullable`, `file`, `mimes:pdf,jpg,jpeg,png`, `max:5120` | File SK STR (maks 5MB) |

Contoh raw input (form-data):

```text
nomor_str: STR.Baru/789/2024
tanggal_terbit: 2024-01-01
tanggal_kadaluarsa: 2029-01-01
is_current: 1
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
    "is_current": true,
    "link_sk": "http://127.0.0.1:8000/dokumen/str/sk-str-2-123456789.pdf"
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
| `is_current` | Boolean (0/1) | Opsional | `sometimes`, `required`, `boolean` | STR masih aktif? |
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
    "is_current": false,
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
        "is_current": true,
        "link_dokumen": "http://127.0.0.1:8000/dokumen/penugasan-klinis/sk-penugasan-klinis-1-123456789.pdf"
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
| `is_current` | Boolean (0/1) | Ya | `required`, `boolean` | Penugasan masih aktif? |
| `dokumen_file` | File | Tidak | `nullable`, `file`, `mimes:pdf,jpg,jpeg,png`, `max:5120` | File dokumen (maks 5MB) |

Contoh raw input (form-data):

```text
nomor_surat: PK.Baru/789/2024
tgl_mulai: 2024-01-01
tgl_kadaluarsa: 2029-01-01
is_current: 1
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
    "is_current": true,
    "link_dokumen": "http://127.0.0.1:8000/dokumen/penugasan-klinis/sk-penugasan-klinis-2-123456789.pdf"
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
| `is_current` | Boolean (0/1) | Opsional | `sometimes`, `required`, `boolean` | Penugasan masih aktif? |
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
    "is_current": false,
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
- Parameter URL (Opsional): `?page=1`
- Auth: Wajib Bearer token
- Role yang diizinkan: `admin`, `hrd`, `direktur`

Mengambil daftar seluruh pegawai beserta ringkasan jumlahnya secara ter-paginasi (10 item per halaman).

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
    "pegawai": {
      "current_page": 1,
      "data": [
        {
          "id_pegawai": 1,
          "nama": "Dr. Andi",
          "nip": "198001012005011001",
          "link_photo_profil": "http://localhost:8000/storage/photos/andi.jpg",
          "jabatan": "Dokter Spesialis",
          "unit_kerja": "Poli Penyakit Dalam",
          "email": "andi@example.com",
          "no_telp": "08123456789",
          "status": "aktif"
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
      "jenis_kelamin": "Laki-laki",
      "tempat_lahir": "Surabaya",
      "tanggal_lahir": "1990-01-01",
      "agama": "Islam",
      "status_perkawinan": "Menikah",
      "alamat": "Jl. Mawar No. 1",
      "no_hp": "0812...",
      "no_telp": null,
      "npwp": "...",
      "bpjs_kesehatan": "...",
      "bpjs_ketenagakerjaan": "..."
    },
    "keluarga": {
      "pasangan": [],
      "anak": [],
      "orang_tua": [],
      "kontak_darurat": [],
      "tanggungan_lain": []
    },
    "riwayat_karir": {
      "jabatan": [],
      "str": [],
      "sip": [],
      "penugasan_klinis": []
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
        "jp": 16,
        "status_diklat": "sudah terlaksana"
      }
    ]
  }
}
```

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

#### Ubah Role Pegawai (Hanya Admin)

- **Route:** `PATCH /api/pegawai/{id}/change-role`
- **Body Type:** `application/json`
- **Auth:** Wajib Bearer token
- **Role yang diizinkan:** `admin`

Digunakan oleh Admin untuk mengubah role akun Pegawai yang sudah ada. Catatan: Admin tidak dapat mengubah rolenya sendiri.

- **Request Payload:**

| Field | Type | Wajib | Keterangan |
|-------|------|-------|------------|
| `role` | String | Ya | Salah satu dari: `pegawai`, `admin`, `hrd`, `direktur` |

- **Contoh Request Payload (JSON):**
  ```json
  {
    "role": "hrd"
  }
  ```

- **Response:** `200 OK`
  ```json
  {
    "success": true,
    "message": "Role pegawai berhasil diubah",
    "data": {
      "id": 101,
      "nik": "3509191234567890",
      "nama": "Ahmad Subarjo",
      "role": "hrd"
    }
  }
  ```

- **Response Gagal (Mengubah diri sendiri):** `400 Bad Request`
  ```json
  {
    "success": false,
    "message": "Tidak dapat mengubah role diri sendiri."
  }
  ```

### 19. STR/SIP (Admin/HRD/Direktur)

- Method: `GET`
- URL: `/api/str-sip`
- Auth: Wajib Bearer token
- Role yang diizinkan: `admin`, `hrd`, `direktur`

Mengambil daftar STR dan SIP seluruh pegawai beserta ringkasan statusnya.

Aturan status:

- **Aktif:** `tanggal_selesai >= today`
- **Hampir Habis:** sisa hari `<= 30` dan `tanggal_selesai >= today`
- **Tidak Aktif:** `tanggal_selesai < today` atau `tanggal_selesai` kosong

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
        "link_pdf": "http://localhost:8000/dokumen/str/sk-str-1-1715778987.pdf",
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
        "link_pdf": "http://localhost:8000/dokumen/sip/sk-sip-1-1715778987.pdf",
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
        "tanggal_kadaluarsa": "2025-05-10",
        "is_current": true
      }
    ],
    "sip": [
      {
        "jenis_sip": "SIP Praktik Rumah Sakit",
        "nomor_sip": "SIP-1234",
        "tanggal_terbit": "2021-06-01",
        "tanggal_kadaluarsa": "2026-06-01",
        "is_current": true
      }
    ],
    "penugasan_klinis": [
      {
        "nomor_surat": "SK-KLINIS-2022",
        "tanggal_mulai": "2022-01-01",
        "tanggal_kadaluarsa": "2026-12-31",
        "is_current": true
      }
    ],
    "ttd": {
      "kota": "Kalisat",
      "tanggal": "2026-04-28"
    }
  }
}
```

## Ringkasan Endpoint Per Role

Berikut rangkuman endpoint yang bisa diakses masing-masing role. Semua endpoint butuh header `Authorization: Bearer <jwt_token>`.

### Admin

- **Umum:** `GET /api/role`, `GET /api/dashboard`, `GET /api/diklat`, `GET /api/profile`, `GET /api/pegawai`, `GET /api/generate/cv`
- **Profile:** `PATCH /api/profile`, `POST /api/profil/profil-picture`, `POST /api/profile/profile-picture`, `POST /api/profil/ktp`, `POST /api/profile/kk`
- **Notifikasi:** `GET /api/notifications`, `PATCH /api/notifications/{id}/read`, `PATCH /api/notifications/read-all`
- **Riwayat Pendidikan:** `GET|POST /api/riwayat-karir/pendidikan`, `PATCH|POST|DELETE /api/riwayat-karir/pendidikan/{id}`
- **Riwayat Jabatan:** `GET|POST /api/riwayat-karir/jabatan`, `PATCH|POST|DELETE /api/riwayat-karir/jabatan/{id}`
- **Riwayat Pangkat:** `GET|POST /api/riwayat-karir/pangkat`, `PATCH|POST|DELETE /api/riwayat-karir/pangkat/{id}`
- **Riwayat SIP:** `GET|POST /api/riwayat-karir/sip`, `PATCH|POST|DELETE /api/riwayat-karir/sip/{id}`
- **Riwayat STR:** `GET|POST /api/riwayat-karir/str`, `PATCH|POST|DELETE /api/riwayat-karir/str/{id}`
- **Riwayat Penugasan Klinis:** `GET|POST /api/riwayat-karir/penugasan-klinis`, `PATCH|POST|DELETE /api/riwayat-karir/penugasan-klinis/{id}`
- **Keluarga:** CRUD Pasangan, Anak, Orang Tua, Kontak Darurat
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
- **Diklat (khusus pegawai):** `POST /api/diklat`, `PATCH /api/diklat/{id}`, `DELETE /api/diklat/{id}`
- **Profile:** `PATCH /api/profile`, `POST /api/profil/profil-picture`, `POST /api/profile/profile-picture`, `POST /api/profil/ktp`, `POST /api/profile/kk`
- **Notifikasi:** `GET /api/notifications`, `PATCH /api/notifications/{id}/read`, `PATCH /api/notifications/read-all`
- **Riwayat Pendidikan:** `GET|POST /api/riwayat-karir/pendidikan`, `PATCH|POST|DELETE /api/riwayat-karir/pendidikan/{id}`
- **Riwayat Jabatan:** `GET|POST /api/riwayat-karir/jabatan`, `PATCH|POST|DELETE /api/riwayat-karir/jabatan/{id}`
- **Riwayat Pangkat:** `GET|POST /api/riwayat-karir/pangkat`, `PATCH|POST|DELETE /api/riwayat-karir/pangkat/{id}`
- **Riwayat SIP:** `GET|POST /api/riwayat-karir/sip`, `PATCH|POST|DELETE /api/riwayat-karir/sip/{id}`
- **Riwayat STR:** `GET|POST /api/riwayat-karir/str`, `PATCH|POST|DELETE /api/riwayat-karir/str/{id}`
- **Riwayat Penugasan Klinis:** `GET|POST /api/riwayat-karir/penugasan-klinis`, `PATCH|POST|DELETE /api/riwayat-karir/penugasan-klinis/{id}`
- **Keluarga:** CRUD Pasangan, Anak, Orang Tua, Kontak Darurat

Dashboard pegawai menampilkan ringkasan: identitas (`nama`, `nip`, `jabatan`, `unit_kerja`), diklat (`jumlah_diklat_selesai`, `jumlah_diklat_dijadwalkan_belum_selesai`, `list_jadwal_diklat_mendatang`), dan aksi (`list_aksi`).

### HRD

- **Umum:** `GET /api/role`, `GET /api/dashboard`, `GET /api/diklat`, `GET /api/profile`, `GET /api/pegawai`, `GET /api/str-sip`, `GET /api/generate/cv`
- **Profile:** `PATCH /api/profile`, `POST /api/profil/profil-picture`, `POST /api/profile/profile-picture`, `POST /api/profil/ktp`, `POST /api/profile/kk`
- **Notifikasi:** `GET /api/notifications`, `PATCH /api/notifications/{id}/read`, `PATCH /api/notifications/read-all`
- **Riwayat Pendidikan:** `GET|POST /api/riwayat-karir/pendidikan`, `PATCH|POST|DELETE /api/riwayat-karir/pendidikan/{id}`
- **Riwayat Jabatan:** `GET|POST /api/riwayat-karir/jabatan`, `PATCH|POST|DELETE /api/riwayat-karir/jabatan/{id}`
- **Riwayat Pangkat:** `GET|POST /api/riwayat-karir/pangkat`, `PATCH|POST|DELETE /api/riwayat-karir/pangkat/{id}`
- **Riwayat SIP:** `GET|POST /api/riwayat-karir/sip`, `PATCH|POST|DELETE /api/riwayat-karir/sip/{id}`
- **Riwayat STR:** `GET|POST /api/riwayat-karir/str`, `PATCH|POST|DELETE /api/riwayat-karir/str/{id}`
- **Riwayat Penugasan Klinis:** `GET|POST /api/riwayat-karir/penugasan-klinis`, `PATCH|POST|DELETE /api/riwayat-karir/penugasan-klinis/{id}`
- **Keluarga:** CRUD Pasangan, Anak, Orang Tua, Kontak Darurat

### Direktur

- **Umum:** `GET /api/role`, `GET /api/dashboard`, `GET /api/diklat`, `GET /api/profile`, `GET /api/pegawai`, `GET /api/generate/cv`
- **Profile:** `PATCH /api/profile`, `POST /api/profil/profil-picture`, `POST /api/profile/profile-picture`, `POST /api/profil/ktp`, `POST /api/profile/kk`
- **Notifikasi:** `GET /api/notifications`, `PATCH /api/notifications/{id}/read`, `PATCH /api/notifications/read-all`
- **Riwayat Pendidikan:** `GET|POST /api/riwayat-karir/pendidikan`, `PATCH|POST|DELETE /api/riwayat-karir/pendidikan/{id}`
- **Riwayat Jabatan:** `GET|POST /api/riwayat-karir/jabatan`, `PATCH|POST|DELETE /api/riwayat-karir/jabatan/{id}`
- **Riwayat Pangkat:** `GET|POST /api/riwayat-karir/pangkat`, `PATCH|POST|DELETE /api/riwayat-karir/pangkat/{id}`
- **Riwayat SIP:** `GET|POST /api/riwayat-karir/sip`, `PATCH|POST|DELETE /api/riwayat-karir/sip/{id}`
- **Riwayat STR:** `GET|POST /api/riwayat-karir/str`, `PATCH|POST|DELETE /api/riwayat-karir/str/{id}`
- **Riwayat Penugasan Klinis:** `GET|POST /api/riwayat-karir/penugasan-klinis`, `PATCH|POST|DELETE /api/riwayat-karir/penugasan-klinis/{id}`
- **Keluarga:** CRUD Pasangan, Anak, Orang Tua, Kontak Darurat


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
  -F "is_current=1" \
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
  -F "is_current=1" \
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
  -F "is_current=1" \
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
4. Jalankan request `Login`, lalu copy `access_token` ke variable `token` / `token_admin` / `token_pegawai` sesuai role.
5. Jalankan request lain sesuai kebutuhan test.


## Daftar Request di Collection

Folder dan request yang tersedia di Postman:

1. `01. Umum`
  - `Health Check`
  - `Login`
2. `02. Semua Role`
  - `Cek Role`
  - `Dashboard`
  - `Get Diklat`
  - `Create Diklat (Pegawai)`
  - `Update Diklat (Pegawai)`
  - `Delete Diklat (Pegawai)`
  - `Get Profile`
  - `Patch Profile`
  - `Upload Foto Profile`
  - `Upload KTP`
  - `Upload KK`
  - `Get Riwayat Pendidikan`
  - `Keluarga` (Folder yang memuat CRUD Pasangan, Anak, Orang Tua, Kontak Darurat)
  - `Master Data` (Folder yang memuat GET berbagai data referensi/dropdown)
3. `03. Notifikasi`
  - `List Notifikasi`
4. `04. Admin Change Request`
  - `List Change Requests`
