<?php
$file = 'C:\Users\widy4aa\Documents\Capstone\BE-SIMPEG-RSKALISAT\dokumentasi\postman\BE-SIMPEG-RSKALISAT.postman_collection.json';
$json = json_decode(file_get_contents($file), true);

function printItem($item, $indent = "") {
    if (isset($item['item'])) {
        echo $indent . "[FOLDER] " . $item['name'] . "\n";
        foreach ($item['item'] as $child) {
            printItem($child, $indent . "  ");
        }
    } else {
        echo $indent . "[REQUEST] " . $item['name'] . "\n";
    }
}

foreach($json['item'] as $i) {
    printItem($i);
}
