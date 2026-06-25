<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ChangeRequestAdminController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\CvController;
use App\Http\Controllers\Api\DataKeluargaController;
use App\Http\Controllers\Api\DiklatController;
use App\Http\Controllers\Api\Hrd\HrdKeluargaController;
use App\Http\Controllers\Api\Hrd\HrdPegawaiController;
use App\Http\Controllers\Api\Hrd\HrdRiwayatKarirController;
use App\Http\Controllers\Api\Keluarga\PasanganController;
use App\Http\Controllers\Api\Keluarga\AnakController;
use App\Http\Controllers\Api\Keluarga\OrangTuaController;
use App\Http\Controllers\Api\Keluarga\KontakDaruratController;
use App\Http\Controllers\Api\Keluarga\TanggunganLainController;
use App\Http\Controllers\Api\MasterDataController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\PegawaiController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\RiwayatKarirController;
use App\Http\Controllers\Api\RoleController;
use App\Http\Controllers\Api\MessageController;
use App\Http\Controllers\Api\StrSipController;
use App\Http\Middleware\JwtAuthMiddleware;
use App\Http\Middleware\RoleMiddleware;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Route;

// Public
Route::post('/login', [AuthController::class, 'login']);
Route::post('/forgot-password/request-otp', [\App\Http\Controllers\Api\ForgotPasswordController::class, 'requestOtp']);
Route::post('/forgot-password/reset', [\App\Http\Controllers\Api\ForgotPasswordController::class, 'resetPassword']);

// Shared - admin, pegawai, hrd, direktur
Route::middleware([
    JwtAuthMiddleware::class,
    RoleMiddleware::class.':admin,pegawai,hrd,direktur',
])->group(function () {
    // Role
    Route::get('/role', [RoleController::class, 'show']);

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'show']);

    // Diklat - ringkasan
    Route::get('/diklat', [DiklatController::class, 'index']);

    // Generate CV
    Route::get('/generate/cv', [CvController::class, 'generate']);

    // Me - ringkasan identitas user login
    Route::get('/me', [ProfileController::class, 'me']);

    // Profile
    Route::get('/profile', [ProfileController::class, 'show']);
    Route::patch('/profile', [ProfileController::class, 'update']);
    Route::post('/profil/profil-picture', [ProfileController::class, 'updateProfilePicture']);
    Route::post('/profile/profile-picture', [ProfileController::class, 'updateProfilePicture']);
    Route::post('/profil/ktp', [ProfileController::class, 'uploadKtp']);
    Route::post('/profile/kk', [ProfileController::class, 'uploadKk']);

    // Keluarga - ringkasan
    Route::get('/keluarga', [DataKeluargaController::class, 'index']);

    // Keluarga - pasangan
    Route::get('/keluarga/pasangan', [PasanganController::class, 'index']);
    Route::post('/keluarga/pasangan', [PasanganController::class, 'store']);
    Route::patch('/keluarga/pasangan/{id}', [PasanganController::class, 'update']);
    Route::post('/keluarga/pasangan/{id}', [PasanganController::class, 'update']);
    Route::delete('/keluarga/pasangan/{id}', [PasanganController::class, 'destroy']);

    // Keluarga - anak
    Route::get('/keluarga/anak', [AnakController::class, 'index']);
    Route::post('/keluarga/anak', [AnakController::class, 'store']);
    Route::patch('/keluarga/anak/{id}', [AnakController::class, 'update']);
    Route::post('/keluarga/anak/{id}', [AnakController::class, 'update']);
    Route::delete('/keluarga/anak/{id}', [AnakController::class, 'destroy']);

    // Keluarga - orang tua
    Route::get('/keluarga/orang-tua', [OrangTuaController::class, 'index']);
    Route::post('/keluarga/orang-tua', [OrangTuaController::class, 'store']);
    Route::patch('/keluarga/orang-tua/{id}', [OrangTuaController::class, 'update']);
    Route::delete('/keluarga/orang-tua/{id}', [OrangTuaController::class, 'destroy']);

    // Keluarga - kontak darurat
    Route::get('/keluarga/kontak-darurat', [KontakDaruratController::class, 'index']);
    Route::post('/keluarga/kontak-darurat', [KontakDaruratController::class, 'store']);
    Route::patch('/keluarga/kontak-darurat/{id}', [KontakDaruratController::class, 'update']);
    Route::delete('/keluarga/kontak-darurat/{id}', [KontakDaruratController::class, 'destroy']);

    // Keluarga - tanggungan lain (self-service)
    Route::get('/keluarga/tanggungan-lain', [TanggunganLainController::class, 'index']);
    Route::post('/keluarga/tanggungan-lain', [TanggunganLainController::class, 'store']);
    Route::patch('/keluarga/tanggungan-lain/{id}', [TanggunganLainController::class, 'update']);
    Route::delete('/keluarga/tanggungan-lain/{id}', [TanggunganLainController::class, 'destroy']);

    // Auth
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/auth/change-password', [AuthController::class, 'changePassword']);

    // Riwayat karir - pendidikan
    Route::get('/riwayat-karir/pendidikan', [RiwayatKarirController::class, 'pendidikan']);
    Route::post('/riwayat-karir/pendidikan', [RiwayatKarirController::class, 'storePendidikan']);
    Route::patch('/riwayat-karir/pendidikan/{id}', [RiwayatKarirController::class, 'updatePendidikan']);
    Route::post('/riwayat-karir/pendidikan/{id}', [RiwayatKarirController::class, 'updatePendidikan']);
    Route::delete('/riwayat-karir/pendidikan/{id}', [RiwayatKarirController::class, 'destroyPendidikan']);

    // Riwayat karir - jabatan
    Route::get('/riwayat-karir/jabatan', [RiwayatKarirController::class, 'jabatan']);
    Route::post('/riwayat-karir/jabatan', [RiwayatKarirController::class, 'storeJabatan']);
    Route::patch('/riwayat-karir/jabatan/{id}', [RiwayatKarirController::class, 'updateJabatan']);
    Route::post('/riwayat-karir/jabatan/{id}', [RiwayatKarirController::class, 'updateJabatan']);
    Route::delete('/riwayat-karir/jabatan/{id}', [RiwayatKarirController::class, 'destroyJabatan']);

    // Riwayat karir - pangkat
    Route::get('/riwayat-karir/pangkat', [RiwayatKarirController::class, 'pangkat']);
    Route::post('/riwayat-karir/pangkat', [RiwayatKarirController::class, 'storePangkat']);
    Route::patch('/riwayat-karir/pangkat/{id}', [RiwayatKarirController::class, 'updatePangkat']);
    Route::post('/riwayat-karir/pangkat/{id}', [RiwayatKarirController::class, 'updatePangkat']);
    Route::delete('/riwayat-karir/pangkat/{id}', [RiwayatKarirController::class, 'destroyPangkat']);

    // Riwayat karir - SIP
    Route::get('/riwayat-karir/sip', [RiwayatKarirController::class, 'sip']);
    Route::post('/riwayat-karir/sip', [RiwayatKarirController::class, 'storeSip']);
    Route::patch('/riwayat-karir/sip/{id}', [RiwayatKarirController::class, 'updateSip']);
    Route::post('/riwayat-karir/sip/{id}', [RiwayatKarirController::class, 'updateSip']);
    Route::delete('/riwayat-karir/sip/{id}', [RiwayatKarirController::class, 'destroySip']);

    // Riwayat karir - STR
    Route::get('/riwayat-karir/str', [RiwayatKarirController::class, 'str']);
    Route::post('/riwayat-karir/str', [RiwayatKarirController::class, 'storeStr']);
    Route::patch('/riwayat-karir/str/{id}', [RiwayatKarirController::class, 'updateStr']);
    Route::post('/riwayat-karir/str/{id}', [RiwayatKarirController::class, 'updateStr']);
    Route::delete('/riwayat-karir/str/{id}', [RiwayatKarirController::class, 'destroyStr']);

    // Riwayat karir - penugasan klinis
    Route::get('/riwayat-karir/penugasan-klinis', [RiwayatKarirController::class, 'penugasanKlinis']);
    Route::post('/riwayat-karir/penugasan-klinis', [RiwayatKarirController::class, 'storePenugasanKlinis']);
    Route::patch('/riwayat-karir/penugasan-klinis/{id}', [RiwayatKarirController::class, 'updatePenugasanKlinis']);
    Route::post('/riwayat-karir/penugasan-klinis/{id}', [RiwayatKarirController::class, 'updatePenugasanKlinis']);
    Route::delete('/riwayat-karir/penugasan-klinis/{id}', [RiwayatKarirController::class, 'destroyPenugasanKlinis']);
});

// Shared - pegawai, hrd, direktur
Route::middleware([
    JwtAuthMiddleware::class,
    RoleMiddleware::class.':pegawai,hrd,direktur',
])->group(function () {
    // Diklat - CRUD pegawai
    Route::post('/diklat', [DiklatController::class, 'store']);
    Route::patch('/diklat/{id}', [DiklatController::class, 'update']);
    Route::delete('/diklat/{id}', [DiklatController::class, 'destroy']);
    Route::post('/diklat/{id}/upload-laporan', [DiklatController::class, 'uploadLaporan']);
});

// Shared - admin, hrd, direktur
Route::middleware([
    JwtAuthMiddleware::class,
    RoleMiddleware::class.':admin,hrd,direktur',
])->group(function () {
    // Pegawai
    Route::get('/pegawai', [PegawaiController::class, 'index']);
    Route::get('/pegawai/{id}', [PegawaiController::class, 'show']);

    // STR/SIP
    Route::get('/str-sip', [StrSipController::class, 'index']);

    // Kirim pesan WhatsApp ke pegawai
    Route::post('/pesan/pegawai/{id}', [MessageController::class, 'sendToPegawai']);
});

// Admin only
Route::middleware([
    JwtAuthMiddleware::class,
    RoleMiddleware::class.':admin',
])->group(function () {
    // Pegawai
    Route::post('/pegawai', [PegawaiController::class, 'store']);
    Route::patch('/pegawai/{id}/change-role', [PegawaiController::class, 'changeRole']);
    Route::patch('/auth/change-nik', [AuthController::class, 'changeNik']);

    // Settings
    Route::get('/settings/whatsapp', [\App\Http\Controllers\Api\SettingController::class, 'getWhatsappSetting']);
    Route::put('/settings/whatsapp', [\App\Http\Controllers\Api\SettingController::class, 'updateWhatsappSetting']);

    // Change request
    Route::prefix('admin')->group(function () {
        Route::get('/change-requests', [ChangeRequestAdminController::class, 'index']);
        Route::get('/change-requests/{id}', [ChangeRequestAdminController::class, 'show']);
        Route::patch('/change-requests/{id}/accept', [ChangeRequestAdminController::class, 'accept']);
        Route::patch('/change-requests/{id}/reject', [ChangeRequestAdminController::class, 'reject']);
    });
});

// HRD and Direktur
Route::middleware([
    JwtAuthMiddleware::class,
    RoleMiddleware::class.':hrd,direktur',
])->group(function () {
    // Diklat - all
    Route::get('/diklat/all', [DiklatController::class, 'all']);
});

// HRD only
Route::middleware([
    JwtAuthMiddleware::class,
    RoleMiddleware::class.':hrd',
])->group(function () {
    // HRD - Manajemen Data Pegawai
    Route::prefix('hrd/pegawai/{id}')->group(function () {
        Route::patch('/inti', [HrdPegawaiController::class, 'updateInti']);
        Route::patch('/pribadi', [HrdPegawaiController::class, 'updatePribadi']);
        Route::post('/pribadi', [HrdPegawaiController::class, 'updatePribadi']);

        // Keluarga - pasangan
        Route::get('/keluarga/pasangan', [HrdKeluargaController::class, 'indexPasangan']);
        Route::post('/keluarga/pasangan', [HrdKeluargaController::class, 'storePasangan']);
        Route::patch('/keluarga/pasangan/{keluargaId}', [HrdKeluargaController::class, 'updatePasangan']);
        Route::post('/keluarga/pasangan/{keluargaId}', [HrdKeluargaController::class, 'updatePasangan']);
        Route::delete('/keluarga/pasangan/{keluargaId}', [HrdKeluargaController::class, 'destroyPasangan']);

        // Keluarga - anak
        Route::get('/keluarga/anak', [HrdKeluargaController::class, 'indexAnak']);
        Route::post('/keluarga/anak', [HrdKeluargaController::class, 'storeAnak']);
        Route::patch('/keluarga/anak/{keluargaId}', [HrdKeluargaController::class, 'updateAnak']);
        Route::post('/keluarga/anak/{keluargaId}', [HrdKeluargaController::class, 'updateAnak']);
        Route::delete('/keluarga/anak/{keluargaId}', [HrdKeluargaController::class, 'destroyAnak']);

        // Keluarga - orang tua
        Route::get('/keluarga/orang-tua', [HrdKeluargaController::class, 'indexOrangTua']);
        Route::post('/keluarga/orang-tua', [HrdKeluargaController::class, 'storeOrangTua']);
        Route::patch('/keluarga/orang-tua/{keluargaId}', [HrdKeluargaController::class, 'updateOrangTua']);
        Route::delete('/keluarga/orang-tua/{keluargaId}', [HrdKeluargaController::class, 'destroyOrangTua']);

        // Keluarga - kontak darurat
        Route::get('/keluarga/kontak-darurat', [HrdKeluargaController::class, 'indexKontakDarurat']);
        Route::post('/keluarga/kontak-darurat', [HrdKeluargaController::class, 'storeKontakDarurat']);
        Route::patch('/keluarga/kontak-darurat/{keluargaId}', [HrdKeluargaController::class, 'updateKontakDarurat']);
        Route::delete('/keluarga/kontak-darurat/{keluargaId}', [HrdKeluargaController::class, 'destroyKontakDarurat']);

        // Keluarga - tanggungan lain
        Route::get('/keluarga/tanggungan-lain', [HrdKeluargaController::class, 'indexTanggunganLain']);
        Route::post('/keluarga/tanggungan-lain', [HrdKeluargaController::class, 'storeTanggunganLain']);
        Route::patch('/keluarga/tanggungan-lain/{keluargaId}', [HrdKeluargaController::class, 'updateTanggunganLain']);
        Route::delete('/keluarga/tanggungan-lain/{keluargaId}', [HrdKeluargaController::class, 'destroyTanggunganLain']);

        // Riwayat Karir - jabatan
        Route::get('/riwayat-karir/jabatan', [HrdRiwayatKarirController::class, 'jabatan']);
        Route::post('/riwayat-karir/jabatan', [HrdRiwayatKarirController::class, 'storeJabatan']);
        Route::patch('/riwayat-karir/jabatan/{riwayatId}', [HrdRiwayatKarirController::class, 'updateJabatan']);
        Route::post('/riwayat-karir/jabatan/{riwayatId}', [HrdRiwayatKarirController::class, 'updateJabatan']);
        Route::delete('/riwayat-karir/jabatan/{riwayatId}', [HrdRiwayatKarirController::class, 'destroyJabatan']);

        // Riwayat Karir - STR
        Route::get('/riwayat-karir/str', [HrdRiwayatKarirController::class, 'str']);
        Route::post('/riwayat-karir/str', [HrdRiwayatKarirController::class, 'storeStr']);
        Route::patch('/riwayat-karir/str/{riwayatId}', [HrdRiwayatKarirController::class, 'updateStr']);
        Route::post('/riwayat-karir/str/{riwayatId}', [HrdRiwayatKarirController::class, 'updateStr']);
        Route::delete('/riwayat-karir/str/{riwayatId}', [HrdRiwayatKarirController::class, 'destroyStr']);

        // Riwayat Karir - SIP
        Route::get('/riwayat-karir/sip', [HrdRiwayatKarirController::class, 'sip']);
        Route::post('/riwayat-karir/sip', [HrdRiwayatKarirController::class, 'storeSip']);
        Route::patch('/riwayat-karir/sip/{riwayatId}', [HrdRiwayatKarirController::class, 'updateSip']);
        Route::post('/riwayat-karir/sip/{riwayatId}', [HrdRiwayatKarirController::class, 'updateSip']);
        Route::delete('/riwayat-karir/sip/{riwayatId}', [HrdRiwayatKarirController::class, 'destroySip']);

        // Riwayat Karir - penugasan klinis
        Route::get('/riwayat-karir/penugasan-klinis', [HrdRiwayatKarirController::class, 'penugasanKlinis']);
        Route::post('/riwayat-karir/penugasan-klinis', [HrdRiwayatKarirController::class, 'storePenugasanKlinis']);
        Route::patch('/riwayat-karir/penugasan-klinis/{riwayatId}', [HrdRiwayatKarirController::class, 'updatePenugasanKlinis']);
        Route::post('/riwayat-karir/penugasan-klinis/{riwayatId}', [HrdRiwayatKarirController::class, 'updatePenugasanKlinis']);
        Route::delete('/riwayat-karir/penugasan-klinis/{riwayatId}', [HrdRiwayatKarirController::class, 'destroyPenugasanKlinis']);

        // Riwayat Karir - pangkat
        Route::get('/riwayat-karir/pangkat', [HrdRiwayatKarirController::class, 'pangkat']);
        Route::post('/riwayat-karir/pangkat', [HrdRiwayatKarirController::class, 'storePangkat']);
        Route::patch('/riwayat-karir/pangkat/{riwayatId}', [HrdRiwayatKarirController::class, 'updatePangkat']);
        Route::post('/riwayat-karir/pangkat/{riwayatId}', [HrdRiwayatKarirController::class, 'updatePangkat']);
        Route::delete('/riwayat-karir/pangkat/{riwayatId}', [HrdRiwayatKarirController::class, 'destroyPangkat']);

        // Riwayat Karir - pendidikan
        Route::get('/riwayat-karir/pendidikan', [HrdRiwayatKarirController::class, 'pendidikan']);
        Route::post('/riwayat-karir/pendidikan', [HrdRiwayatKarirController::class, 'storePendidikan']);
        Route::patch('/riwayat-karir/pendidikan/{riwayatId}', [HrdRiwayatKarirController::class, 'updatePendidikan']);
        Route::post('/riwayat-karir/pendidikan/{riwayatId}', [HrdRiwayatKarirController::class, 'updatePendidikan']);
        Route::delete('/riwayat-karir/pendidikan/{riwayatId}', [HrdRiwayatKarirController::class, 'destroyPendidikan']);

        // Reminder WhatsApp
        Route::post('/reminder/str-sip', [HrdRiwayatKarirController::class, 'sendReminderStrSip']);
        Route::post('/reminder/penugasan-klinis', [HrdRiwayatKarirController::class, 'sendReminderPenugasanKlinis']);
    });

    // Diklat - master HRD
    Route::post('/hrd/diklat', [DiklatController::class, 'storeMaster']);
    Route::put('/hrd/diklat/{id}', [DiklatController::class, 'updateMaster']);
    Route::get('/hrd/diklat/{id}/peserta', [DiklatController::class, 'peserta']);
    Route::post('/hrd/diklat/{id}/peserta', [DiklatController::class, 'syncPeserta']);
    Route::get('/hrd/diklat/status/layak', [DiklatController::class, 'menungguKelayakan']);
    Route::get('/hrd/diklat/status/validasi', [DiklatController::class, 'menungguValidasi']);
    Route::patch('/hrd/diklat/{id}/status/layak', [DiklatController::class, 'updateStatusKelayakan']);
    Route::patch('/hrd/diklat/{id}/status/validasi', [DiklatController::class, 'updateStatusValidasi']);

    // Generate Laporan
    Route::get('/generate/laporan-diklat', [\App\Http\Controllers\Api\LaporanController::class, 'laporanDiklat']);
});

// Authenticated - master data
Route::middleware([
    JwtAuthMiddleware::class,
])->prefix('form')->group(function () {
    // Master data
    Route::get('/kategori-diklat', [MasterDataController::class, 'kategoriDiklat']);
    Route::get('/tipe-diklat', [MasterDataController::class, 'tipeDiklat']);
    Route::get('/jenis-pegawai', [MasterDataController::class, 'jenisPegawai']);
    Route::get('/unit-kerja', [MasterDataController::class, 'unitKerja']);
    Route::get('/jenis-biaya', [MasterDataController::class, 'jenisBiaya']);
    Route::get('/golongan-ruang', [MasterDataController::class, 'golonganRuang']);
    Route::get('/profesi', [MasterDataController::class, 'profesi']);
    Route::get('/jenis-sip', [MasterDataController::class, 'jenisSip']);
});

// HRD only - master data CRUD
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

// Authenticated - notifikasi
Route::middleware([
    JwtAuthMiddleware::class,
])->group(function () {
    // Notifikasi
    Route::get('/notifications', [NotificationController::class, 'index']);
    Route::patch('/notifications/{id}/read', [NotificationController::class, 'markAsRead']);
    Route::patch('/notifications/read-all', [NotificationController::class, 'markAllAsRead']);
});

// Public
Route::get('/health', function (): JsonResponse {
    return response()->json([
        'success' => true,
        'message' => 'API is running',
        'data' => [
            'status' => 'up',
        ],
    ]);
});
