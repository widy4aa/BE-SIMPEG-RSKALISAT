<?php

use App\Http\Controllers\Api\DataKeluargaController;
use App\Http\Controllers\Api\Keluarga\Self\AnakController;
use App\Http\Controllers\Api\Keluarga\Self\KontakDaruratController;
use App\Http\Controllers\Api\Keluarga\Self\OrangTuaController;
use App\Http\Controllers\Api\Keluarga\Self\PasanganController;
use App\Http\Controllers\Api\Keluarga\Self\TanggunganLainController;
use App\Http\Middleware\JwtAuthMiddleware;
use App\Http\Middleware\RoleMiddleware;
use Illuminate\Support\Facades\Route;

Route::middleware([
    JwtAuthMiddleware::class,
    RoleMiddleware::class.':admin,pegawai,hrd,direktur',
])->group(function () {
    Route::get('/keluarga', [DataKeluargaController::class, 'index']);

    Route::get('/keluarga/pasangan', [PasanganController::class, 'index']);
    Route::post('/keluarga/pasangan', [PasanganController::class, 'store']);
    Route::patch('/keluarga/pasangan/{id}', [PasanganController::class, 'update']);
    Route::post('/keluarga/pasangan/{id}', [PasanganController::class, 'update']);
    Route::delete('/keluarga/pasangan/{id}', [PasanganController::class, 'destroy']);

    Route::get('/keluarga/anak', [AnakController::class, 'index']);
    Route::post('/keluarga/anak', [AnakController::class, 'store']);
    Route::patch('/keluarga/anak/{id}', [AnakController::class, 'update']);
    Route::post('/keluarga/anak/{id}', [AnakController::class, 'update']);
    Route::delete('/keluarga/anak/{id}', [AnakController::class, 'destroy']);

    Route::get('/keluarga/orang-tua', [OrangTuaController::class, 'index']);
    Route::post('/keluarga/orang-tua', [OrangTuaController::class, 'store']);
    Route::patch('/keluarga/orang-tua/{id}', [OrangTuaController::class, 'update']);
    Route::delete('/keluarga/orang-tua/{id}', [OrangTuaController::class, 'destroy']);

    Route::get('/keluarga/kontak-darurat', [KontakDaruratController::class, 'index']);
    Route::post('/keluarga/kontak-darurat', [KontakDaruratController::class, 'store']);
    Route::patch('/keluarga/kontak-darurat/{id}', [KontakDaruratController::class, 'update']);
    Route::delete('/keluarga/kontak-darurat/{id}', [KontakDaruratController::class, 'destroy']);

    Route::get('/keluarga/tanggungan-lain', [TanggunganLainController::class, 'index']);
    Route::post('/keluarga/tanggungan-lain', [TanggunganLainController::class, 'store']);
    Route::patch('/keluarga/tanggungan-lain/{id}', [TanggunganLainController::class, 'update']);
    Route::delete('/keluarga/tanggungan-lain/{id}', [TanggunganLainController::class, 'destroy']);
});
