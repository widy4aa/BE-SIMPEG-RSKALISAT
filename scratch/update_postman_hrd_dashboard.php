<?php

$file = 'C:\Users\widy4aa\Documents\Capstone\BE-SIMPEG-RSKALISAT\dokumentasi\postman\BE-SIMPEG-RSKALISAT.postman_collection.json';
$json = json_decode(file_get_contents($file), true);

$newItem = [
    'name' => 'Dashboard (HRD)',
    'request' => [
        'method' => 'GET',
        'header' => [
            [
                'key' => 'Authorization',
                'value' => 'Bearer {{token_hrd}}',
                'type' => 'text'
            ]
        ],
        'url' => [
            'raw' => '{{base_url}}/api/dashboard',
            'host' => [
                '{{base_url}}'
            ],
            'path' => [
                'api',
                'dashboard'
            ]
        ]
    ],
    'response' => []
];

foreach ($json['item'] as &$folder) {
    if (isset($folder['name']) && $folder['name'] === '02. Dashboard') {
        $folder['item'][] = $newItem;
    }
}

file_put_contents($file, json_encode($json, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
echo "Success\n";
