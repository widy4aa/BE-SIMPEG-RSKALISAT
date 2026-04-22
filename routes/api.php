<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ChangeRequestAdminController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\DiklatController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\RiwayatKarirController;
use App\Http\Controllers\Api\RoleController;
use App\Http\Middleware\JwtAuthMiddleware;
use App\Http\Middleware\RoleMiddleware;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login']);
Route::middleware([
    JwtAuthMiddleware::class,
    RoleMiddleware::class.':admin,pegawai,hrd,direktur',
])->get('/role', [RoleController::class, 'show']);

Route::middleware([
    JwtAuthMiddleware::class,
    RoleMiddleware::class.':admin,pegawai,hrd,direktur',
])->get('/dashboard', [DashboardController::class, 'show']);

Route::middleware([
    JwtAuthMiddleware::class,
    RoleMiddleware::class.':admin,pegawai,hrd,direktur',
])->get('/diklat', [DiklatController::class, 'index']);

Route::middleware([
    JwtAuthMiddleware::class,
    RoleMiddleware::class.':pegawai',
])->post('/diklat', [DiklatController::class, 'store']);

Route::middleware([
    JwtAuthMiddleware::class,
    RoleMiddleware::class.':pegawai',
])->patch('/diklat/{id}', [DiklatController::class, 'update']);

Route::middleware([
    JwtAuthMiddleware::class,
    RoleMiddleware::class.':pegawai',
])->delete('/diklat/{id}', [DiklatController::class, 'destroy']);

Route::middleware([
    JwtAuthMiddleware::class,
    RoleMiddleware::class.':admin,pegawai,hrd,direktur',
])->get('/profile', [ProfileController::class, 'show']);

Route::middleware([
    JwtAuthMiddleware::class,
    RoleMiddleware::class.':admin,pegawai,hrd,direktur',
])->get('/riwayat-karir/pendidikan', [RiwayatKarirController::class, 'pendidikan']);

Route::middleware([
    JwtAuthMiddleware::class,
    RoleMiddleware::class.':admin,pegawai,hrd,direktur',
])->get('/riwayat-karir/jabatan', [RiwayatKarirController::class, 'jabatan']);

Route::middleware([
    JwtAuthMiddleware::class,
    RoleMiddleware::class.':admin,pegawai,hrd,direktur',
])->post('/riwayat-karir/jabatan', [RiwayatKarirController::class, 'storeJabatan']);

Route::middleware([
    JwtAuthMiddleware::class,
    RoleMiddleware::class.':admin,pegawai,hrd,direktur',
])->patch('/riwayat-karir/jabatan/{id}', [RiwayatKarirController::class, 'updateJabatan']);

Route::middleware([
    JwtAuthMiddleware::class,
    RoleMiddleware::class.':admin,pegawai,hrd,direktur',
])->post('/riwayat-karir/jabatan/{id}', [RiwayatKarirController::class, 'updateJabatan']);

Route::middleware([
    JwtAuthMiddleware::class,
    RoleMiddleware::class.':admin,pegawai,hrd,direktur',
])->delete('/riwayat-karir/jabatan/{id}', [RiwayatKarirController::class, 'destroyJabatan']);

// Riwayat Pangkat
Route::middleware([
    JwtAuthMiddleware::class,
    RoleMiddleware::class.':admin,pegawai,hrd,direktur',
])->get('/riwayat-karir/pangkat', [RiwayatKarirController::class, 'pangkat']);

Route::middleware([
    JwtAuthMiddleware::class,
    RoleMiddleware::class.':admin,pegawai,hrd,direktur',
])->post('/riwayat-karir/pangkat', [RiwayatKarirController::class, 'storePangkat']);

Route::middleware([
    JwtAuthMiddleware::class,
    RoleMiddleware::class.':admin,pegawai,hrd,direktur',
])->patch('/riwayat-karir/pangkat/{id}', [RiwayatKarirController::class, 'updatePangkat']);

Route::middleware([
    JwtAuthMiddleware::class,
    RoleMiddleware::class.':admin,pegawai,hrd,direktur',
])->post('/riwayat-karir/pangkat/{id}', [RiwayatKarirController::class, 'updatePangkat']);

Route::middleware([
    JwtAuthMiddleware::class,
    RoleMiddleware::class.':admin,pegawai,hrd,direktur',
])->delete('/riwayat-karir/pangkat/{id}', [RiwayatKarirController::class, 'destroyPangkat']);

// Riwayat SIP
Route::middleware([
    JwtAuthMiddleware::class,
    RoleMiddleware::class.':admin,pegawai,hrd,direktur',
])->get('/riwayat-karir/sip', [RiwayatKarirController::class, 'sip']);

Route::middleware([
    JwtAuthMiddleware::class,
    RoleMiddleware::class.':admin,pegawai,hrd,direktur',
])->post('/riwayat-karir/sip', [RiwayatKarirController::class, 'storeSip']);

Route::middleware([
    JwtAuthMiddleware::class,
    RoleMiddleware::class.':admin,pegawai,hrd,direktur',
])->patch('/riwayat-karir/sip/{id}', [RiwayatKarirController::class, 'updateSip']);

Route::middleware([
    JwtAuthMiddleware::class,
    RoleMiddleware::class.':admin,pegawai,hrd,direktur',
])->post('/riwayat-karir/sip/{id}', [RiwayatKarirController::class, 'updateSip']);

Route::middleware([
    JwtAuthMiddleware::class,
    RoleMiddleware::class.':admin,pegawai,hrd,direktur',
])->delete('/riwayat-karir/sip/{id}', [RiwayatKarirController::class, 'destroySip']);

// Riwayat STR
Route::middleware([
    JwtAuthMiddleware::class,
    RoleMiddleware::class.':admin,pegawai,hrd,direktur',
])->get('/riwayat-karir/str', [RiwayatKarirController::class, 'str']);

Route::middleware([
    JwtAuthMiddleware::class,
    RoleMiddleware::class.':admin,pegawai,hrd,direktur',
])->post('/riwayat-karir/str', [RiwayatKarirController::class, 'storeStr']);

Route::middleware([
    JwtAuthMiddleware::class,
    RoleMiddleware::class.':admin,pegawai,hrd,direktur',
])->patch('/riwayat-karir/str/{id}', [RiwayatKarirController::class, 'updateStr']);

Route::middleware([
    JwtAuthMiddleware::class,
    RoleMiddleware::class.':admin,pegawai,hrd,direktur',
])->post('/riwayat-karir/str/{id}', [RiwayatKarirController::class, 'updateStr']);

Route::middleware([
    JwtAuthMiddleware::class,
    RoleMiddleware::class.':admin,pegawai,hrd,direktur',
])->delete('/riwayat-karir/str/{id}', [RiwayatKarirController::class, 'destroyStr']);

// Riwayat Penugasan Klinis
Route::middleware([
    JwtAuthMiddleware::class,
    RoleMiddleware::class.':admin,pegawai,hrd,direktur',
])->get('/riwayat-karir/penugasan-klinis', [RiwayatKarirController::class, 'penugasanKlinis']);

Route::middleware([
    JwtAuthMiddleware::class,
    RoleMiddleware::class.':admin,pegawai,hrd,direktur',
])->post('/riwayat-karir/penugasan-klinis', [RiwayatKarirController::class, 'storePenugasanKlinis']);

Route::middleware([
    JwtAuthMiddleware::class,
    RoleMiddleware::class.':admin,pegawai,hrd,direktur',
])->patch('/riwayat-karir/penugasan-klinis/{id}', [RiwayatKarirController::class, 'updatePenugasanKlinis']);

Route::middleware([
    JwtAuthMiddleware::class,
    RoleMiddleware::class.':admin,pegawai,hrd,direktur',
])->post('/riwayat-karir/penugasan-klinis/{id}', [RiwayatKarirController::class, 'updatePenugasanKlinis']);

Route::middleware([
    JwtAuthMiddleware::class,
    RoleMiddleware::class.':admin,pegawai,hrd,direktur',
])->delete('/riwayat-karir/penugasan-klinis/{id}', [RiwayatKarirController::class, 'destroyPenugasanKlinis']);

Route::middleware([
    JwtAuthMiddleware::class,
    RoleMiddleware::class.':admin,pegawai,hrd,direktur',
])->post('/riwayat-karir/pendidikan', [RiwayatKarirController::class, 'storePendidikan']);

Route::middleware([
    JwtAuthMiddleware::class,
    RoleMiddleware::class.':admin,pegawai,hrd,direktur',
])->patch('/riwayat-karir/pendidikan/{id}', [RiwayatKarirController::class, 'updatePendidikan']);

Route::middleware([
    JwtAuthMiddleware::class,
    RoleMiddleware::class.':admin,pegawai,hrd,direktur',
])->post('/riwayat-karir/pendidikan/{id}', [RiwayatKarirController::class, 'updatePendidikan']);

Route::middleware([
    JwtAuthMiddleware::class,
    RoleMiddleware::class.':admin,pegawai,hrd,direktur',
])->delete('/riwayat-karir/pendidikan/{id}', [RiwayatKarirController::class, 'destroyPendidikan']);

Route::middleware([
    JwtAuthMiddleware::class,
    RoleMiddleware::class.':admin,pegawai,hrd,direktur',
])->patch('/profile', [ProfileController::class, 'update']);

Route::middleware([
    JwtAuthMiddleware::class,
    RoleMiddleware::class.':admin,pegawai,hrd,direktur',
])->post('/profil/profil-picture', [ProfileController::class, 'updateProfilePicture']);

Route::middleware([
    JwtAuthMiddleware::class,
    RoleMiddleware::class.':admin,pegawai,hrd,direktur',
])->post('/profile/profile-picture', [ProfileController::class, 'updateProfilePicture']);

Route::middleware([
    JwtAuthMiddleware::class,
    RoleMiddleware::class.':admin,pegawai,hrd,direktur',
])->post('/profil/ktp', [ProfileController::class, 'uploadKtp']);

Route::middleware([
    JwtAuthMiddleware::class,
    RoleMiddleware::class.':admin,pegawai,hrd,direktur',
])->post('/profile/kk', [ProfileController::class, 'uploadKk']);

Route::middleware([
    JwtAuthMiddleware::class,
])->group(function () {
    Route::get('/notifications', [NotificationController::class, 'index']);
    Route::patch('/notifications/{id}/read', [NotificationController::class, 'markAsRead']);
    Route::patch('/notifications/read-all', [NotificationController::class, 'markAllAsRead']);
});

Route::middleware([
    JwtAuthMiddleware::class,
    RoleMiddleware::class.':admin',
])->prefix('admin')->group(function () {
    Route::get('/change-requests', [ChangeRequestAdminController::class, 'index']);
    Route::get('/change-requests/{id}', [ChangeRequestAdminController::class, 'show']);
    Route::patch('/change-requests/{id}/accept', [ChangeRequestAdminController::class, 'accept']);
    Route::patch('/change-requests/{id}/reject', [ChangeRequestAdminController::class, 'reject']);
});

Route::get('/health', function (): JsonResponse {
    return response()->json([
        'success' => true,
        'message' => 'API is running',
        'data' => [
            'status' => 'up',
        ],
    ]);
});
