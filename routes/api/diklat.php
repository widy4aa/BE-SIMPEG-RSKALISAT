<?php

use App\Http\Controllers\Api\DiklatController;
use App\Http\Controllers\Api\LaporanController;
use App\Http\Middleware\JwtAuthMiddleware;
use App\Http\Middleware\RoleMiddleware;
use Illuminate\Support\Facades\Route;

Route::middleware([
    JwtAuthMiddleware::class,
    RoleMiddleware::class.':admin,pegawai,hrd,direktur',
])->group(function () {
    Route::get('/diklat', [DiklatController::class, 'index']);
});

Route::middleware([
    JwtAuthMiddleware::class,
    RoleMiddleware::class.':pegawai,hrd,direktur',
])->group(function () {
    Route::post('/diklat', [DiklatController::class, 'store']);
    Route::patch('/diklat/{id}', [DiklatController::class, 'update']);
    Route::delete('/diklat/{id}', [DiklatController::class, 'destroy']);
    Route::post('/diklat/{id}/upload-laporan', [DiklatController::class, 'uploadLaporan']);
});

Route::middleware([
    JwtAuthMiddleware::class,
    RoleMiddleware::class.':hrd,direktur',
])->group(function () {
    Route::get('/diklat/all', [DiklatController::class, 'all']);
});

Route::middleware([
    JwtAuthMiddleware::class,
    RoleMiddleware::class.':hrd',
])->group(function () {
    Route::post('/hrd/diklat', [DiklatController::class, 'storeMaster']);
    Route::put('/hrd/diklat/{id}', [DiklatController::class, 'updateMaster']);
    Route::get('/hrd/diklat/{id}/peserta', [DiklatController::class, 'peserta']);
    Route::post('/hrd/diklat/{id}/peserta', [DiklatController::class, 'syncPeserta']);
    Route::get('/hrd/diklat/status/layak', [DiklatController::class, 'menungguKelayakan']);
    Route::get('/hrd/diklat/status/validasi', [DiklatController::class, 'menungguValidasi']);
    Route::patch('/hrd/diklat/{id}/status/layak', [DiklatController::class, 'updateStatusKelayakan']);
    Route::patch('/hrd/diklat/{id}/status/validasi', [DiklatController::class, 'updateStatusValidasi']);

    Route::get('/generate/laporan-diklat', [LaporanController::class, 'laporanDiklat']);
});
