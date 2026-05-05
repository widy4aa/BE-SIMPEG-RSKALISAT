<?php
$file = 'C:\Users\widy4aa\Documents\Capstone\BE-SIMPEG-RSKALISAT\dokumentasi\postman\BE-SIMPEG-RSKALISAT.postman_collection.json';
$json = json_decode(file_get_contents($file), true);

$itemsFlat = [];

function flattenItems($item, &$flatArray) {
    if (isset($item['item'])) {
        foreach ($item['item'] as $child) {
            flattenItems($child, $flatArray);
        }
    } else {
        $flatArray[$item['name']] = $item;
    }
}

foreach($json['item'] as $i) {
    flattenItems($i, $itemsFlat);
}

// Function to find request by name
function getRequest($name, &$itemsFlat) {
    if (isset($itemsFlat[$name])) {
        return $itemsFlat[$name];
    }
    return null;
}

// Build the new structure
$newItems = [];

// 01. Umum & Auth
$newItems[] = [
    'name' => '01. Umum & Auth',
    'item' => array_values(array_filter([
        getRequest('Health Check', $itemsFlat),
        getRequest('Login', $itemsFlat),
        getRequest('Cek Role', $itemsFlat)
    ]))
];

// 02. Dashboard
$newItems[] = [
    'name' => '02. Dashboard',
    'item' => array_values(array_filter([
        getRequest('Dashboard', $itemsFlat),
        getRequest('Dashboard (Admin)', $itemsFlat)
    ]))
];

// 03. Profile & Data Diri
$newItems[] = [
    'name' => '03. Profile & CV',
    'item' => array_values(array_filter([
        getRequest('Get Profile', $itemsFlat),
        getRequest('Patch Profile', $itemsFlat),
        getRequest('Upload Foto Profile', $itemsFlat),
        getRequest('Upload Foto Profile (Alias)', $itemsFlat),
        getRequest('Upload KTP', $itemsFlat),
        getRequest('Generate CV', $itemsFlat)
    ]))
];

// 04. Keluarga
$keluargaItem = null;
foreach($json['item'] as $i) {
    if (isset($i['item']) && $i['name'] === '02. Semua Role') {
        foreach($i['item'] as $child) {
            if (isset($child['item']) && $child['name'] === 'Keluarga') {
                $keluargaItem = $child;
                $keluargaItem['name'] = '04. Keluarga';
                break;
            }
        }
    }
}
if ($keluargaItem) {
    $newItems[] = $keluargaItem;
}

// 05. Master Data
$masterDataItem = null;
foreach($json['item'] as $i) {
    if (isset($i['item']) && $i['name'] === '02. Semua Role') {
        foreach($i['item'] as $child) {
            if (isset($child['item']) && $child['name'] === 'Master Data') {
                $masterDataItem = $child;
                $masterDataItem['name'] = '05. Master Data';
                break;
            }
        }
    }
}
if ($masterDataItem) {
    $newItems[] = $masterDataItem;
}

// 06. Notifikasi
$notifikasiItem = null;
foreach($json['item'] as $i) {
    if (isset($i['name']) && $i['name'] === '03. Notifikasi') {
        $notifikasiItem = $i;
        $notifikasiItem['name'] = '06. Notifikasi';
    }
}
if ($notifikasiItem) {
    $newItems[] = $notifikasiItem;
}

// 07. Riwayat Karir
$riwayatKarirItem = null;
foreach($json['item'] as $i) {
    if (isset($i['name']) && $i['name'] === '05. Riwayat Karir') {
        $riwayatKarirItem = $i;
        $riwayatKarirItem['name'] = '07. Riwayat Karir';
    }
}
if ($riwayatKarirItem) {
    $newItems[] = $riwayatKarirItem;
}

// 08. Diklat
$diklatPegawaiReqs = array_values(array_filter([
    getRequest('Get Diklat', $itemsFlat),
    getRequest('Create Diklat (Pegawai)', $itemsFlat),
    getRequest('Update Diklat (Pegawai)', $itemsFlat),
    getRequest('Delete Diklat (Pegawai)', $itemsFlat)
]));

$diklatHrdReqs = [];
$templateReq = getRequest('Get Diklat', $itemsFlat);
if ($templateReq) {
    $hrdReq1 = $templateReq; $hrdReq1['name'] = 'GET Semua Diklat';
    $hrdReq2 = $templateReq; $hrdReq2['name'] = 'POST Buat Diklat'; $hrdReq2['request']['method'] = 'POST';
    $hrdReq3 = $templateReq; $hrdReq3['name'] = 'PATCH Update Diklat'; $hrdReq3['request']['method'] = 'PATCH';
    $hrdReq4 = $templateReq; $hrdReq4['name'] = 'DELETE Diklat'; $hrdReq4['request']['method'] = 'DELETE';
    $diklatHrdReqs = [$hrdReq1, $hrdReq2, $hrdReq3, $hrdReq4];
}

$newItems[] = [
    'name' => '08. Diklat',
    'item' => [
        [
            'name' => 'Pegawai',
            'item' => $diklatPegawaiReqs
        ],
        [
            'name' => 'HRD',
            'item' => $diklatHrdReqs
        ]
    ]
];

// 09. Pegawai Management (Admin)
$newItems[] = [
    'name' => '09. Pegawai Management (Admin)',
    'item' => array_values(array_filter([
        getRequest('Get Pegawai', $itemsFlat),
        getRequest('Create Pegawai (Admin)', $itemsFlat),
        getRequest('Change Role Pegawai (Admin)', $itemsFlat)
    ]))
];

// 10. Admin Change Request
$changeRequestItem = null;
foreach($json['item'] as $i) {
    if (isset($i['name']) && $i['name'] === '04. Admin Change Request') {
        $changeRequestItem = $i;
        $changeRequestItem['name'] = '10. Admin Change Request';
    }
}
if ($changeRequestItem) {
    $newItems[] = $changeRequestItem;
}

$json['item'] = $newItems;

file_put_contents($file, json_encode($json, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
echo "Successfully reorganized Postman Collection!\n";
