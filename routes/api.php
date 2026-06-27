<?php

$routeFiles = [
    'auth.php',
    'profile.php',
    'dashboard.php',
    'diklat.php',
    'keluarga.php',
    'riwayat-karir.php',
    'pegawai.php',
    'hrd.php',
    'master-data.php',
    'notifications.php',
    'health.php',
];

foreach ($routeFiles as $routeFile) {
    require __DIR__.'/api/'.$routeFile;
}
