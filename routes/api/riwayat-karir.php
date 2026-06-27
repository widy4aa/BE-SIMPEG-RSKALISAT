<?php

use App\Http\Controllers\Api\RiwayatKarir\Self\JabatanController;
use App\Http\Controllers\Api\RiwayatKarir\Self\PangkatController;
use App\Http\Controllers\Api\RiwayatKarir\Self\PendidikanController;
use App\Http\Controllers\Api\RiwayatKarir\Self\PenugasanKlinisController;
use App\Http\Controllers\Api\RiwayatKarir\Self\SipController;
use App\Http\Controllers\Api\RiwayatKarir\Self\StrController;
use App\Http\Middleware\JwtAuthMiddleware;
use App\Http\Middleware\RoleMiddleware;
use Illuminate\Support\Facades\Route;

Route::middleware([
    JwtAuthMiddleware::class,
    RoleMiddleware::class.':admin,pegawai,hrd,direktur',
])->group(function () {
    Route::get('/riwayat-karir/pendidikan', [PendidikanController::class, 'index']);
    Route::post('/riwayat-karir/pendidikan', [PendidikanController::class, 'store']);
    Route::patch('/riwayat-karir/pendidikan/{id}', [PendidikanController::class, 'update']);
    Route::post('/riwayat-karir/pendidikan/{id}', [PendidikanController::class, 'update']);
    Route::delete('/riwayat-karir/pendidikan/{id}', [PendidikanController::class, 'destroy']);

    Route::get('/riwayat-karir/jabatan', [JabatanController::class, 'index']);
    Route::post('/riwayat-karir/jabatan', [JabatanController::class, 'store']);
    Route::patch('/riwayat-karir/jabatan/{id}', [JabatanController::class, 'update']);
    Route::post('/riwayat-karir/jabatan/{id}', [JabatanController::class, 'update']);
    Route::delete('/riwayat-karir/jabatan/{id}', [JabatanController::class, 'destroy']);

    Route::get('/riwayat-karir/pangkat', [PangkatController::class, 'index']);
    Route::post('/riwayat-karir/pangkat', [PangkatController::class, 'store']);
    Route::patch('/riwayat-karir/pangkat/{id}', [PangkatController::class, 'update']);
    Route::post('/riwayat-karir/pangkat/{id}', [PangkatController::class, 'update']);
    Route::delete('/riwayat-karir/pangkat/{id}', [PangkatController::class, 'destroy']);

    Route::get('/riwayat-karir/sip', [SipController::class, 'index']);
    Route::post('/riwayat-karir/sip', [SipController::class, 'store']);
    Route::patch('/riwayat-karir/sip/{id}', [SipController::class, 'update']);
    Route::post('/riwayat-karir/sip/{id}', [SipController::class, 'update']);
    Route::delete('/riwayat-karir/sip/{id}', [SipController::class, 'destroy']);

    Route::get('/riwayat-karir/str', [StrController::class, 'index']);
    Route::post('/riwayat-karir/str', [StrController::class, 'store']);
    Route::patch('/riwayat-karir/str/{id}', [StrController::class, 'update']);
    Route::post('/riwayat-karir/str/{id}', [StrController::class, 'update']);
    Route::delete('/riwayat-karir/str/{id}', [StrController::class, 'destroy']);

    Route::get('/riwayat-karir/penugasan-klinis', [PenugasanKlinisController::class, 'index']);
    Route::post('/riwayat-karir/penugasan-klinis', [PenugasanKlinisController::class, 'store']);
    Route::patch('/riwayat-karir/penugasan-klinis/{id}', [PenugasanKlinisController::class, 'update']);
    Route::post('/riwayat-karir/penugasan-klinis/{id}', [PenugasanKlinisController::class, 'update']);
    Route::delete('/riwayat-karir/penugasan-klinis/{id}', [PenugasanKlinisController::class, 'destroy']);
});
