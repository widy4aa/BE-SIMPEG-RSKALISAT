<?php

$file = 'C:\Users\widy4aa\Documents\Capstone\BE-SIMPEG-RSKALISAT\dokumentasi\postman\BE-SIMPEG-RSKALISAT.postman_collection.json';
$json = json_decode(file_get_contents($file), true);

$newItem = [
    'name' => 'Create Pegawai (Admin)',
    'request' => [
        'method' => 'POST',
        'header' => [
            [
                'key' => 'Authorization',
                'value' => 'Bearer {{token_admin}}',
                'type' => 'text'
            ]
        ],
        'body' => [
            'mode' => 'raw',
            'raw' => "{\n    \"nik\": \"3509191234567890\",\n    \"nama\": \"Ahmad Subarjo\",\n    \"password\": \"password123\"\n}",
            'options' => [
                'raw' => [
                    'language' => 'json'
                ]
            ]
        ],
        'url' => [
            'raw' => '{{base_url}}/api/pegawai',
            'host' => [
                '{{base_url}}'
            ],
            'path' => [
                'api',
                'pegawai'
            ]
        ]
    ],
    'response' => []
];

// Determine where to insert it. "Get Pegawai" is at the root level, not in a folder, based on my previous grep.
$inserted = false;
foreach($json['item'] as &$folder) {
    if (isset($folder['name']) && $folder['name'] === 'Master Data') {
        // Let's just put it under the main array instead of folder
    }
}

// Just push it to the main items array right after "Get Pegawai" if possible
$newItems = [];
foreach ($json['item'] as $item) {
    $newItems[] = $item;
    if (isset($item['name']) && $item['name'] === 'Get Pegawai') {
        $newItems[] = $newItem;
        $inserted = true;
    }
}

if (!$inserted) {
    $newItems[] = $newItem;
}

$json['item'] = $newItems;

file_put_contents($file, json_encode($json, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
echo "Success\n";
