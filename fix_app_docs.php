<?php

$docApp = file_get_contents('dokumentasi/dokumentasi_app.md');
$appendApp = <<<EOT

## Data Keluarga dan Pasangan

- Struktur `keluarga` dipisah menjadi 5 tabel independen: `pasangan`, `anak`, `orang_tua`, `kontak_darurat`, `tanggungan_lain`.
- Modul *Pasangan* dan *Anak* sudah menggunakan *form request*, *repository*, dan *service* mandiri yang bisa menerima *upload* file (tersimpan di `public/dokumen/pasangan` dan `public/dokumen/anak`).

EOT;

$docApp = str_replace('## Database Schema', $appendApp . "\n## Database Schema", $docApp);
file_put_contents('dokumentasi/dokumentasi_app.md', $docApp);

echo "done app docs\n";

