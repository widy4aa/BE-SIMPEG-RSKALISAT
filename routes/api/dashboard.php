<?php

use App\Http\Controllers\Api\Dashboard\DashboardController;
use App\Http\Middleware\JwtAuthMiddleware;
use App\Http\Middleware\RoleMiddleware;
use Illuminate\Support\Facades\Route;

Route::middleware([
    JwtAuthMiddleware::class,
    RoleMiddleware::class.':admin,pegawai,hrd,direktur',
])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'show']);
});
