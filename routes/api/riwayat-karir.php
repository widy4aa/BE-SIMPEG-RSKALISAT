<?php

use App\Http\Controllers\Api\RiwayatKarirController;
use App\Http\Middleware\JwtAuthMiddleware;
use App\Http\Middleware\RoleMiddleware;
use Illuminate\Support\Facades\Route;

Route::middleware([
    JwtAuthMiddleware::class,
    RoleMiddleware::class.':admin,pegawai,hrd,direktur',
])->group(function () {
    Route::get('/riwayat-karir/pendidikan', [RiwayatKarirController::class, 'pendidikan']);
    Route::post('/riwayat-karir/pendidikan', [RiwayatKarirController::class, 'storePendidikan']);
    Route::patch('/riwayat-karir/pendidikan/{id}', [RiwayatKarirController::class, 'updatePendidikan']);
    Route::post('/riwayat-karir/pendidikan/{id}', [RiwayatKarirController::class, 'updatePendidikan']);
    Route::delete('/riwayat-karir/pendidikan/{id}', [RiwayatKarirController::class, 'destroyPendidikan']);

    Route::get('/riwayat-karir/jabatan', [RiwayatKarirController::class, 'jabatan']);
    Route::post('/riwayat-karir/jabatan', [RiwayatKarirController::class, 'storeJabatan']);
    Route::patch('/riwayat-karir/jabatan/{id}', [RiwayatKarirController::class, 'updateJabatan']);
    Route::post('/riwayat-karir/jabatan/{id}', [RiwayatKarirController::class, 'updateJabatan']);
    Route::delete('/riwayat-karir/jabatan/{id}', [RiwayatKarirController::class, 'destroyJabatan']);

    Route::get('/riwayat-karir/pangkat', [RiwayatKarirController::class, 'pangkat']);
    Route::post('/riwayat-karir/pangkat', [RiwayatKarirController::class, 'storePangkat']);
    Route::patch('/riwayat-karir/pangkat/{id}', [RiwayatKarirController::class, 'updatePangkat']);
    Route::post('/riwayat-karir/pangkat/{id}', [RiwayatKarirController::class, 'updatePangkat']);
    Route::delete('/riwayat-karir/pangkat/{id}', [RiwayatKarirController::class, 'destroyPangkat']);

    Route::get('/riwayat-karir/sip', [RiwayatKarirController::class, 'sip']);
    Route::post('/riwayat-karir/sip', [RiwayatKarirController::class, 'storeSip']);
    Route::patch('/riwayat-karir/sip/{id}', [RiwayatKarirController::class, 'updateSip']);
    Route::post('/riwayat-karir/sip/{id}', [RiwayatKarirController::class, 'updateSip']);
    Route::delete('/riwayat-karir/sip/{id}', [RiwayatKarirController::class, 'destroySip']);

    Route::get('/riwayat-karir/str', [RiwayatKarirController::class, 'str']);
    Route::post('/riwayat-karir/str', [RiwayatKarirController::class, 'storeStr']);
    Route::patch('/riwayat-karir/str/{id}', [RiwayatKarirController::class, 'updateStr']);
    Route::post('/riwayat-karir/str/{id}', [RiwayatKarirController::class, 'updateStr']);
    Route::delete('/riwayat-karir/str/{id}', [RiwayatKarirController::class, 'destroyStr']);

    Route::get('/riwayat-karir/penugasan-klinis', [RiwayatKarirController::class, 'penugasanKlinis']);
    Route::post('/riwayat-karir/penugasan-klinis', [RiwayatKarirController::class, 'storePenugasanKlinis']);
    Route::patch('/riwayat-karir/penugasan-klinis/{id}', [RiwayatKarirController::class, 'updatePenugasanKlinis']);
    Route::post('/riwayat-karir/penugasan-klinis/{id}', [RiwayatKarirController::class, 'updatePenugasanKlinis']);
    Route::delete('/riwayat-karir/penugasan-klinis/{id}', [RiwayatKarirController::class, 'destroyPenugasanKlinis']);
});
