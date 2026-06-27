<?php

use App\Http\Controllers\Api\Notification\NotificationController;
use App\Http\Middleware\JwtAuthMiddleware;
use Illuminate\Support\Facades\Route;

Route::middleware([
    JwtAuthMiddleware::class,
])->group(function () {
    Route::get('/notifications', [NotificationController::class, 'index']);
    Route::patch('/notifications/{id}/read', [NotificationController::class, 'markAsRead']);
    Route::patch('/notifications/read-all', [NotificationController::class, 'markAllAsRead']);
});
