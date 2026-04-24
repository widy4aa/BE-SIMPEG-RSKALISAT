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
11. [Upload File KK (Tanpa Approval)](#82-upload-file-kk-tanpa-approval)
12. [Notifikasi](#9-notifikasi)
13. [List Notifikasi](#91-list-notifikasi)
14. [Tandai 1 Notifikasi Sudah Dibaca](#92-tandai-1-notifikasi-sudah-dibaca)
15. [Tandai Semua Notifikasi Sudah Dibaca](#93-tandai-semua-notifikasi-sudah-dibaca)
16. [Riwayat Karir Pendidikan](#10-riwayat-karir-pendidikan)
17. [Riwayat Karir Jabatan](#11-riwayat-karir-jabatan)

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

BAB VI Data Keluarga
1. [Get Ringkasan Data Keluarga](#1-get-ringkasan-data-keluarga)
2. [Modul Pasangan](#2-modul-pasangan)
3. [Modul Anak](#3-modul-anak)
4. [Modul Orang Tua](#4-modul-orang-tua)
5. [Modul Kontak Darurat](#5-modul-kontak-darurat)

BAB VII Master Data (Form Dropdowns)
1. [List Endpoint Master Data](#master-data-form-dropdowns)
4. [Reject Change Request](#13-reject-change-request-admin)

BAB VI Data Uji dan Simulasi

1. [Akun Seeder Untuk Uji Login](#akun-seeder-untuk-uji-login)
2. [Quick Test via cURL](#quick-test-via-curl)

BAB VII Postman

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
          "jenis_pelaksana": "internal",
          "catatan": "Workshop peningkatan komunikasi lintas unit.",
          "sertif_file_path": "dokumen/sertif-diklat/budi-audit-internal.pdf",
          "no_sertif": "SERTIF/SDM/2026/0001"
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
- `catatan`: catatan tambahan diklat.
- `sertif_file_path`: path file sertifikat diklat.
- `no_sertif`: nomor sertifikat diklat.

Aturan hitung `status`:

- `mendatang`: hari ini < `tanggal_mulai`
- `berlangsung`: hari ini di antara `tanggal_mulai` dan `tanggal_selesai`
- `selesai`: hari ini > `tanggal_selesai`

Catatan bentuk payload:

- `admin`: `ringkasan` + `list_diklat`
- `pegawai`: `ringkasan` + `riwayat_diklat`
- `hrd`: `ringkasan` + `list_usulan`
- `direktur`: `ringkasan` + `keputusan_terbaru`

Catatan field `catatan`:

- Untuk role `pegawai`, `catatan` berada di setiap item `riwayat_diklat`.
- Untuk role `admin`, `hrd`, dan `direktur`, `catatan` juga berada di setiap item list sesuai role.

Catatan field `status`:

- Status hitung by tanggal (`mendatang`, `berlangsung`, `selesai`) saat ini diterapkan pada item role `pegawai`.
- Item role `admin`, `hrd`, dan `direktur` saat ini belum menggunakan field `status`.

#### Create Diklat Pegawai

- Method: `POST`
- URL: `/api/diklat`
- Auth: Wajib Bearer token
- Role yang diizinkan: `pegawai`
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
  - `status_validasi` otomatis `null`

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

Fitur untuk mengelola riwayat pangkat user yang sedang login.

#### GET Riwayat Pangkat

Menampilkan daftar riwayat pangkat yang dimiliki user (diurutkan berdasarkan `started_at` menurun).

Contoh response `200 OK`:

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

Menambahkan data riwayat pangkat baru untuk user yang sedang login beserta lampiran SK.

| Parameter | Tipe | Wajib | Keterangan |
| :--- | :--- | :--- | :--- |
| `nama_pangkat` | String | Ya | Nama pangkat |
| `is_current` | Boolean (0/1) | Ya | Apakah pangkat ini masih aktif? |
| `pejabat_penetap` | String | Tidak | Nama pejabat penetap |
| `tmt_sk` | Date | Tidak | Tanggal sk pangkat (Format: YYYY-MM-DD) |
| `started_at` | Date | Tidak | Tanggal mulai jabatan/pangkat |
| `ended_at` | Date | Tidak | Tanggal selesai |
| `sk_pangkat` | File | Tidak | File SK pangkat (max 5MB, format pdf/jpg/png) |
| `note` | String | Tidak | Catatan tambahan |

Contoh response `201 Created`:

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

Memperbarui sebagian data riwayat pangkat berdasarkan `{id}`.
Untuk menghindari limitasi *multipart/form-data* di PHP, Anda dapat menggunakan *method* **`POST`** (tanpa perlu `_method=PATCH`). File SK lama akan otomatis dihapus jika Anda mengunggah file SK baru.

Field request (*multipart/form-data*):
- `nama_pangkat` (sometimes, string)
- `is_current` (sometimes, boolean: 1/0)
- `pejabat_penetap` (sometimes, string)
- `tmt_sk` (sometimes, date)
- `started_at` (sometimes, date)
- `ended_at` (sometimes, date)
- `sk_pangkat` (sometimes, file: pdf/jpg/png, max 5MB)
- `note` (sometimes, string)

Contoh response `200 OK`:

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

Menghapus riwayat pangkat beserta file SK-nya (jika ada) milik user yang sedang login.

Contoh response `200 OK`:

```json
{
  "success": true,
  "message": "Riwayat pangkat berhasil dihapus."
}
```

### 13. Riwayat Karir SIP

Fitur untuk mengelola riwayat SIP (Surat Izin Praktik) user yang sedang login.

#### GET Riwayat SIP

Menampilkan daftar riwayat SIP yang dimiliki user (diurutkan berdasarkan `tanggal_terbit` menurun).

Contoh response `200 OK`:

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

Menambahkan data riwayat SIP baru untuk user yang sedang login beserta lampirannya.

| Parameter | Tipe | Wajib | Keterangan |
| :--- | :--- | :--- | :--- |
| `jenis_sip_id` | Integer | Tidak | ID Jenis SIP |
| `nomor_sip` | String | Ya | Nomor surat SIP |
| `tanggal_terbit` | Date | Ya | Tanggal terbit (Format: YYYY-MM-DD) |
| `tanggal_kadaluarsa` | Date | Ya | Tanggal kedaluwarsa |
| `is_current` | Boolean (0/1) | Ya | Apakah SIP ini masih aktif? |
| `sk_sip` | File | Tidak | File SK SIP (max 5MB, format pdf/jpg/png) |

Contoh response `201 Created`:

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

Memperbarui sebagian data riwayat SIP berdasarkan `{id}`.
Gunakan *method* **`POST`** (tanpa `_method=PATCH`) jika mengirim file untuk menghindari limitasi PHP. File SK lama otomatis dihapus.

Field request (*multipart/form-data*):
- `jenis_sip_id` (sometimes, integer)
- `nomor_sip` (sometimes, string)
- `tanggal_terbit` (sometimes, date)
- `tanggal_kadaluarsa` (sometimes, date)
- `is_current` (sometimes, boolean: 1/0)
- `sk_sip` (sometimes, file: pdf/jpg/png, max 5MB)

Contoh response `200 OK`:

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

Menghapus riwayat SIP beserta file-nya (jika ada) milik user yang sedang login.

Contoh response `200 OK`:

```json
{
  "success": true,
  "message": "Riwayat SIP berhasil dihapus."
}
```

### 14. Riwayat Karir STR

Fitur untuk mengelola riwayat STR (Surat Tanda Registrasi) user yang sedang login.

#### GET Riwayat STR

Menampilkan daftar riwayat STR yang dimiliki user (diurutkan berdasarkan `tanggal_terbit` menurun).

Contoh response `200 OK`:

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

Menambahkan data riwayat STR baru untuk user yang sedang login beserta lampirannya.

| Parameter | Tipe | Wajib | Keterangan |
| :--- | :--- | :--- | :--- |
| `nomor_str` | String | Ya | Nomor surat STR |
| `tanggal_terbit` | Date | Ya | Tanggal terbit (Format: YYYY-MM-DD) |
| `tanggal_kadaluarsa` | Date | Ya | Tanggal kedaluwarsa |
| `is_current` | Boolean (0/1) | Ya | Apakah STR ini masih aktif? |
| `sk_str` | File | Tidak | File SK STR (max 5MB, format pdf/jpg/png) |

Contoh response `201 Created`:

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

Memperbarui sebagian data riwayat STR berdasarkan `{id}`.
Gunakan *method* **`POST`** (tanpa `_method=PATCH`) jika mengirim file untuk menghindari limitasi PHP. File SK lama otomatis dihapus.

Field request (*multipart/form-data*):
- `nomor_str` (sometimes, string)
- `tanggal_terbit` (sometimes, date)
- `tanggal_kadaluarsa` (sometimes, date)
- `is_current` (sometimes, boolean: 1/0)
- `sk_str` (sometimes, file: pdf/jpg/png, max 5MB)

Contoh response `200 OK`:

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

Menghapus riwayat STR beserta file-nya (jika ada) milik user yang sedang login.

Contoh response `200 OK`:

```json
{
  "success": true,
  "message": "Riwayat STR berhasil dihapus."
}
```

### 15. Riwayat Karir Penugasan Klinis

Fitur untuk mengelola riwayat penugasan klinis user yang sedang login.

#### GET Riwayat Penugasan Klinis

Menampilkan daftar riwayat penugasan klinis yang dimiliki user (diurutkan berdasarkan `tgl_mulai` menurun).

Contoh response `200 OK`:

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

Menambahkan data riwayat penugasan klinis baru untuk user yang sedang login beserta lampirannya.

| Parameter | Tipe | Wajib | Keterangan |
| :--- | :--- | :--- | :--- |
| `nomor_surat` | String | Ya | Nomor surat penugasan klinis |
| `tgl_mulai` | Date | Ya | Tanggal mulai (Format: YYYY-MM-DD) |
| `tgl_kadaluarsa` | Date | Ya | Tanggal kedaluwarsa |
| `is_current` | Boolean (0/1) | Ya | Apakah penugasan ini masih aktif? |
| `dokumen_file` | File | Tidak | File dokumen (max 5MB, format pdf/jpg/png) |

Contoh response `201 Created`:

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

Memperbarui sebagian data riwayat penugasan klinis berdasarkan `{id}`.
Gunakan *method* **`POST`** (tanpa `_method=PATCH`) jika mengirim file untuk menghindari limitasi PHP. File dokumen lama otomatis dihapus.

Field request (*multipart/form-data*):
- `nomor_surat` (sometimes, string)
- `tgl_mulai` (sometimes, date)
- `tgl_kadaluarsa` (sometimes, date)
- `is_current` (sometimes, boolean: 1/0)
- `dokumen_file` (sometimes, file: pdf/jpg/png, max 5MB)

Contoh response `200 OK`:

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

Menghapus riwayat penugasan klinis beserta file-nya (jika ada) milik user yang sedang login.

Contoh response `200 OK`:

```json
{
  "success": true,
  "message": "Riwayat penugasan klinis berhasil dihapus."
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
  - `POST /api/profile/kk`
  - `GET /api/notifications`
  - `PATCH /api/notifications/{id}/read`
  - `PATCH /api/notifications/read-all`
  - `GET /api/riwayat-karir/pendidikan`
  - `POST /api/riwayat-karir/pendidikan`
  - `PATCH /api/riwayat-karir/pendidikan/{id}`
  - `DELETE /api/riwayat-karir/pendidikan/{id}`
  - `GET /api/riwayat-karir/jabatan`
  - `POST /api/riwayat-karir/jabatan`
  - `PATCH /api/riwayat-karir/jabatan/{id}`
  - `DELETE /api/riwayat-karir/jabatan/{id}`
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
  - `POST /api/profile/kk`
  - `GET /api/notifications`
  - `PATCH /api/notifications/{id}/read`
  - `PATCH /api/notifications/read-all`
  - `GET /api/riwayat-karir/pendidikan`
  - `POST /api/riwayat-karir/pendidikan`
  - `PATCH /api/riwayat-karir/pendidikan/{id}`
  - `DELETE /api/riwayat-karir/pendidikan/{id}`
  - `GET /api/riwayat-karir/jabatan`
  - `POST /api/riwayat-karir/jabatan`
  - `PATCH /api/riwayat-karir/jabatan/{id}`
  - `DELETE /api/riwayat-karir/jabatan/{id}`
- Catatan dashboard:
  - `message`: `Selamat datang pegawai`
  - `data.dashboard` menampilkan ringkasan lengkap pegawai:
    - identitas: `nama`, `nip`, `jabatan`, `jenis_jabatan`, `unit_kerja`
    - diklat: `jumlah_diklat_selesai`, `jumlah_diklat_dijadwalkan_belum_selesai`, `list_jadwal_diklat_mendatang`
    - notifikasi info: gunakan endpoint terpisah `GET /api/notifications`
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
  - `POST /api/profile/kk`
  - `GET /api/notifications`
  - `PATCH /api/notifications/{id}/read`
  - `PATCH /api/notifications/read-all`
  - `GET /api/riwayat-karir/pendidikan`
  - `POST /api/riwayat-karir/pendidikan`
  - `PATCH /api/riwayat-karir/pendidikan/{id}`
  - `DELETE /api/riwayat-karir/pendidikan/{id}`
  - `GET /api/riwayat-karir/jabatan`
  - `POST /api/riwayat-karir/jabatan`
  - `PATCH /api/riwayat-karir/jabatan/{id}`
  - `DELETE /api/riwayat-karir/jabatan/{id}`
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
  - `POST /api/profile/kk`
  - `GET /api/notifications`
  - `PATCH /api/notifications/{id}/read`
  - `PATCH /api/notifications/read-all`
  - `GET /api/riwayat-karir/pendidikan`
  - `POST /api/riwayat-karir/pendidikan`
  - `PATCH /api/riwayat-karir/pendidikan/{id}`
  - `DELETE /api/riwayat-karir/pendidikan/{id}`
  - `GET /api/riwayat-karir/jabatan`
  - `POST /api/riwayat-karir/jabatan`
  - `PATCH /api/riwayat-karir/jabatan/{id}`
  - `DELETE /api/riwayat-karir/jabatan/{id}`
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


## Data Keluarga

Bagian ini memuat dokumentasi seluruh layanan (CRUD) terkait Data Keluarga yang terbagi menjadi entitas independen: **Pasangan**, **Anak**, **Orang Tua**, dan **Kontak Darurat**. Seluruh endpoint mewajibkan penggunaan *Bearer Token* dari user (Pegawai/Admin/HRD).

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

#### A. Tambah Data Pasangan
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

#### B. Ubah Data Pasangan
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

#### C. Hapus Data Pasangan
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

#### A. Tambah Data Anak
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

#### B. Ubah Data Anak
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

#### C. Hapus Data Anak
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

#### A. Tambah Data Orang Tua
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

#### B. Ubah Data Orang Tua
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

#### C. Hapus Data Orang Tua
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

#### A. Tambah Data Kontak Darurat
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

#### B. Ubah Data Kontak Darurat
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

#### C. Hapus Data Kontak Darurat
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

## Master Data (Form Dropdowns)

Semua endpoint master data diakses menggunakan metode `GET` dan wajib menyertakan Header `Authorization: Bearer <token>`.
Respons mengembalikan array `data` yang memuat `id` dan `nama` untuk keperluan opsi *dropdown* form di antarmuka frontend.

### List Endpoint Master Data

- `GET /api/form/kategori-diklat`
- `GET /api/form/tipe-diklat`
- `GET /api/form/jenis-pegawai`
- `GET /api/form/unit-kerja`
- `GET /api/form/jenis-biaya`
- `GET /api/form/golongan-ruang`
- `GET /api/form/profesi`
- `GET /api/form/jenis-sip`

**Contoh Response Master Data (`GET /api/form/jenis-pegawai`):**
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
