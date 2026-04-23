<?php

$docApi = file_get_contents('dokumentasi/dokumentasi_api.md');
$appendApi = <<<EOT

## Data Keluarga

### GET /api/data-keluarga
- **Method:** `GET`
- **Route:** `/api/data-keluarga`
- **Headers:** `Authorization: Bearer {token}`
- **Response:** Summary data keluarga beserta list anggotanya (pasangan, anak, dll).

### GET /api/keluarga/pasangan
- **Method:** `GET`
- **Route:** `/api/keluarga/pasangan`
- **Response:** List data pasangan.

### POST /api/keluarga/pasangan
- **Method:** `POST`
- **Route:** `/api/keluarga/pasangan`
- **Body:** `multipart/form-data` (nama_lengkap, tanggal_lahir, buku_nikah_file, dll)

### PATCH /api/keluarga/pasangan/{id}
- **Method:** `PATCH` atau `POST` (untuk form-data update)
- **Route:** `/api/keluarga/pasangan/{id}`

### DELETE /api/keluarga/pasangan/{id}
- **Method:** `DELETE`
- **Route:** `/api/keluarga/pasangan/{id}`

EOT;

file_put_contents('dokumentasi/dokumentasi_api.md', str_replace('## Daftar Request di Collection', $appendApi . "\n## Daftar Request di Collection", $docApi));


$docApp = file_get_contents('dokumentasi/dokumentasi_app.md');
$appendApp = <<<EOT

## Data Keluarga dan Pasangan

- Struktur `keluarga` dipisah menjadi 5 tabel independen: `pasangan`, `anak`, `orang_tua`, `kontak_darurat`, `tanggungan_lain`.
- Modul *Pasangan* sudah menggunakan *form request*, *repository*, dan *service* mandiri yang bisa menerima *upload* file buku nikah (tersimpan di `public/dokumen/pasangan`).

EOT;

file_put_contents('dokumentasi/dokumentasi_app.md', str_replace('## Database Schema', $appendApp . "\n## Database Schema", $docApp));

echo "Documentation Updated.\n";

EOT;
