<?php

$file = 'dokumentasi/postman/BE-SIMPEG-RSKALISAT.postman_collection.json';
$json = json_decode(file_get_contents($file), true);

// Add GET Notifications
$hasGet = false;
foreach ($json['item'] as &$folder) {
    if ($folder['name'] === '03. Notifikasi') {
        foreach ($folder['item'] as $req) {
            if ($req['name'] === 'Get Notifikasi') {
                $hasGet = true;
                break;
            }
        }
        if (!$hasGet) {
            array_unshift($folder['item'], [
                "name" => "Get Notifikasi",
                "request" => [
                    "auth" => ["type" => "bearer", "bearer" => [["key" => "token", "value" => "{{token}}", "type" => "string"]]],
                    "method" => "GET",
                    "header" => [],
                    "url" => ["raw" => "{{base_url}}/api/notifications", "host" => ["{{base_url}}"], "path" => ["api", "notifications"]]
                ],
                "response" => []
            ]);
        }
    }
}
unset($folder);

// Rename Riwayat Karir folder to 05. Riwayat Karir if it doesn't have 05
foreach ($json['item'] as &$folder) {
    if ($folder['name'] === 'Riwayat Karir' || $folder['name'] === '05. Riwayat Karir') {
        $folder['name'] = '05. Riwayat Karir';
        
        // Let's reorganize its content
        // Currently it might have flat requests like "GET Riwayat Pangkat", "POST Riwayat SIP", etc.
        // We want subfolders: Pendidikan, Jabatan, Pangkat, SIP, STR, Penugasan Klinis
    }
}
unset($folder);

file_put_contents($file, json_encode($json, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
echo "Success";
