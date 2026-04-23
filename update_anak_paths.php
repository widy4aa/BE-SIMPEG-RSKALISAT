<?php

$fileApi = 'dokumentasi/dokumentasi_api.md';
$docApi = file_get_contents($fileApi);
$docApi = str_replace('/api/anak', '/api/keluarga/anak', $docApi);
file_put_contents($fileApi, $docApi);

$filePostman = 'dokumentasi/postman/BE-SIMPEG-RSKALISAT.postman_collection.json';
$json = json_decode(file_get_contents($filePostman), true);

foreach ($json['item'] as &$folder) {
    if ($folder['name'] === '02. Semua Role') {
        foreach ($folder['item'] as &$subfolder) {
            if ($subfolder['name'] === 'Keluarga') {
                foreach ($subfolder['item'] as &$req) {
                    if (str_contains($req['name'], 'Keluarga Anak')) {
                        if (isset($req['request']['url']['raw'])) {
                            $req['request']['url']['raw'] = str_replace('/api/anak', '/api/keluarga/anak', $req['request']['url']['raw']);
                        }
                        if (isset($req['request']['url']['path'])) {
                            $newPath = [];
                            foreach ($req['request']['url']['path'] as $p) {
                                if ($p === 'anak') {
                                    $newPath[] = 'keluarga';
                                    $newPath[] = 'anak';
                                } else {
                                    $newPath[] = $p;
                                }
                            }
                            $req['request']['url']['path'] = $newPath;
                        }
                    }
                }
            }
        }
    }
}

file_put_contents($filePostman, json_encode($json, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

echo "Routes updated in docs and postman.\n";
