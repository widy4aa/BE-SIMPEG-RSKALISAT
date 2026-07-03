<?php

use App\Http\Controllers\Api\Diklat\Managed\DiklatController as ManagedDiklatController;
use App\Http\Controllers\Api\Diklat\Managed\DiklatIndexController as ManagedDiklatIndexController;
use App\Http\Controllers\Api\Diklat\Self\DiklatController as SelfDiklatController;
use App\Http\Controllers\Api\Diklat\Self\DiklatIndexController as SelfDiklatIndexController;
use App\Http\Controllers\Api\Laporan\LaporanController;
use App\Http\Middleware\JwtAuthMiddleware;
use App\Http\Middleware\RoleMiddleware;
use Illuminate\Support\Facades\Route;

// Semua role: view diklat (logika berbeda per role ditangani di controller)
Route::middleware([
    JwtAuthMiddleware::class,
    RoleMiddleware::class.':admin,pegawai,hrd,direktur',
])->group(function () {
    Route::get('/diklat', [ManagedDiklatIndexController::class, 'index']);
});


// Pegawai/HRD/Direktur: mutate own diklat records
Route::middleware([
    JwtAuthMiddleware::class,
    RoleMiddleware::class.':pegawai,hrd,direktur',
])->group(function () {
    Route::post('/diklat', [SelfDiklatController::class, 'store']);
    Route::patch('/diklat/{id}', [SelfDiklatController::class, 'update']);
    Route::delete('/diklat/{id}', [SelfDiklatController::class, 'destroy']);
    Route::post('/diklat/{id}/upload-laporan', [SelfDiklatController::class, 'uploadLaporan']);
});

// HRD/Direktur: view all diklat
Route::middleware([
    JwtAuthMiddleware::class,
    RoleMiddleware::class.':hrd,direktur',
])->group(function () {
    Route::get('/diklat/all', [ManagedDiklatIndexController::class, 'all']);
});

// HRD only: manage master diklat + peserta + status
Route::middleware([
    JwtAuthMiddleware::class,
    RoleMiddleware::class.':hrd',
])->group(function () {
    Route::post('/hrd/diklat', [ManagedDiklatController::class, 'storeMaster']);
    Route::put('/hrd/diklat/{id}', [ManagedDiklatController::class, 'updateMaster']);
    Route::delete('/hrd/diklat/{id}', [ManagedDiklatController::class, 'destroyMaster']);
    Route::get('/hrd/diklat/{id}/peserta', [ManagedDiklatController::class, 'peserta']);
    Route::post('/hrd/diklat/{id}/peserta', [ManagedDiklatController::class, 'syncPeserta']);
    Route::get('/hrd/diklat/status/layak', [ManagedDiklatController::class, 'menungguKelayakan']);
    Route::get('/hrd/diklat/status/validasi', [ManagedDiklatController::class, 'menungguValidasi']);
    Route::patch('/hrd/diklat/{id}/status/layak', [ManagedDiklatController::class, 'updateStatusKelayakan']);
    Route::patch('/hrd/diklat/{id}/status/validasi', [ManagedDiklatController::class, 'updateStatusValidasi']);
    Route::post('/hrd/diklat/{diklatId}/pegawai/{pegawaiId}/reminder-upload-laporan', [ManagedDiklatController::class, 'remindUploadLaporan']);

    Route::get('/generate/laporan-diklat', [LaporanController::class, 'laporanDiklat']);
});
