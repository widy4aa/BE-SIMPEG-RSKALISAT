<?php
$file = 'c:\Users\widy4aa\Documents\Capstone\BE-SIMPEG-RSKALISAT\dokumentasi\postman\BE-SIMPEG-RSKALISAT.postman_collection.json';
$content = file_get_contents($file);
$json = json_decode($content, true);

// recursively search for item arrays and body
function addWaktu(&$items) {
    if (!is_array($items)) return;
    foreach ($items as &$item) {
        if (isset($item['item'])) {
            addWaktu($item['item']);
        }
        if (isset($item['request']['body']['formdata'])) {
            $hasTanggalSelesai = false;
            $hasWaktu = false;
            foreach ($item['request']['body']['formdata'] as $field) {
                if ($field['key'] === 'tanggal_selesai') $hasTanggalSelesai = true;
                if ($field['key'] === 'waktu') $hasWaktu = true;
            }
            if ($hasTanggalSelesai && !$hasWaktu) {
                // insert waktu after tanggal_selesai
                $newFormData = [];
                foreach ($item['request']['body']['formdata'] as $field) {
                    $newFormData[] = $field;
                    if ($field['key'] === 'tanggal_selesai') {
                        $newFormData[] = [
                            'key' => 'waktu',
                            'value' => '08:00:00',
                            'type' => 'text'
                        ];
                    }
                }
                $item['request']['body']['formdata'] = $newFormData;
                echo "Added waktu to form data in " . $item['name'] . "\n";
            }
        }
        if (isset($item['request']['body']['raw'])) {
            $raw = $item['request']['body']['raw'];
            $decoded = json_decode($raw, true);
            if ($decoded && isset($decoded['tanggal_selesai']) && !isset($decoded['waktu'])) {
                // To insert after tanggal_selesai
                $newDecoded = [];
                foreach ($decoded as $k => $v) {
                    $newDecoded[$k] = $v;
                    if ($k === 'tanggal_selesai') {
                        $newDecoded['waktu'] = '08:00:00';
                    }
                }
                $item['request']['body']['raw'] = json_encode($newDecoded, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
                echo "Added waktu to raw json in " . $item['name'] . "\n";
            }
        }
    }
}

if ($json) {
    addWaktu($json['item']);
    file_put_contents($file, json_encode($json, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
    echo "Done Postman Update.\n";
} else {
    echo "Failed to decode JSON.\n";
}
