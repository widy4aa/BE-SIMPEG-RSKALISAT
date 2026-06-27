<?php

use App\Http\Controllers\Api\ChangeRequestAdminController;
use App\Http\Controllers\Api\MessageController;
use App\Http\Controllers\Api\Pegawai\Managed\PegawaiListController;
use App\Http\Controllers\Api\SettingController;
use App\Http\Controllers\Api\StrSipController;
use App\Http\Middleware\JwtAuthMiddleware;
use App\Http\Middleware\RoleMiddleware;
use Illuminate\Support\Facades\Route;

Route::middleware([
    JwtAuthMiddleware::class,
    RoleMiddleware::class.':admin,hrd,direktur',
])->group(function () {
    Route::get('/pegawai', [PegawaiListController::class, 'index']);
    Route::get('/pegawai/{id}', [PegawaiListController::class, 'show']);

    Route::get('/str-sip', [StrSipController::class, 'index']);
    Route::post('/pesan/pegawai/{id}', [MessageController::class, 'sendToPegawai']);
});

Route::middleware([
    JwtAuthMiddleware::class,
    RoleMiddleware::class.':admin',
])->group(function () {
    Route::post('/pegawai', [PegawaiListController::class, 'store']);
    Route::patch('/pegawai/{id}/change-role', [PegawaiListController::class, 'changeRole']);

    Route::get('/settings/whatsapp', [SettingController::class, 'getWhatsappSetting']);
    Route::put('/settings/whatsapp', [SettingController::class, 'updateWhatsappSetting']);

    Route::prefix('admin')->group(function () {
        Route::get('/change-requests', [ChangeRequestAdminController::class, 'index']);
        Route::get('/change-requests/{id}', [ChangeRequestAdminController::class, 'show']);
        Route::patch('/change-requests/{id}/accept', [ChangeRequestAdminController::class, 'accept']);
        Route::patch('/change-requests/{id}/reject', [ChangeRequestAdminController::class, 'reject']);
    });
});
