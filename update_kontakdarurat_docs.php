<?php

$fileApi = 'dokumentasi/dokumentasi_api.md';
$docApi = file_get_contents($fileApi);

$appendApi = <<<EOT

### GET /api/keluarga/kontak-darurat
- **Method:** `GET`
- **Route:** `/api/keluarga/kontak-darurat`
- **Response:** List data kontak darurat.

### POST /api/keluarga/kontak-darurat
- **Method:** `POST`
- **Route:** `/api/keluarga/kontak-darurat`
- **Body:** `application/x-www-form-urlencoded` atau JSON (nama_kontak, hubungan_keluarga, nomor_hp, alamat)

### PATCH /api/keluarga/kontak-darurat/{id}
- **Method:** `PATCH`
- **Route:** `/api/keluarga/kontak-darurat/{id}`

### DELETE /api/keluarga/kontak-darurat/{id}
- **Method:** `DELETE`
- **Route:** `/api/keluarga/kontak-darurat/{id}`

EOT;

$docApi = str_replace('## Daftar Request di Collection', $appendApi . "\n## Daftar Request di Collection", $docApi);
file_put_contents($fileApi, $docApi);

$fileApp = 'dokumentasi/dokumentasi_app.md';
$docApp = file_get_contents($fileApp);
$docApp = str_replace(
    'Modul *Pasangan*, *Anak*, dan *Orang Tua* sudah menggunakan',
    'Modul *Pasangan*, *Anak*, *Orang Tua*, dan *Kontak Darurat* sudah menggunakan',
    $docApp
);
file_put_contents($fileApp, $docApp);


$filePostman = 'dokumentasi/postman/BE-SIMPEG-RSKALISAT.postman_collection.json';
$json = json_decode(file_get_contents($filePostman), true);

$getKontakDaruratRequest = [
    'name' => 'Get Keluarga Kontak Darurat',
    'request' => [
        'method' => 'GET',
        'header' => [['key' => 'Accept', 'value' => 'application/json']],
        'url' => ['raw' => '{{base_url}}/api/keluarga/kontak-darurat', 'host' => ['{{base_url}}'], 'path' => ['api', 'keluarga', 'kontak-darurat']],
        'auth' => ['type' => 'bearer', 'bearer' => [['key' => 'token', 'value' => '{{token_pegawai}}', 'type' => 'string']]]
    ]
];

$postKontakDaruratRequest = [
    'name' => 'Create Keluarga Kontak Darurat',
    'request' => [
        'method' => 'POST',
        'header' => [['key' => 'Accept', 'value' => 'application/json']],
        'body' => [
            'mode' => 'urlencoded',
            'urlencoded' => [
                ['key' => 'nama_kontak', 'value' => 'Kontak Test', 'type' => 'text'],
                ['key' => 'hubungan_keluarga', 'value' => 'Saudara', 'type' => 'text'],
                ['key' => 'nomor_hp', 'value' => '08123456789', 'type' => 'text'],
                ['key' => 'alamat', 'value' => 'Jl. Kenangan No. 2', 'type' => 'text']
            ]
        ],
        'url' => ['raw' => '{{base_url}}/api/keluarga/kontak-darurat', 'host' => ['{{base_url}}'], 'path' => ['api', 'keluarga', 'kontak-darurat']],
        'auth' => ['type' => 'bearer', 'bearer' => [['key' => 'token', 'value' => '{{token_pegawai}}', 'type' => 'string']]]
    ]
];

$patchKontakDaruratRequest = [
    'name' => 'Update Keluarga Kontak Darurat',
    'request' => [
        'method' => 'PATCH',
        'header' => [['key' => 'Accept', 'value' => 'application/json']],
        'body' => [
            'mode' => 'urlencoded',
            'urlencoded' => [
                ['key' => 'nama_kontak', 'value' => 'Kontak Edit', 'type' => 'text']
            ]
        ],
        'url' => ['raw' => '{{base_url}}/api/keluarga/kontak-darurat/1', 'host' => ['{{base_url}}'], 'path' => ['api', 'keluarga', 'kontak-darurat', '1']],
        'auth' => ['type' => 'bearer', 'bearer' => [['key' => 'token', 'value' => '{{token_pegawai}}', 'type' => 'string']]]
    ]
];

$deleteKontakDaruratRequest = [
    'name' => 'Delete Keluarga Kontak Darurat',
    'request' => [
        'method' => 'DELETE',
        'header' => [['key' => 'Accept', 'value' => 'application/json']],
        'url' => ['raw' => '{{base_url}}/api/keluarga/kontak-darurat/1', 'host' => ['{{base_url}}'], 'path' => ['api', 'keluarga', 'kontak-darurat', '1']],
        'auth' => ['type' => 'bearer', 'bearer' => [['key' => 'token', 'value' => '{{token_pegawai}}', 'type' => 'string']]]
    ]
];

foreach ($json['item'] as &$folder) {
    if ($folder['name'] === '02. Semua Role') {
        foreach ($folder['item'] as &$subfolder) {
            if ($subfolder['name'] === 'Keluarga') {
                $subfolder['item'][] = $getKontakDaruratRequest;
                $subfolder['item'][] = $postKontakDaruratRequest;
                $subfolder['item'][] = $patchKontakDaruratRequest;
                $subfolder['item'][] = $deleteKontakDaruratRequest;
            }
        }
    }
}

file_put_contents($filePostman, json_encode($json, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

echo "Routes Kontak Darurat updated in docs and postman.\n";
