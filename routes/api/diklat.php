<?php

use App\Http\Controllers\Api\Diklat\DiklatIndexController;
use App\Http\Controllers\Api\Diklat\DiklatPegawaiController;
use App\Http\Controllers\Api\Diklat\HrdDiklatController;
use App\Http\Controllers\Api\LaporanController;
use App\Http\Middleware\JwtAuthMiddleware;
use App\Http\Middleware\RoleMiddleware;
use Illuminate\Support\Facades\Route;

Route::middleware([
    JwtAuthMiddleware::class,
    RoleMiddleware::class.':admin,pegawai,hrd,direktur',
])->group(function () {
    Route::get('/diklat', [DiklatIndexController::class, 'index']);
});

Route::middleware([
    JwtAuthMiddleware::class,
    RoleMiddleware::class.':pegawai,hrd,direktur',
])->group(function () {
    Route::post('/diklat', [DiklatPegawaiController::class, 'store']);
    Route::patch('/diklat/{id}', [DiklatPegawaiController::class, 'update']);
    Route::delete('/diklat/{id}', [DiklatPegawaiController::class, 'destroy']);
    Route::post('/diklat/{id}/upload-laporan', [DiklatPegawaiController::class, 'uploadLaporan']);
});

Route::middleware([
    JwtAuthMiddleware::class,
    RoleMiddleware::class.':hrd,direktur',
])->group(function () {
    Route::get('/diklat/all', [DiklatIndexController::class, 'all']);
});

Route::middleware([
    JwtAuthMiddleware::class,
    RoleMiddleware::class.':hrd',
])->group(function () {
    Route::post('/hrd/diklat', [HrdDiklatController::class, 'storeMaster']);
    Route::put('/hrd/diklat/{id}', [HrdDiklatController::class, 'updateMaster']);
    Route::get('/hrd/diklat/{id}/peserta', [HrdDiklatController::class, 'peserta']);
    Route::post('/hrd/diklat/{id}/peserta', [HrdDiklatController::class, 'syncPeserta']);
    Route::get('/hrd/diklat/status/layak', [HrdDiklatController::class, 'menungguKelayakan']);
    Route::get('/hrd/diklat/status/validasi', [HrdDiklatController::class, 'menungguValidasi']);
    Route::patch('/hrd/diklat/{id}/status/layak', [HrdDiklatController::class, 'updateStatusKelayakan']);
    Route::patch('/hrd/diklat/{id}/status/validasi', [HrdDiklatController::class, 'updateStatusValidasi']);

    Route::get('/generate/laporan-diklat', [LaporanController::class, 'laporanDiklat']);
});
