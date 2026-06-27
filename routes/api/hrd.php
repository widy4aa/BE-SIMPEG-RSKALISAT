<?php

use App\Http\Controllers\Api\Hrd\HrdKeluargaController;
use App\Http\Controllers\Api\Hrd\HrdJabatanController;
use App\Http\Controllers\Api\Hrd\HrdPangkatController;
use App\Http\Controllers\Api\Hrd\HrdPegawaiController;
use App\Http\Controllers\Api\Hrd\HrdPendidikanController;
use App\Http\Controllers\Api\Hrd\HrdPenugasanKlinisController;
use App\Http\Controllers\Api\Hrd\HrdReminderController;
use App\Http\Controllers\Api\Hrd\HrdSipController;
use App\Http\Controllers\Api\Hrd\HrdStrController;
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

        Route::get('/keluarga/pasangan', [HrdKeluargaController::class, 'indexPasangan']);
        Route::post('/keluarga/pasangan', [HrdKeluargaController::class, 'storePasangan']);
        Route::patch('/keluarga/pasangan/{keluargaId}', [HrdKeluargaController::class, 'updatePasangan']);
        Route::post('/keluarga/pasangan/{keluargaId}', [HrdKeluargaController::class, 'updatePasangan']);
        Route::delete('/keluarga/pasangan/{keluargaId}', [HrdKeluargaController::class, 'destroyPasangan']);

        Route::get('/keluarga/anak', [HrdKeluargaController::class, 'indexAnak']);
        Route::post('/keluarga/anak', [HrdKeluargaController::class, 'storeAnak']);
        Route::patch('/keluarga/anak/{keluargaId}', [HrdKeluargaController::class, 'updateAnak']);
        Route::post('/keluarga/anak/{keluargaId}', [HrdKeluargaController::class, 'updateAnak']);
        Route::delete('/keluarga/anak/{keluargaId}', [HrdKeluargaController::class, 'destroyAnak']);

        Route::get('/keluarga/orang-tua', [HrdKeluargaController::class, 'indexOrangTua']);
        Route::post('/keluarga/orang-tua', [HrdKeluargaController::class, 'storeOrangTua']);
        Route::patch('/keluarga/orang-tua/{keluargaId}', [HrdKeluargaController::class, 'updateOrangTua']);
        Route::delete('/keluarga/orang-tua/{keluargaId}', [HrdKeluargaController::class, 'destroyOrangTua']);

        Route::get('/keluarga/kontak-darurat', [HrdKeluargaController::class, 'indexKontakDarurat']);
        Route::post('/keluarga/kontak-darurat', [HrdKeluargaController::class, 'storeKontakDarurat']);
        Route::patch('/keluarga/kontak-darurat/{keluargaId}', [HrdKeluargaController::class, 'updateKontakDarurat']);
        Route::delete('/keluarga/kontak-darurat/{keluargaId}', [HrdKeluargaController::class, 'destroyKontakDarurat']);

        Route::get('/keluarga/tanggungan-lain', [HrdKeluargaController::class, 'indexTanggunganLain']);
        Route::post('/keluarga/tanggungan-lain', [HrdKeluargaController::class, 'storeTanggunganLain']);
        Route::patch('/keluarga/tanggungan-lain/{keluargaId}', [HrdKeluargaController::class, 'updateTanggunganLain']);
        Route::delete('/keluarga/tanggungan-lain/{keluargaId}', [HrdKeluargaController::class, 'destroyTanggunganLain']);

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
