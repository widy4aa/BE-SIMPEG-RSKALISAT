<?php

$fileApi = 'dokumentasi/dokumentasi_api.md';
$docApi = file_get_contents($fileApi);

$appendApi = <<<EOT

## Master Data (Form Dropdowns)

Semua endpoint master data diakses menggunakan metode `GET` dan wajib menyertakan Header `Authorization: Bearer <token>`.
Respons mengembalikan array `data` yang memuat id dan nama untuk keperluan opsi dropdown di frontend.

### GET /api/form/kategori-diklat
- **Route:** `/api/form/kategori-diklat`

### GET /api/form/tipe-diklat
- **Route:** `/api/form/tipe-diklat`

### GET /api/form/jenis-pegawai
- **Route:** `/api/form/jenis-pegawai`

### GET /api/form/unit-kerja
- **Route:** `/api/form/unit-kerja`

### GET /api/form/jenis-biaya
- **Route:** `/api/form/jenis-biaya`

### GET /api/form/golongan-ruang
- **Route:** `/api/form/golongan-ruang`

### GET /api/form/profesi
- **Route:** `/api/form/profesi`

### GET /api/form/jenis-sip
- **Route:** `/api/form/jenis-sip`

EOT;

$docApi = str_replace('## Daftar Request di Collection', $appendApi . "\n## Daftar Request di Collection", $docApi);
file_put_contents($fileApi, $docApi);

$filePostman = 'dokumentasi/postman/BE-SIMPEG-RSKALISAT.postman_collection.json';
$json = json_decode(file_get_contents($filePostman), true);

$masterDataItems = [
    [ 'name' => 'Kategori Diklat', 'path' => 'kategori-diklat' ],
    [ 'name' => 'Tipe Diklat', 'path' => 'tipe-diklat' ],
    [ 'name' => 'Jenis Pegawai', 'path' => 'jenis-pegawai' ],
    [ 'name' => 'Unit Kerja', 'path' => 'unit-kerja' ],
    [ 'name' => 'Jenis Biaya', 'path' => 'jenis-biaya' ],
    [ 'name' => 'Golongan Ruang', 'path' => 'golongan-ruang' ],
    [ 'name' => 'Profesi', 'path' => 'profesi' ],
    [ 'name' => 'Jenis SIP', 'path' => 'jenis-sip' ]
];

$masterDataFolder = [
    'name' => 'Master Data',
    'item' => []
];

foreach ($masterDataItems as $item) {
    $masterDataFolder['item'][] = [
        'name' => 'Get ' . $item['name'],
        'request' => [
            'method' => 'GET',
            'header' => [['key' => 'Accept', 'value' => 'application/json']],
            'url' => ['raw' => '{{base_url}}/api/form/' . $item['path'], 'host' => ['{{base_url}}'], 'path' => ['api', 'form', $item['path']]],
            'auth' => ['type' => 'bearer', 'bearer' => [['key' => 'token', 'value' => '{{token_pegawai}}', 'type' => 'string']]]
        ]
    ];
}

foreach ($json['item'] as &$folder) {
    if ($folder['name'] === '02. Semua Role') {
        $folder['item'][] = $masterDataFolder;
    }
}

file_put_contents($filePostman, json_encode($json, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

echo "Routes Master Data updated in docs and postman.\n";
