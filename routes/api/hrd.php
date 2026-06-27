<?php

use App\Http\Controllers\Api\Hrd\HrdKeluargaController;
use App\Http\Controllers\Api\Hrd\HrdPegawaiController;
use App\Http\Controllers\Api\Hrd\HrdRiwayatKarirController;
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

        Route::get('/riwayat-karir/jabatan', [HrdRiwayatKarirController::class, 'jabatan']);
        Route::post('/riwayat-karir/jabatan', [HrdRiwayatKarirController::class, 'storeJabatan']);
        Route::patch('/riwayat-karir/jabatan/{riwayatId}', [HrdRiwayatKarirController::class, 'updateJabatan']);
        Route::post('/riwayat-karir/jabatan/{riwayatId}', [HrdRiwayatKarirController::class, 'updateJabatan']);
        Route::delete('/riwayat-karir/jabatan/{riwayatId}', [HrdRiwayatKarirController::class, 'destroyJabatan']);

        Route::get('/riwayat-karir/str', [HrdRiwayatKarirController::class, 'str']);
        Route::post('/riwayat-karir/str', [HrdRiwayatKarirController::class, 'storeStr']);
        Route::patch('/riwayat-karir/str/{riwayatId}', [HrdRiwayatKarirController::class, 'updateStr']);
        Route::post('/riwayat-karir/str/{riwayatId}', [HrdRiwayatKarirController::class, 'updateStr']);
        Route::delete('/riwayat-karir/str/{riwayatId}', [HrdRiwayatKarirController::class, 'destroyStr']);

        Route::get('/riwayat-karir/sip', [HrdRiwayatKarirController::class, 'sip']);
        Route::post('/riwayat-karir/sip', [HrdRiwayatKarirController::class, 'storeSip']);
        Route::patch('/riwayat-karir/sip/{riwayatId}', [HrdRiwayatKarirController::class, 'updateSip']);
        Route::post('/riwayat-karir/sip/{riwayatId}', [HrdRiwayatKarirController::class, 'updateSip']);
        Route::delete('/riwayat-karir/sip/{riwayatId}', [HrdRiwayatKarirController::class, 'destroySip']);

        Route::get('/riwayat-karir/penugasan-klinis', [HrdRiwayatKarirController::class, 'penugasanKlinis']);
        Route::post('/riwayat-karir/penugasan-klinis', [HrdRiwayatKarirController::class, 'storePenugasanKlinis']);
        Route::patch('/riwayat-karir/penugasan-klinis/{riwayatId}', [HrdRiwayatKarirController::class, 'updatePenugasanKlinis']);
        Route::post('/riwayat-karir/penugasan-klinis/{riwayatId}', [HrdRiwayatKarirController::class, 'updatePenugasanKlinis']);
        Route::delete('/riwayat-karir/penugasan-klinis/{riwayatId}', [HrdRiwayatKarirController::class, 'destroyPenugasanKlinis']);

        Route::get('/riwayat-karir/pangkat', [HrdRiwayatKarirController::class, 'pangkat']);
        Route::post('/riwayat-karir/pangkat', [HrdRiwayatKarirController::class, 'storePangkat']);
        Route::patch('/riwayat-karir/pangkat/{riwayatId}', [HrdRiwayatKarirController::class, 'updatePangkat']);
        Route::post('/riwayat-karir/pangkat/{riwayatId}', [HrdRiwayatKarirController::class, 'updatePangkat']);
        Route::delete('/riwayat-karir/pangkat/{riwayatId}', [HrdRiwayatKarirController::class, 'destroyPangkat']);

        Route::get('/riwayat-karir/pendidikan', [HrdRiwayatKarirController::class, 'pendidikan']);
        Route::post('/riwayat-karir/pendidikan', [HrdRiwayatKarirController::class, 'storePendidikan']);
        Route::patch('/riwayat-karir/pendidikan/{riwayatId}', [HrdRiwayatKarirController::class, 'updatePendidikan']);
        Route::post('/riwayat-karir/pendidikan/{riwayatId}', [HrdRiwayatKarirController::class, 'updatePendidikan']);
        Route::delete('/riwayat-karir/pendidikan/{riwayatId}', [HrdRiwayatKarirController::class, 'destroyPendidikan']);

        Route::post('/reminder/str-sip', [HrdRiwayatKarirController::class, 'sendReminderStrSip']);
        Route::post('/reminder/penugasan-klinis', [HrdRiwayatKarirController::class, 'sendReminderPenugasanKlinis']);
    });
});
