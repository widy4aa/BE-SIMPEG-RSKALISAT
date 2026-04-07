<?php

use App\Http\Controllers\Api\AuthController;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login']);

Route::get('/health', function (): JsonResponse {
    return response()->json([
        'success' => true,
        'message' => 'API is running',
        'data' => [
            'status' => 'up',
        ],
    ]);
});
