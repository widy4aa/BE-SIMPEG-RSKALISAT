<?php

$file = 'dokumentasi/postman/BE-SIMPEG-RSKALISAT.postman_collection.json';
$json = json_decode(file_get_contents($file), true);

$dataKeluargaRequest = [
    'name' => 'Data Keluarga (Summary)',
    'request' => [
        'method' => 'GET',
        'header' => [
            ['key' => 'Accept', 'value' => 'application/json']
        ],
        'url' => [
            'raw' => '{{base_url}}/api/data-keluarga',
            'host' => ['{{base_url}}'],
            'path' => ['api', 'data-keluarga']
        ],
        'auth' => [
            'type' => 'bearer',
            'bearer' => [['key' => 'token', 'value' => '{{token_pegawai}}', 'type' => 'string']]
        ]
    ]
];

$getPasanganRequest = [
    'name' => 'Get Keluarga Pasangan',
    'request' => [
        'method' => 'GET',
        'header' => [
            ['key' => 'Accept', 'value' => 'application/json']
        ],
        'url' => [
            'raw' => '{{base_url}}/api/keluarga/pasangan',
            'host' => ['{{base_url}}'],
            'path' => ['api', 'keluarga', 'pasangan']
        ],
        'auth' => [
            'type' => 'bearer',
            'bearer' => [['key' => 'token', 'value' => '{{token_pegawai}}', 'type' => 'string']]
        ]
    ]
];

$postPasanganRequest = [
    'name' => 'Create Keluarga Pasangan',
    'request' => [
        'method' => 'POST',
        'header' => [
            ['key' => 'Accept', 'value' => 'application/json']
        ],
        'body' => [
            'mode' => 'formdata',
            'formdata' => [
                ['key' => 'nama_lengkap', 'value' => 'Test Pasangan', 'type' => 'text'],
                ['key' => 'tanggal_lahir', 'value' => '1995-01-01', 'type' => 'text'],
                ['key' => 'buku_nikah_file', 'type' => 'file', 'src' => '']
            ]
        ],
        'url' => [
            'raw' => '{{base_url}}/api/keluarga/pasangan',
            'host' => ['{{base_url}}'],
            'path' => ['api', 'keluarga', 'pasangan']
        ],
        'auth' => [
            'type' => 'bearer',
            'bearer' => [['key' => 'token', 'value' => '{{token_pegawai}}', 'type' => 'string']]
        ]
    ]
];

$patchPasanganRequest = [
    'name' => 'Update Keluarga Pasangan',
    'request' => [
        'method' => 'PATCH',
        'header' => [
            ['key' => 'Accept', 'value' => 'application/json']
        ],
        'body' => [
            'mode' => 'urlencoded',
            'urlencoded' => [
                ['key' => 'nama_lengkap', 'value' => 'Test Pasangan Edit', 'type' => 'text'],
                ['key' => 'tanggal_lahir', 'value' => '1995-02-02', 'type' => 'text']
            ]
        ],
        'url' => [
            'raw' => '{{base_url}}/api/keluarga/pasangan/1',
            'host' => ['{{base_url}}'],
            'path' => ['api', 'keluarga', 'pasangan', '1']
        ],
        'auth' => [
            'type' => 'bearer',
            'bearer' => [['key' => 'token', 'value' => '{{token_pegawai}}', 'type' => 'string']]
        ]
    ]
];

$deletePasanganRequest = [
    'name' => 'Delete Keluarga Pasangan',
    'request' => [
        'method' => 'DELETE',
        'header' => [
            ['key' => 'Accept', 'value' => 'application/json']
        ],
        'url' => [
            'raw' => '{{base_url}}/api/keluarga/pasangan/1',
            'host' => ['{{base_url}}'],
            'path' => ['api', 'keluarga', 'pasangan', '1']
        ],
        'auth' => [
            'type' => 'bearer',
            'bearer' => [['key' => 'token', 'value' => '{{token_pegawai}}', 'type' => 'string']]
        ]
    ]
];


// Find "02. Semua Role"
foreach ($json['item'] as &$folder) {
    if ($folder['name'] === '02. Semua Role') {
        
        // Find or create "Riwayat Karir" or "Keluarga" subfolder
        $keluargaFolderFound = false;
        if (!isset($folder['item'])) $folder['item'] = [];
        
        foreach ($folder['item'] as &$subfolder) {
            if ($subfolder['name'] === 'Keluarga') {
                $subfolder['item'][] = $dataKeluargaRequest;
                $subfolder['item'][] = $getPasanganRequest;
                $subfolder['item'][] = $postPasanganRequest;
                $subfolder['item'][] = $patchPasanganRequest;
                $subfolder['item'][] = $deletePasanganRequest;
                $keluargaFolderFound = true;
                break;
            }
        }
        
        if (!$keluargaFolderFound) {
            $folder['item'][] = [
                'name' => 'Keluarga',
                'item' => [
                    $dataKeluargaRequest,
                    $getPasanganRequest,
                    $postPasanganRequest,
                    $patchPasanganRequest,
                    $deletePasanganRequest
                ]
            ];
        }
        break;
    }
}

file_put_contents($file, json_encode($json, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

echo "Postman updated!";
