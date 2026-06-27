<?php

use App\Http\Controllers\Api\Generate\CvController;
use App\Http\Controllers\Api\Profile\ProfileController;
use App\Http\Controllers\Api\Role\RoleController;
use App\Http\Middleware\JwtAuthMiddleware;
use App\Http\Middleware\RoleMiddleware;
use Illuminate\Support\Facades\Route;

Route::middleware([
    JwtAuthMiddleware::class,
    RoleMiddleware::class.':admin,pegawai,hrd,direktur',
])->group(function () {
    Route::get('/role', [RoleController::class, 'show']);
    Route::get('/generate/cv', [CvController::class, 'generate']);
    Route::get('/me', [ProfileController::class, 'me']);

    Route::get('/profile', [ProfileController::class, 'show']);
    Route::patch('/profile', [ProfileController::class, 'update']);
    Route::post('/profil/profil-picture', [ProfileController::class, 'updateProfilePicture']);
    Route::post('/profile/profile-picture', [ProfileController::class, 'updateProfilePicture']);
    Route::post('/profil/ktp', [ProfileController::class, 'uploadKtp']);
    Route::post('/profile/kk', [ProfileController::class, 'uploadKk']);
});
