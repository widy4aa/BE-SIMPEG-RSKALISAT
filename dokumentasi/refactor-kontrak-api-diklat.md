# Rencana Refactor Kontrak API Diklat

Dokumen ini adalah bahan diskusi dengan frontend sebelum backend mengubah kontrak API. Refactor ini belum wajib dilakukan sekarang karena berpotensi memengaruhi payload request/response yang sudah dipakai frontend.

Tujuan utama:
- Menyamakan nama field agar konsisten.
- Memisahkan konsep sertifikat dan laporan diklat.
- Menghindari field boolean dengan nama ambigu.
- Menjaga backward compatibility selama masa transisi.

## Prinsip Migrasi

Rekomendasi implementasi backend:
1. Tambahkan field baru tanpa langsung menghapus field lama.
2. Backend mengirim field lama dan field baru dalam 1-2 sprint.
3. Frontend pindah membaca field baru.
4. Setelah frontend aman, backend hapus field lama.

## Ringkasan Perubahan Field

| Area | Sebelum | Sesudah | Status | Catatan |
|---|---|---|---|---|
| Jenis pelaksanaan diklat | `jenis_pelaksana` | `jenis_pelaksanaan` | Disarankan | Samakan dengan nama kolom database dan domain. |
| Flag upload laporan | `uploadlaporan` | `can_upload_laporan` | Disarankan | Nama lama tidak konsisten casing dan kurang jelas sebagai boolean. |
| Path file laporan/sertifikat | `sertif_file_path` | `laporan_file_path` | Perlu diskusi | Saat ini field dipakai untuk laporan/sertifikat. Nama baru lebih sesuai flow upload laporan. |
| File upload saat create/update | `upload_sertif` | `sertifikat_file` | Opsional | Hanya jika frontend ingin naming lebih eksplisit. Bisa ditunda. |
| File upload laporan | `upload_laporan` | `laporan_file` | Opsional | Lebih pendek dan konsisten dengan response baru. |
| Nomor sertifikat | `no_sertif` | `nomor_sertifikat` | Opsional | Lebih mudah dibaca, tapi perubahan cukup luas. |
| Status kelayakan request HRD | `status_kelayakan: true/false` | `is_layak: true/false` | Opsional | Field lama masih cukup bisa dipahami. |
| Status validasi request HRD | `status_validasi: true/false` | `is_valid: true/false` | Opsional | Mengurangi ambigu antara request boolean dan response string. |

## Detail Per Endpoint

### 1. GET `/api/diklat`

Dipakai untuk list diklat berdasarkan role login.

#### Response Item Sebelum

```json
{
  "id": 1,
  "nama": "Pelatihan BTCLS",
  "kategori": "Teknis",
  "jenis": "Tenaga Kesehatan",
  "pelaksana": "RS Kalisat",
  "tanggal_mulai": "2026-06-20",
  "tanggal_selesai": "2026-06-21",
  "status": "selesai",
  "tempat": "Aula",
  "waktu": "08:00:00",
  "created_by": "Budi",
  "jp": 8,
  "total_biaya": 250000,
  "jenis_biaya": "Mandiri",
  "jenis_pelaksana": "internal",
  "catatan": "-",
  "sertif_file_path": "dokumen/sertif-diklat/sertif-1.pdf",
  "no_sertif": "CERT-001",
  "status_validasi": "sudah di validasi",
  "uploadlaporan": false
}
```

#### Response Item Sesudah

```json
{
  "id": 1,
  "nama": "Pelatihan BTCLS",
  "kategori": "Teknis",
  "jenis": "Tenaga Kesehatan",
  "pelaksana": "RS Kalisat",
  "tanggal_mulai": "2026-06-20",
  "tanggal_selesai": "2026-06-21",
  "status": "selesai",
  "tempat": "Aula",
  "waktu": "08:00:00",
  "created_by": "Budi",
  "jp": 8,
  "total_biaya": 250000,
  "jenis_biaya": "Mandiri",
  "jenis_pelaksanaan": "internal",
  "catatan": "-",
  "laporan_file_path": "dokumen/sertif-diklat/sertif-1.pdf",
  "nomor_sertifikat": "CERT-001",
  "status_validasi": "sudah di validasi",
  "can_upload_laporan": false
}
```

#### Transisi Aman

Selama masa transisi, backend bisa mengirim dua versi field:

```json
{
  "jenis_pelaksana": "internal",
  "jenis_pelaksanaan": "internal",
  "sertif_file_path": "dokumen/sertif-diklat/sertif-1.pdf",
  "laporan_file_path": "dokumen/sertif-diklat/sertif-1.pdf",
  "no_sertif": "CERT-001",
  "nomor_sertifikat": "CERT-001",
  "uploadlaporan": false,
  "can_upload_laporan": false
}
```

## 2. POST `/api/diklat`

Dipakai pegawai/HRD/direktur untuk membuat riwayat diklat mandiri.

### Request Sebelum

```json
{
  "nama_kegiatan": "Pelatihan BTCLS",
  "kategori": "Teknis",
  "jenis_diklat": "Tenaga Kesehatan",
  "penyelenggara": "RS Kalisat",
  "lokasi": "Aula",
  "tanggal_mulai": "2026-06-20",
  "tanggal_selesai": "2026-06-21",
  "no_sertif": "CERT-001",
  "upload_sertif": "file",
  "jp": 8,
  "jenis_biaya": "Mandiri",
  "total_biaya": 250000,
  "catatan": "-",
  "jenis_pelaksana": "internal",
  "waktu": "08:00:00"
}
```

### Request Sesudah

```json
{
  "nama_kegiatan": "Pelatihan BTCLS",
  "kategori": "Teknis",
  "jenis_diklat": "Tenaga Kesehatan",
  "penyelenggara": "RS Kalisat",
  "lokasi": "Aula",
  "tanggal_mulai": "2026-06-20",
  "tanggal_selesai": "2026-06-21",
  "nomor_sertifikat": "CERT-001",
  "sertifikat_file": "file",
  "jp": 8,
  "jenis_biaya": "Mandiri",
  "total_biaya": 250000,
  "catatan": "-",
  "jenis_pelaksanaan": "internal",
  "waktu": "08:00:00"
}
```

### Rekomendasi

Untuk request create/update, perubahan field berikut bisa ditunda agar frontend tidak perlu banyak mengubah form sekaligus:
- `no_sertif` ke `nomor_sertifikat`
- `upload_sertif` ke `sertifikat_file`

Yang paling penting untuk disamakan lebih dulu:
- `jenis_pelaksana` ke `jenis_pelaksanaan`

## 3. PATCH `/api/diklat/{id}`

Kontrak request mengikuti POST `/api/diklat`, tetapi semua field bersifat optional.

### Request Sebelum

```json
{
  "nama_kegiatan": "Pelatihan Update",
  "jenis_pelaksana": "internal",
  "no_sertif": "CERT-002",
  "upload_sertif": "file"
}
```

### Request Sesudah

```json
{
  "nama_kegiatan": "Pelatihan Update",
  "jenis_pelaksanaan": "internal",
  "nomor_sertifikat": "CERT-002",
  "sertifikat_file": "file"
}
```

Catatan backend saat ini: jenis pelaksanaan tidak boleh diubah setelah dibuat.

## 4. POST `/api/diklat/{id}/upload-laporan`

Dipakai untuk upload atau edit laporan/sertifikat setelah diklat selesai.

### Request Sebelum

```json
{
  "upload_laporan": "file",
  "no_sertif": "CERT-001"
}
```

### Request Sesudah

```json
{
  "laporan_file": "file",
  "nomor_sertifikat": "CERT-001"
}
```

### Response Sebelum

```json
{
  "id_diklat": 1,
  "id_jadwal_diklat": 10,
  "no_sertif": "CERT-001",
  "sertif_file_path": "dokumen/sertif-diklat/sertif-1.pdf",
  "status_validasi": null,
  "uploaded_at": "2026-06-28 10:00:00"
}
```

### Response Sesudah

```json
{
  "id_diklat": 1,
  "id_jadwal_diklat": 10,
  "nomor_sertifikat": "CERT-001",
  "laporan_file_path": "dokumen/sertif-diklat/sertif-1.pdf",
  "status_validasi": null,
  "uploaded_at": "2026-06-28 10:00:00"
}
```

## 5. GET `/api/diklat/all`

Dipakai HRD/direktur untuk melihat semua master diklat.

### Field yang Berubah

| Sebelum | Sesudah |
|---|---|
| `jenis_pelaksana` | `jenis_pelaksanaan` |

Field lain bisa tetap sama.

## 6. POST `/hrd/diklat`

Dipakai HRD untuk membuat master diklat.

### Request Sebelum

```json
{
  "nama_kegiatan": "Pelatihan HRD",
  "kategori": "Teknis",
  "jenis_diklat": "ASN",
  "penyelenggara": "RS Kalisat",
  "lokasi": "Aula",
  "tanggal_mulai": "2026-06-20",
  "tanggal_selesai": "2026-06-21",
  "jp": 8,
  "jenis_biaya": "Dinas",
  "total_biaya": 500000,
  "catatan": "-",
  "jenis_pelaksana": "internal",
  "waktu": "08:00:00"
}
```

### Request Sesudah

```json
{
  "nama_kegiatan": "Pelatihan HRD",
  "kategori": "Teknis",
  "jenis_diklat": "ASN",
  "penyelenggara": "RS Kalisat",
  "lokasi": "Aula",
  "tanggal_mulai": "2026-06-20",
  "tanggal_selesai": "2026-06-21",
  "jp": 8,
  "jenis_biaya": "Dinas",
  "total_biaya": 500000,
  "catatan": "-",
  "jenis_pelaksanaan": "internal",
  "waktu": "08:00:00"
}
```

## 7. PUT `/hrd/diklat/{id}`

Kontrak sama seperti POST `/hrd/diklat`.

| Sebelum | Sesudah |
|---|---|
| `jenis_pelaksana` | `jenis_pelaksanaan` |

## 8. PATCH `/hrd/diklat/{id}/status/layak`

Dipakai HRD untuk approve/tolak kelayakan diklat external.

### Request Sebelum

```json
{
  "status_kelayakan": true
}
```

### Request Sesudah

```json
{
  "is_layak": true
}
```

### Rekomendasi

Perubahan ini opsional. Field `status_kelayakan` masih bisa dipakai, tetapi `is_layak` lebih jelas karena request berupa boolean sedangkan response berupa string `layak` atau `tidak layak`.

## 9. PATCH `/hrd/diklat/{id}/status/validasi`

Dipakai HRD untuk approve/tolak validasi laporan diklat internal.

### Request Sebelum

```json
{
  "status_validasi": true
}
```

### Request Sesudah

```json
{
  "is_valid": true
}
```

### Rekomendasi

Perubahan ini opsional. Field `status_validasi` masih bisa dipakai, tetapi `is_valid` lebih jelas karena request berupa boolean sedangkan response berupa string `valid` atau `tidak valid`.

## Prioritas Implementasi

### Prioritas 1 - Paling Disarankan

Field response list/detail:
- `jenis_pelaksana` ke `jenis_pelaksanaan`
- `uploadlaporan` ke `can_upload_laporan`
- `sertif_file_path` ke `laporan_file_path`

Alasan: ini paling terasa di frontend saat render list dan tombol upload laporan.

### Prioritas 2 - Bisa Setelah Frontend Aman

Field request create/update/upload:
- `jenis_pelaksana` ke `jenis_pelaksanaan`
- `upload_laporan` ke `laporan_file`
- `upload_sertif` ke `sertifikat_file`
- `no_sertif` ke `nomor_sertifikat`

Alasan: perubahan request lebih rawan karena menyentuh form submit dan upload file.

### Prioritas 3 - Opsional

Field boolean HRD:
- `status_kelayakan` ke `is_layak`
- `status_validasi` ke `is_valid`

Alasan: lebih bersih, tetapi tidak mendesak.

## Rekomendasi Backward Compatibility

Backend sebaiknya menerima field lama dan baru untuk request:

| Field Lama | Field Baru | Aturan Backend |
|---|---|---|
| `jenis_pelaksana` | `jenis_pelaksanaan` | Terima keduanya, prioritaskan field baru jika keduanya dikirim. |
| `no_sertif` | `nomor_sertifikat` | Terima keduanya, simpan ke kolom yang sama. |
| `upload_sertif` | `sertifikat_file` | Terima keduanya, simpan ke path yang sama. |
| `upload_laporan` | `laporan_file` | Terima keduanya, simpan ke path yang sama. |
| `status_kelayakan` | `is_layak` | Terima keduanya, convert boolean ke `layak`/`tidak layak`. |
| `status_validasi` | `is_valid` | Terima keduanya, convert boolean ke `valid`/`tidak valid`. |

Response juga sebaiknya mengirim field lama dan baru selama masa transisi.

## Hal yang Tidak Disarankan Diubah Sekarang

- URL endpoint.
- Struktur pagination Laravel.
- Nilai status existing: `mendatang`, `berlangsung`, `selesai`, `belum terlaksana`, `sedang terlaksana`, `sudah terlaksana`.
- Nilai approval existing: `layak`, `tidak layak`, `valid`, `tidak valid`.

## Checklist Koordinasi Frontend

- Frontend sudah tahu field baru yang akan dibaca.
- Frontend siap fallback ke field lama selama masa transisi.
- Backend siap mengirim field lama dan baru bersamaan.
- Ada kesepakatan tanggal penghapusan field lama.
- Setelah migrasi frontend selesai, backend boleh menghapus field lama dan test lama disesuaikan.

