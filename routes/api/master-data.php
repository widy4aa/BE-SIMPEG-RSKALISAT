<?php

use App\Http\Controllers\Api\MasterDataController;
use App\Http\Middleware\JwtAuthMiddleware;
use App\Http\Middleware\RoleMiddleware;
use Illuminate\Support\Facades\Route;

Route::middleware([
    JwtAuthMiddleware::class,
])->prefix('form')->group(function () {
    Route::get('/kategori-diklat', [MasterDataController::class, 'kategoriDiklat']);
    Route::get('/tipe-diklat', [MasterDataController::class, 'tipeDiklat']);
    Route::get('/jenis-pegawai', [MasterDataController::class, 'jenisPegawai']);
    Route::get('/unit-kerja', [MasterDataController::class, 'unitKerja']);
    Route::get('/jenis-biaya', [MasterDataController::class, 'jenisBiaya']);
    Route::get('/golongan-ruang', [MasterDataController::class, 'golonganRuang']);
    Route::get('/profesi', [MasterDataController::class, 'profesi']);
    Route::get('/jenis-sip', [MasterDataController::class, 'jenisSip']);
});

Route::middleware([
    JwtAuthMiddleware::class,
    RoleMiddleware::class.':hrd',
])->prefix('form')->group(function () {
    Route::post('/kategori-diklat', [MasterDataController::class, 'storeKategoriDiklat']);
    Route::patch('/kategori-diklat/{id}', [MasterDataController::class, 'updateKategoriDiklat']);
    Route::delete('/kategori-diklat/{id}', [MasterDataController::class, 'destroyKategoriDiklat']);

    Route::post('/tipe-diklat', [MasterDataController::class, 'storeTipeDiklat']);
    Route::patch('/tipe-diklat/{id}', [MasterDataController::class, 'updateTipeDiklat']);
    Route::delete('/tipe-diklat/{id}', [MasterDataController::class, 'destroyTipeDiklat']);

    Route::post('/jenis-pegawai', [MasterDataController::class, 'storeJenisPegawai']);
    Route::patch('/jenis-pegawai/{id}', [MasterDataController::class, 'updateJenisPegawai']);
    Route::delete('/jenis-pegawai/{id}', [MasterDataController::class, 'destroyJenisPegawai']);

    Route::post('/unit-kerja', [MasterDataController::class, 'storeUnitKerja']);
    Route::patch('/unit-kerja/{id}', [MasterDataController::class, 'updateUnitKerja']);
    Route::delete('/unit-kerja/{id}', [MasterDataController::class, 'destroyUnitKerja']);

    Route::post('/jenis-biaya', [MasterDataController::class, 'storeJenisBiaya']);
    Route::patch('/jenis-biaya/{id}', [MasterDataController::class, 'updateJenisBiaya']);
    Route::delete('/jenis-biaya/{id}', [MasterDataController::class, 'destroyJenisBiaya']);

    Route::post('/golongan-ruang', [MasterDataController::class, 'storeGolonganRuang']);
    Route::patch('/golongan-ruang/{id}', [MasterDataController::class, 'updateGolonganRuang']);
    Route::delete('/golongan-ruang/{id}', [MasterDataController::class, 'destroyGolonganRuang']);

    Route::post('/profesi', [MasterDataController::class, 'storeProfesi']);
    Route::patch('/profesi/{id}', [MasterDataController::class, 'updateProfesi']);
    Route::delete('/profesi/{id}', [MasterDataController::class, 'destroyProfesi']);

    Route::post('/jenis-sip', [MasterDataController::class, 'storeJenisSip']);
    Route::patch('/jenis-sip/{id}', [MasterDataController::class, 'updateJenisSip']);
    Route::delete('/jenis-sip/{id}', [MasterDataController::class, 'destroyJenisSip']);
});
