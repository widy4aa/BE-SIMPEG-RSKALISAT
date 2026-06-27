<?php

use App\Http\Controllers\Api\Hrd\HrdAnakController;
use App\Http\Controllers\Api\Hrd\HrdJabatanController;
use App\Http\Controllers\Api\Hrd\HrdKontakDaruratController;
use App\Http\Controllers\Api\Hrd\HrdPangkatController;
use App\Http\Controllers\Api\Hrd\HrdPegawaiController;
use App\Http\Controllers\Api\Hrd\HrdPendidikanController;
use App\Http\Controllers\Api\Hrd\HrdPenugasanKlinisController;
use App\Http\Controllers\Api\Hrd\HrdOrangTuaController;
use App\Http\Controllers\Api\Hrd\HrdPasanganController;
use App\Http\Controllers\Api\Hrd\HrdReminderController;
use App\Http\Controllers\Api\Hrd\HrdSipController;
use App\Http\Controllers\Api\Hrd\HrdStrController;
use App\Http\Controllers\Api\Hrd\HrdTanggunganLainController;
use App\Http\Middleware\JwtAuthMiddleware;
use App\Http\Middleware\RoleMiddleware;
use Illuminate\Support\Facades\Route;

Route::middleware([
    JwtAuthMiddleware::class,
    RoleMiddleware::class.':hrd',
])->group(function () {
    Route::prefix('hrd/pegawai/{id}')->group(function () {
        Route::patch('/inti', [HrdPegawaiController::class, 'updateInti']);
        Route::patch('/pribadi', [HrdPegawaiController::class, 'updatePribadi']);
        Route::post('/pribadi', [HrdPegawaiController::class, 'updatePribadi']);

        Route::get('/keluarga/pasangan', [HrdPasanganController::class, 'index']);
        Route::post('/keluarga/pasangan', [HrdPasanganController::class, 'store']);
        Route::patch('/keluarga/pasangan/{keluargaId}', [HrdPasanganController::class, 'update']);
        Route::post('/keluarga/pasangan/{keluargaId}', [HrdPasanganController::class, 'update']);
        Route::delete('/keluarga/pasangan/{keluargaId}', [HrdPasanganController::class, 'destroy']);

        Route::get('/keluarga/anak', [HrdAnakController::class, 'index']);
        Route::post('/keluarga/anak', [HrdAnakController::class, 'store']);
        Route::patch('/keluarga/anak/{keluargaId}', [HrdAnakController::class, 'update']);
        Route::post('/keluarga/anak/{keluargaId}', [HrdAnakController::class, 'update']);
        Route::delete('/keluarga/anak/{keluargaId}', [HrdAnakController::class, 'destroy']);

        Route::get('/keluarga/orang-tua', [HrdOrangTuaController::class, 'index']);
        Route::post('/keluarga/orang-tua', [HrdOrangTuaController::class, 'store']);
        Route::patch('/keluarga/orang-tua/{keluargaId}', [HrdOrangTuaController::class, 'update']);
        Route::delete('/keluarga/orang-tua/{keluargaId}', [HrdOrangTuaController::class, 'destroy']);

        Route::get('/keluarga/kontak-darurat', [HrdKontakDaruratController::class, 'index']);
        Route::post('/keluarga/kontak-darurat', [HrdKontakDaruratController::class, 'store']);
        Route::patch('/keluarga/kontak-darurat/{keluargaId}', [HrdKontakDaruratController::class, 'update']);
        Route::delete('/keluarga/kontak-darurat/{keluargaId}', [HrdKontakDaruratController::class, 'destroy']);

        Route::get('/keluarga/tanggungan-lain', [HrdTanggunganLainController::class, 'index']);
        Route::post('/keluarga/tanggungan-lain', [HrdTanggunganLainController::class, 'store']);
        Route::patch('/keluarga/tanggungan-lain/{keluargaId}', [HrdTanggunganLainController::class, 'update']);
        Route::delete('/keluarga/tanggungan-lain/{keluargaId}', [HrdTanggunganLainController::class, 'destroy']);

        Route::get('/riwayat-karir/jabatan', [HrdJabatanController::class, 'index']);
        Route::post('/riwayat-karir/jabatan', [HrdJabatanController::class, 'store']);
        Route::patch('/riwayat-karir/jabatan/{riwayatId}', [HrdJabatanController::class, 'update']);
        Route::post('/riwayat-karir/jabatan/{riwayatId}', [HrdJabatanController::class, 'update']);
        Route::delete('/riwayat-karir/jabatan/{riwayatId}', [HrdJabatanController::class, 'destroy']);

        Route::get('/riwayat-karir/str', [HrdStrController::class, 'index']);
        Route::post('/riwayat-karir/str', [HrdStrController::class, 'store']);
        Route::patch('/riwayat-karir/str/{riwayatId}', [HrdStrController::class, 'update']);
        Route::post('/riwayat-karir/str/{riwayatId}', [HrdStrController::class, 'update']);
        Route::delete('/riwayat-karir/str/{riwayatId}', [HrdStrController::class, 'destroy']);

        Route::get('/riwayat-karir/sip', [HrdSipController::class, 'index']);
        Route::post('/riwayat-karir/sip', [HrdSipController::class, 'store']);
        Route::patch('/riwayat-karir/sip/{riwayatId}', [HrdSipController::class, 'update']);
        Route::post('/riwayat-karir/sip/{riwayatId}', [HrdSipController::class, 'update']);
        Route::delete('/riwayat-karir/sip/{riwayatId}', [HrdSipController::class, 'destroy']);

        Route::get('/riwayat-karir/penugasan-klinis', [HrdPenugasanKlinisController::class, 'index']);
        Route::post('/riwayat-karir/penugasan-klinis', [HrdPenugasanKlinisController::class, 'store']);
        Route::patch('/riwayat-karir/penugasan-klinis/{riwayatId}', [HrdPenugasanKlinisController::class, 'update']);
        Route::post('/riwayat-karir/penugasan-klinis/{riwayatId}', [HrdPenugasanKlinisController::class, 'update']);
        Route::delete('/riwayat-karir/penugasan-klinis/{riwayatId}', [HrdPenugasanKlinisController::class, 'destroy']);

        Route::get('/riwayat-karir/pangkat', [HrdPangkatController::class, 'index']);
        Route::post('/riwayat-karir/pangkat', [HrdPangkatController::class, 'store']);
        Route::patch('/riwayat-karir/pangkat/{riwayatId}', [HrdPangkatController::class, 'update']);
        Route::post('/riwayat-karir/pangkat/{riwayatId}', [HrdPangkatController::class, 'update']);
        Route::delete('/riwayat-karir/pangkat/{riwayatId}', [HrdPangkatController::class, 'destroy']);

        Route::get('/riwayat-karir/pendidikan', [HrdPendidikanController::class, 'index']);
        Route::post('/riwayat-karir/pendidikan', [HrdPendidikanController::class, 'store']);
        Route::patch('/riwayat-karir/pendidikan/{riwayatId}', [HrdPendidikanController::class, 'update']);
        Route::post('/riwayat-karir/pendidikan/{riwayatId}', [HrdPendidikanController::class, 'update']);
        Route::delete('/riwayat-karir/pendidikan/{riwayatId}', [HrdPendidikanController::class, 'destroy']);

        Route::post('/reminder/str-sip', [HrdReminderController::class, 'sendReminderStrSip']);
        Route::post('/reminder/penugasan-klinis', [HrdReminderController::class, 'sendReminderPenugasanKlinis']);
    });
});
