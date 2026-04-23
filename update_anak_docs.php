<?php

$docApi = file_get_contents('dokumentasi/dokumentasi_api.md');
$docApi = str_replace('/api/data-keluarga', '/api/keluarga', $docApi);

$appendApi = <<<EOT

### GET /api/anak
- **Method:** `GET`
- **Route:** `/api/anak`
- **Response:** List data anak.

### POST /api/anak
- **Method:** `POST`
- **Route:** `/api/anak`
- **Body:** `multipart/form-data` (nama_lengkap, tanggal_lahir, jenis_kelamin, status_anak, dll + akta_kelahiran_file)

### PATCH /api/anak/{id}
- **Method:** `PATCH` atau `POST` (untuk form-data update)
- **Route:** `/api/anak/{id}`

### DELETE /api/anak/{id}
- **Method:** `DELETE`
- **Route:** `/api/anak/{id}`

EOT;

$docApi = str_replace('## Daftar Request di Collection', $appendApi . "\n## Daftar Request di Collection", $docApi);
file_put_contents('dokumentasi/dokumentasi_api.md', $docApi);


$file = 'dokumentasi/postman/BE-SIMPEG-RSKALISAT.postman_collection.json';
$json = json_decode(file_get_contents($file), true);

$getAnakRequest = [
    'name' => 'Get Keluarga Anak',
    'request' => [
        'method' => 'GET',
        'header' => [['key' => 'Accept', 'value' => 'application/json']],
        'url' => ['raw' => '{{base_url}}/api/anak', 'host' => ['{{base_url}}'], 'path' => ['api', 'anak']],
        'auth' => ['type' => 'bearer', 'bearer' => [['key' => 'token', 'value' => '{{token_pegawai}}', 'type' => 'string']]]
    ]
];

$postAnakRequest = [
    'name' => 'Create Keluarga Anak',
    'request' => [
        'method' => 'POST',
        'header' => [['key' => 'Accept', 'value' => 'application/json']],
        'body' => [
            'mode' => 'formdata',
            'formdata' => [
                ['key' => 'nama_lengkap', 'value' => 'Test Anak', 'type' => 'text'],
                ['key' => 'tanggal_lahir', 'value' => '2015-01-01', 'type' => 'text'],
                ['key' => 'status_anak', 'value' => 'Kandung', 'type' => 'text'],
                ['key' => 'akta_kelahiran_file', 'type' => 'file', 'src' => '']
            ]
        ],
        'url' => ['raw' => '{{base_url}}/api/anak', 'host' => ['{{base_url}}'], 'path' => ['api', 'anak']],
        'auth' => ['type' => 'bearer', 'bearer' => [['key' => 'token', 'value' => '{{token_pegawai}}', 'type' => 'string']]]
    ]
];

$patchAnakRequest = [
    'name' => 'Update Keluarga Anak',
    'request' => [
        'method' => 'PATCH',
        'header' => [['key' => 'Accept', 'value' => 'application/json']],
        'body' => [
            'mode' => 'urlencoded',
            'urlencoded' => [
                ['key' => 'nama_lengkap', 'value' => 'Test Anak Edit', 'type' => 'text']
            ]
        ],
        'url' => ['raw' => '{{base_url}}/api/anak/1', 'host' => ['{{base_url}}'], 'path' => ['api', 'anak', '1']],
        'auth' => ['type' => 'bearer', 'bearer' => [['key' => 'token', 'value' => '{{token_pegawai}}', 'type' => 'string']]]
    ]
];

$deleteAnakRequest = [
    'name' => 'Delete Keluarga Anak',
    'request' => [
        'method' => 'DELETE',
        'header' => [['key' => 'Accept', 'value' => 'application/json']],
        'url' => ['raw' => '{{base_url}}/api/anak/1', 'host' => ['{{base_url}}'], 'path' => ['api', 'anak', '1']],
        'auth' => ['type' => 'bearer', 'bearer' => [['key' => 'token', 'value' => '{{token_pegawai}}', 'type' => 'string']]]
    ]
];

// Update endpoint in postman collection
foreach ($json['item'] as &$folder) {
    if ($folder['name'] === '02. Semua Role') {
        foreach ($folder['item'] as &$subfolder) {
            if ($subfolder['name'] === 'Keluarga') {
                // fix data keluarga url
                foreach ($subfolder['item'] as &$req) {
                    if ($req['name'] === 'Data Keluarga (Summary)') {
                        $req['request']['url']['raw'] = '{{base_url}}/api/keluarga';
                        $req['request']['url']['path'] = ['api', 'keluarga'];
                    }
                }
                $subfolder['item'][] = $getAnakRequest;
                $subfolder['item'][] = $postAnakRequest;
                $subfolder['item'][] = $patchAnakRequest;
                $subfolder['item'][] = $deleteAnakRequest;
            }
        }
    }
}

file_put_contents($file, json_encode($json, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

echo "Done\n";

