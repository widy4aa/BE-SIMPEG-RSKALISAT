<?php

use App\Http\Controllers\Api\RiwayatKarir\Managed\JabatanController as ManagedJabatanController;
use App\Http\Controllers\Api\RiwayatKarir\Managed\PangkatController as ManagedPangkatController;
use App\Http\Controllers\Api\Keluarga\Managed\AnakController as ManagedAnakController;
use App\Http\Controllers\Api\Pegawai\Managed\PegawaiController as ManagedPegawaiController;
use App\Http\Controllers\Api\RiwayatKarir\Managed\PendidikanController as ManagedPendidikanController;
use App\Http\Controllers\Api\RiwayatKarir\Managed\PenugasanKlinisController as ManagedPenugasanKlinisController;
use App\Http\Controllers\Api\Keluarga\Managed\OrangTuaController as ManagedOrangTuaController;
use App\Http\Controllers\Api\Keluarga\Managed\PasanganController as ManagedPasanganController;
use App\Http\Controllers\Api\Hrd\HrdReminderController;
use App\Http\Controllers\Api\RiwayatKarir\Managed\SipController as ManagedSipController;
use App\Http\Controllers\Api\RiwayatKarir\Managed\StrController as ManagedStrController;
use App\Http\Controllers\Api\Keluarga\Managed\TanggunganLainController as ManagedTanggunganLainController;
use App\Http\Controllers\Api\Keluarga\Managed\KontakDaruratController as ManagedKontakDaruratController;
use App\Http\Middleware\JwtAuthMiddleware;
use App\Http\Middleware\RoleMiddleware;
use Illuminate\Support\Facades\Route;

Route::middleware([
    JwtAuthMiddleware::class,
    RoleMiddleware::class.':hrd',
])->group(function () {
    Route::prefix('hrd/pegawai/{id}')->group(function () {
        Route::patch('/inti', [ManagedPegawaiController::class, 'updateInti']);
        Route::patch('/pribadi', [ManagedPegawaiController::class, 'updatePribadi']);
        Route::post('/pribadi', [ManagedPegawaiController::class, 'updatePribadi']);

        Route::get('/keluarga/pasangan', [ManagedPasanganController::class, 'index']);
        Route::post('/keluarga/pasangan', [ManagedPasanganController::class, 'store']);
        Route::patch('/keluarga/pasangan/{keluargaId}', [ManagedPasanganController::class, 'update']);
        Route::post('/keluarga/pasangan/{keluargaId}', [ManagedPasanganController::class, 'update']);
        Route::delete('/keluarga/pasangan/{keluargaId}', [ManagedPasanganController::class, 'destroy']);

        Route::get('/keluarga/anak', [ManagedAnakController::class, 'index']);
        Route::post('/keluarga/anak', [ManagedAnakController::class, 'store']);
        Route::patch('/keluarga/anak/{keluargaId}', [ManagedAnakController::class, 'update']);
        Route::post('/keluarga/anak/{keluargaId}', [ManagedAnakController::class, 'update']);
        Route::delete('/keluarga/anak/{keluargaId}', [ManagedAnakController::class, 'destroy']);

        Route::get('/keluarga/orang-tua', [ManagedOrangTuaController::class, 'index']);
        Route::post('/keluarga/orang-tua', [ManagedOrangTuaController::class, 'store']);
        Route::patch('/keluarga/orang-tua/{keluargaId}', [ManagedOrangTuaController::class, 'update']);
        Route::delete('/keluarga/orang-tua/{keluargaId}', [ManagedOrangTuaController::class, 'destroy']);

        Route::get('/keluarga/kontak-darurat', [ManagedKontakDaruratController::class, 'index']);
        Route::post('/keluarga/kontak-darurat', [ManagedKontakDaruratController::class, 'store']);
        Route::patch('/keluarga/kontak-darurat/{keluargaId}', [ManagedKontakDaruratController::class, 'update']);
        Route::delete('/keluarga/kontak-darurat/{keluargaId}', [ManagedKontakDaruratController::class, 'destroy']);

        Route::get('/keluarga/tanggungan-lain', [ManagedTanggunganLainController::class, 'index']);
        Route::post('/keluarga/tanggungan-lain', [ManagedTanggunganLainController::class, 'store']);
        Route::patch('/keluarga/tanggungan-lain/{keluargaId}', [ManagedTanggunganLainController::class, 'update']);
        Route::delete('/keluarga/tanggungan-lain/{keluargaId}', [ManagedTanggunganLainController::class, 'destroy']);

        Route::get('/riwayat-karir/jabatan', [ManagedJabatanController::class, 'index']);
        Route::post('/riwayat-karir/jabatan', [ManagedJabatanController::class, 'store']);
        Route::patch('/riwayat-karir/jabatan/{riwayatId}', [ManagedJabatanController::class, 'update']);
        Route::post('/riwayat-karir/jabatan/{riwayatId}', [ManagedJabatanController::class, 'update']);
        Route::delete('/riwayat-karir/jabatan/{riwayatId}', [ManagedJabatanController::class, 'destroy']);

        Route::get('/riwayat-karir/str', [ManagedStrController::class, 'index']);
        Route::post('/riwayat-karir/str', [ManagedStrController::class, 'store']);
        Route::patch('/riwayat-karir/str/{riwayatId}', [ManagedStrController::class, 'update']);
        Route::post('/riwayat-karir/str/{riwayatId}', [ManagedStrController::class, 'update']);
        Route::delete('/riwayat-karir/str/{riwayatId}', [ManagedStrController::class, 'destroy']);

        Route::get('/riwayat-karir/sip', [ManagedSipController::class, 'index']);
        Route::post('/riwayat-karir/sip', [ManagedSipController::class, 'store']);
        Route::patch('/riwayat-karir/sip/{riwayatId}', [ManagedSipController::class, 'update']);
        Route::post('/riwayat-karir/sip/{riwayatId}', [ManagedSipController::class, 'update']);
        Route::delete('/riwayat-karir/sip/{riwayatId}', [ManagedSipController::class, 'destroy']);

        Route::get('/riwayat-karir/penugasan-klinis', [ManagedPenugasanKlinisController::class, 'index']);
        Route::post('/riwayat-karir/penugasan-klinis', [ManagedPenugasanKlinisController::class, 'store']);
        Route::patch('/riwayat-karir/penugasan-klinis/{riwayatId}', [ManagedPenugasanKlinisController::class, 'update']);
        Route::post('/riwayat-karir/penugasan-klinis/{riwayatId}', [ManagedPenugasanKlinisController::class, 'update']);
        Route::delete('/riwayat-karir/penugasan-klinis/{riwayatId}', [ManagedPenugasanKlinisController::class, 'destroy']);

        Route::get('/riwayat-karir/pangkat', [ManagedPangkatController::class, 'index']);
        Route::post('/riwayat-karir/pangkat', [ManagedPangkatController::class, 'store']);
        Route::patch('/riwayat-karir/pangkat/{riwayatId}', [ManagedPangkatController::class, 'update']);
        Route::post('/riwayat-karir/pangkat/{riwayatId}', [ManagedPangkatController::class, 'update']);
        Route::delete('/riwayat-karir/pangkat/{riwayatId}', [ManagedPangkatController::class, 'destroy']);

        Route::get('/riwayat-karir/pendidikan', [ManagedPendidikanController::class, 'index']);
        Route::post('/riwayat-karir/pendidikan', [ManagedPendidikanController::class, 'store']);
        Route::patch('/riwayat-karir/pendidikan/{riwayatId}', [ManagedPendidikanController::class, 'update']);
        Route::post('/riwayat-karir/pendidikan/{riwayatId}', [ManagedPendidikanController::class, 'update']);
        Route::delete('/riwayat-karir/pendidikan/{riwayatId}', [ManagedPendidikanController::class, 'destroy']);

        Route::post('/reminder/str-sip', [HrdReminderController::class, 'sendReminderStrSip']);
        Route::post('/reminder/penugasan-klinis', [HrdReminderController::class, 'sendReminderPenugasanKlinis']);
    });
});
