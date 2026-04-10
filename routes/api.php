<?php

use App\Http\Controllers\Api\AuthController;
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

Route::get('/health', function (): JsonResponse {
    return response()->json([
        'success' => true,
        'message' => 'API is running',
        'data' => [
            'status' => 'up',
        ],
    ]);
});
