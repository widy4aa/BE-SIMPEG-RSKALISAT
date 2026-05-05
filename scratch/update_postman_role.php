<?php

$file = 'C:\Users\widy4aa\Documents\Capstone\BE-SIMPEG-RSKALISAT\dokumentasi\postman\BE-SIMPEG-RSKALISAT.postman_collection.json';
$json = json_decode(file_get_contents($file), true);

$newItem = [
    'name' => 'Change Role Pegawai (Admin)',
    'request' => [
        'method' => 'PATCH',
        'header' => [
            [
                'key' => 'Authorization',
                'value' => 'Bearer {{token_admin}}',
                'type' => 'text'
            ]
        ],
        'body' => [
            'mode' => 'raw',
            'raw' => "{\n    \"role\": \"hrd\"\n}",
            'options' => [
                'raw' => [
                    'language' => 'json'
                ]
            ]
        ],
        'url' => [
            'raw' => '{{base_url}}/api/pegawai/1/change-role',
            'host' => [
                '{{base_url}}'
            ],
            'path' => [
                'api',
                'pegawai',
                '1',
                'change-role'
            ]
        ]
    ],
    'response' => []
];

// Determine where to insert it. "Create Pegawai (Admin)" is at the root level.
$newItems = [];
foreach ($json['item'] as $item) {
    $newItems[] = $item;
    if (isset($item['name']) && $item['name'] === 'Create Pegawai (Admin)') {
        $newItems[] = $newItem;
    }
}

$json['item'] = $newItems;

file_put_contents($file, json_encode($json, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
echo "Success\n";
