<?php

$fileApi = 'dokumentasi/dokumentasi_api.md';
$docApi = file_get_contents($fileApi);

$appendApi = <<<EOT

### GET /api/keluarga/orang-tua
- **Method:** `GET`
- **Route:** `/api/keluarga/orang-tua`
- **Response:** List data orang tua.

### POST /api/keluarga/orang-tua
- **Method:** `POST`
- **Route:** `/api/keluarga/orang-tua`
- **Body:** `application/x-www-form-urlencoded` atau JSON (nama_ayah, nama_ibu, status_hidup, alamat)

### PATCH /api/keluarga/orang-tua/{id}
- **Method:** `PATCH`
- **Route:** `/api/keluarga/orang-tua/{id}`

### DELETE /api/keluarga/orang-tua/{id}
- **Method:** `DELETE`
- **Route:** `/api/keluarga/orang-tua/{id}`

EOT;

$docApi = str_replace('## Daftar Request di Collection', $appendApi . "\n## Daftar Request di Collection", $docApi);
file_put_contents($fileApi, $docApi);

$fileApp = 'dokumentasi/dokumentasi_app.md';
$docApp = file_get_contents($fileApp);
$docApp = str_replace(
    'Modul *Pasangan* dan *Anak* sudah menggunakan',
    'Modul *Pasangan*, *Anak*, dan *Orang Tua* sudah menggunakan',
    $docApp
);
file_put_contents($fileApp, $docApp);


$filePostman = 'dokumentasi/postman/BE-SIMPEG-RSKALISAT.postman_collection.json';
$json = json_decode(file_get_contents($filePostman), true);

$getOrangTuaRequest = [
    'name' => 'Get Keluarga Orang Tua',
    'request' => [
        'method' => 'GET',
        'header' => [['key' => 'Accept', 'value' => 'application/json']],
        'url' => ['raw' => '{{base_url}}/api/keluarga/orang-tua', 'host' => ['{{base_url}}'], 'path' => ['api', 'keluarga', 'orang-tua']],
        'auth' => ['type' => 'bearer', 'bearer' => [['key' => 'token', 'value' => '{{token_pegawai}}', 'type' => 'string']]]
    ]
];

$postOrangTuaRequest = [
    'name' => 'Create Keluarga Orang Tua',
    'request' => [
        'method' => 'POST',
        'header' => [['key' => 'Accept', 'value' => 'application/json']],
        'body' => [
            'mode' => 'urlencoded',
            'urlencoded' => [
                ['key' => 'nama_ayah', 'value' => 'Ayah Test', 'type' => 'text'],
                ['key' => 'nama_ibu', 'value' => 'Ibu Test', 'type' => 'text'],
                ['key' => 'status_hidup', 'value' => 'Hidup', 'type' => 'text'],
                ['key' => 'alamat', 'value' => 'Jl. Kenangan No. 1', 'type' => 'text']
            ]
        ],
        'url' => ['raw' => '{{base_url}}/api/keluarga/orang-tua', 'host' => ['{{base_url}}'], 'path' => ['api', 'keluarga', 'orang-tua']],
        'auth' => ['type' => 'bearer', 'bearer' => [['key' => 'token', 'value' => '{{token_pegawai}}', 'type' => 'string']]]
    ]
];

$patchOrangTuaRequest = [
    'name' => 'Update Keluarga Orang Tua',
    'request' => [
        'method' => 'PATCH',
        'header' => [['key' => 'Accept', 'value' => 'application/json']],
        'body' => [
            'mode' => 'urlencoded',
            'urlencoded' => [
                ['key' => 'nama_ayah', 'value' => 'Ayah Edit', 'type' => 'text']
            ]
        ],
        'url' => ['raw' => '{{base_url}}/api/keluarga/orang-tua/1', 'host' => ['{{base_url}}'], 'path' => ['api', 'keluarga', 'orang-tua', '1']],
        'auth' => ['type' => 'bearer', 'bearer' => [['key' => 'token', 'value' => '{{token_pegawai}}', 'type' => 'string']]]
    ]
];

$deleteOrangTuaRequest = [
    'name' => 'Delete Keluarga Orang Tua',
    'request' => [
        'method' => 'DELETE',
        'header' => [['key' => 'Accept', 'value' => 'application/json']],
        'url' => ['raw' => '{{base_url}}/api/keluarga/orang-tua/1', 'host' => ['{{base_url}}'], 'path' => ['api', 'keluarga', 'orang-tua', '1']],
        'auth' => ['type' => 'bearer', 'bearer' => [['key' => 'token', 'value' => '{{token_pegawai}}', 'type' => 'string']]]
    ]
];

foreach ($json['item'] as &$folder) {
    if ($folder['name'] === '02. Semua Role') {
        foreach ($folder['item'] as &$subfolder) {
            if ($subfolder['name'] === 'Keluarga') {
                $subfolder['item'][] = $getOrangTuaRequest;
                $subfolder['item'][] = $postOrangTuaRequest;
                $subfolder['item'][] = $patchOrangTuaRequest;
                $subfolder['item'][] = $deleteOrangTuaRequest;
            }
        }
    }
}

file_put_contents($filePostman, json_encode($json, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

echo "Routes Orang Tua updated in docs and postman.\n";
