<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ChangeRequestAdminController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\DiklatController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\ProfileController;
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
    RoleMiddleware::class.':admin,pegawai,hrd,direktur',
])->get('/profile', [ProfileController::class, 'show']);

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
])->group(function () {
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
